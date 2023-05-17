<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit67fb06f36e50582a3b1e03d1674f83ab
{
    private static $loader;

    public static function loadClassLoader($class)
    {
        if ('Composer\Autoload\ClassLoader' === $class) {
            require __DIR__ . '/ClassLoader.php';
        }
    }

    /**
     * @return \Composer\Autoload\ClassLoader
     */
    public static function getLoader()
    {
        if (null !== self::$loader) {
            return self::$loader;
        }

        spl_autoload_register(array('ComposerAutoloaderInit67fb06f36e50582a3b1e03d1674f83ab', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit67fb06f36e50582a3b1e03d1674f83ab', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit67fb06f36e50582a3b1e03d1674f83ab::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}