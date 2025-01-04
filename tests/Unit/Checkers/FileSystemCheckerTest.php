<?php

declare(strict_types=1);

use Peck\Checkers\FileSystemChecker;
use Peck\Config;
use Peck\Plugins\Cache;
use Peck\Services\Spellcheckers\InMemorySpellchecker;
use PhpSpellcheck\Spellchecker\Aspell;

it('does not detect issues in the given directory', function (): void {
    $checker = new FileSystemChecker(
        Config::instance(),
        InMemorySpellchecker::default(),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../../src',
    ]);

    expect($issues)->toBeEmpty();
});

it('detects issues in the given directory', function (): void {
    $checker = new FileSystemChecker(
        Config::instance(),
        InMemorySpellchecker::default(),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../Fixtures',
    ]);

    expect($issues)->toHaveCount(4)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/FolderWithTypoos')
        ->and($issues[0]->line)->toBe(0)
        ->and($issues[0]->column)->toBe(47)
        ->and($issues[0]->misspelling->word)->toBe('Typoos')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'Typos',
            'Typo\'s',
            'Types',
            'Type\'s',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FileThatShouldBeIgnroed.php')
        ->and($issues[1]->line)->toBe(0)
        ->and($issues[1]->column)->toBe(74)
        ->and($issues[1]->misspelling->word)->toBe('Ignroed')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'Ignored',
            'Ignores',
            'Ignore',
            'Inroad',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FileWithTppyo.php')
        ->and($issues[2]->line)->toBe(0)
        ->and($issues[2]->column)->toBe(66)
        ->and($issues[2]->misspelling->word)->toBe('Tppyo')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'Typo',
            'Tokyo',
            'Typos',
            'Topi',
        ])->and($issues[3]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FolderThatShouldBeIgnored/FileThatShoudBeIgnoredBecauseItsInsideWhitelistedFolder.php')
        ->and($issues[3]->line)->toBe(0)
        ->and($issues[3]->column)->toBe(92)
        ->and($issues[3]->misspelling->word)->toBe('Shoud')
        ->and($issues[3]->misspelling->suggestions)->toBe([
            'Should',
            'Shroud',
            'Shod',
            'Shout',
        ]);
});

it('detects issues in the given directory, but ignores the whitelisted words', function (): void {
    $config = new Config(
        whitelistedWords: ['Ignroed'],
    );

    $checker = new FileSystemChecker(
        $config,
        new InMemorySpellchecker(
            $config,
            Aspell::create(),
            Cache::default(),
        ),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../Fixtures',
    ]);

    expect($issues)->toHaveCount(3)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/FolderWithTypoos')
        ->and($issues[0]->line)->toBe(0)
        ->and($issues[0]->column)->toBe(47)
        ->and($issues[0]->misspelling->word)->toBe('Typoos')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'Typos',
            'Typo\'s',
            'Types',
            'Type\'s',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FileWithTppyo.php')
        ->and($issues[1]->line)->toBe(0)
        ->and($issues[1]->column)->toBe(66)
        ->and($issues[1]->misspelling->word)->toBe('Tppyo')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'Typo',
            'Tokyo',
            'Typos',
            'Topi',
        ])
        ->and($issues[2]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FolderThatShouldBeIgnored/FileThatShoudBeIgnoredBecauseItsInsideWhitelistedFolder.php')
        ->and($issues[2]->line)->toBe(0)
        ->and($issues[2]->column)->toBe(92)
        ->and($issues[2]->misspelling->word)->toBe('Shoud')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'Should',
            'Shroud',
            'Shod',
            'Shout',
        ]);
});

it('detects issues in the given directory, but ignores the whitelisted directories', function (): void {
    $checker = new FileSystemChecker(
        new Config(
            whitelistedDirectories: ['FolderThatShouldBeIgnored'],
        ),
        InMemorySpellchecker::default(),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../Fixtures',
    ]);

    expect($issues)->toHaveCount(3)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/FolderWithTypoos')
        ->and($issues[0]->line)->toBe(0)
        ->and($issues[0]->column)->toBe(47)
        ->and($issues[0]->misspelling->word)->toBe('Typoos')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'Typos',
            'Typo\'s',
            'Types',
            'Type\'s',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FileThatShouldBeIgnroed.php')
        ->and($issues[1]->line)->toBe(0)
        ->and($issues[1]->column)->toBe(74)
        ->and($issues[1]->misspelling->word)->toBe('Ignroed')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'Ignored',
            'Ignores',
            'Ignore',
            'Inroad',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/FolderWithTypoos/FileWithTppyo.php')
        ->and($issues[2]->line)->toBe(0)
        ->and($issues[2]->column)->toBe(66)
        ->and($issues[2]->misspelling->word)->toBe('Tppyo')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'Typo',
            'Tokyo',
            'Typos',
            'Topi',
        ]);
});
