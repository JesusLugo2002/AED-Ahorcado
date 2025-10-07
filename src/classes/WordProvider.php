<?php declare(strict_types=1);

namespace classes;

/**
 * clase WordProvider
 * @author JesusLugo2002
 * Encargado de proveer la palabra aleatoria para el juego usando ficheros externos como fuente
 */
class WordProvider {
    public $filePath;

    public function __construct(string $filePath) {
        $this->filePath = $filePath;
    }
        
    /**
     * Aplica el formato necesario a la palabra pasada para el juego: sin acentos y caracteres no alfabeticos,
     * retornado en mayusculas y sin saltos de linea.
     *
     * @param  mixed $word La palabra a formatear
     * @return string La palabra formateada
     */
    private function formatWord(string $word): string {
        $encodedWord = iconv('utf-8', 'ASCII//TRANSLIT', $word);
        return strtoupper(trim($encodedWord));
    }

    /**
     * Devuelve una palabra aleatoria del fichero con el que trabaja la clase, formateada.
     *
     * @return string La palabra aleatoria y formateada
     */
    public function getRandomWord(): string|false {
        if (!$words = file($this->filePath)) {
            return false;
        }
        $randomWord = $words[array_rand($words)];
        return $this->formatWord($randomWord);
    }
}

?>