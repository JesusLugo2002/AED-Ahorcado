<?php declare(strict_types=1);

const WORDS_FILEPATH = "./files/words.txt";

class Game {
    private $word;
    public $maxAttempts;
    private $attemptsLeft;
    private $usedLetters;

    public function __construct(string $word, int $maxAttempts = 6, ?array $state = null) {
        $this->word = $word;
        $this->maxAttempts = $maxAttempts;

        if ($state) {
            $this->maxAttempts = $state["max_attempts"];
            $this->attemptsLeft = $state["attempts_left"];
            $this->usedLetters = $state["used_letters"];
        } else {
            $this->maxAttempts = $maxAttempts;
            $this->attemptsLeft = $this->maxAttempts;
            $this->usedLetters = [];
        }
    }

    public function guessLetter(string $letter): void {
        $upperLetter = strtoupper($letter);
        if (in_array($upperLetter, $this->getUsedLetters())) {
            return;
        }
        $this->usedLetters[] = $upperLetter;
        if (!str_contains($this->getWord(), strtoupper($upperLetter))) {
            $this->attemptsLeft--;
        }
    }

    public function getMaskedWord(): string {
        $maskedWord = "";
        foreach (str_split($this->getWord()) as $letter) {
            $maskedWord .= in_array($letter, $this->usedLetters) ? $letter : "_";
        }
        return $maskedWord;
    }

    public function getAttemptsLeft(): int {
        return $this->attemptsLeft;
    }

    public function getUsedLetters(): array {
        return $this->usedLetters;
    }

    public function isWon(): bool {
        return $this->getMaskedWord() == $this->getWord();
    }

    public function isLost(): bool {
        return !$this->isWon() && !$this->attemptsLeft;
    }

    public function getWord(): string {
        return $this->word;
    }

    public function toState(): array {
        return ["word" => $this->getWord(), "max_attempts" => $this->maxAttempts, "attempts_left" => $this->getAttemptsLeft(), "used_letters" => $this->getUsedLetters()];
    }
}

class WordProvider {
    public $filePath;

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }

    public function getRandomWord(): string|false {
        if (!$words = file($this->filePath)) {
            return false;
        }
        $randomWord = $words[array_rand($words)];
        $cleanedWord = iconv('utf-8', 'ASCII//TRANSLIT', trim($randomWord));
        return strtoupper($cleanedWord);
    }
}

class Storage {
    private $key;

    public function __construct(String $key = "ahorcado") {
        $this->key = $key;
        $_SESSION["key"] = $key;
        session_start();
    }

    public function get(string $name, mixed $default = ""): mixed {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return $default;
    }

    public function set(string $name, mixed $value): void {
        $_SESSION[$name] = $value;
    }

    public function reset(): void {
        session_destroy();
        header("Location: index.php");
    }
}

class Renderer {
    static public function ascii(int $attemptsLeft): string {
        $status = [
        6 => " 
  +---+
  |   |
      |
      |
      |
      |
========= ",
        5 => " 
  +---+
  |   |
  O   |
      |
      |
      |
========= ",
        4 => " 
  +---+
  |   |
  O   |
  |   |
      |
      |
========= ",
        3 => " 
  +---+
  |   |
  O   |
 /|   |
      |
      |
========= ",
        2 => " 
  +---+
  |   |
  O   |
 /|\  |
      |
      |
========= ",
        1 => " 
  +---+
  |   |
  O   |
 /|\  |
 /    |
      |
========= ",
        0 => " 
  +---+
  |   |
  O   |
 /|\  |
 / \  |
      |
========= "
    ];
        return "<pre>" . $status[$attemptsLeft] . "</pre>";
    }
}

$storage = new Storage();
$wordProvider = new WordProvider(WORDS_FILEPATH);
$state = $storage->get("state", null);
$randomWord = $state["word"] ?? $wordProvider->getRandomWord();
$game = new Game($randomWord, state: $state);

if (isset($_POST['letter'])) {
    $game->guessLetter($_POST['letter']);
}

$word = $game->getWord();
$message = "";

if ($game->isWon()) {
    $message = "Felicidades ¡Ganaste! La palabra era: $word"; 
} else if ($game->isLost()) {
    $message = "Lo siento ¡Perdiste! La palabra era: $word";
}

$storage->set("state", $game->toState());
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ahorcado en PHP</title>
</head>
<script>
    window.onload = function() {
        const input = document.querySelector('input[name="letter"]');
        if (input) { 
            input.focus();
        } else {
            const input = document.querySelector('a[href="reset.php"]');
            if (input) input.focus();
        }
    };
</script>
<body>
<h1>Juego del Ahorcado</h1>

<?php echo Renderer::ascii($game->getAttemptsLeft()); ?>

<p>Palabra: <?php echo implode(" ", str_split($game->getMaskedWord())); ?></p>
<p>Intentos restantes: <?php echo $game->getAttemptsLeft(); ?></p>
<p>Letras usadas: <?php echo implode(", ", $game->getUsedLetters()); ?></p>

<?php if (!$message): ?>
    <form method="post">
        <label>Introduce una letra:</label>
        <input type="text" name="letter" maxlength="1" required>
        <button type="submit">Adivinar</button>
    </form>
<?php else: ?>
    <p><strong><?php echo $message; ?></strong></p>
    <a href="reset.php">Jugar de nuevo</a>
<?php endif; ?>

</body>
</html>