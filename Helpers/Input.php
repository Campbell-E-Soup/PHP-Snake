<?php

namespace Helpers;

class Input {
    protected static bool $rawMode = false;
    protected static array $watchedKeys = ["w","a","s","d","q"];


    public static function init(): void
    {
        if (!self::$rawMode) {
            system('stty -icanon -echo');
            self::$rawMode = true;
        }
    }

    public static function restore(): void
    {
        if (self::$rawMode) {
            system('stty icanon echo');
            self::$rawMode = false;
        }
    }

    public static function getKey(): ?string
    {
        $read = [STDIN];
        $write = $except = null;
        $hasInput = stream_select($read, $write, $except, 0, 0);

        if (!$hasInput) return null;

        $char = fgetc(STDIN);

        if (ord($char) === 27) { // ESC
            //read all three inputs for arrow keys
            if (stream_select($read, $write, $except, 0, 100000)) {
                $next1 = fgetc(STDIN);
                if (stream_select($read, $write, $except, 0, 100000)) {
                    $next2 = fgetc(STDIN);
                    $seq = $char . $next1 . $next2;
                    return match ($seq) {
                        "\e[A" => 'w',
                        "\e[B" => 's',
                        "\e[C" => 'd',
                        "\e[D" => 'a',
                        default => null
                    };
                }
            }
            return null;
        }

        $charLower = strtolower($char);
        if (in_array($charLower, self::$watchedKeys)) {
            return $charLower;
        }

        return null;
    }
}