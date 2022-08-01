<?php

// includes
spl_autoload_register(function ($class_name) {
    include $class_name . '.php';
});

// start
require './handler.php';

class CsException extends \Exception { }