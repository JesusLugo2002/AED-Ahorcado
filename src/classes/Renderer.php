<?php declare(strict_types=1); 

namespace classes;

/**
 * clase Renderer
 * @author JesusLugo2002
 * Encargada de mostrar el dibujo del ahorcado y otros componentes html.
 */
class Renderer {    
    /**
     * Devuelve una etiqueta `<pre/>` que contiene el dibujo segun el
     * numero de intentos restantes.
     *
     * @param  int $attemptsLeft Los intentos restantes
     * @return string El dibujo a imprimir
     */
    public static function ascii(int $attemptsLeft): string {
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
    
    /**
     * Devuelve un mensaje si la partida ha finalizado, y dicho mensaje depende de si ha sido una victoria
     * o una derrota. De lo contrario, devuelve una cadena vacia.
     *
     * @param  mixed $isWon Si ha sido una victoria
     * @param  mixed $isLost Si ha sido una derrota
     * @param  mixed $word Palabra que aparece en el mensaje final
     * @return string Mensaje final o cadena vacia
     */
    public static function getEndMessage(bool $isWon, bool $isLost, string $word): string {
        if (!$isWon && !$isLost) {
            return "";
        }
        
        $wonMessage = "<p class='text-success display-5 text-center'>Felicidades, ¡Has ganado!</p>";
        $lostMessage = "<p class='text-danger display-5 text-center'>Lo siento, ¡Has perdido!</p>
        <p class='text-danger fs-1 text-center'>La palabra era <span class='fw-bold'>$word</span>.</p>";
        return $isWon ? $wonMessage : $lostMessage;
    }
}


?>