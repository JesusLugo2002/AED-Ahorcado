<?php declare(strict_types=1);

namespace App\Domain\Repository;

interface SessionRepositoryInterface {
    public function set(string $name, mixed $value): void;
    public function get(string $name, mixed $default = ""): mixed;

    public function destroy(): void;
}

?>