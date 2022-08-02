<?php

require './helpers.php';

// explode input commands
//$commands = file_get_contents('./commands');

$rounds = (int) readline();
for($r = 0; $r < $rounds; $r++) {
    $round_commands_left = readline();
    $round_commands_left = (int) explode(' ', $round_commands_left)[1];

    game()->round_commands_left = $round_commands_left;
    game()->start_round();

    for($c = 0; $c < $round_commands_left; $c++) {
        $command = readline();


        $output = (new Command($command))->output();
        $foo = game()->round_commands_left;
        if ($output !== ''){
            echo $output/* . "\033[31m$foo \033[0m\n"*/;
//            if ($line !== array_key_last_php7($commands))
//                echo "\n";

            if ($GLOBALS['winner'] ?? false){
                echo "\n" . $GLOBALS['winner'];
                unset($GLOBALS['winner']);
            }
        }
    }
}

if ($GLOBALS['winner'] ?? false){
    echo "\n" . $GLOBALS['winner'];
    unset($GLOBALS['winner']);
}



//foreach ($commands as $line => $command) {
//    if (ctype_digit(trim($command[0]))) {
////        game()->rounds_left = (int) trim($command[0]);
//        continue;
//    }
//
//    if ($GLOBALS['winner'] ?? false){
//        echo $GLOBALS['winner'];
//        unset($GLOBALS['winner']);
//    }
//
//    // where magic happens :D
//    $output = (new Command(trim($command)))->output();
//    if ($output !== ''){
//        echo $output;
//        if ($line !== array_key_last_php7($commands))
//            echo "\n";
//    }
//}
//
//if ($GLOBALS['winner'] ?? false){
//    echo "\n" . $GLOBALS['winner'];
//    unset($GLOBALS['winner']);
//}

// accepts php code
//if ($argv[1] ?? false && $argv[1] === '-i') {
//    echo "\n\n    \033[32m";
//    $php_code = readline("anything...?\033[0m");
//    eval($php_code);
//}
