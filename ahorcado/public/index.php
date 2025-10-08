<?php 
require __DIR__ . '/../src/Infrastructure/Autoload/Autoloader.php';
\App\Infrastructure\Autoload\Autoloader::register('App\\', __DIR__ . '/../src');
$config = require __DIR__ . '/../config/config.php';

use App\Infrastructure\Persistence\JsonWordRepository as WordProvider;

$wordProvider = new WordProvider($config['storage']['words_file']);
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
