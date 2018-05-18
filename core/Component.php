<?php

namespace core;

/**
 * Class Component
 * @package core
 */
class Component
{
    /** @var array  */
    protected static $_config = [];
    /** @var array  */
    protected static $_components = [];

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name) {
        if (class_exists('\components\\' . ucfirst($name))) {
            return $this->getComponent($name);
        }

        $getter = 'get' . ucfirst($name);

        if (method_exists($this, $getter)) {
            $this->$getter();
        }
    }

    /**
     * @param string $name
     * @return object
     */
    protected function getComponent($name) {
        if (!isset(self::$_components[$name])) {
            $className = '\components\\' . ucfirst($name);
            self::$_components[$name] = $className;
        }

        return self::$_components[$name];
    }
}