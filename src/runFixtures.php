<?php
namespace Erla\WpToolsetFixtures;
use Erla\WpToolsetFixtures\Creator\MenuCreator;
use Erla\WpToolsetFixtures\Creator\PagesCreator;
use Erla\WpToolsetFixtures\Creator\TermsCreator;
use Erla\WpToolsetFixtures\Vault\IdVault;

require '../vendor/autoload.php';

// process cmd line arguments
$supportedKeys = ['pages', 'terms', 'menus'];
$argumentsMap = [];
foreach ($args as $arg) {
    list($key, $path) = explode('=', $arg);
    if (!file_exists($path)) {
        echo "Plik \"$path\" nie istnieje";
        die;
    }
    if (!in_array($key, $supportedKeys)) {
        echo "Nieobsługiwany typ: \"$key\"" . PHP_EOL;
        echo "Obsługiwane typy to: " . implode(' ', $supportedKeys);
        die;
    }
    $argumentsMap[$key] = $path;
}

// clear all
\WP_CLI::runcommand('site empty --yes --allow-root');

// do scaffold
$vault = new IdVault();

$creators = [
    [
        'creator' => new PagesCreator($vault),
        'feed' => $argumentsMap['pages']
    ],
    [
        'creator' => new TermsCreator($vault),
        'feed' => $argumentsMap['terms']
    ],
    [
        'creator' => new MenuCreator($vault),
        'feed' => $argumentsMap['menus']
    ]
];

foreach ($creators as $item) {
    if (!$item['feed']) {
        continue;
    }
    $item['creator']->greet();
    $item['creator']->create($item['feed']);
}
