<?php declare(strict_types=1);

const WORDS_FILEPATH = "./files/words.txt";

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

$storage = new Storage();
if (!$storage->get("word")) {
    $wordProvider = new WordProvider(WORDS_FILEPATH);
    $storage->set("word", $wordProvider->getRandomWord());
    $storage->set("tries", 6);
    $storage->set("used_letters", []);
}

if (isset($_POST['letter'])) {
    $letter = strtoupper($_POST['letter']);
    $usedLetters = $storage->get("used_letters");
    if (!in_array($letter, $usedLetters)) {
        $usedLetters[] = $letter;
        $storage->set("used_letters", $usedLetters);
        if (strpos($storage->get("word"), $letter) === false) {
            $storage->set("tries", $storage->get("tries") - 1);
        }
    }
}

$output = "";
foreach (str_split($storage->get("word")) as $letter) {
    $output .= in_array($letter, $storage->get("used_letters")) ? $letter : "_";
}

$message = "";
$word = $storage->get("word");
if ($output === $word) {
    $message = "Felicidades ¡Ganaste! La palabra era: $word"; 
}
if ($storage->get("tries") <= 0) {
    $message = "Lo siento ¡Perdiste! La palabra era: $word";
}

function dibujoAhorcado($intentos) {
    $estados = [
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
    return "<pre>" . $estados[$intentos] . "</pre>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Ahorcado en PHP</title>
</head>
<body>
<h1>Juego del Ahorcado</h1>

<?php echo dibujoAhorcado($storage->get("tries")); ?>

<p>Palabra: <?php echo implode(" ", str_split($output)); ?></p>
<p>Intentos restantes: <?php echo $storage->get("tries"); ?></p>
<p>Letras usadas: <?php echo implode(", ", $storage->get("used_letters")); ?></p>

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