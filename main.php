<?php

require_once "Helpers/Input.php";
require_once "MenuSystem/Menu.php";
require_once "MenuSystem/MenuItem.php";
require_once "SaveData/Data.php";
require_once "Game/GameArea.php";

use Game\GameArea;
use Helpers\Input;
use MenuSystem\Menu;
use MenuSystem\MenuItem;
use SaveData\Data;

//call required for start up
Input::init();
Data::Load();
//game page config
$config = new Menu([
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
        //exit
        new MenuItem("Exit",[false],saveable: false)
    ],
    1 //avoid settings divider
);
$game = new GameArea();
$gameStates = [$game,$config,null]; //null exits program
    $menu = new Menu([new MenuItem("Play",[]),new MenuItem("Config",items: []),new MenuItem("Exit",[])]);
    while (true) {
        $location = $menu->renderStart();
        if ($location === null) break;
        $display = $gameStates[$location];
        if ($display == null) {
            break; //something has gone wrong, or you want to exit
        }
        else {
            $display->render();
            //update and refresh game (either game is done or settings may have changed)
            $game = new GameArea();
            $gameStates[0] = $game;
        }
    }

//call required to close
Input::restore();
Data::Save();

echo "Exited cleanly.\n";