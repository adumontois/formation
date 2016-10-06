<?php
/**
 * Created by PhpStorm.
 * User: adumontois
 * Date: 03/10/2016
 * Time: 14:22
 */

/**
 * @var $app OCFram\Application
 * Représente l'application à lancer
 */

// Charger les autoloads
const DEFAULT_APP = 'Frontend';

if ( !isset( $_GET[ 'app' ] ) OR !file_exists( __DIR__ . '\..\App\\' . $_GET[ 'app' ] . '\\' . $_GET[ 'app' ] . 'Application.php' ) ) {
	$_GET[ 'app' ] = DEFAULT_APP;
}

require __DIR__ . '\..\lib\OCFram\SplClassLoader.php';

$OCFramLoader = new SplClassLoader( 'OCFram', __DIR__ . '\..\lib' );
$OCFramLoader->register();

$appLoader = new SplClassLoader( 'App', __DIR__ . '\..' );
$appLoader->register();

$modelLoader = new SplClassLoader( 'Model', __DIR__ . '\..\lib\vendors' );
$modelLoader->register();

$entityLoader = new SplClassLoader( 'Entity', __DIR__ . '\..\lib\vendors' );
$entityLoader->register();

$formBuilderLoader = new SplClassLoader( 'FormBuilder', __DIR__ . '\..\lib\vendors' );
$formBuilderLoader->register();


$appClass = 'App\\' . $_GET[ 'app' ] . '\\' . $_GET[ 'app' ] . 'Application';
$app      = new $appClass;
$app->run();