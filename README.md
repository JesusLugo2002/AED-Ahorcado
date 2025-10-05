<div align=justify>

# El Ahorcado

<div align=center>
  <img src="./img/v1-final.png">
</div>

<hr

Este es un proyecto simple en **PHP** que implementa el clásico juego del **ahorcado** en el navegador usando sesiones.

# Tabla de contenidos

- [Requisitos](#requisitos)
- [Puesta en marcha](#puesta-en-marcha)
- [Estructura de archivos](#estructura-de-archivos)
- [Mockup](#mockup)
- [Clases](#clases)
- [Flujo de uso](#flujo-de-uso)


## Requisitos

- Tener docker y contenedor de docker de **PHP >= 7.4**.
- Un navegador web.
- Paquete `just` para lanzar comandos con facilidad (opcional).

## Puesta en marcha

1. Si tiene instalado el paquete `just` en su sistema, basta con lanzar `just` o `just start` en la raiz del proyecto para levantarlo.
2. Si no, levante el proyecto con `docker compose up -d --build`.
3. Una vez levantado, acceda al proyecto a través de [localhost:8080](http://localhost:8080/).
4. Para cerrar el proyecto, ejecute `just stop` (si tiene instalado `just`) o `docker compose down`.

## Estructura de archivos

```
src
└── public
    ├── files
    │   └── words.txt
    ├── img
    │   └── icon.svg
    ├── index.php
    └── reset.php
```

## Mockup

<div align=center>
  <img src="./img/mockup.drawio.svg">
</div>

## Clases

```
WordProvider  ──►  Game  ◄── Storage
       │               │
       └──────────────►│
                       │
                   Renderer
```

- **WordProvider**: obtiene palabras desde ficheros u otras fuentes.
- **Game**: encapsula la lógica del juego (estado, intentos, victoria/derrota).
- **Storage**: maneja la persistencia del estado (sesiones).
- **Renderer**: dibuja el ahorcado en ASCII según intentos restantes.

## Flujo de uso

1. `Storage` carga estado de sesión.
2. `WordProvider` da la palabra inicial si no existe.
3. `Game` gestiona lógica de letras e intentos.
4. `Storage` guarda de nuevo el estado (`toState()`).
5. `Renderer` convierte intentos restantes en el dibujo ASCII.
6. `index.php` genera HTML con datos de `Game` + `Renderer`.

</div>