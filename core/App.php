<?php

namespace core;

define('DS', DIRECTORY_SEPARATOR);
define('COMPONENTS_DIR', ROOT . '/components/');
define('CORE_DIR', ROOT . '/core/');
define('METHODS_DIR', ROOT . '/methods/');
define('MODELS_DIR', ROOT . '/models/');
define('CONFIG_DIR', CORE_DIR . 'config/');
define('PHP_EXT', '.php');

/**
 * Class App
 * @package core
 */
class App
{
    /** @var  App */
    protected static $_instance = null;
    /** @var array  */
    protected static $_config = [];

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

    public function run($config) {
        $fileNmeConfig = CONFIG_DIR . $config . PHP_EXT;

        if (!file_exists($fileNmeConfig)) {
            throw new \Exception('Not found config file');
        }

        self::$_config = include ($fileNmeConfig);
    }
}