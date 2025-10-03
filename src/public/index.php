<?php
session_start();

const WORDS_FILEPATH = "./files/words.txt";

class WordProvider {
    public $filePath;

    function __construct(string $filePath) {
        $this->filePath = $filePath;
    }

    function getRandomWord(): string|false {
        if (!$words = file($this->filePath)) {
            return false;
        }
        $randomWord = $words[array_rand($words)];
        $cleanedWord = iconv('utf-8', 'ASCII//TRANSLIT', trim($randomWord));
        return strtoupper($cleanedWord);
    }
}

if (!isset($_SESSION['palabra'])) {
    $wordProvider = new WordProvider(WORDS_FILEPATH);
    $_SESSION['palabra'] = $wordProvider->getRandomWord();
    $_SESSION['intentos'] = 6;
    $_SESSION['letras_usadas'] = [];
}

if (isset($_POST['letra'])) {
    $letra = strtoupper($_POST['letra']);
    if (!in_array($letra, $_SESSION['letras_usadas'])) {
        $_SESSION['letras_usadas'][] = $letra;
        if (strpos($_SESSION['palabra'], $letra) === false) {
            $_SESSION['intentos']--;
        }
    }
}

$mostrar = "";
foreach (str_split($_SESSION['palabra']) as $letra) {
    $mostrar .= in_array($letra, $_SESSION['letras_usadas']) ? $letra : "_";
}

$mensaje = "";
if ($mostrar === $_SESSION['palabra']) {
    $mensaje = "Felicidades ¡Ganaste! La palabra era: " . $_SESSION['palabra'];
}
if ($_SESSION['intentos'] <= 0) {
    $mensaje = "Lo siento ¡Perdiste! La palabra era: " . $_SESSION['palabra'];
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

<?php echo dibujoAhorcado($_SESSION['intentos']); ?>

<p>Palabra: <?php echo implode(" ", str_split($mostrar)); ?></p>
<p>Intentos restantes: <?php echo $_SESSION['intentos']; ?></p>
<p>Letras usadas: <?php echo implode(", ", $_SESSION['letras_usadas']); ?></p>

<?php if ($mensaje == ""): ?>
    <form method="post">
        <label>Introduce una letra:</label>
        <input type="text" name="letra" maxlength="1" required>
        <button type="submit">Adivinar</button>
    </form>
<?php else: ?>
    <p><strong><?php echo $mensaje; ?></strong></p>
    <a href="reset.php">Jugar de nuevo</a>
<?php endif; ?>

</body>
</html>
