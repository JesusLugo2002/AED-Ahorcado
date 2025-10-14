<?php declare(strict_types=1);

require __DIR__ . '/../src/Infrastructure/Autoload/Autoloader.php';
\App\Infrastructure\Autoload\Autoloader::register('App\\', __DIR__ . '/../src');
$config = require __DIR__ . '/../config/config.php';
$viewsDirectory = $config['storage']['views_dir'];

use App\Application\Services\GameService as GameService;

$gameService = new GameService($config);
if ($inGame = $gameService->isInGame()) {
    $alert = $gameService->handleGuessLetter();
    $attempts = $gameService->getAttempts();
    $leftAttempts = $attempts['left_attempts'];
    $maxAttempts = $attempts['max_attempts'];
    $usedLetters = $gameService->displayUsedLetters();
    $maskedWord = $gameService->displayMaskedWord();
    $result = $gameService->displayResult();
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Jesus Lugo">
    <title>El Ahorcado - Jesús Lugo</title>
    <link rel="shortcut icon" href="./icon.svg" type="image/x-icon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
    <main class="container d-flex justify-content-center align-items-middle flex-column">
        <?php include "$viewsDirectory/title.html"?>
        <?php if ($inGame): ?>
            <?php echo $gameService->getStateDraw($leftAttempts) ?>
            <div class="row border-bottom pb-3 mb-3">
                <?php include "$viewsDirectory/inGame/leftSection.php" ?>
                <?php include "$viewsDirectory/inGame/rightSection.php" ?>
            </div>
            <div class="row text-center fw-italic">
                <?php include "$viewsDirectory/inGame/stats.php" ?>
            </div>
        <?php else: ?>
            <?php echo $gameService->getStateDraw() ?>
            <?php include "$viewsDirectory/newGameForm.html" ?>
        <?php endif ?>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
