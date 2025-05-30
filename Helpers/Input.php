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

    private static string|null $lastKey = null;

    public static function getKey(): ?string
    {
        stream_set_blocking(STDIN, false);

        $read = [STDIN];
        $write = $except = null;
        $hasInput = stream_select($read, $write, $except, 0, 0);
        if (!$hasInput) {
            self::$lastKey = null;
            return null;
        }

        // Drain all available characters from input buffer
        $buffer = '';
        while (($char = fgetc(STDIN)) !== false) {
            $buffer .= $char;
            if (strlen($buffer) > 10) break; // safety
        }

        // Get only the last sequence from buffer
        $matches = [];
        preg_match_all("/\e\[[ABCD]|./", $buffer, $matches); // match arrows or individual chars
        $last = end($matches[0]) ?? null;
        if (!$last) return null;

        $key = match ($last) {
            "\e[A" => 'w',
            "\e[B" => 's',
            "\e[C" => 'd',
            "\e[D" => 'a',
            default => strtolower($last)
        };

        if (!in_array($key, self::$watchedKeys ?? [])) {
            return null;
        }

        self::$lastKey = $key;
        return $key;
    }
}