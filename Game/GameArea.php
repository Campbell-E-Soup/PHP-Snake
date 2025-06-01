<?php

namespace Game;



use Helpers\Input;
use SaveData\Data;
use Game\Snake;

class GameArea {
    public array $gameGrid;
    public static array $borderStyle;
    public static string $borderColor;
    public static string $snakeColor;
    public static string $pelletColor;
    public static array $gameSize;
    public static string $highlight;
    public Snake $snake;
    public function __construct() {
        //initilize game grid
            $width = self::$gameSize[1]+2;
            $height = self::$gameSize[0]+2;

        for ($y = 0; $y < $height; $y++) {
            for ($x = 0; $x < $width; $x++) {
                $place = "  "; //what will be placed in grid
                if ($x == 0 || $y == 0 || $x == $width-1 || $y == $height-1) {
                    $place = "B"; //border piece (will be stylized in later)
                }
                $this->gameGrid[$y][$x] = $place;
            }
        }

        $this->snake = new Snake($width,$height);
        $this->snake->drawSnake($this->gameGrid);

        $this->placeFood();
    }

    public function render() {
        echo "\e[2J\e[H\e[?25l"; //clear cursor and terminal
            $width = count($this->gameGrid[0]);
            $height = count($this->gameGrid);
            $gameover = false;
            $score = 0;
            while (true) {
                //move snake and add snake to game grid
                    $key = Input::getKey();
                    $this->snake->updateDir($key);
                    $gameover = $this->snake->move($this->gameGrid);
                    if ($this->snake->drawSnake($this->gameGrid)) {
                        $score++;
                    }
                    if ($gameover) break;
                echo "\e[H";//move cursor to top
                echo self::$highlight . "SCORE $score\n\033[0m";
                for ($y = 0; $y < $height; $y++) {
                    for ($x = 0; $x < $width; $x++) {
                        $out = $this->gameGrid[$y][$x];
                        if ($out == "B") {
                            $out = self::$borderStyle[self::borderSelect($x,$y,$width,$height)];
                            $color = self::$borderColor;
                            $out = $color . $out . "\033[0m";
                        }
                        else if ($out == "S") {
                            $color = self::$snakeColor;
                            $out = $color . "██" . "\033[0m";
                            //clear cell so it can be updated later
                            $this->gameGrid[$y][$x] = "  ";
                        }
                        else if ($out == "F") {
                            $color = self::$pelletColor;
                            $out = $color . "██" . "\033[0m";
                        }
                        echo $out;
                    }
                    echo "\n";
                }
                if ($key == "q") {
                    echo "\e[0m\e[?25h\e[2J\e[H";
                    return false;
                }
                $this->placeFood($this->snake->grow);
                usleep(Data::$gameSpeed);
            }
            echo "\033[1;31m\nGame Over! Press 'q' to quit or press 'wasd' to restart";
            $restart = false;
            while (true) {
                $key = Input::getKey();
                if ($key == "q") {
                    break;
                }
                else if ($key !== null) {
                    $restart = true;
                    break;
                }
                usleep(Data::$gameSpeed);
            }
        echo "\e[0m\e[?25h\e[2J\e[H";
        return $restart;
    }

    public static function loadGameData() {
        //"Border Style"
            self::$borderStyle = Data::GetData("Border Style",(int)Data::$save_data["Border Style"]);
        //"Border Color"
            self::$borderColor = Data::GetData("Border Color",(int)Data::$save_data["Border Color"]);
        //"Game Size"
            self::$gameSize = Data::GetData("Game Size",(int)Data::$save_data["Game Size"]);
        //"Snake Color"
            self::$snakeColor = Data::GetData("Snake Color",(int)Data::$save_data["Snake Color"]);
        //"Pellet Color"
            self::$pelletColor = Data::GetData("Pellet Color",(int)Data::$save_data["Pellet Color"]);
        //"Text Color"
            self::$highlight = Data::GetData("Text Color",(int)Data::$save_data["Text Color"]);
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

    public function placeFood($bool = true) {
        if (!$bool) return;
        //place food at random coord check that it is empty if not keep trying
        $width = count($this->gameGrid[0])-3;
        $height = count($this->gameGrid)-3;
        $rX = random_int(2,$width);
        $rY = random_int(2,$height);
        $limit = $width * $height/2;
        while ($this->gameGrid[$rY][$rX] !== "  ") {
            $rX = random_int(1,$width);
            $rY = random_int(1,$height);
            if ($limit <= 0) { //just in case
                break;
            }
            $limit++;
        }
        $this->gameGrid[$rY][$rX] = "F";
    }
}