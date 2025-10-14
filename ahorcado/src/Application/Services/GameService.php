<?php declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\Repository\JsonGameRepository as GameRepository;
use App\Infrastructure\Repository\JsonWordRepository as WordProvider;
use App\Infrastructure\Repository\SessionRepository as SessionRepository;
use App\Domain\Entity\Game as Game;

/**
 * Clase GameService
 * @author JesusLugo2002
 * Se encarga de gestionar el flujo del juego y configurar la partida.
 */
final class GameService {
    private array $gameConfig;
    private SessionRepository $sessionRepository;
    private GameRepository $gameRepository;
    private WordProvider $wordProvider;
    private ?Game $game = null;

    public function __construct(private array $config) {
        $this->gameConfig = $config['game'];
        $this->sessionRepository =  new SessionRepository();
        $this->gameRepository = new GameRepository($config['storage']['games_file']);
        $this->wordProvider = new WordProvider($config['storage']['words_file']);
        $this->game = null;

        $this->handleService();
    }
    
    /**
     * Ejecuta la funcion principal del servicio
     *
     * @return void
     */
    public function handleService(): void {
        if (isset($_POST['start_game'])) {
            $this->createNewGame();
        } else if (isset($_POST['restart_game'])) {
            $this->reset();
        }

        if ($sessionGameId = $this->isInGame()) {
            $this->game = $this->gameRepository->load($sessionGameId);
        }
    }
    
    /**
     * Crea un nuevo juego extrayendo una palabra aleatoria y guardando la ID del juego en sesion.
     *
     * @return string La ID del juego creado
     */
    private function createNewGame(): string {
        $randomWord = $this->wordProvider->getRandomWord();
        $this->game = new Game( $randomWord, $this->gameConfig['max_attempts']);

        $gameId = $this->gameRepository->save($this->game, $_POST['player_name']);
        $this->sessionRepository->set("game_id", $gameId);
        header("Location: index.php");
        return $gameId;
    }
        
    /**
     * Destruye la sesion y recarga la web.
     *
     * @return void
     */
    private function reset(): void {
        $this->sessionRepository->destroy();
        header("Location: index.php");
    }
    
    /**
     * Comprueba si la partida esta en juego buscando una ID del juego en sesion
     *
     * @return string La ID del juego si se encuentra en sesion, de lo contrario, `false`
     */
    public function isInGame(): string|bool {
        $sessionGameId = $this->sessionRepository->get("game_id");
        return $sessionGameId ?? false;
    }

    
    /**
     * Devuelve la etiqueta HTML correspondiente con la victoria/derrota de la partida
     *
     * @return string
     */
    public function displayResult(): string {
        $result = "";
        if ($this->game->isLost()) {
            $result = "<p class='text-danger display-5 my-3'>Lastima, ¡has perdido! La palabra era ". $this->game->getWord() .".</p>";
        } else if ($this->game->isWordGuessed()) {
            $result = "<p class='text-success display-5 my-3'>Felicidades, ¡has ganado!</p>";
        }
        return $result;
    }
    
    /**
     * Lanza el "adivinado" de letra y devuelve una alerta si algo salio mal 
     *
     * @return string|null La alerta (en caso de que algo salga mal)
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
    
    /**
     * Devuelve el banner/dibujo de los intentos restantes.
     *
     * @param  int $state Intentos restantes (0 = Perder)
     * @return string La etiqueta HTML que contiene el banner
     */
    public function getStateDraw(int $leftAttempts = 0): string {
        return "<img src='./img/banner$leftAttempts.gif' style='image-rendering: pixelated'/>";
    } 
        
    /**
     * Devuelve la palabra "enmascarada" separada por espacios.
     *
     * @return string
     */
    public function displayMaskedWord(): string {
        return implode(" ", str_split($this->game->getMaskedWord()));
    }
        
    /**
     * Devuelve la lista de letras usadas separada por comas.
     *
     * @return string
     */
    public function displayUsedLetters(): string {
        return implode(", ", $this->game->getUsedLetters());
    }
    
    /**
     * Devuelve los intentos restantes y los intentos maximos.
     *
     * @return array
     */
    public function getAttempts(): array {
        return ["left_attempts" => $this->game->getLeftAttempts(), "max_attempts" => $this->game->getMaxAttempts()];
    }
}

?>