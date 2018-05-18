<?php

namespace core;

define('DS', DIRECTORY_SEPARATOR);
define('COMPONENTS_DIR', ROOT . '/components/');
define('CORE_DIR', ROOT . '/core/');
define('METHODS_DIR', ROOT . '/methods/');
define('MODELS_DIR', ROOT . '/models/');
define('PHP_EXT', '.php');

class App
{
    /** @var  App */
    protected static $_instance = null;

    /**
     * @return App
     */
    public static function getInstance() {
        if (null === self::$_instance) {
            self::$_instance = new self;
        }

        return self::$_instance;
    }

    /**
     * App constructor.
     */
    private function __construct() {
    }
}