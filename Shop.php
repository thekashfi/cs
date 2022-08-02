<?php

class Shop
{
    public $guns;
    private static $instance;

    private function __construct() {
        $this->generate_guns();
    }

    public static function get_instance(){
        if (self::$instance === null)
            self::$instance = new self;
        return self::$instance;
    }

    public function buy(string $gun_name)
    {
        return $this->gun($gun_name);
    }

    /**
     * return player's gun of given type.
     */
    private function gun(string $gun_name)
    {
        foreach ($this->guns as $gun) {
            if ($gun->name === $gun_name)
                return $gun;
        }
        return null;
    }

    private function generate_guns()
    {
        $this->guns[] = new Gun('AK', 'Terrorist', 'heavy', 2700, 31, 100);
        $this->guns[] = new Gun('AWP', null, 'heavy', 4300, 110, 50);
        $this->guns[] = new Gun('Revolver', 'Terrorist', 'pistol', 600, 51, 150);
        $this->guns[] = new Gun('Glock-18', 'Terrorist', 'pistol', 300, 11, 200);
        $this->guns[] = new Gun('M4A1', 'Counter-Terrorist', 'heavy', 2700, 11, 200);
        $this->guns[] = new Gun('Desert-Eagle', 'Counter-Terrorist', 'pistol', 600, 53, 175);
        $this->guns[] = new Gun('UPS-S', 'Counter-Terrorist', 'pistol', 300, 13, 225);
    }
}