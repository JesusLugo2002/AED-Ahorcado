<?php declare(strict_types=1);

namespace App\Presentation\Controllers;

final class Renderer {
    public static function getState(int $state = 6): string {
        return "<img src='./img/banner$state.gif' style='image-rendering: pixelated'/>";
    } 

    public static function displayMaskedWord(string $maskedWord): string {
        return implode(" ", str_split($maskedWord));
    }

    public static function displayUsedLetters(array $usedLetters): string {
        return implode(", ", $usedLetters);
    }
}
?>