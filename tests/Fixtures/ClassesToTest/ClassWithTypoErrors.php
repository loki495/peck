<?php

namespace Tests\Fixtures\ClassesToTest;

/**
 * Class ClassWithTypoErrors
 *
 * This class is used to tst type errors in class properties, methods, method parameters and class documentation block.
 *
 * @internal
 */
class ClassWithTypoErrors
{
    public int $propertyWithoutTypoError = 1;

    public int $properytWithTypoError = 2;

    public function methodWithoutTypoError(): string
    {
        return 'This is a method without a typo error';
    }

    public function methodWithTypoErorr(): string
    {
        return 'This is a method with a typo error';
    }

    public function methodWithTypoErrorInParameters(string $parameterWithoutTypoError, string $parameterWithTypoErorr): string
    {
        return 'This is a method with a typo error in parameters';
    }

    public function methodWithoutTypoErrorInParameters(string $parameterWithoutTypoError): string
    {
        return 'This is a method without a typo error in parameters';
    }
}
