<?php

class Team
{
    public array $players = [];
    public int $wins;
    public int $loses;

    public function won(): void
    {

    }

    public function lose(): void
    {

    }

    /**
     * join a player to a team.
     */
    public function join_player(Player $player): bool // void
    {
        // ! $this->player_exists($player->name) ?: exception('you are already in this game');
        if ($this->player_exists($player->name))
            throw new CsException('you are already in this game'); // TODO: ask: maybe one line these errors?!
        if (count($this->players) >= 10)
            throw new CsException('this team is full');

        $this->players[] = $player;

        $player->add_money(1000);

        return true;
    }


    /**
     * checks if player exists in team or not.
     */
    public function player_exists($name): bool
    {
        return (bool) $this->get_player($name);
    }

    /**
     * if player exists in team. returns the player. otherwise returns false.
     */
    public function get_player($name): Player|null
    {
        foreach ($this->players as $player) {
            if ($player->name === $name)
                return $player;
        }
        return null;
    }

//    public function names(): array
//    {
//        $names = [];
//        foreach ($this->players as $player) {
//            $names[] = $player->name;
//        }
//        return $names;
//    }
}