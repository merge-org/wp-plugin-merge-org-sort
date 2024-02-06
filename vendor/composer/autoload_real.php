<?php

// autoload_real.php @generated by Composer

class ComposerAutoloaderInit16daa3b8664a797ee0a02f16c0b1be70
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

        require __DIR__ . '/platform_check.php';

        spl_autoload_register(array('ComposerAutoloaderInit16daa3b8664a797ee0a02f16c0b1be70', 'loadClassLoader'), true, true);
        self::$loader = $loader = new \Composer\Autoload\ClassLoader(\dirname(__DIR__));
        spl_autoload_unregister(array('ComposerAutoloaderInit16daa3b8664a797ee0a02f16c0b1be70', 'loadClassLoader'));

        require __DIR__ . '/autoload_static.php';
        call_user_func(\Composer\Autoload\ComposerStaticInit16daa3b8664a797ee0a02f16c0b1be70::getInitializer($loader));

        $loader->register(true);

        return $loader;
    }
}
