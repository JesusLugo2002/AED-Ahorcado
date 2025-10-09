<?php declare(strict_types=1);
require __DIR__ . '/../src/Infrastructure/Autoload/Autoloader.php';
\App\Infrastructure\Autoload\Autoloader::register('App\\', __DIR__ . '/../src');
$config = require __DIR__ . '/../config/config.php';

use App\Infrastructure\Persistence\JsonWordRepository as WordProvider;
use App\Domain\Entity\Game as Game;

$wordProvider = new WordProvider($config['storage']['words_file']);
$game = new Game($wordProvider->getRandomWord(), $config['game']['max_attempts']);

echo $game;
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="author" content="Jesus Lugo">
    <title>Ahorcado en PHP</title>
</head>
<body>
</body>
</html>
