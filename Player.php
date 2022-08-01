<?php

class Player
{
    public string $name;
    public string $team;
    private array $guns = [];
    public int $health = 0;
    public int $money = 0;
    public int $kills = 0;
    public int $deaths = 0;
    public int $joined_at;

    /**
     * create CTPlayer or TPlayer.
     */
    public static function create(string $name, string $time): Player
    {
        $player = new self;
        $player->name = $name;
        $player->set_joined_at($time);
        return $player;
    }

    /**
     * joins the given player to the relevant team.
     */
    public function join(string $team): Player
    {
        if ($team === ct()->name)
            ct()->join_player($this);
        else
            t()->join_player($this);

        $this->team = $team;
        return $this;
    }

    public function shoot(Player $attacked, string $gun_type): void
    {
        if ($this->health === 0)
            throw new CsException('attacker is dead');

        if ($attacked->health === 0)
            throw new CsException('attacked is dead');

        if (! $gun = $this->gun($gun_type))
            throw new CsException('no such gun');

        if ($this->team === $attacked->team)
            throw new CsException('friendly fire');

        $attacked_health = $attacked->shot($gun->damage);
        if ($attacked_health === 0) {
            $this->add_money($gun->reward);
            $this->kills++;
        }
    }

    public function shot($damage): bool
    {
        $health = $this->decrease_health($damage);
        if ($health === 0)
            $this->deaths++;

        return $health;
    }


    /**
     * buy gun for player.
     */
    public function buy(string $gun_name): void
    {
        $gun = shop()->buy($gun_name);

        if ($gun->team !== $this->team && $gun->team !== null)
            exception('invalid category gun');

        if ($this->gun($gun->type))
            exception("you have a {$gun->type}");

        if ($gun->price > $this->money)
            exception('no enough money');

        $this->guns[] = $gun;
        $this->subtract_money($gun->price);
    }

    /**
     * return player's gun with given type. and null if not found between player's guns.
     */
    private function gun(string $gun_type): Gun|null
    {
        foreach ($this->guns as $gun) {
            if ($gun->type === $gun_type)
                return $gun;
        }
        return null;
    }

    public function decrease_health(int $damage): int
    {
        $this->health -= $damage;
        $this->health > 0 ?: $this->health = 0;
        return $this->health;
    }

    public function add_money (int $money): void
    {
        $this->money += $money;
        $this->money < 10000 ?: $this->money = 10000;
    }

    public function subtract_money(int $money): void
    {
        $this->money -= $money;
    }

    private function set_joined_at(string $time)
    {
        $milliseconds = explode(':', $time)[2];
        $this->joined_at = strtotime(str_replace(':' . $milliseconds, '', $time)) . $milliseconds;
    }
}