<?php
// cs.php
class CsException extends \Exception
{
}





// command.php
class Command
{
    private $output = '';
    private $player_name;
    public $command;

    public function __construct($command)
    {
        $this->command = explode(' ', trim($command));
        $method = toUnderline(strtolower($this->command[0]));

        if (method_exists($this, $method))
            try {
                if ($this->command[0] !== 'ROUND' && --game()->round_commands_left === 0) {
                    $GLOBALS['winner'] = game()->get_round_winner()->name . ' won';
                }

                $this->$method();
            } catch (CsException $exception) {
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
        $time = substr_replace($this->command[3], "", -1);

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
        game()->round_commands_left = (int)$this->command[1];

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






// CounterTerrorist.php
class CounterTerrorist extends Team
{
    public $name = 'Counter-Terrorist';
    private static $instance = null;

    private function __construct()
    {
    }

    public static function get_instance()
    {
        if (self::$instance === null)
            self::$instance = new static;
        return self::$instance;
    }
}








// Game.php
class Game
{
//    public int $rounds_left;
    public $round_commands_left;
    private static $instance = null;

    private function __construct()
    {
    }

    public static function get_instance()
    {
        if (self::$instance === null)
            self::$instance = new self;
        return self::$instance;
    }

    /**
     * checks if player exists in game or not.
     */
    public function player_exists($name): bool
    {
        return (bool)$this->get_player($name);
    }

    /**
     * if player exists in game. returns the player. otherwise returns false.
     */
    public function get_player($name)
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
    public function board()
    {
        ct()->board();
        echo "\n";
        t()->board();
    }

    /**
     * specifies round winner. increase wins count of that team and return it.
     */
    public function get_round_winner()
    {
        $ct_players = count(ct()->players);
        $t_players = count(t()->players);
        $ct_alives = count($this->get_alives(ct()->players));
        $t_alives = count($this->get_alives(t()->players));

        if ($ct_players && !$ct_alives)
            $winner = t();

        if ($t_players && !$t_alives)
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
            if ($p->health !== 0)
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
        return (bool)$this->get_player($name);
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
    public function board()
    {
        echo $this->name . ":\n";


        $players = [];
        foreach ($this->players as $player) {
            $players[] = (array)$player;
        }

        array_multisort(
            array_column($players, 'kills'), SORT_DESC,
            array_column($players, 'deaths'), SORT_ASC,
            array_column($players, 'joined_at'), SORT_ASC,
            $players);

        foreach ($players as $rank => $p) {
            echo $rank + 1 . " {$p['name']} {$p['kills']} {$p['deaths']}";

            if ($rank !== array_key_last_php7($players))
                echo "\n";
        }
    }
}

class Gun
{ // TODO: should be singleton :D
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










class Player
{
    public $name;
    public $team;
    public $guns = [];
    public $health = 0;
    public $money = 0;
    public $kills = 0;
    public $deaths = 0;
    public $joined_at;

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

    public function shoot(Player $attacked, string $gun_type)
    {
        if ($this->health === 0)
            exception('attacker is dead');

        if ($attacked->health === 0)
            exception('attacked is dead');

        if (!($gun = $this->gun($gun_type)))
            exception('no such gun');

        if ($this->team === $attacked->team)
            exception('friendly fire');

        $attacked->shot($this, $gun);
    }

    public function shot(Player $attacker, Gun $gun)
    {
        $health = $this->decrease_health($gun->damage);
        if ($health !== 0)
            return;

        $this->die();
        $attacker->add_money($gun->reward);
        $attacker->kills++;
    }

    private function die()
    {
        $this->deaths++;
        $this->guns = [];
    }


    /**
     * buy gun for player.
     */
    public function buy(string $gun_name)
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
    private function gun(string $gun_type)
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

    public function add_money(int $money)
    {
        $this->money += $money;
        $this->money < 10000 ?: $this->money = 10000;
    }

    public function subtract_money(int $money)
    {
        $this->money -= $money;
    }

    private function set_join_time(string $time)
    {
        $milliseconds = explode(':', $time)[2];
        $this->joined_at = strtotime(str_replace(':' . $milliseconds, '', $time)) . $milliseconds;
    }

    private function set_join_health(string $time)
    {
        $time = (int)(explode(':', $time)[1] . explode(':', $time)[2]);
        if ($time <= 3000) {
            $this->health = 100;
            $this->guns[] = new Gun('Knife', null, 'knife', 0, 43, 500);
        }
    }
}









class Shop
{
    public $guns;
    private static $instance;

    private function __construct()
    {
        $this->generate_guns();
    }

    public static function get_instance()
    {
        if (self::$instance === null)
            self::$instance = new self;
        return self::$instance;
    }

    public function buy(string $gun_name)
    {
        return $this->gun($gun_name);
    }

    /**
     * return player's gun of given type.
     */
    private function gun(string $gun_name)
    {
        foreach ($this->guns as $gun) {
            if ($gun->name === $gun_name)
                return $gun;
        }
        return null;
    }

    private function generate_guns()
    {
        $this->guns[] = new Gun('AK', 'Terrorist', 'heavy', 2700, 31, 100);
        $this->guns[] = new Gun('AWP', null, 'heavy', 4300, 110, 50);
        $this->guns[] = new Gun('Revolver', 'Terrorist', 'pistol', 600, 51, 150);
        $this->guns[] = new Gun('Glock-18', 'Terrorist', 'pistol', 300, 11, 200);
        $this->guns[] = new Gun('M4A1', 'Counter-Terrorist', 'heavy', 2700, 11, 200);
        $this->guns[] = new Gun('Desert-Eagle', 'Counter-Terrorist', 'pistol', 600, 53, 175);
        $this->guns[] = new Gun('UPS-S', 'Counter-Terrorist', 'pistol', 300, 13, 225);
    }
}








class Terrorist extends Team
{
    public $name = 'Terrorist';
    private static $instance = null;

    private function __construct()
    {
    }

    public static function get_instance()
    {
        if (self::$instance === null)
            self::$instance = new static;
        return self::$instance;
    }
}








// helpers.php
/**
 * die and dump (var_dump)
 */
function dd($value)
{
    echo "\033[33m";
    var_dump($value);
    echo "\033[0m\n";
    exit;
}

/**
 * dashes(-) to underlines(_)
 */
function toUnderline(string $string): string
{
    return str_replace('-', '_', $string);
}

/**
 * prints error
 */
function error(string $error, $break = true)
{
    echo "$error" ;
}

/**
 * throw new CsException
 */
function exception(string $error_message)
{
    throw new CsException($error_message);
}

/**
 * returns CounterTerrorist object (singleton)
 */
function ct(): CounterTerrorist
{
    return CounterTerrorist::get_instance();
}

/**
 * returns Terrorist object (singleton)
 */
function t(): Terrorist
{
    return Terrorist::get_instance();
}

/**
 * returns Game object (singleton)
 */
function game(): Game
{
    return Game::get_instance();
}

/**
 * returns Shop object (singleton)
 */
function shop(): Shop
{
    return Shop::get_instance();
}

function array_key_last_php7($array)
{
    return array_keys($array)[count($array) - 1];
}






// handler.php
$rounds = (int) readline();
for($r = 0; $r < $rounds; $r++) {
    $round_commands_left = readline();
    $round_commands_left = (int) explode(' ', $round_commands_left)[1];

    game()->round_commands_left = $round_commands_left;
    game()->start_round();

    for($c = 0; $c < $round_commands_left; $c++) {
        $command = readline();


        $output = (new Command($command))->output() . "\n";
        $foo = game()->round_commands_left;
        if ($output !== ''){
            echo $output/* . "\033[31m$foo \033[0m\n"*/;
//            if ($line !== array_key_last_php7($commands))
//                echo "\n";

            if ($GLOBALS['winner'] ?? false){
                echo $GLOBALS['winner'];
                unset($GLOBALS['winner']);
            }
        }
    }
}

if ($GLOBALS['winner'] ?? false){
    echo $GLOBALS['winner'];
    unset($GLOBALS['winner']);
}

function echo_memory_usage() {
    $mem_usage = memory_get_usage(true);

    if ($mem_usage < 1024)
        echo $mem_usage." bytes";
    elseif ($mem_usage < 1048576)
        echo round($mem_usage/1024,2)." kilobytes";
    else
        echo round($mem_usage/1048576,2)." megabytes";

    echo "<br/>";
}

echo_memory_usage();