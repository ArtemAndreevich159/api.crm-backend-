<?php

namespace components;

use core\Component;

/**
 * Class Request
 * @package components
 */
class Request extends Component
{
    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function get($name, $default = null) {
        return isset($_GET[$name]) ? $_GET[$name] : $default;
    }

    /**
     * @param string $name
     * @param null $default
     * @return mixed|null
     */
    public function post($name, $default = null) {
        return isset($_POST[$name]) ? $_POST[$name] : $default;
    }

    /**
     * @return bool
     */
    public function isAjax() {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH'])
            && !empty($_SERVER['HTTP_X_REQUESTED_WITH'])
            && 'xmlhttprequest' == strtolower($_SERVER['HTTP_X_REQUESTED_WITH']);
    }

    /**
     * @param string $name
     * @param null $default
     * @return string
     */
    public function getHeader($name, $default = null) {
        $header = 'HTTP_' . mb_strtoupper(str_replace('-', '_', $name));
        return isset($_SERVER[$header]) ? $_SERVER[$header] : $default;
    }

    /**
     * @param null $default
     * @return null|string
     */
    public function getURI($default = null) {
        if (!empty($_SERVER['REQUEST_URI'])) {
            return trim($_SERVER['REQUEST_URI'], '/');
        }

        return $default;
    }
}