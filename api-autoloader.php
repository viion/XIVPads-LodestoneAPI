<?php

/*  Autoload
 *  - This is XIVPads autoloader, if you load in a library that has its own auto loader
 *  - make sure to add it to the ignore list so it can manage itself!
 */
class AutoLoad
{
    function __construct()
    {
        spl_autoload_register(array(__CLASS__, 'autoload'));
    }

    public function autoload($classname)
    {
        $file = explode('\\', $classname);
        $file = $file[count($file) - 1];
        $file = __DIR__ .'/src/'. strtolower($file) .'.php';

        require $file;
    }
}
(new AutoLoad());