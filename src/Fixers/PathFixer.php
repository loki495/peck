<?php

declare(strict_types=1);

namespace Peck\Fixers;

use Peck\Support\SpellcheckFormatter;
use Peck\ValueObjects\Issue;

use function Laravel\Prompts\suggest;
use function Laravel\Prompts\confirm;

use function Termwind\render;

final class PathFixer
{
    /**
     * @var array<string, string>
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

        $pattern = '/(\b|[a-z_])('.$issue->misspelling->word.')(\b|[A-Z_])/i';

        preg_match_all($pattern, strtolower($issue->file), $occurrences, PREG_OFFSET_CAPTURE | PREG_SET_ORDER);

        if ($occurrences === []) {
            return false;
        }

        foreach ($occurrences as $occurrence) {
            $offset = $occurrence[2][1];

            $selectedSuggestion = SpellcheckFormatter::fixCapitalization($issue->file, $issue->misspelling->word, $offset, (string) $selectedSuggestion);

            if (!$selectedSuggestion) {
                return false;
            }

            $path = self::adjustAlreadyFixedPath($issue->file);

            $newName = substr_replace($issue->file, $selectedSuggestion, $offset, strlen($issue->misspelling->word));

            render(<<<HTML
                <div class="mx-2 mb-1">
                    <div class="space-x-1 bg-green text-black px-1 font-bold">FIXED</div>
                    <div>
                        <div class="text-red">-{$issue->file}</div>
                        <div class="text-green">+{$newName}</div>
                    </div>
                </div>
                HTML
            );

            if (file_exists($newName)) {
                // if $newName exists, move files from old to now path?
                return false;
            }

            rename($issue->file, $newName);
            self::$fixesDone[$issue->file] = $newName;
        }

        return true;
    }

    private static function adjustAlreadyFixedPath(string $path): string {
        dump($path, self::$fixesDone);
        foreach (self::$fixesDone ?? [] as $oldPath => $fixedPath) {
            dump("Testing: $oldPath => $fixedPath - " . strpos($path, $oldPath));
            if (strpos($path, $oldPath) === 0) {
                // $fixed is a block of a pach that has already been fixed
                $path = str_replace($oldPath, $fixedPath, $path);
                dd($path);
            }
        }
        return $path;
    }
}
