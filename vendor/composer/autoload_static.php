<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitaa75611dc34894d37ea367db20cb7908
{
    public static $files = array (
        'f953a7ad743b42e01cad79625f2bfe94' => __DIR__ . '/../..' . '/src/conversion_helper.php',
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->classMap = ComposerStaticInitaa75611dc34894d37ea367db20cb7908::$classMap;

        }, null, ClassLoader::class);
    }
}
