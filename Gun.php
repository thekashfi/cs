<?php

class Gun {
    //pistol/gun/knife

    public string $name;
    public string|null $team;
    public string $type;
    public int $price;
    public int $damage;
    public int $reward;

    public function __construct(string $name, string|null $team, string $type, int $price, int $damage, int $reward)
    {
        $this->team = $team;
        $this->type = $type;
        $this->damage = $damage;
        $this->reward = $reward;
        $this->name = $name;
        $this->price = $price;
    }
}