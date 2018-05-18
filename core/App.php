<?php

namespace core;

define('DS', DIRECTORY_SEPARATOR);
define('COMPONENTS_DIR', ROOT . '/components/');
define('CORE_DIR', ROOT . '/core/');
define('CONTROLLERS_DIR', ROOT . '/controllers/');
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

    /**
     * Точка входа в приложение
     * @param $config
     * @throws \Exception
     */
    public function run($config) {
        $fileNmeConfig = CONFIG_DIR . $config . PHP_EXT;

        if (!file_exists($fileNmeConfig)) {
            throw new \Exception('Not found config file');
        }

        self::$_config = include ($fileNmeConfig);

        $this->proccessRequest();
    }

    /**
     * Получает URI
     * todo Реализовать в компоненте Request
     * @param null $default
     * @return null|string
     */
    protected function getURI($default = null) {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }

        return $default;
    }

    /**
     * @throws \Exception
     */
    protected function proccessRequest() {
        $uri = $this->getURI();

        if (!$uri) {
            throw new \Exception('Bad request');
        }

        $routes = explode('/', $uri);
        $method = array_shift($routes);

        $methodArr = explode('.', $method);

        if (2 !== count($methodArr)) {
            throw new \Exception('Wrong method');
        }

        $controllerName = array_shift($methodArr);
        $actionName = array_shift($methodArr);

        $controllerClassName = 'controllers\\' . ucfirst($controllerName) . 'Controller';
        $action = 'action' . ucfirst($actionName);

        if (!class_exists($controllerClassName)) {
            throw new \Exception('Unknown method');
        }

        $controllerObj = new $controllerClassName;

        if (!method_exists($controllerObj, $action)) {
            throw new \Exception('Unknown method');
        }

        $result = call_user_func_array([$controllerObj, $action], $routes);
        $resultJSON = json_encode($result);

        echo $resultJSON;
    }
}