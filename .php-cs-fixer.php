<?php

$finder = (new PhpCsFixer\Finder())
    ->in(__DIR__) // Aplica en todo el proyecto
    ->exclude('bootstrap/cache'); // Excluye esta carpeta

return (new PhpCsFixer\Config())
    ->setRules([
        '@PSR12' => true, // Cumple PSR-12
        'array_syntax' => ['syntax' => 'short'], // Usa [] en lugar de array()
        'ordered_imports' => ['sort_algorithm' => 'alpha'], // Ordena los imports alfabéticamente
        'line_ending' => true,
    ])
    ->setFinder($finder);
