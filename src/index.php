<?php declare(strict_types=1);

include_once "./classes/SessionStorage.php";
include_once "./classes/WordProvider.php";
include_once "./classes/Renderer.php";
include_once "./classes/Game.php";

use classes\SessionStorage as Storage;
use classes\WordProvider as WordProvider;
use classes\Renderer as Renderer;
use classes\Game as Game;

const WORDS_FILEPATH = "./data/words.txt";

$wordProvider = new WordProvider(WORDS_FILEPATH);
$storage = new Storage();
$state = $storage->get("state");
$game = new Game($wordProvider->getRandomWord(), state: $state);

if (isset($_POST['letter'])) {
    $game->guessLetter($_POST['letter']);
}

$word = $game->getWord();
$message = Renderer::getEndMessage($game->isWon(), $game->isLost(), $word);
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
<body>
    <main class="container d-flex justify-content-center align-items-middle flex-column">
        <?php echo include "components/title.php" ?>
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
                        <?php include "components/letterForm.php" ?>
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
    <script src="./script.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>