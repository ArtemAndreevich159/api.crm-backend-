<?php

namespace core;

/**
 * Class Autoloader
 * @package core
 */
class Autoloader
{
    /**
     * Регистрация автозагрузчика классов
     */
    public static function register() {
        spl_autoload_register(function ($class) {
            $file = ROOT . DIRECTORY_SEPARATOR
                . str_replace('\\', DIRECTORY_SEPARATOR, $class) . '.php';

            if (file_exists($file)) {
                require $file;
                return true;
            }

            return false;
        });
    }
}