<?php

class CounterTerrorist extends Team {
    public $name = 'Counter-Terrorist';
    private static $instance = null;

    private function __construct(){}

    public static function get_instance()
    {
        if (self::$instance === null)
            self::$instance = new static;
        return self::$instance;
    }
}