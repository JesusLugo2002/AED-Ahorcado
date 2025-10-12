<?php declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Domain\Entity\Game as Game;
use App\Infrastructure\Persistence\JsonGameRepository as GameRepository;

/**
 * Clase GameController
 * @author JesusLugo2002
 * Se encarga de devolver etiquetas HTML y elementos visibles en la web dependientes del estado del juego.
 */
final class GameController {
    private GameRepository $gameRepository;
    public function __construct(private Game $game, private array $config) {
        $this->gameRepository = new GameRepository($config['storage']['games_file']);
    }   
    
    /**
     * Devuelve el mensaje final tras haber perdido/ganado la partida
     *
     * @return string El mensaje en etiquetas <p/>
     */
    public function getResult(): string {
        $result = "";
        if ($this->game->isLost()) {
            $result = "<p class='text-danger display-5 my-3'>Lastima, ¡has perdido! La palabra era ". $this->game->getWord() .".</p>";
        } else if ($this->game->isWordGuessed()) {
            $result = "<p class='text-success display-5 my-3'>Felicidades, ¡has ganado!</p>";
        }
        return $result;
    }
    
    /**
     * Ejecuta el "adivinado" de letra y devuelve una alerta si sucede un error.
     *
     * @return string|null Si todo va bien, no devuelve nada, si no, devuelve el mensaje de error.
     */
    public function handleGuessLetter(): string|null {
        if (isset($_POST['letter'])) {
            try {
                $this->game->guess($_POST['letter']);
                $this->gameRepository->save($this->game);
            } catch (\Throwable $th) {
                return $th->getMessage();
            }
        }
        return null;
    }
}
?>