<?php declare(strict_types=1);

namespace App\Domain\Repository;

use App\Domain\Entity\Game as Game;

interface GameRepositoryInterface {
    public function save(Game $game): string;
    public function load(string $gameId): Game|null;
}
?>