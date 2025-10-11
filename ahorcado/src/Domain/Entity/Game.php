<?php declare(strict_types=1);

namespace App\Domain\Entity;

/**
 * Clase Game
 * @author JesusLugo2002
 * Entidad de dominio que representa una partida (nueva o previa) del juego.
 */
final class Game {
    private ?string $id;
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
        return "Palabra a adivinar: $this->targetWord - Intentos: $this->leftAttempts/$this->maxAttempts - Enmascarada: " . $this->getMaskedWord() . " - Status: " . $this->getStatus();
    }

    public function getId(): string|null {
        return $this->id ?? null;
    }

    public function setId(string $newId): void {
        $this->id = $newId;
    }
    
    /**
     * Devuelve la palabra a adivinar.
     *
     * @return string Palabra objetivo.
     */
    public function getWord(): string {
        return $this->targetWord;
    }
    
    /**
     * Devuelve los intentos restantes.
     *
     * @return int Intentos restantes.
     */
    public function getLeftAttempts(): int {
        return $this->leftAttempts;
    }
    
    /**
     * Devuelve los intentos maximos.
     *
     * @return int Intentos maximos.
     */
    public function getMaxAttempts(): int {
        return $this->maxAttempts;
    }
    
    /**
     * Devuelve una lista de las letras usadas.
     *
     * @return array Letras usadas.
     */
    public function getUsedLetters(): array {
        return $this->usedLetters;
    }
    
    /**
     * Devuelve el estado de la partida.
     *
     * @return string `"Started"` si la partida ha empezado. `"Lost"` si ha perdido.
     *  `"Won"` si ha ganado. `"In progress"` si no hay resultados. 
     */
    public function getStatus(): string {
        if ($this->leftAttempts == $this->maxAttempts) return "Started";
        if ($this->isLost()) return "Lost";
        if ($this->isWordGuessed()) return "Won";
        return "In progress";
    }
    
    /**
     * Normaliza la letra pasada, removiendo tildes y convirtiendolo
     * en mayusculas.
     *
     * @param  string $letter
     * @return string|bool La letra si ha sido normalizada, si no, `false`.
     */
    private function normalizeLetter(string $letter): string|bool {
        return iconv('utf-8', 'ASCII//TRANSLIT', strtoupper($letter));
    }
        
    /**
     * Devuelve la palabra en su version enmascarada (reemplaza caracteres con '_' a menos que
     * la letra haya sido adivinada)
     *
     * @return string La palabra enmascarada.
     */
    public function getMaskedWord(): string {
        $maskedWord = "";
        foreach (str_split($this->targetWord) as $letter) {
            $maskedWord .= in_array($letter, $this->usedLetters) ? $letter : "_";
        }
        return $maskedWord;
    }    
    /**
     * Comprueba si la letra pasada esta en la palabra a adivinar. Si no lo esta, resta un intento.
     *
     * @param  string $letter Letra a buscar en la palabra.
     * @return bool `true` si ha adivinado la letra, si no, `false`.
     */
    public function guess(string $letter): bool {
        if (!$letter) throw new \InvalidArgumentException("Debe haber un caracter");
        if (!$letter = $this->normalizeLetter($letter)) throw new \Exception("No se pudo normalizar el caracter");
        if (!ctype_alpha($letter)) throw new \Exception("El caracter no es alfabético");
        if (in_array($letter, $this->usedLetters)) throw new \Exception("La letra ya fue usada");
        $this->usedLetters[] = $letter;
        if (!str_contains($this->targetWord, $letter)) {
            $this->leftAttempts--;
            return false;
        }
        return true;
    }
    
    /**
     * Comprueba si la palabra ya ha sido adivinada.
     *
     * @return bool
     */
    public function isWordGuessed(): bool {
        return $this->targetWord == $this->getMaskedWord();
    }
    
    /**
     * Comprueba si se ha perdido la partida (el jugador termino sin intentos y sin adivinar la palabra)
     *
     * @return bool
     */
    public function isLost(): bool {
        return $this->leftAttempts <= 0 && !$this->isWordGuessed();
    }
    
    /**
     * Serializa los datos de la partida en un array.
     *
     * @return array Datos serializados en un array asociativo.
     */
    public function toArray(): array {
        return [
            "target_word" => $this->targetWord,
            "max_attempts" => $this->maxAttempts,
            "left_attempts" => $this->leftAttempts,
            "used_letters" => $this->usedLetters
        ];
    }
    
    /**
     * Construye una partida basada en el array asociativo `$gameState`- 
     *
     * @param  array $gameState Array con los datos de la partida previa.
     * @return Game El objeto `Game` con los datos de la partida previa.
     */
    public static function fromArray(array $gameState): Game {
        return new Game(
            $gameState['target_word'],
            $gameState['max_attempts'],
            $gameState
        );
    }

}

?>