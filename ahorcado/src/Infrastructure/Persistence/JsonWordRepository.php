<?php declare(strict_types=1); 

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\WordRepositoryInterface as WordRepositoryInterface;

/**
 * Clase JsonWordRepository
 * @author JesusLugo2002
 * Encargada de la gestion de las palabras disponibles para el juego.
 */
final class JsonWordRepository implements WordRepositoryInterface {

    public function __construct(private string $file) {}
    
    /**
     * Aplica el formato requerido en el juego (mayusculas, sin acentos) a la palabra
     * pasada por `$word`.
     *
     * @param  string $word La palabra a formatear.
     * @return string La palabra formateada.
     */
    private function formatWord(string $word): string {
        $word = iconv('utf-8', 'ASCII//TRANSLIT', $word);
        return trim(strtoupper($word));
    }
        
    /**
     * Obtiene una palabra aleatoria del fichero y la retorna en su forma normalizada.
     *
     * @return string La palabra aleatoria normalizada.
     */
    public function getRandomWord(): string {
        if (!$json = file_get_contents($this->file)) {
            throw new \RuntimeException("No se puede abrir el fichero $this->file");
        }
        $data = json_decode($json, true);
        $words = $data['words'] ?? [];
        if (!$words) {
            throw new \RuntimeException("No existen palabras disponibles en el fichero $this->file");
        }
        $randomIndex = array_rand($words);
        return $this->formatWord($words[$randomIndex]);
    }
}

?>