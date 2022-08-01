<?php

class Game
{
    public $rounds_past;
    public $rounds_left;
    private static $instance = null;

    private function __construct() {}

    public static function get_instance() {
        if (self::$instance === null) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    /**
     * checks if player exists in game or not.
     */
    public function player_exists($name): bool
    {
        return (bool) $this->get_player($name);
    }

    /**
     * if player exists in game. returns the player. otherwise returns false.
     */
    public function get_player($name): Player|null
    {
        foreach (array_merge(ct()->players, t()->players) as $player) {
            if ($player->name === $name)
                return $player;
        }
        return null;
    }
}