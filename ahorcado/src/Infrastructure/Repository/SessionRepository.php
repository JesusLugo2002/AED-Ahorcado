<?php declare(strict_types=1);

namespace App\Infrastructure\Repository;

use App\Infrastructure\Repository\Interfaces\SessionRepositoryInterface as SessionRepositoryInterface;

/**
 * Clase SessionRepository
 * @author JesusLugo2002
 * Se encarga de la gestion de informacion con la variable de sesion del navegador.
 */
final class SessionRepository implements SessionRepositoryInterface {

    public function __construct() {
        session_start();
    }
        
    /**
     * Devuelve el valor obtenido de $_SESSION y, si no es conseguido, el valor determinado por defecto.
     *
     * @param  string $name Nombre de la clave.
     * @param  mixed $default Valor por defecto.
     * @return mixed Valor obtenido o valor por defecto.
     */
    public function get(string $name, mixed $default = ""): mixed {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $default;
    }
    
    /**
     * Añade/actualiza el valor en la clave pasada.
     *
     * @param  mixed $name Nombre de la clave.
     * @param  mixed $value Valor a guardar.
     * @return void
     */
    public function set(string $name, mixed $value): void {
        $_SESSION[$name] = $value;
    }
    
    /**
     * Destruye la sesion actual.
     *
     * @return void
     */
    public function destroy(): void {
        session_destroy();
    }
}

?>