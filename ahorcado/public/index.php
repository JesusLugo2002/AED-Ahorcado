<?php declare(strict_types=1);
require __DIR__ . '/../src/Infrastructure/Autoload/Autoloader.php';
\App\Infrastructure\Autoload\Autoloader::register('App\\', __DIR__ . '/../src');
$config = require __DIR__ . '/../config/config.php';

use App\Infrastructure\Persistence\JsonWordRepository as WordProvider;
use App\Domain\Entity\Game as Game;

$wordProvider = new WordProvider($config['storage']['words_file']);
$game = new Game($wordProvider->getRandomWord(), $config['game']['max_attempts']);

if (isset($_POST['letter'])) {
    $message = $_POST['letter'];
} else {
    $message = "No hay letra";
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
        <p><?php echo $game ?></p>
        <div class="row">
            <form method="post" class="col text-center">
                <label for="letter">Letra: </label>
                <input type="text" name="letter" id="letter" maxlength="1" required>
                <input type="submit" class="btn btn-outline-success" value="Adivinar">
            </form>
            <p class="col text-center"><?php echo $message ?></p>
            <a href="." class="col btn btn-outline-primary">Reiniciar juego</a>
        </div>
    </main>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
</body>
</html>
