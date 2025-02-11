<?php

declare(strict_types=1);
require_once __DIR__ . '/../vendor/autoload.php';

/**
 * Derafu: Twig - UI Component and Extension Library.
 *
 * Copyright (c) 2025 Esteban De La Fuente Rubio / Derafu <https://www.derafu.org>
 * Licensed under the MIT License.
 * See LICENSE file for more details.
 */

use Derafu\Twig\Service\TwigService;

$options = [
    'paths' => [
        __DIR__ . '/../templates',
        __DIR__ . '/pages',
    ],
];

$twigService = new TwigService($options);

$basePath = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$template = (ltrim($_SERVER['REQUEST_URI'], '/') ?: 'index') . '.html.twig';
$templateFilename = realpath(__DIR__ . '/pages/' . $template);

if (!$templateFilename) {
    echo $twigService->render('error404');
} else {
    $templateCode = str_starts_with($template, 'demo/components/')
        ? file_get_contents($templateFilename)
        : null
    ;
    if ($templateCode) {
        $templateCode =
            '{# derafu_twig:public/pages/' . $template . ' #}' . "\n\n"
            . $templateCode
        ;

    }
    $data = [
        'base_path' => $basePath,
        'html_twig' => $templateCode,
    ];
    if ($template === 'index.html.twig') {
        $data['readme'] = file_get_contents('../README.md');
    }
    echo $twigService->render($template, $data);
}
