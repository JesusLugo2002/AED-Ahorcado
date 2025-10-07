<?php declare(strict_types=1);

namespace classes;

/**
 * clase Game
 * @author JesusLugo2002
 * Encargado de gestionar los datos de cada partida, manteniendo persistencia entre sesiones cuando
 * existe una partida creada.
 */
class Game {
    private $word;
    public $maxAttempts;
    private $attemptsLeft;
    private $usedLetters;

    public function __construct(string $word, int $maxAttempts = 6, ?array $state = null) {
        if ($state) {
            $this->word = $state["word"];
            $this->maxAttempts = $state["max_attempts"];
            $this->attemptsLeft = $state["attempts_left"];
            $this->usedLetters = $state["used_letters"];
        } else {
            $this->word = $word;
            $this->maxAttempts = $maxAttempts;
            $this->attemptsLeft = $this->maxAttempts;
            $this->usedLetters = [];
        }
    }
    
    /**
     * Recibe una letra y resta un intento (o no) si la palabra objetivo contiene (o no) esta letra.
     *
     * @param  string $letter La letra con la que se intenta adivinar la palabra.
     * @return bool `true` si se ha adivinado, si no, `false`.
     */
    public function guessLetter(string $letter): bool {
        $upperLetter = strtoupper($letter);
        if (in_array($upperLetter, $this->getUsedLetters())) {
            return false;
        }
        $this->usedLetters[] = $upperLetter;
        if (!str_contains($this->getWord(), strtoupper($upperLetter))) {
            $this->attemptsLeft--;
        }
        return true;
    }
    
    /**
     * Devuelve la palabra objetivo reemplazando las letras con "_" y dejando descubiertas las letras acertadas.
     *
     * @return string La palabra enmascarada.
     */
    public function getMaskedWord(): string {
        $maskedWord = "";
        foreach (str_split($this->getWord()) as $letter) {
            $maskedWord .= in_array($letter, $this->usedLetters) ? $letter : "_";
        }
        return $maskedWord;
    }
    
    /**
     * Devuelve los intentos restantes de la partida.
     *
     * @return int Intentos restantes de la partida.
     */
    public function getAttemptsLeft(): int {
        return $this->attemptsLeft;
    }
    
    /**
     * Devuelve un array con las letras usadas en la partida.
     *
     * @return array Letras usadas de la partida.
     */
    public function getUsedLetters(): array {
        return $this->usedLetters;
    }
    
    /**
     * Determina si el jugador obtuvo una victoria, igualando la palabra enmascarada con
     * la palabra objetivo.
     *
     * @return bool `true` si el jugador ha ganado, si no, `false`.
     */
    public function isWon(): bool {
        return $this->getMaskedWord() == $this->getWord();
    }
    
    /**
     * Determina si el jugador ha perdido la partida, comprobando que no hayan intentos restantes.
     *
     * @return bool `true` si el jugador ha perdido, si no, `false`.
     */
    public function isLost(): bool {
        return !$this->isWon() && !$this->attemptsLeft;
    }
    
    /**
     * Devuelve la palabra a adivinar.
     *
     * @return string La palabra a adivinar.
     */
    public function getWord(): string {
        return $this->word;
    }
    
    /**
     * Serializa las variables de la partida para su uso o almacenamiento.
     *
     * @return array Un `array` asociativo con los valores de la partida.
     */
    public function toState(): array {
        return ["word" => $this->getWord(), "max_attempts" => $this->maxAttempts, "attempts_left" => $this->getAttemptsLeft(), "used_letters" => $this->getUsedLetters()];
    }
}

?>