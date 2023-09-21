<?php

namespace Native\Laravel\Compactor;

use PhpToken;

class Php
{
    public function canProcessFile(string $path): bool
    {
        return pathinfo($path, PATHINFO_EXTENSION) === 'php';
    }

    public function compact(string $file, string $contents): string
    {
        if ($this->canProcessFile($file)) {
            return $this->compactContent($contents);
        }

        $this->compactContent($contents);
    }

    protected function compactContent(string $contents): string
    {
        $output = '';
        $tokens = PhpToken::tokenize($contents);
        $tokenCount = count($tokens);

        for ($index = 0; $index < $tokenCount; $index++) {
            $token = $tokens[$index];
            $tokenText = $token->text;

            if ($token->is([T_COMMENT, T_DOC_COMMENT])) {
                if (str_starts_with($tokenText, '#[')) {
                    // This is, in all likelihood, the start of a PHP >= 8.0 attribute.
                    // Note: $tokens may be updated by reference as well!
                    $retokenized = $this->retokenizeAttribute($tokens, $index);

                    if ($retokenized !== null) {
                        array_splice($tokens, $index, 1, $retokenized);
                        $tokenCount = count($tokens);
                    }

                    $attributeCloser = self::findAttributeCloser($tokens, $index);

                    if (is_int($attributeCloser)) {
                        $output .= '#[';
                    } else {
                        // Turns out this was not an attribute. Treat it as a plain comment.
                        $output .= str_repeat("\n", mb_substr_count($tokenText, "\n"));
                    }
                } elseif (str_contains($tokenText, '@')) {
                    try {
                        $output .= $this->compactAnnotations($tokenText);
                    } catch (RuntimeException) {
                        $output .= $tokenText;
                    }
                } else {
                    $output .= str_repeat("\n", mb_substr_count($tokenText, "\n"));
                }
            } elseif ($token->is(T_WHITESPACE)) {
                $whitespace = $tokenText;
                $previousIndex = ($index - 1);

                // Handle whitespace potentially being split into two tokens after attribute retokenization.
                $nextToken = $tokens[$index + 1] ?? null;

                if ($nextToken !== null
                    && $nextToken->is(T_WHITESPACE)
                ) {
                    $whitespace .= $nextToken->text;
                    $index++;
                }

                // reduce wide spaces
                $whitespace = preg_replace('{[ \t]+}', ' ', $whitespace);

                // normalize newlines to \n
                $whitespace = preg_replace('{(?:\r\n|\r|\n)}', "\n", $whitespace);

                // If the new line was split off from the whitespace token due to it being included in
                // the previous (comment) token (PHP < 8), remove leading spaces.

                $previousToken = $tokens[$previousIndex];

                if ($previousToken->is(T_COMMENT)
                    && str_contains($previousToken->text, "\n")
                ) {
                    $whitespace = ltrim($whitespace, ' ');
                }

                // trim leading spaces
                $whitespace = preg_replace('{\n +}', "\n", $whitespace);

                $output .= $whitespace;
            } else {
                $output .= $tokenText;
            }
        }

        return $output;
    }

    private function compactAnnotations(string $docblock): string
    {
        return $docblock;
    }

    /**
     * @param  list<PhpToken>  $tokens
     */
    private static function findAttributeCloser(array $tokens, int $opener): ?int
    {
        $tokenCount = count($tokens);
        $brackets = [$opener];
        $closer = null;

        for ($i = ($opener + 1); $i < $tokenCount; $i++) {
            $tokenText = $tokens[$i]->text;

            // Allow for short arrays within attributes.
            if ($tokenText === '[') {
                $brackets[] = $i;

                continue;
            }

            if ($tokenText === ']') {
                array_pop($brackets);

                if (count($brackets) === 0) {
                    $closer = $i;
                    break;
                }
            }
        }

        return $closer;
    }

    /**
     * @param  non-empty-list<PhpToken>  $tokens
     */
    private function retokenizeAttribute(array &$tokens, int $opener): ?array
    {
        Assert::keyExists($tokens, $opener);

        /** @var PhpToken $token */
        $token = $tokens[$opener];
        $attributeBody = mb_substr($token->text, 2);
        $subTokens = PhpToken::tokenize('<?php '.$attributeBody);

        // Replace the PHP open tag with the attribute opener as a simple token.
        array_splice($subTokens, 0, 1, ['#[']);

        $closer = self::findAttributeCloser($subTokens, 0);

        // Multi-line attribute or attribute containing something which looks like a PHP close tag.
        // Retokenize the rest of the file after the attribute opener.
        if ($closer === null) {
            foreach (array_slice($tokens, $opener + 1) as $token) {
                $attributeBody .= $token->text;
            }

            $subTokens = PhpToken::tokenize('<?php '.$attributeBody);
            array_splice($subTokens, 0, 1, ['#[']);

            $closer = self::findAttributeCloser($subTokens, 0);

            if ($closer !== null) {
                array_splice(
                    $tokens,
                    $opener + 1,
                    count($tokens),
                    array_slice($subTokens, $closer + 1),
                );

                $subTokens = array_slice($subTokens, 0, $closer + 1);
            }
        }

        if ($closer === null) {
            return null;
        }

        return $subTokens;
    }
}
