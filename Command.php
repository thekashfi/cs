<?php

class Command
{
    private string $output = '';
    private string $player_name;

    public function __construct(public $command)
    {
        $this->command = explode(' ', $this->command);

        $method = toUnderline(strtolower($this->command[0]));

        if (method_exists($this, $method)) { //TODO: remove braces from if
            try {
                echo $this->$method();
            } catch (CsException $exception){
                error($exception->getMessage(), false);
            }
        }

    }

    /**
     * add user to a team.
     */
    public function add_user(): void
    {
        $name = $this->command[1];
        $team = $this->command[2];
        $time = $this->command[3];

        if (game()->player_exists($name))
            exception('you are already in this game');

        // Player::create($name)->join($team); // TODO: how do i write this syntax.

        Player::create($name, $time)->join($team);
        $this->output = 'this user added to ' . $team;
    }

    /**
     * return a user's money amount.
     */
    public function get_money(): void
    {
        $this->player_name = $this->command[1];

        $player = $this->find_or_fail();

        $this->output = $player->money;
    }

    /**
     * return a user's health amount.
     */
    public function get_health(): void
    {
        $this->player_name = $this->command[1];

        $player = $this->find_or_fail();

        $this->output = $player->health;
    }

    /**
     * shoot a player.
     */
    public function tap(): void
    {
        $attacker = $this->command[1];
        $attacked = $this->command[2];
        $gun_type = $this->command[3];

        $attacker = $this->find_or_fail($attacker);

        $attacked = $this->find_or_fail($attacked);

        $attacker->shoot($attacked, $gun_type);
        $this->output = 'nice shot';
    }

    /**
     * player buy gun.
     */
    public function buy(): void
    {
        $this->player_name = $this->command[1];
        $gun = $this->command[2];
        $time = substr_replace($this->command[3] ,"", -1);

        $player = $this->find_or_fail();

        if ($player->health === 0)
            exception('deads can not buy'); // TODO: exception()

        if (strtotime($time) >= strtotime('00:45:00'))
            exception('you are out of time');

        $player->buy($gun);
        $this->output = 'I hope you can use it';
    }

    /**
     * print player's <rank> <name> <kills> <deaths>
     */
    public function score_board(): void
    {
        game()->board();
    }

    public function output(): string
    {
        return $this->output;
    }

    /**
     * return player if find it. otherwise throw 'invalid username' exception.
     */
    private function find_or_fail(string $player_name = null): Player|Exception
    {
        return game()->get_player($player_name ?? $this->player_name) ?: exception('invalid username');
    }
}













