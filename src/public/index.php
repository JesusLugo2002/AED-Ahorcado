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
    
    /**
     * Recibe una letra y resta un intento (o no) si la palabra objetivo contiene (o no) esta letra.
     *
     * @param  string $letter
     * @return void
     */
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
    
    /**
     * Devuelve la palabra objetivo reemplazando las letras con "_" y dejando descubiertas las letras acertadas.
     *
     * @return string
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
     * @return int
     */
    public function getAttemptsLeft(): int {
        return $this->attemptsLeft;
    }
    
    /**
     * Devuelve un array con las letras usadas en la partida.
     *
     * @return array
     */
    public function getUsedLetters(): array {
        return $this->usedLetters;
    }
    
    /**
     * Determina si el jugador obtuvo una victoria.
     *
     * @return bool
     */
    public function isWon(): bool {
        return $this->getMaskedWord() == $this->getWord();
    }
    
    /**
     * Determina si el jugador ha perdido la partida.
     *
     * @return bool
     */
    public function isLost(): bool {
        return !$this->isWon() && !$this->attemptsLeft;
    }
    
    /**
     * Devuelve la palabra objetivo.
     *
     * @return string
     */
    public function getWord(): string {
        return $this->word;
    }
    
    /**
     * Serializa las variables de la partida para su uso o almacenamiento.
     *
     * @return array
     */
    public function toState(): array {
        return ["word" => $this->getWord(), "max_attempts" => $this->maxAttempts, "attempts_left" => $this->getAttemptsLeft(), "used_letters" => $this->getUsedLetters()];
    }
}

class WordProvider {
    public $filePath;

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }
    
    /**
     * Devuelve una palabra aleatoria del fichero con el que trabaja la clase.
     *
     * @return string
     */
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
    
    /**
     * Devuelve el valor guardado en `$_SESSION` bajo la clave `$name` pasada,
     * y si no existe dicho valor o clave, devuelve el valor determinado
     * en `$default`.
     *
     * @param  string $name
     * @param  mixed $default
     * @return mixed
     */
    public function get(string $name, mixed $default = ""): mixed {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return $default;
    }
    
    /**
     * Guarda el valor `$value` en `$_SESSION` bajo la clave `$name`.
     *
     * @param  mixed $name
     * @param  mixed $value
     * @return void
     */
    public function set(string $name, mixed $value): void {
        $_SESSION[$name] = $value;
    }
    
    /**
     * Destruye la sesion actual y recarga la pagina.
     *
     * @return void
     */
    public function reset(): void {
        session_destroy();
        header("Location: index.php");
    }
}

class Renderer {    
    /**
     * Devuelve una etiqueta `<pre/>` que contiene el dibujo segun el
     * numero de intentos restantes.
     *
     * @param  int $attemptsLeft
     * @return string
     */
    static public function ascii(int $attemptsLeft): string {
        $status = [
        6 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                                |                                           '-'
                                                                |
        ((_))                                                   |
        (o o)                                                   |
=========\_/===============================================================================================================",
        5 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                            O   |                                           '-'
                                                                |
        ((_))                                                   |
        (o o)                                                   |
=========\_/===============================================================================================================",
        4 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                            O   |                                           '-'
                                                            |   |
        ((_))                                                   |
        (o o)                                                   |
=========\_/===============================================================================================================",
        3 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                            O   |                                           '-'
                                                           /|   |
        ((_))                                                   |
        (o o)                                                   |
=========\_/===============================================================================================================",
        2 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                            O   |                                           '-'
                                                           /|\  |
        ((_))                                                   |
        (o o)                                                   |
=========\_/===============================================================================================================",
        1 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                            O   |                                           '-'
                                                           /|\  |
        ((_))                                              /    |
        (o o)                                                   |
=========\_/===============================================================================================================",
        0 => " 
                                                            +---+                                           .-.
                                                            |   |                                            ) )
                                                            O   |                                           '-'
                                                           /|\  |
        ((_))                                              / \  |
        (o o)                                                   |
=========\_/==============================================================================================================="
    ];
        return "<pre class='row justify-content-center'>" . $status[$attemptsLeft] . "</pre>";
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
    $message = "<p class='text-success display-5 text-center'>Felicidades, ¡Has ganado!</p>"; 
} else if ($game->isLost()) {
    $message = "<p class='text-danger display-5 text-center'>Lo siento, ¡Has perdido!</p>
    <p class='text-danger fs-1 text-center'>La palabra era <span class='fw-bold'>$word</span>.</p>";
}

$storage->set("state", $game->toState());
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Jesus Lugo">
    <title>El Ahorcado - Jesús Lugo</title>
    <link rel="shortcut icon" href="./img/icon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
    <main class="container d-flex justify-content-center align-items-middle flex-column">
        <pre class="row justify-content-center mt-5">

██████████ ████       █████████   █████                                                █████         
░░███░░░░░█░░███      ███░░░░░███ ░░███                                                ░░███          
 ░███  █ ░  ░███     ░███    ░███  ░███████    ██████  ████████   ██████   ██████    ███████   ██████ 
 ░██████    ░███     ░███████████  ░███░░███  ███░░███░░███░░███ ███░░███ ░░░░░███  ███░░███  ███░░███
 ░███░░█    ░███     ░███░░░░░███  ░███ ░███ ░███ ░███ ░███ ░░░ ░███ ░░░   ███████ ░███ ░███ ░███ ░███
 ░███ ░   █ ░███     ░███    ░███  ░███ ░███ ░███ ░███ ░███     ░███  ███ ███░░███ ░███ ░███ ░███ ░███
 ██████████ █████    █████   █████ ████ █████░░██████  █████    ░░██████ ░░████████░░████████░░██████ 
░░░░░░░░░░ ░░░░░    ░░░░░   ░░░░░ ░░░░ ░░░░░  ░░░░░░  ░░░░░      ░░░░░░   ░░░░░░░░  ░░░░░░░░  ░░░░░░  
                                                                                                      
                                                                                                      

        </pre>
        <?php echo Renderer::ascii($game->getAttemptsLeft()); ?>

        <div class="row">
            <div class="col">
                <div class="row text-center">
                    <p class="display-5">Adivina la palabra</p>
                    <p class="display-5 fw-bold"><?php echo implode(" ", str_split($game->getMaskedWord())); ?></p>
                </div>
                <div class="row mt-3 text-center">
                    <p class="fs-4">Intentos restantes → <?php echo $game->getAttemptsLeft(); ?></p>
                    <p class="fs-4">Letras usadas → <?php echo implode(", ", $game->getUsedLetters()); ?></p>
                </div>
            </div>
            <div class="col">
                <div class="row">
                    <?php if (!$message): ?>
                        <form class="border-0 p-0 d-flex justify-content-center flex-column align-items-middle" method="post">
                            <label class="display-5 text-center m-0 p-0">Introduce una letra</label>
                            <div class="input-group mt-4">
                                <span class="input-group-text" id="basic-addon1">A-Z</span>
                                <input class="form-control form-control-lg" type="text" name="letter" maxlength="1" required>
                            </div>
                            <button class="btn btn-outline-dark mt-1 px-5" type="submit">Adivinar</button>
                        </form>
                    <?php else: ?>
                        <?php echo $message; ?>
                        <a class="btn btn-outline-dark" href="reset.php">Jugar de nuevo</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="alert alert-primary text-center mt-5" role="alert">
                No se ha lastimado ningún muñequito de palos en la creación de este juego.
            </div>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>