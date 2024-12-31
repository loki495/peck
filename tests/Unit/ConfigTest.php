<?php

declare(strict_types=1);

use Peck\Config;

it('should have a default configuration', function (): void {
    $config = Config::instance();

    expect($config->whitelistedWords)->toBe([
        'config',
        'aspell',
        'args',
        'namespace',
        'doc',
        'bool',
        'php',
    ])->and($config->whitelistedDirectories)->toBe([]);
});

it('should to be a singleton', function (): void {
    $configA = Config::instance();
    $configB = Config::instance();

    expect($configA)->toBe($configB);
});
