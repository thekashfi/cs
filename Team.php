<?php

class Team
{
    public $players = [];
    public $wins = 0;

    public function won()
    {
        $this->wins++;

        foreach ($this->players as $p) {
            $p->add_money(2700);
        }
    }

    public function lose()
    {
        foreach ($this->players as $p) {
            $p->add_money(2400);
        }
    }

    /**
     * join a player to a team.
     */
    public function join_player(Player $player): bool // void
    {
        // ! $this->player_exists($player->name) ?: exception('you are already in this game');
        if ($this->player_exists($player->name))
            exception('you are already in this game'); // TODO: ask: maybe one line these errors?!
        if (count($this->players) >= 10)
            exception('this team is full');

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
    public function get_player($name)
    {
        foreach ($this->players as $player) {
            if ($player->name === $name)
                return $player;
        }
        return null;
    }

    /**
     * prints team score-board.
     */
    public function board() {
        echo $this->name . ":\n";


        $players = [];
        foreach ($this->players as $player) {
            $players[] = (array) $player;
        }

        array_multisort(
            array_column($players, 'kills'),  SORT_DESC,
            array_column($players, 'deaths'), SORT_ASC,
            array_column($players, 'joined_at'), SORT_ASC,
            $players);

        foreach ($players as $rank => $p) {
            echo $rank+1 . " {$p['name']} {$p['kills']} {$p['deaths']}";

            if ($rank !== array_key_last_php7($players))
                echo "\n";
        }
    }
}