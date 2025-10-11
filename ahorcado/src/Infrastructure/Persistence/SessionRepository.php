<?php declare(strict_types=1);

namespace App\Infrastructure\Persistence;

use App\Domain\Repository\SessionRepositoryInterface as SessionRepositoryInterface;

final class SessionRepository implements SessionRepositoryInterface {

    public function __construct() {
        session_start();
    }
    
    public function get(string $name, mixed $default = ""): mixed {
        if (isset($_SESSION[$name])) {
            return $_SESSION[$name];
        }
        return $default;
    }

    public function set(string $name, mixed $value): void {
        $_SESSION[$name] = $value;
    }
}

?>