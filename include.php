<?php

require_once '/var/www/Symfony/vendor/symfony/src/Symfony/Component/ClassLoader/UniversalClassLoader.php';

use Symfony\Component\ClassLoader\UniversalClassLoader;

$loader = new UniversalClassLoader();

$loader->registerNamespaces(array(
	'Symfony'	=> array('/var/www/Symfony/src', '/var/www/Symfony/vendor/symfony/src'),
	'Buzz'		=> 'Buzz/lib'
));

$loader->register();


