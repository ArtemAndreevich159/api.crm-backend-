<?php

define('ROOT', dirname(__FILE__));

require_once ROOT . '/core/Autoloader.php';
\core\Autoloader::register();

\core\App::getInstance()->run('web');