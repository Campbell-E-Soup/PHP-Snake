<?php

namespace MenuSystem;
use SaveData\Data;

class MenuItem {
    public int $index;
    public array $items;
    public string $name;

    public bool $saveable;
    public bool $divider;

    public function __construct($name,$items,$index = 0,$saveable = true,$divider = false) {
        $this->name = $name;
        
        $this->index = $index;

        if ($items === null) {
            //load the array from json
            $data = Data::$game_data[$name];
            $this->items = $data["names"];
        }
        else {
            $this->items = $items;
        }

        if ($index === -1) {
            $data = Data::$save_data[$name];
            $this->index = (int)$data;
        }
        else {
            $this->index = $index;
        }

        $this->saveable = $saveable;
        $this->divider = $divider;
    }

    public function move($key) {
        if ($key == null) {
            return false;
        }

        if ($key == "d") {
            $this->index++;
            if ($this->index >= count($this->items)) {
                $this->index = 0;
            }
            if ($this->saveable) Data::$save_data[$this->name] = $this->index;
            return true;
        }
        else if ($key == "a") {
            $this->index--;
            if ($this->index <= -1) {
                $this->index = count($this->items)-1;
            }
            if ($this->saveable) Data::$save_data[$this->name] = $this->index;
        }
        return false;
    }
}