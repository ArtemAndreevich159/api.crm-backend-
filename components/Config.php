<?php

namespace components;

use core\Component;

/**
 * Class Config
 * @package components
 */
class Config extends Component
{
    /**
     * @param string $path
     * @return array|string
     */
    public function get($path) {
        $pathArr = explode('/', $path);
        $res = self::$_config;

        foreach ($pathArr as $item) {
            if (is_array($res[$item])) {
                $res = $res[$item];
            }
        }

        return $res;
    }
}