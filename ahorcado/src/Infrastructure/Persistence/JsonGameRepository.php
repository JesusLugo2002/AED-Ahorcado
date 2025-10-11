<?php declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Entity\Game as Game;
use App\Domain\Repository\GameRepositoryInterface as GameRepositoryInterface;

/**
 * Clase JsonGameRepository
 * @author JesusLugo2002
 * Clase encargada de la persistencia entre rondas, 
 * guardando/cargando los datos en un fichero externo
 * determinado.
 */
final class JsonGameRepository implements GameRepositoryInterface {
    public function __construct(private string $filename) {
        if (!is_file($filename)) {
            file_put_contents($filename, json_encode(['games' => []], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
        }
    }
    
    /**
     * Añade/actualiza el registro con los datos de la partida actual
     * en el fichero externo.
     *
     * @param  Game $game Juego a guardar
     * @return int Id del juego guardado
     */
    public function save(Game $game, string $playerName = ""): string
    {
        $data = $this->readAll();
        if (!$id = $game->getId()) {
            $id = "$playerName-" . $this->getNextId();
            $game->setId($id);
        }
        $data['games'][$id] = $game->toArray();
        $this->writeAll($data);
        return $id;
    }
    
    /**
     * Carga los datos de la partida bajo el `$gameId` pasado, o devuelve `null` si no se encuentra.
     *
     * @param  string $gameId Id de la partida
     * @return Game|null
     */
    public function load(string $gameId): ?Game
    {
        $data = $this->readAll();
        return isset($data['games'][$gameId]) ? Game::fromArray($data['games'][$gameId]) : null;
    }

    private function getNextId(): string {
        $logs = $this->readAll()['games'];
        return strval(count($logs));
    }
    
    /**
     * Devuelve un array con los datos del fichero externo, o un array vacio en caso
     * de no encontrar datos.
     *
     * @return array
     */
    private function readAll(): array
    {
        $stream = fopen($this->filename, 'c+');
        if ($stream === false) throw new \RuntimeException('No se pudo abrir el fichero de juegos');
        try {
            flock($stream, LOCK_SH);
            $content = stream_get_contents($stream);
            $json = $content ? json_decode($content, true) : ['games' => []];
            return is_array($json) ? $json : ['games' => []];
        } finally {
            flock($stream, LOCK_UN);
            fclose($stream);
        }
    }
    
    /**
     * Escribe/sobrescribe el fichero externo con los datos pasados por `$data`
     *
     * @param  array $data Datos de las partidas
     * @return void
     */
    private function writeAll(array $data): void
    {
        $tmp = "$this->filename.tmp";
        $stream = fopen($tmp, 'w');
        if ($stream === false) throw new \RuntimeException('No se pudo escribir el fichero de juegos');
        try {
            flock($stream, LOCK_EX);
            fwrite($stream, json_encode($data, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
            fflush($stream);
            flock($stream, LOCK_UN);
        } finally {
            fclose($stream);
        }
        rename($tmp, $this->filename);
    }
}

?>