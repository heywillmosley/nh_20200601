<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit925e102bad64dd22618e8624d9f99af4
{
    public static $prefixLengthsPsr4 = array (
        'B' => 
        array (
            'Braintree\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Braintree\\' => 
        array (
            0 => __DIR__ . '/..' . '/braintree/braintree_php/lib/Braintree',
        ),
    );

    public static $prefixesPsr0 = array (
        'B' => 
        array (
            'Braintree' => 
            array (
                0 => __DIR__ . '/..' . '/braintree/braintree_php/lib',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit925e102bad64dd22618e8624d9f99af4::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit925e102bad64dd22618e8624d9f99af4::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit925e102bad64dd22618e8624d9f99af4::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}
