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

    // Translations.
    'The "trans" filter does not take parameters for a message that is already translatable: they are the ones of the message.' =>
        'El filtro "trans" no recibe parámetros para un mensaje que ya es traducible: son los del mensaje.',

    // Components.
    'Component {component}: {message}' =>
        'Componente {component}: {message}',
    'Item title is required.' =>
        'El título del ítem es obligatorio.',
    'Item content is required.' =>
        'El contenido del ítem es obligatorio.',
    'Invalid number of columns. Must be 1, 2, 3, 4, or 6.' =>
        'Cantidad de columnas inválida. Debe ser 1, 2, 3, 4 o 6.',
    'Invalid number of columns. Must be 1, 2, 3, 4 or 6.' =>
        'Cantidad de columnas inválida. Debe ser 1, 2, 3, 4 o 6.',
    'The number of columns must be 1, 2, 3, 4 or 6.' =>
        'La cantidad de columnas debe ser 1, 2, 3, 4 o 6.',
    'Invalid date format for event: {date}' =>
        'Formato de fecha inválido para el evento: {date}',
    'Directory "{directory}" does not exist.' =>
        'El directorio "{directory}" no existe.',

    // Lint.
    'The directory {directory} does not exist.' =>
        'El directorio {directory} no existe.',
    'The Twig environment has no routing functions: register the {extension}.' =>
        'El entorno de Twig no tiene funciones de rutas: registra {extension}.',
    'The Twig environment has no translation extension: register the {extension}.' =>
        'El entorno de Twig no tiene la extensión de traducción: registra {extension}.',
];
