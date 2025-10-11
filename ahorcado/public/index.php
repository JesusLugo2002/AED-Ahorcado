<?php declare(strict_types=1);
require __DIR__ . '/../src/Infrastructure/Autoload/Autoloader.php';
\App\Infrastructure\Autoload\Autoloader::register('App\\', __DIR__ . '/../src');
$config = require __DIR__ . '/../config/config.php';

use App\Infrastructure\Persistence\JsonWordRepository as WordProvider;
use App\Infrastructure\Persistence\JsonGameRepository as GameRepository;
use App\Domain\Entity\Game as Game;

session_start();

$gameRepository = new GameRepository($config['storage']['games_file']);

if (isset($_POST['start_game'])) {
    $_SESSION['in_game'] = true;
    $wordProvider = new WordProvider($config['storage']['words_file']);
    $word = $wordProvider->getRandomWord();
    $maxAttempts = $config['game']['max_attempts'];
    $game = new Game( $word, $maxAttempts);
    $gameId = $gameRepository->save($game, $_POST['player_name']);
    $game->setId($gameId);
    $_SESSION['game_id'] = $gameId;
    header("Location: index.php");
} else if (isset($_POST['restart_game'])) {
    session_destroy();
    header("Location: index.php");
}

$inGame = isset($_SESSION['in_game']) && $_SESSION['in_game'];
$isGameIdSaved = isset($_SESSION['game_id']);

if ($inGame && $isGameIdSaved) {
    $game = $gameRepository->load($_SESSION['game_id']);
    $message = $_POST['letter'] ?? "No hay letra";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Jesus Lugo">
    <title>Ahorcado en PHP</title>  
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body>
    <main class="container mt-5">
        <?php if ($inGame): ?>
            <h3><?php echo $game ?? "" ?></h3>
            <div class="row mt-5">
                <form method="post" class="col text-center">
                    <label for="letter">Letra: </label>
                    <input type="text" name="letter" id="letter" maxlength="1" required>
                    <input type="submit" class="btn btn-outline-success" value="Adivinar">
                </form>
                <p class="col text-center"><?php echo $message ?></p>
                <form method="post" class="col text-center">
                    <input type="hidden" name="restart_game">
                    <input type="submit" class="col btn btn-outline-primary" value="Salir del juego">
                </form>
            </div>
        <?php else: ?>
            <form method="post" action=".">
                <input type="hidden" name="start_game">
                <input type="text" name="player_name" placeholder="Nombre" required>
                <input type="submit" value="Iniciar juego">
            </form>
        <?php endif ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
