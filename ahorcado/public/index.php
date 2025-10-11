<?php declare(strict_types=1);
require __DIR__ . '/../src/Infrastructure/Autoload/Autoloader.php';
\App\Infrastructure\Autoload\Autoloader::register('App\\', __DIR__ . '/../src');
$config = require __DIR__ . '/../config/config.php';
$viewsDirectory = $config['storage']['views_dir'];

use App\Infrastructure\Persistence\JsonWordRepository as WordProvider;
use App\Infrastructure\Persistence\JsonGameRepository as GameRepository;
use App\Infrastructure\Persistence\SessionRepository as SessionRepository;
use App\Domain\Entity\Game as Game;
use App\Presentation\Controllers\Renderer as Renderer;

$sessionRepository = new SessionRepository();
$gameRepository = new GameRepository($config['storage']['games_file']);

if (isset($_POST['start_game'])) {
    $sessionRepository->set("in_game", true);
    $wordProvider = new WordProvider($config['storage']['words_file']);
    $word = $wordProvider->getRandomWord();
    $maxAttempts = $config['game']['max_attempts'];
    $game = new Game( $word, $maxAttempts);
    $gameId = $gameRepository->save($game, $_POST['player_name']);
    $sessionRepository->set("game_id", $gameId);
    header("Location: index.php");
} else if (isset($_POST['restart_game'])) {
    session_destroy();
    header("Location: index.php");
}

$sessionInGame = $sessionRepository->get("in_game");
$sessionGameId = $sessionRepository->get("game_id");
$isInGame = isset($sessionInGame) && $sessionInGame;
$isGameIdSaved = isset($sessionGameId);

if ($isInGame && $isGameIdSaved) {
    $game = $gameRepository->load($sessionGameId);
    $alert = "";
    
    if (isset($_POST['letter'])) {
        try {
            $game->guess($_POST['letter']);
            $gameRepository->save($game);
        } catch (\Throwable $th) {
            $alert = $th->getMessage();
        }
    }
    $maskedWord = Renderer::displayMaskedWord($game->getMaskedWord());
    $usedLetters = $game->getUsedLetters();
    $leftAttempts = $game->getLeftAttempts();
    $maxAttempts = $game->getMaxAttempts();

    $result = "";
    if ($game->isLost()) {
        $result = "<p class='text-danger display-5 my-3'>Lastima, ¡has perdido! La palabra era ". $game->getWord() .".</p>";
    } else if ($game->isWordGuessed()) {
        $result = "<p class='text-success display-5 my-3'>Felicidades, ¡has ganado!.</p>";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Jesus Lugo">
    <title>Ahorcado en PHP</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <main class="container d-flex justify-content-center align-items-middle flex-column">
        <?php include "$viewsDirectory/title.html"?>
        <?php if ($isInGame): ?>
            <?php echo Renderer::getState($leftAttempts) ?>
            <div class="row border-bottom pb-3 mb-3">
                <div class="col text-center">
                    <h2 class="display-5 my-3">Adivina la palabra</h2>
                    <h2 class="display-5 fw-bold">
                        <?php echo $maskedWord ?>
                    </h2>
                </div>
                <div class="col text-center">
                    <?php if ($result): ?>
                        <?php echo $result ?>
                        <form method="post" class="text-center mt-3">
                            <input type="hidden" name="restart_game">
                            <input type="submit" class="col btn btn-outline-dark mt-2" value="Nuevo juego">
                        </form>
                    <?php else: ?>
                        <h2 class="display-5 my-3">Introduce una letra</h2>
                        <?php if ($alert): ?>
                            <div class="alert alert-warning mt-3 text-center fw-bold" role="alert">
                                <i class="bi bi-exclamation-triangle-fill"></i> <?php echo $alert ?>
                            </div>
                        <?php endif ?>
                        <?php include "$viewsDirectory/gameForm.html" ?>
                    <?php endif ?>  
                </div>
            </div>
            <div class="row text-center fw-italic">
                <p>
                    <span class="text-danger"><i class="bi bi-heart-fill"></i> Intentos: <?php echo "$leftAttempts/$maxAttempts"?></span>
                    <?php if ($usedLetters): ?>
                        <span class="text-secondary px-2">
                            <i class="bi bi-alphabet-uppercase"></i> Letras usadas: <?php echo Renderer::displayUsedLetters($usedLetters) ?>
                        </span>
                    <?php endif ?>
                </p>
            </div>
        <?php else: ?>
            <?php echo Renderer::getState() ?>
            <?php include "$viewsDirectory/newGameForm.html" ?>
        <?php endif ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
