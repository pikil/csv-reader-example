<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite159dcde229eb04b338756fdf5ff03ac
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Pikil\\CsvReader\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Pikil\\CsvReader\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
        'Pikil\\CsvReader\\Commands\\Command' => __DIR__ . '/../..' . '/src/Commands/Command.php',
        'Pikil\\CsvReader\\Commands\\ParseCsv' => __DIR__ . '/../..' . '/src/Commands/ParseCsv.php',
        'Pikil\\CsvReader\\Routing\\Router' => __DIR__ . '/../..' . '/src/Routing/Router.php',
        'Pikil\\CsvReader\\Routing\\ServiceRouter' => __DIR__ . '/../..' . '/src/Routing/ServiceRouter.php',
        'Pikil\\CsvReader\\Utils\\Result' => __DIR__ . '/../..' . '/src/Utils/Result.php',
        'Pikil\\CsvReader\\Utils\\ResultResponse' => __DIR__ . '/../..' . '/src/Utils/ResultResponse.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite159dcde229eb04b338756fdf5ff03ac::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite159dcde229eb04b338756fdf5ff03ac::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInite159dcde229eb04b338756fdf5ff03ac::$classMap;

        }, null, ClassLoader::class);
    }
}
