<?php

require_once "Helpers/Input.php";
require_once "MenuSystem/Menu.php";
require_once "MenuSystem/MenuItem.php";
require_once "SaveData/Data.php";

use Helpers\Input;
use MenuSystem\Menu;
use MenuSystem\MenuItem;
use SaveData\Data;

//call required for start up
Input::init();
Data::Load();
Menu::loadMenuData();
//game page config
$gameStates = [null,new Menu([new MenuItem("Text Color",null,-1),new MenuItem("Exit",[false])]),null];
    $menu = new Menu([new MenuItem("Play",[]),new MenuItem("Config",items: []),new MenuItem("Exit",[])]);
    while (true) {
        $location = $menu->render_start();
        if ($location === null) break;
        $display = $gameStates[$location];
        if ($display == null) {
            break; //something has gone wrong, or you want to exit
        }
        else {
            $display->render();
        }
    }
//call required to close
Input::restore();
Data::Save();

echo "Exited cleanly.\n";
echo "$location \n";