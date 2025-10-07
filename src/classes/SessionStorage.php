<?php declare(strict_types=1);

namespace classes;

/**
 * clase SessionStorage
 * @author JesusLugo2002
 * Gestiona el almacenamiento de datos en la variable de sesion
 */
class SessionStorage {
    private $key;

    public function __construct(String $key = "ahorcado") {
        session_start();
        $this->key = $key;
    }
    
    /**
     * Devuelve el valor guardado en `$_SESSION` bajo la clave `$name` pasada,
     * y si no existe dicho valor o clave, devuelve el valor determinado
     * en `$default`.
     *
     * @param  string $name Nombre de la clave
     * @param  mixed $default Valor devuelto por defecto si no se encuentra el valor buscado
     * @return mixed Valor buscado bajo la clave `$name` o, en su defecto, la pasada por `$default`
     */
    public function get(string $name, mixed $default = null): mixed {
        if (array_key_exists($name, $_SESSION)) {
            return $_SESSION[$name];
        }
        return $default;
    }
    
    /**
     * Guarda el valor `$value` en `$_SESSION` bajo la clave `$name`.
     *
     * @param  mixed $name Nombre de la clave donde se guardara el valor
     * @param  mixed $value Valor a almacenar
     * @return bool `true` si se ha almacenado correctamente, si no, `false`
     */
    public function set(string $name, mixed $value): bool {
        $_SESSION[$name] = $value;
        return array_key_exists($name, $_SESSION);
    }
    
    /**
     * Destruye la sesion actual y recarga la pagina.
     *
     * @return bool `true` si se ha destruido la sesion actual, si no, `false`
     */
    public function reset(): bool {
        $destroyed = session_destroy();
        header("Location: index.php");
        return $destroyed;
    }
}
?>