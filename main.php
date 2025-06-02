<?php

require_once "Helpers/Input.php";
require_once "MenuSystem/Menu.php";
require_once "MenuSystem/MenuItem.php";
require_once "SaveData/Data.php";
require_once "Game/GameArea.php";
require_once "Game/Snake.php";

use Game\GameArea;
use Helpers\Input;
use MenuSystem\Menu;
use MenuSystem\MenuItem;
use SaveData\Data;

//call required for start up
Input::init();
Data::Load();
//game page config
$config = Menu::createSettings();
$game = new GameArea();
$gameStates = [$game,$config,null]; //null exits program
    $menu = new Menu([new MenuItem("Play",[],saveable:false),new MenuItem("Config",items: [],saveable:false),new MenuItem("Exit",[],saveable:false)]);
    while (true) {
        $location = $menu->renderStart();
        if ($location === null) break;
        $display = $gameStates[$location];
        if ($display == null) {
            break; //something has gone wrong, or you want to exit
        }
        else {
            $restart = true;
            while ($restart) {
                $restart = $display->render();
                //update and refresh game (either game is done or settings may have changed)
                $game = new GameArea();
                $gameStates[0] = $game;
                $display = $game; //only should matter when restarting
            }
        }
    }

//call required to close
Input::restore();
Data::Save();

echo "Exited cleanly.\n";