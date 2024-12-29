<?php

use Peck\Checkers\ClassChecker;
use Peck\Config;
use Peck\Services\Spellcheckers\InMemorySpellchecker;
use PhpSpellcheck\Spellchecker\Aspell;

it('does not detect issues in the given directory', function (): void {
    $checker = new ClassChecker(
        Config::instance(),
        InMemorySpellchecker::default(),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../../src',
    ]);

    expect($issues)->toBeEmpty();
});

it('detects issues in the given directory', function (): void {
    $checker = new ClassChecker(
        Config::instance(),
        InMemorySpellchecker::default(),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../Fixtures/ClassesToTest',
    ]);

    expect($issues)->toHaveCount(5)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[0]->line)->toBe(23)
        ->and($issues[0]->misspelling->word)->toBe('erorr')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'error',
            'errors',
            'Orr',
            'err',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[1]->line)->toBe(28)
        ->and($issues[1]->misspelling->word)->toBe('erorr')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'error',
            'errors',
            'Orr',
            'err',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[2]->line)->toBe(16)
        ->and($issues[2]->misspelling->word)->toBe('properyt')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'property',
            'propriety',
            'properer',
            'properest',
        ])->and($issues[3]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[3]->line)->toBe(8)
        ->and($issues[3]->misspelling->word)->toBe('tst')
        ->and($issues[3]->misspelling->suggestions)->toBe([
            'test',
            'tat',
            'ST',
            'St',
        ])->and($issues[4]->file)->toEndWith('tests/Fixtures/ClassesToTest/FolderThatShouldBeIgnored/ClassWithTypoErrors.php')
        ->and($issues[4]->line)->toBe(7)
        ->and($issues[4]->misspelling->word)->toBe('properyt')
        ->and($issues[4]->misspelling->suggestions)->toBe([
            'property',
            'propriety',
            'properer',
            'properest',
        ]);
});

it('detects issues in the given directory, but ignores the whitelisted words', function (): void {
    $config = new Config(
        whitelistedWords: ['Properyt'],
    );

    $checker = new ClassChecker(
        $config,
        new InMemorySpellchecker(
            $config,
            Aspell::create(),
        ),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../Fixtures/ClassesToTest',
    ]);

    expect($issues)->toHaveCount(3)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[0]->line)->toBe(23)
        ->and($issues[0]->misspelling->word)->toBe('erorr')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'error',
            'errors',
            'Orr',
            'err',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[1]->line)->toBe(28)
        ->and($issues[1]->misspelling->word)->toBe('erorr')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'error',
            'errors',
            'Orr',
            'err',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[2]->line)->toBe(8)
        ->and($issues[2]->misspelling->word)->toBe('tst')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'test',
            'tat',
            'ST',
            'St',
        ]);
});

it('detects issues in the given directory, but ignores the whitelisted directories', function (): void {
    $checker = new ClassChecker(
        new Config(
            whitelistedDirectories: ['FolderThatShouldBeIgnored'],
        ),
        InMemorySpellchecker::default(),
    );

    $issues = $checker->check([
        'directory' => __DIR__.'/../../Fixtures/ClassesToTest',
    ]);

    expect($issues)->toHaveCount(4)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[0]->line)->toBe(23)
        ->and($issues[0]->misspelling->word)->toBe('erorr')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'error',
            'errors',
            'Orr',
            'err',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[1]->line)->toBe(28)
        ->and($issues[1]->misspelling->word)->toBe('erorr')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'error',
            'errors',
            'Orr',
            'err',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[2]->line)->toBe(16)
        ->and($issues[2]->misspelling->word)->toBe('properyt')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'property',
            'propriety',
            'properer',
            'properest',
        ])->and($issues[3]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[3]->line)->toBe(8)
        ->and($issues[3]->misspelling->word)->toBe('tst')
        ->and($issues[3]->misspelling->suggestions)->toBe([
            'test',
            'tat',
            'ST',
            'St',
        ]);
});
