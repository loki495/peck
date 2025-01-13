<?php

declare(strict_types=1);

namespace Peck\Fixers;

use Peck\Support\SpellcheckFormatter;
use Peck\ValueObjects\Issue;

use function Laravel\Prompts\suggest;

use function Termwind\render;

final class SourceCodeFixer
{
    /**
     * @var array<string, array<string, array<int, bool>>>
     */
    private static array $fixesDone = [];

    /**
     * These suggestions will carry over between issues
     *
     * @var array<string, string>
     */
    private static array $selectedSuggestions = [];

    public static function fix(Issue $issue): bool
    {
        // check if typo has been fixed already (same mispelling in this file)
        if (self::$fixesDone[$issue->file][$issue->misspelling->word][$issue->line] ?? false) {
            return true;
        }

        // re-use selection for this mispelling if any, else prompt user
        $selectedSuggestion = self::$selectedSuggestions[$issue->misspelling->word] ?? suggest(
            label: "Change '{$issue->misspelling->word}' to",
            options: $issue->misspelling->suggestions,
            placeholder: implode(', ', $issue->misspelling->suggestions),
            hint: 'Tab for menu, Enter on empty to skip',
        );

        // this will ignore future mispellings in the same file
        self::$selectedSuggestions[$issue->misspelling->word] = $selectedSuggestion;

        if (!$selectedSuggestion) {
            render(<<<HTML
                <div class="mx-2 mb-1">
                    <div class="space-x-1 mb-1 bg-yellow text-black px-1 font-bold">IGNORED</div>
                </div>
            HTML
            );
            return false;
        }

        $lines = file($issue->file) ?: [];

        $issueWordLength = strlen($issue->misspelling->word);

        foreach ($lines as $lineIndex => $line) {
            $pattern = '/(\b|[a-z_])('.$issue->misspelling->word.')(\b|[A-Z_])/i';

            preg_match_all($pattern, strtolower($line), $occurrences, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

            if ($occurrences === []) {
                continue;
            }

            foreach ($occurrences as $occurrence) {
                $offset = $occurrence[2][1];

                $selectedSuggestion = SpellcheckFormatter::fixCapitalization($line, $issue->misspelling->word, $offset, (string) $selectedSuggestion);
                if (!$selectedSuggestion) {
                    continue;
                }

                $oldLine = $line;
                $newLine = substr_replace($line, $selectedSuggestion, $offset, strlen($issue->misspelling->word));

                render(<<<HTML
                    <div class="mx-2 mb-1">
                        <div class="space-x-1 bg-green text-black px-1 font-bold">FIXED (line {$lineIndex})</div>
                        <div>
                            <div class="text-red">-{$oldLine}</div>
                            <div class="text-green">+{$newLine}</div>
                        </div>
                    </div>
                    HTML
                );
                // confirm each occurrence?
                $lines[$lineIndex] = $newLine;

                self::$fixesDone[$issue->file][$issue->misspelling->word][$lineIndex] = true;
            }
        }

        file_put_contents($issue->file, implode('', $lines));

        return true;


    }
}
