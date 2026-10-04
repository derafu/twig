<?php

declare(strict_types=1);

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2026 Esteban De La Fuente Rubio / Derafu <https://www.derafu.dev>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

return [
    // Extensions.
    'Routing context not set.' =>
        'El contexto de rutas no está definido.',
    'MarkdownExtension requires Derafu\\Markdown to be installed. Run "composer require derafu/markdown" to enable this extension.' =>
        'MarkdownExtension requiere que Derafu\\Markdown esté instalado. Ejecuta "composer require derafu/markdown" para habilitar esta extensión.',

    // Components.
    'Directory "{directory}" does not exist.' =>
        'El directorio "{directory}" no existe.',

    // Lint.
    'The directory {directory} does not exist.' =>
        'El directorio {directory} no existe.',
    'The Twig environment has no routing functions: register the {extension}.' =>
        'El entorno de Twig no tiene funciones de rutas: registra {extension}.',
];
