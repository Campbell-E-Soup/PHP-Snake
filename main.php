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
$config = new Menu([
        //game settings
        new MenuItem("[Game Settings]",[false],saveable: false,divider: true), //divider
        new MenuItem("Difficulty",null,-1),
        new MenuItem("Game Speed",null,-1),
        new MenuItem("Game Area" ,null,-1),
        //visual settings
        new MenuItem("[Visuals]",[false],saveable: false,divider: true), //divider
        new MenuItem("Text Color",null,-1),
        new MenuItem("Border Color",null,-1),
        new MenuItem("Border Style",null,-1),
        //exit
        new MenuItem("Exit",[false],saveable: false)
    ],
    1 //avoid settings divider
);
$gameStates = [null,$config,null]; //null exits program
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