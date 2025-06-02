<?php

namespace Game;

class Snake {
    public $body;
    public ?string $direction;
    public bool $grow = false;

    public function __construct($width,$height) {
        $this->body = [
            ['x' => (int)floor($width/2),
            'y' => (int)floor($height/2)]
        ];
        $this->direction = null;
    }

    public function drawSnake(array &$grid): bool {
        foreach ($this->body as $segment) {
            if ($grid[$segment['y']][$segment['x']] === "F") $this->grow = true;
            $grid[$segment['y']][$segment['x']] = "S";
        }//the snake pieces will be cleared to " " in the draw each frame
        return $this->grow;
    }

    public function move($grid) {
        if ($this->direction === null) return false;
        $dir = $this->direction;
        $xOffset = (int)($dir == "d") - (int)($dir == "a");
        $yOffset = (int)($dir == "s") - (int)($dir == "w");

        $head = $this->body[0];
        $x = $head['x'] + $xOffset;
        $y = $head['y'] + $yOffset;
        $newHead = [
            'x' => $x,
            'y' => $y
        ];
        array_unshift($this->body, $newHead);
        if (!$this->grow) array_pop($this->body);
        $this->grow = false;

        return ($grid[$y][$x] == "B" || $this->checkDuplicates());
    }

    public function updateDir($key) { //updates direction if key is not null
        if ($key !== null && $key !== "q") {
            $opposites = [
                'w' => 's',
                's' => 'w',
                'a' => 'd',
                'd' => 'a',
            ];
            if (count($this->body) <= 1 || $this->direction !== $opposites[$key]) {
                $this->direction = $key;
            }
        }
    }

    public function checkDuplicates() {
        $head = $this->body[0];
        for($i = 1; $i < count($this->body); $i++) {
            $segment = $this->body[$i];
            if ($segment['y'] == $head['y'] && $segment['x'] == $head['x']) {
                return true;
            }
        }
        return false;
    }
}