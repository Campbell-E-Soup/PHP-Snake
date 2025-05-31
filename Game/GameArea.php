<?php

namespace Game;

use Helpers\Input;
use SaveData\Data;

class GameArea {
    public array $gameGrid;
    public static array $borderStyle;
    public static string $borderColor;
    public static array $gameSize;
    public function __construct() {
        //initilize game grid
            $width = self::$gameSize[1]+2;
            $height = self::$gameSize[0]+2;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $place = " "; //what will be placed in grid
                if ($x == 0 || $y == 0 || $x == $width-1 || $y == $height-1) {
                    $place = "B"; //border piece (will be stylized in later)
                }
                $this->gameGrid[$y][$x] = $place;
            }
        }
    }

    public function render() {
        echo "\e[2J\e[H\e[?25l"; //clear cursor and terminal
            $width = count($this->gameGrid[0]);
            $height = count($this->gameGrid);
            while (true) {
                echo "\e[H";//move cursor to top
                for ($y = 0; $y < $height; $y++) {
                    for ($x = 0; $x < $width; $x++) {
                        $out = $this->gameGrid[$y][$x];
                        if ($out == "B") {
                            $out = self::$borderStyle[self::borderSelect($x,$y,$width,$height)];
                            $color = self::$borderColor;
                            $out = $color . $out . "\033[0m";
                        }
                        echo $out;
                    }
                    echo "\n";
                }
                if (Input::getKey() == "q") {
                    break;
                }
                usleep(Data::$gameSpeed);
            }
        echo "\e[0m\e[?25h\e[2J\e[H";
    }

    public static function loadGameData() {
        //"Border Style"
            self::$borderStyle = Data::GetData("Border Style",(int)Data::$save_data["Border Style"]);
        //"Border Color"
            self::$borderColor = Data::GetData("Border Color",(int)Data::$save_data["Border Color"]);
        //"Game Size"
            self::$gameSize = Data::GetData("Game Size",(int)Data::$save_data["Game Size"]);
    }

    public static function borderSelect($x, $y, $width, $height) {
        if ($x == 0 && $y == 0) return 0;                   // ┌
        if ($x == $width - 1 && $y == 0) return 2;          // ┐
        if ($x == $width - 1 && $y == $height-1) return 4;  // ┘
        if ($x == 0 && $y == $height-1) return 5;           // └
        if ($y == 0 || $y == $height-1) return 1;           // ─
        if ($x == 0 || $x == $width-1) return 3;            // │
        return -1;
    }
}