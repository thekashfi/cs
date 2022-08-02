<?php

class Command
{
    private $output = '';
    private $player_name;
    public $command;

    public function __construct($command)
    {
        $this->command = explode(' ', $command);
        $method = toUnderline(strtolower($this->command[0]));

        if (method_exists($this, $method))
            try {
                $this->$method();

                if ($this->command[0] !== 'ROUND' && --game()->round_commands_left === 0) {
//                    if($this->command[0] === 'SCORE-BOARD') {
//                        dd(game()->get_round_winner()->name);
//                    }
                    $GLOBALS['winner'] = game()->get_round_winner()->name . ' won' . "\n";
                }
            } catch (CsException $exception){
                error($exception->getMessage(), false);
            }
    }

    /**
     * add user to a team.
     */
    public function add_user()
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
    public function get_money()
    {
        $this->player_name = $this->command[1];

        $player = $this->find_or_fail();

        $this->output = $player->money;
    }

    /**
     * return a user's health amount.
     */
    public function get_health()
    {
        $this->player_name = $this->command[1];

        $player = $this->find_or_fail();

        $this->output = $player->health;
    }

    /**
     * shoot a player.
     */
    public function tap()
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
    public function buy()
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
    public function score_board()
    {
        game()->board();
    }

    /**
     * set number of this round commands. and do start_round needed functionalities.
     */
    public function round()
    {
        game()->round_commands_left = (int) $this->command[1];

        game()->start_round();
    }

    public function output(): string
    {
        return $this->output;
    }

    /**
     * return player if find it. otherwise throw 'invalid username' exception.
     */
    private function find_or_fail(string $player_name = null)
    {
        return game()->get_player($player_name ?? $this->player_name) ?: exception('invalid username');
    }
}