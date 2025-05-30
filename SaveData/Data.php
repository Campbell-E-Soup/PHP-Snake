<?php

namespace SaveData;

//this is a class that saves and loads the config data

class Data {
    public static $gameSpeed = 100000;
    public static $game_data;
    public static $save_data;
    public static function Save() {
        $filePath = __DIR__ . '/save-data.json';
        file_put_contents($filePath, json_encode(self::$save_data, JSON_PRETTY_PRINT));
    }

    public static function Load() {
        self::$game_data = self::LoadJSONTo(__DIR__ . '/game-data.json');
        self::$save_data = self::LoadJSONTo(__DIR__ . '/save-data.json');
    }

    private static function LoadJSONTo($filePath) {
        $json = file_get_contents($filePath);
        $json_data = null;
        if ($json === false) {
            die('Error reading the JSON file');
        }

        $json_data = json_decode($json, true); 

        return $json_data;
    }

    public static function GetData($data_name,$index) {
        $data = self::$game_data[$data_name];
        $arr = $data["values"];
        return $arr[$index];
    }
}