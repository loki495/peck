<?php

declare(strict_types=1);

namespace Peck\Support;

final readonly class SpellcheckFormatter
{
    /**
     * Transforms the given input (method or class names) into a
     * human-readable format which can be used for spellchecking.
     */
    public static function format(string $input): string
    {
        // Remove leading underscores
        $input = ltrim($input, '_');

        // Replace underscores and dashes with spaces
        $input = str_replace(['_', '-'], ' ', $input);

        // Insert spaces between lowercase and uppercase letters (camelCase or PascalCase)
        $input = (string) preg_replace('/([a-z])([A-Z])/', '$1 $2', $input);

        // Split sequences of uppercase letters, ensuring the last uppercase letter starts a new word
        $input = (string) preg_replace('/([A-Z]+)([A-Z][a-z])/', '$1 $2', $input);

        // Replace multiple spaces with a single space
        $input = (string) preg_replace('/\s+/', ' ', $input);

        // Convert the final result to lowercase
        return strtolower($input);
    }

    /**
     * Fixes the capitalization of the given word to match the mispelling in the original string
     */
    public static function fixCapitalization(string $string, string $oldWord, int $offset, string $word): ?string
    {
        $wordLength = strlen($oldWord);
        if (strtolower($t = substr($string, $offset, $wordLength)) !== strtolower($oldWord)) {
            return null;
        }

        if ($string[$offset] === strtoupper($string[$offset])) {
            // if the first letter in original word is uppercase

            if ($string[$offset + 1] === strtoupper($string[$offset + 1])) {
                // if also the second letter in original word is uppercase,
                // assume whole word is uppercase
                $word = strtoupper($word);
            } elseif ($string[$offset + $wordLength - 1] === strtoupper($string[$offset + $wordLength - 1])) {
                // but not the last letter in original word
                // ignore match
                // i.e something like ClassWithTypoOnConstants
                //     matched 'TypoO'
                //          (first and last letters are capitalized but not second)
                //     but should not count
                return null;
            } else {
                // only first letter is capitalized
                $word = ucfirst($word);
            }
        } else {
            $word = strtolower($word);
        }

        return $word;
    }
}
