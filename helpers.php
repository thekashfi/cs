<?php

/**
 * die and dump (var_dump)
 */
function dd($value) {
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
    echo "$error \n";
//    if ($break)
//        die("        \033[31m$error \033[0m\n");
//    echo "        \033[36m$error \033[0m\n";
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

function array_key_last_php7($array) {
    return array_keys($array)[count($array)-1];
}