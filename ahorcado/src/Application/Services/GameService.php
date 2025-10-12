<?php declare(strict_types=1);

namespace App\Application\Services;

use App\Infrastructure\Persistence\JsonGameRepository as GameRepository;
use App\Infrastructure\Persistence\JsonWordRepository as WordProvider;
use App\Infrastructure\Persistence\SessionRepository as SessionRepository;
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
    private ?Game $game;

    public function __construct(private array $config) {
        $this->gameConfig = $config['game'];
        $this->sessionRepository = new SessionRepository();
        $this->gameRepository = new GameRepository($config['storage']['games_file']);
        $this->wordProvider = new WordProvider($config['storage']['words_file']);
        $this->game = null;

        if (isset($_POST['start_game'])) {
            $this->createNewGame();
        } else if (isset($_POST['restart_game'])) {
            $this->reset();
        }
    }
    
    /**
     * Devuelve el juego configurado para continuar la partida.
     *
     * @return Game Juego configurado.
     */
    public function handle(): ?Game {
        $sessionInGame = $this->sessionRepository->get("in_game");
        $sessionGameId = $this->sessionRepository->get("game_id");
        $isInGame = isset($sessionInGame) && $sessionInGame;
        $isGameIdSaved = isset($sessionGameId);

        if ($isInGame && $isGameIdSaved) {
            $this->game = $this->gameRepository->load($sessionGameId);
        }
        return $this->game;
    }
    
    /**
     * Crea un juego nuevo con una palabra aleatoria y guarda sus datos en sesion.
     *
     * @return void
     */
    private function createNewGame(): void {
        $this->sessionRepository->set("in_game", true);
        $randomWord = $this->wordProvider->getRandomWord();
        $this->game = new Game( $randomWord, $this->gameConfig['max_attempts']);
        $gameId = $this->gameRepository->save($this->game, $_POST['player_name']);
        $this->sessionRepository->set("game_id", $gameId);
        header("Location: index.php");
    }
    
    /**
     * Reinicia el juego.
     *
     * @return void
     */
    private function reset(): void {
        $this->sessionRepository->destroy();
        header("Location: index.php");
    }
}

?>