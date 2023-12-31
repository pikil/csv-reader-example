<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInite159dcde229eb04b338756fdf5ff03ac
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

        spl_autoload_register(array('ComposerAutoloaderInite159dcde229eb04b338756fdf5ff03ac', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInite159dcde229eb04b338756fdf5ff03ac', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInite159dcde229eb04b338756fdf5ff03ac::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
