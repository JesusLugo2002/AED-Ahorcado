<?php declare(strict_types=1);

namespace App\Presentation\Controllers;

use App\Domain\Entity\Game as Game;
use App\Infrastructure\Persistence\JsonGameRepository as GameRepository;

final class GameController {
    private GameRepository $gameRepository;
    public function __construct(private Game $game, private array $config) {
        $this->gameRepository = new GameRepository($config['storage']['games_file']);
    }   

    public function getResult(): string {
        $result = "";
        if ($this->game->isLost()) {
            $result = "<p class='text-danger display-5 my-3'>Lastima, ¡has perdido! La palabra era ". $this->game->getWord() .".</p>";
        } else if ($this->game->isWordGuessed()) {
            $result = "<p class='text-success display-5 my-3'>Felicidades, ¡has ganado!</p>";
        }
        return $result;
    }

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