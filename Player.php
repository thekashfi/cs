<?php

class Player
{
    public string $name;
    public string $team;
    public array $guns = [];
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
        $player->set_join_time($time);
        $player->set_join_health($time);
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
            exception('attacker is dead');

        if ($attacked->health === 0)
            exception('attacked is dead');

        if (! ($gun = $this->gun($gun_type)))
            exception('no such gun');

        if ($this->team === $attacked->team)
            exception('friendly fire');

        $attacked->shot($this, $gun);
    }

    public function shot(Player $attacker, Gun $gun): void
    {
        $health = $this->decrease_health($gun->damage);
        if ($health !== 0)
            return;

        $this->die();
        $attacker->add_money($gun->reward);
        $attacker->kills++;
    }

    private function die(): void
    {
        $this->deaths++;
        $this->guns = [];
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

    private function set_join_time(string $time): void
    {
        $milliseconds = explode(':', $time)[2];
        $this->joined_at = strtotime(str_replace(':' . $milliseconds, '', $time)) . $milliseconds;
    }

    private function set_join_health(string $time): void
    {
        $time = (int) (explode(':', $time)[1] . explode(':', $time)[2]);
        if ($time <= 3000) {
            $this->health = 100;
            $this->guns[] = new Gun('Knife', null, 'knife', 0, 43, 500);
        }
    }
}