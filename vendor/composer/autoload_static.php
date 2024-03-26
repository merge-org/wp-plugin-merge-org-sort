<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitbb1908561f90b3c9b51d66b2aeec5eaa
{
    public static $prefixLengthsPsr4 = array (
        'M' => 
        array (
            'MergeOrg\\WpPluginSort\\' => 22,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'MergeOrg\\WpPluginSort\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitbb1908561f90b3c9b51d66b2aeec5eaa::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitbb1908561f90b3c9b51d66b2aeec5eaa::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitbb1908561f90b3c9b51d66b2aeec5eaa::$classMap;

        }, null, ClassLoader::class);
    }
}
