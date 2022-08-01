<?php

class Player
{
    public string $name;
    public Gun $knife;
    public Gun $pistol;
    public Gun $heavy;
    public int $health = 0;
    public int $money = 0;

    /**
     * create CTPlayer or TPlayer.
     */
    public static function create(string $name, string $team): CTPlayer|TPlayer
    {
        if ($team === 'Counter-Terrorist')
            $player = new CTPlayer;
        else
            $player = new TPlayer;

        $player->name = $name;

        // join
        if ($player instanceof CTPlayer)
            ct()->join_player($player);
        else
            t()->join_player($player);

        return $player;
    }

//    /**
//     * joins the given player to the relevant team.
//     */
//    public function join(CTPlayer|TPlayer $player): void
//    {
//        if ($player instanceof CTPlayer)
//            ct()->join_player($player);
//        else
//            t()->join_player($player);
//    }

    public function shoot(Player $player, string $gun): bool
    {
        if ($this->health === 0)
            throw new CsException('attacker is dead');

        if ($player->health === 0)
            throw new CsException('attacked is dead');

        if ($damage = $this->{$gun}?->damage === null)
            throw new CsException('no such gun');

        if ($this->team->name === $player->team->name)
            throw new CsException('friendly fire');

        return $player->shot($damage);
    }

    public function shot($damage): bool
    {
        $health = $this->decrease_health($damage);
        return true; // TODO: refactor this and shoot returns...
    }

    public function was_teammate($shot_player)
    {

    }

    public function has_killed()
    {

    }

    public function get_reward()
    {

    }

    public function buy()
    {

    }

    public function decrease_health(int $damage): int
    {
        $this->health -= $damage;
        $this->health > 0 ?: $this->health = 0;
        return $this->health;
    }

    public function add_money (int $money): void
    {
        $this->money = $money;
        $this->money < 10000 ?: $this->money = 10000;
    }

    public function subtract_money()
    {

    }
}