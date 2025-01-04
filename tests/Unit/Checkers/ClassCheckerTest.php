<?php

declare(strict_types=1);

use Peck\Checkers\ClassChecker;
use Peck\Config;
use Peck\Services\Spellcheckers\InMemorySpellchecker;
use PhpSpellcheck\Spellchecker\Aspell;
use Symfony\Component\Finder\SplFileInfo;

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

    expect($issues)->toHaveCount(10)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[0]->line)->toBe(30)
        ->and($issues[0]->column)->toBe(34)
        ->and($issues[0]->misspelling->word)->toBe('Erorr')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[1]->line)->toBe(36)
        ->and($issues[1]->column)->toBe(17)
        ->and($issues[1]->misspelling->word)->toBe('metohd')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'method',
            'meted',
            'mooted',
            'mated',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[2]->line)->toBe(43)
        ->and($issues[2]->column)->toBe(112)
        ->and($issues[2]->misspelling->word)->toBe('Erorr')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[3]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[3]->line)->toBe(46)
        ->and($issues[3]->column)->toBe(26)
        ->and($issues[3]->misspelling->word)->toBe('Erorr')
        ->and($issues[3]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[4]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[4]->line)->toBe(18)
        ->and($issues[4]->column)->toBe(16)
        ->and($issues[4]->misspelling->word)->toBe('properyt')
        ->and($issues[4]->misspelling->suggestions)->toBe([
            'property',
            'propriety',
            'properer',
            'properest',
        ])->and($issues[5]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[5]->line)->toBe(21)
        ->and($issues[5]->column)->toBe(37)
        ->and($issues[5]->misspelling->word)->toBe('bolck')
        ->and($issues[5]->misspelling->suggestions)->toBe([
            'block',
            'bock',
            'bloc',
            'bilk',
        ])->and($issues[6]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[6]->line)->toBe(10)
        ->and($issues[6]->column)->toBe(25)
        ->and($issues[6]->misspelling->word)->toBe('tst')
        ->and($issues[6]->misspelling->suggestions)->toBe([
            'test',
            'tat',
            'ST',
            'St',
        ])->and($issues[7]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoOnConstants.php')
        ->and($issues[7]->line)->toBe(11)
        ->and($issues[7]->column)->toBe(29)
        ->and($issues[7]->misspelling->word)->toBe('TYPOO')
        ->and($issues[7]->misspelling->suggestions)->toBe([
            'TYPO',
            'TYPOS',
            'TYPE',
            'TOPI',
        ])->and($issues[8]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoOnConstants.php')
        ->and($issues[8]->line)->toBe(11)
        ->and($issues[8]->column)->toBe(52)
        ->and($issues[8]->misspelling->word)->toBe('typoo')
        ->and($issues[8]->misspelling->suggestions)->toBe([
            'typo',
            'typos',
            'type',
            'topi',
        ])->and($issues[9]->file)->toEndWith('tests/Fixtures/ClassesToTest/FolderThatShouldBeIgnored/ClassWithTypoErrors.php')
        ->and($issues[9]->line)->toBe(9)
        ->and($issues[9]->column)->toBe(16)
        ->and($issues[9]->misspelling->word)->toBe('properyt')
        ->and($issues[9]->misspelling->suggestions)->toBe([
            'property',
            'propriety',
            'properer',
            'properest',
        ]);
});

it('detects issues in the given directory, but ignores the whitelisted words', function (): void {
    $config = new Config(
        whitelistedWords: ['Properyt', 'bolck'],
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

    expect($issues)->toHaveCount(7)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[0]->line)->toBe(30)
        ->and($issues[0]->column)->toBe(34)
        ->and($issues[0]->misspelling->word)->toBe('Erorr')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[1]->line)->toBe(36)
        ->and($issues[1]->column)->toBe(17)
        ->and($issues[1]->misspelling->word)->toBe('metohd')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'method',
            'meted',
            'mooted',
            'mated',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[2]->line)->toBe(43)
        ->and($issues[2]->column)->toBe(112)
        ->and($issues[2]->misspelling->word)->toBe('Erorr')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[3]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[3]->line)->toBe(46)
        ->and($issues[3]->column)->toBe(26)
        ->and($issues[3]->misspelling->word)->toBe('Erorr')
        ->and($issues[3]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[4]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[4]->line)->toBe(10)
        ->and($issues[4]->column)->toBe(25)
        ->and($issues[4]->misspelling->word)->toBe('tst')
        ->and($issues[4]->misspelling->suggestions)->toBe([
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

    expect($issues)->toHaveCount(9)
        ->and($issues[0]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[0]->line)->toBe(30)
        ->and($issues[0]->column)->toBe(34)
        ->and($issues[0]->misspelling->word)->toBe('Erorr')
        ->and($issues[0]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[1]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[1]->line)->toBe(36)
        ->and($issues[1]->column)->toBe(17)
        ->and($issues[1]->misspelling->word)->toBe('metohd')
        ->and($issues[1]->misspelling->suggestions)->toBe([
            'method',
            'meted',
            'mooted',
            'mated',
        ])->and($issues[2]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[2]->line)->toBe(43)
        ->and($issues[2]->column)->toBe(112)
        ->and($issues[2]->misspelling->word)->toBe('Erorr')
        ->and($issues[2]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[3]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[3]->line)->toBe(46)
        ->and($issues[3]->column)->toBe(26)
        ->and($issues[3]->misspelling->word)->toBe('Erorr')
        ->and($issues[3]->misspelling->suggestions)->toBe([
            'Error',
            'Errors',
            'Orr',
            'Err',
        ])->and($issues[4]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[4]->line)->toBe(18)
        ->and($issues[4]->column)->toBe(16)
        ->and($issues[4]->misspelling->word)->toBe('properyt')
        ->and($issues[4]->misspelling->suggestions)->toBe([
            'property',
            'propriety',
            'properer',
            'properest',
        ])->and($issues[5]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[5]->line)->toBe(21)
        ->and($issues[5]->column)->toBe(37)
        ->and($issues[5]->misspelling->word)->toBe('bolck')
        ->and($issues[5]->misspelling->suggestions)->toBe([
            'block',
            'bock',
            'bloc',
            'bilk',
        ])->and($issues[6]->file)->toEndWith('tests/Fixtures/ClassesToTest/ClassWithTypoErrors.php')
        ->and($issues[6]->line)->toBe(10)
        ->and($issues[7]->column)->toBe(29)
        ->and($issues[6]->misspelling->word)->toBe('tst')
        ->and($issues[6]->misspelling->suggestions)->toBe([
            'test',
            'tat',
            'ST',
            'St',
        ]);
});

it('handles well when it can not detect the line problem', function (): void {
    $checker = new ClassChecker(
        new Config(
            whitelistedDirectories: ['FolderThatShouldBeIgnored'],
        ),
        InMemorySpellchecker::default(),
    );

    $splFileInfo = new SplFileInfo(__FILE__, '', '');

    $line = (fn (): array => $this->getErrorsLineAndColumn($splFileInfo, str_repeat('a', 100)))->call($checker);

    expect($line)->toHaveCount(0);
});
