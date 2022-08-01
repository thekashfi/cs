<?php

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
function error(string $error, $break = true): void
{
    if ($break)
        die("        \033[31m$error \033[0m\n");
    echo "        \033[36m$error \033[0m\n";
}

/**
 * break output flow and prints error
 */
function exception(string $error_message): void
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