<?php declare(strict_types=1);

namespace App\Presentation\Controllers;

/**
 * Clase Renderer
 * @author JesusLugo2002
 * Se encarga de devolver elementos web independientes del estado del juego.
 */
final class Renderer {    
    /**
     * Devuelve el banner/imagen a mostrar dependiendo del numero de estado recibido.
     *
     * @param  int $state Estado del ahorcado
     * @return string Etiqueta <img/> que contiene la url y estilo.
     */
    public static function getState(int $state = 6): string {
        return "<img src='./img/banner$state.gif' style='image-rendering: pixelated'/>";
    } 
    
    /**
     * Devuelve un `string` con separacion de la palabra pasada por `$maskedWord`.
     *
     * @param  string $maskedWord Palabra a separar.
     * @return string Palabra separada.
     */
    public static function displayMaskedWord(string $maskedWord): string {
        return implode(" ", str_split($maskedWord));
    }
    
    /**
     * Devuelve, separado por comas, las letras usadas.
     *
     * @param  array $usedLetters Las letras usadas de la partida.
     * @return string Un texto con las letras separadas por comas.
     */
    public static function displayUsedLetters(array $usedLetters): string {
        return implode(", ", $usedLetters);
    }
}
?>