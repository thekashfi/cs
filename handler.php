<?php

require './helpers.php';

// explode input commands
$commands = file_get_contents('./commands');
//$commands = preg_split("/\d\d:\d\d:\d\d\d\K/", $commands, null, PREG_SPLIT_NO_EMPTY);
$commands = explode("\n", $commands);

// print inputs
echo "    \033[32minput:\n \033[0m\n";
foreach ($commands as $line => $command) {
    echo "        " . $line+1 . ' ' . trim($command);
    if ($line !== array_key_last($commands))
        echo "\n";
}

// print outputs
echo "\n\n";
echo "    \033[32moutput:\n \033[0m\n";
foreach ($commands as $line => $command) {
    if (ctype_digit(trim($command[0]))) {
//        game()->rounds_left = (int) trim($command[0]);
        continue;
    }

    if ($GLOBALS['winner'] ?? false){
        echo $GLOBALS['winner'];
        unset($GLOBALS['winner']);
    }

    // where magic happens :D
    $output = (new Command(trim($command)))->output();
    if ($output !== ''){
        echo "        " . $line+1 . ' ' . $output;
        if ($line !== array_key_last($commands))
            echo "\n";
    }
}

// accepts php code
if ($argv[1] ?? false && $argv[1] === '-i') {
    echo "\n\n    \033[32m";
    $php_code = readline("anything...?\033[0m");
    eval($php_code);
}
