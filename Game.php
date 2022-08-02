<?php

class Game
{
//    public int $rounds_left;
    public int $round_commands_left;
    private static $instance = null;

    private function __construct() {}

    public static function get_instance() {
        if (self::$instance === null)
            self::$instance = new self;
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

    /**
     * if player exists in game. returns the player. otherwise returns false.
     */
    public function players(): array
    {
        return array_merge(ct()->players, t()->players);
    }

    /**
     * prints full game score-board.
     */
    public function board() {
        ct()->board();
        echo "\n";
        t()->board();
    }

    /**
     * specifies round winner. increase wins count of that team and return it.
     */
    public function get_round_winner(): CounterTerrorist|Terrorist
    {
        $ct_players = count(ct()->players);
        $t_players = count(t()->players);
        $ct_alives = count($this->get_alives(ct()->players));
        $t_alives = count($this->get_alives(t()->players));

        if ($ct_players && ! $ct_alives)
            $winner = t();

        if ($t_players && ! $t_alives)
            $winner = ct();

        // increase wins count
        ($winner = $winner ?? ct())->won();
        $winner instanceof CounterTerrorist ? t()->lose() : ct()->lose();
        return $winner;
    }

    private function get_alives(array $players)
    {
        $alives = [];
        foreach ($players as $p) {
            if ($p->health === 0)
                $alives[] = $p;
        }
        return $alives;
    }

//    public function get_game_winner(): CounterTerrorist|Terrorist
//    {
//        return ct()->wins > t()->wins ? ct() : t();
//    }

    public function start_round()
    {
        foreach (game()->players() as $p) {
            $p->health = 100;
            $p->guns[] = new Gun('Knife', null, 'knife', 0, 43, 500);
        }
    }
}