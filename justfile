start:
    docker compose up -d --build && echo "\nEnlace del juego -> http://localhost:8080/"

stop:
    docker compose down
