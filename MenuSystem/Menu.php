<?php

namespace MenuSystem;

use Game\GameArea;
use Helpers\Input;
use MenuSystem\MenuItem;
use SaveData\Data;

class Menu {
    /**
     * Summary of items
     * @var MenuItem[]
     */
    public array $items; //last will always be back
    public int $index;
    public ?string $name;

    static string $highlight = "\e[1;33m";

    public function __construct($items,$index = 0) {
        $this->index = $index;
        $this->items = $items;
    }

    public function render() {
        echo "\e[2J\e[H\e[?25l"; //clear cursor and terminal
        $exit = false;
        while (!$exit) {
            echo "\e[H";//move cursor to top
            $key = Input::getKey();
            $this->moveMenu($key);
            foreach ($this->items as $i => $item) {
                $modifier = " "; //as we over right this assures all lines are uniform no mater if selected :)
                if ($i == $this->index) {
                    $modifier = self::$highlight . "█";
                    $clicked = $item->move($key);
                    self::loadMenuData();
                    if ($clicked && count($item->items) <= 1) {
                        $exit = true;
                    }
                }
                //align
                if (!$item->divider) {
                    $modifier = $modifier . "   ";
                }
                else {
                    $modifier = self::$highlight;
                }
                //draw item;
                $div = ":";
                $setting = $item->items[$item->index];
                $name = $item->name;
                if (!$setting) $div = ""; //should handle exit etc.
                $out = "$modifier $name $div $setting" . "       ";
                echo "$out\n\e[0m";
            }
            
            if ($key == "q") $exit = true;
            usleep(Data::$gameSpeed);
        }
        //save
        Data::Save();
        //load prefs into memory
        GameArea::loadGameData();
        echo "\e[0m\e[?25h\e[2J\e[H"; //reset cursor and clear terminal
        return false;
    }
    public function moveMenu($key) {
        $loopCheck = 0;
        if ($key == null) {
            return false;
        }
        $count = count($this->items);
        if ($key == "s") {
            $this->index++;
            if ($this->index >= $count) {
                $this->index = 0;
            }
            while ($this->items[$this->index]->divider) {
                $this->index++;
                if ($this->index >= $count) { //annoying that we need to do this but yeah
                    $this->index = 0;
                }
                if ($loopCheck >= $count) break; //make sure if menu us all dividers IT SHOULD NEVER BE BUT BETTER SAFE THAN SORRY
                $loopCheck++;
            }
            return true;
        }
        else if ($key == "w") {
            $this->index--;
            if ($this->index <= -1) {
                $this->index = $count-1;
            }
            while ($this->items[$this->index]->divider) {
                $this->index--;
                if ($this->index <= -1) {
                    $this->index = $count-1;
                }
                if ($loopCheck >= $count) break; //make sure if menu us all dividers IT SHOULD NEVER BE BUT BETTER SAFE THAN SORRY
                $loopCheck++;
            }
        }
    }
    public function renderStart() {
        echo "\e[2J\e[H\e[?25l"; //clear cursor and terminal
        $location = null;
        while ($location === null) {
            echo "\e[H";//move cursor to top
            Menu::drawLogo();
            $key = Input::getKey();
            $this->moveMenu($key);
            foreach ($this->items as $i => $item) {
                $modifier = " "; //this assures all lines are uniform no mater if selected
                if ($i == $this->index) {
                    $modifier = self::$highlight . "█";
                    if ($item->move($key)) {
                        $location = $i;
                    }
                }
                
                //draw item;
                $name = $item->name;
                echo "$modifier $name\n\e[0m";
            }

            if ($key == "q") {
                echo "\e[0m\e[?25h\e[2J\e[H"; //reset cursor and clear terminal
                return 2; //the exit condition
            }
            usleep(Data::$gameSpeed);
        }
        echo "\e[0m\e[?25h\e[2J\e[H"; //reset cursor and clear terminal
        return $location;
    }

    
    public static function loadMenuData() {
        //this loads the data that the menus use and load it to the menus statics
        //"Text Color"
            self::$highlight = Data::GetData("Text Color",(int)Data::$save_data["Text Color"]);
    }
    
    public static function createSettings() {
        return new Menu([
                //game settings
                new MenuItem("[Game Settings]",[false],saveable: false,divider: true), //divider
                new MenuItem("Difficulty",null,-1),
                new MenuItem("Game Speed",null,-1),
                new MenuItem("Game Size" ,null,-1),
                //visual settings
                new MenuItem("[Visuals]",[false],saveable: false,divider: true), //divider
                new MenuItem("Text Color",null,-1),
                new MenuItem("Border Color",null,-1),
                new MenuItem("Border Style",null,-1),
                new MenuItem("Snake Color",null,-1),
                new MenuItem("Pellet Color",null,-1),
                //exit
                new MenuItem("Exit",[false],saveable: false)
            ],
            1 //avoid settings divider
        );
    }

    public static function drawLogo() {
        echo Menu::$highlight;
        echo " ╱███████  ╱██   ╱██ ╱███████         ╱██████  ╱██   ╱██  ╱██████  ╱██   ╱██ ╱████████\n";
        echo "| ██__  ██| ██  | ██| ██__  ██       ╱██__  ██| ███ | ██ ╱██__  ██| ██  ╱██╱| ██_____╱\n";
        echo "| ██  ╲ ██| ██  | ██| ██  ╲ ██      | ██  ╲__╱| ████| ██| ██  ╲ ██| ██ ╱██╱ | ██      \n";
        echo "| ███████╱| ████████| ███████╱      |  ██████ | ██ ██ ██| ████████| █████╱  | █████   \n";
        echo "| ██____╱ | ██__  ██| ██____╱        ╲____  ██| ██  ████| ██__  ██| ██  ██  | ██__╱   \n";
        echo "| ██      | ██  | ██| ██             ╱██  ╲ ██| ██╲  ███| ██  | ██| ██╲  ██ | ██   \n";
        echo "| ██      | ██  | ██| ██            |  ██████╱| ██ ╲  ██| ██  | ██| ██ ╲  ██| ████████\n";
        echo "|__╱      |__╱  |__╱|__╱             ╲______╱ |__╱  ╲__╱|__╱  |__╱|__╱  ╲__╱|________╱\n\n";
        echo "\033[0m";
    }
}