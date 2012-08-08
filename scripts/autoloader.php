<?php

return function ($class) {
    $class = ltrim($class, "\\");
    if (0 === strpos($class, 'ComponentHub\\')) {
        $path = dirname(__DIR__) . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
        $path .= str_replace(array('_', '\\'), DIRECTORY_SEPARATOR, $class) . '.php';
        require $path;
    }
};
