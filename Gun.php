<?php

class Gun { // TODO: should be singleton :D
    //pistol/gun/knife

    public $name;
    public $team;
    public $type;
    public $price;
    public $damage;
    public $reward;

    public function __construct(string $name, $team, string $type, int $price, int $damage, int $reward)
    {
        $this->team = $team;
        $this->type = $type;
        $this->damage = $damage;
        $this->reward = $reward;
        $this->name = $name;
        $this->price = $price;
    }
}