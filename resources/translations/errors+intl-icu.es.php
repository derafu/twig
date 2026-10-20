<?php

declare(strict_types=1);

/**
 * Derafu: Twig - Twig Environment, Extensions and Lint Tools for PHP.
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
    'The Markdown content could not be processed: {error}' =>
        'No se pudo procesar el contenido Markdown: {error}',

    // Translations.
    'The "trans" filter does not take parameters for a message that is already translatable: they are the ones of the message.' =>
        'El filtro "trans" no recibe parámetros para un mensaje que ya es traducible: son los del mensaje.',

    // Providers.
    'Directory "{directory}" does not exist.' =>
        'El directorio "{directory}" no existe.',
    'The file {file} can not be read.' =>
        'El archivo {file} no se puede leer.',

    // Middleware.
    'The parameter {parameter} must be a string.' =>
        'El parámetro {parameter} debe ser un string.',

    // Lint.
    'The directory {directory} does not exist.' =>
        'El directorio {directory} no existe.',
    'The Twig environment has no routing functions: register the {extension}.' =>
        'El entorno de Twig no tiene funciones de rutas: registra {extension}.',
    'The Twig environment has no translation extension: register the {extension}.' =>
        'El entorno de Twig no tiene la extensión de traducción: registra {extension}.',
];
