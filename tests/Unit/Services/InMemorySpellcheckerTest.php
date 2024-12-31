<?php

declare(strict_types=1);

use Peck\Services\Spellcheckers\InMemorySpellchecker;

it('does not detect issues', function (): void {
    $spellchecker = InMemorySpellchecker::default();

    $issues = $spellchecker->check('Hello viewers');

    expect($issues)->toBeEmpty();
});

it('detects issues', function (): void {
    $spellchecker = InMemorySpellchecker::default();

    $issues = $spellchecker->check('Hello viewerss');

    expect($issues)->toHaveCount(1)
        ->and($issues[0]->word)->toBe('viewerss')
        ->and($issues[0]->suggestions)->toBe([
            'viewers',
            'viewer\'s',
            'viewer',
            'viewed',
        ]);
});
