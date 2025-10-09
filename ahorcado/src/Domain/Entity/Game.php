<?php declare(strict_types=1);

namespace App\Domain\Entity;

final class Game {
    private string $targetWord;
    private int $maxAttempts;
    private int $leftAttempts;
    private array $usedLetters;

    public function __construct(string $targetWord, int $maxAttempts, ?array $gameState = null) {
        $this->targetWord = $targetWord;
        $this->maxAttempts = $maxAttempts;
        if ($gameState) {
            $this->leftAttempts = $gameState['left_attempts'];
            $this->usedLetters = $gameState['used_letters'];
        } else {
            $this->leftAttempts = $maxAttempts;
            $this->usedLetters = [];
        }
    }

    public function __tostring(): string {
        return "- Palabra a adivinar: $this->targetWord - Intentos: $this->leftAttempts/$this->maxAttempts - Enmascarada: " . $this->getMaskedWord() . " - Status: " . $this->getStatus();
    }

    public function getWord(): string {
        return $this->targetWord;
    }

    public function getLeftAttempts(): int {
        return $this->leftAttempts;
    }

    public function getMaxAttempts(): int {
        return $this->maxAttempts;
    }

    public function getUsedLetters(): array {
        return $this->usedLetters;
    }

    public function getStatus(): string {
        if ($this->leftAttempts == $this->maxAttempts) return "Started";
        if ($this->isLost()) return "Lost";
        if ($this->isWordGuessed()) return "Won";
        return "In progress";
    }

    private function normalizeLetter(string $letter) {
        return iconv('utf-8', 'ASCII//TRANSLIT', strtoupper($letter));
    }
    
    public function getMaskedWord(): string {
        $maskedWord = "";
        foreach (str_split($this->targetWord) as $letter) {
            $maskedWord .= in_array($letter, $this->usedLetters) ? $letter : "_";
        }
        return $maskedWord;
    }
    public function guess(string $letter): bool {
        if (!$letter) throw new \InvalidArgumentException("Debe haber un caracter");
        $letter = $this->normalizeLetter($letter);
        if (!ctype_alpha($letter)) throw new \Exception("El caracter no es alfabético");
        if (in_array($letter, $this->usedLetters)) throw new \Exception("La letra ya fue usada");
        $this->usedLetters[] = $letter;
        if (!str_contains($this->targetWord, $letter)) {
            $this->leftAttempts--;
            return false;
        }
        return true;
    }

    public function isWordGuessed(): bool {
        return $this->targetWord == $this->getMaskedWord();
    }

    public function isLost(): bool {
        return $this->leftAttempts <= 0 && !$this->isWordGuessed();
    }

    public function toArray(): array {
        return [
            "target_word" => $this->targetWord,
            "max_attempts" => $this->maxAttempts,
            "left_attempts" => $this->leftAttempts,
            "used_letters" => $this->usedLetters
        ];
    }

    public static function fromArray(array $gameState): Game {
        return new Game(
            $gameState['target_word'],
            $gameState['max_attempts'],
            $gameState
        );
    }

}

?>