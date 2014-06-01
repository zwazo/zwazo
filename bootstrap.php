<?php

define('ROOT_DIR', __DIR__ );
define('ROOT_VENDOR', __DIR__.'/vendor');

$loader = require_once ROOT_VENDOR.'/autoload.php';
// @see 
//     https://getcomposer.org/doc/01-basic-usage.md
//     adding namespaces on the fly, etc.

$app = new Silex\Application();
// $app['debug'] = true;

// ------------------
// Errors display
// ------------------
use Symfony\Component\HttpFoundation\Response;
$app->error(function (\Exception $e, $code) use ($app) {
	echo $e->getMessage();
	if (404 == $code) {
		return $app['twig']->render('error404.html.twig', array(
			'code'    => $code,
		));
	} else {
		return $app['twig']->render('error.html.twig', array(
			'code'    => $code,
			'message' => $e->getMessage(),
		));
	}
});

// ------------------
// Services
// ------------------
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => ROOT_DIR.'/app/views',
));

// translation service
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
use Symfony\Component\Translation\Loader\YamlFileLoader;
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', ROOT_DIR.'/app/locales/en.yml', 'en');
    // $translator->addResource('yaml', ROOT_APP.'/locales/fr.yml', 'fr');

    return $translator;
}));

// ------------------
// routes and Dispatch
// ------------------

// mca route / default route
$app->get('/{controller}/{action}', function($controller, $action) use ($app) {
	$controller = str_replace('/', '', $controller);
	$action     = str_replace('/', '', $action);
	if (empty($controller)) { 
		return $app->redirect( $app['request']->getUri()."home/index" );
	} else if (empty($action)) {
		return $app->redirect( $app['request']->getUri()."{$controller}/index" );
	}

	$content = '...';
	
	$ctrlnamesp = 'App\Controller\\'.ucfirst($controller);
	if (!class_exists($ctrlnamesp,true)) {
		$app->abort(404, "{$controller}Controller not Found");
	}
	$ctrl = new $ctrlnamesp();
	if (!method_exists($ctrl, "{$action}Action")) {
		$app->abort(404, "{$controller}::{$action} not Found");
	}

	try {
		call_user_func_array(array($ctrl,"init"), array(
			$app
		));
		call_user_func_array(array($ctrl,"{$action}Action"), array(
			
		));
	} catch (Exception $e) {
		$app->abort(500, $e->getMessage());
	}

	return $app['twig']->render('skin.twig', $ctrl->vars() );
})
->assert('controller', '[a-zA-Z0-9]+/*')
->assert('action', '[a-zA-Z0-9]+/*')
->value('controller', null)
->value('action', null)
;

// static routes
$app->get('/css', function() use ($app) { /* ... */ })->bind('url_css');
$app->get('/js',  function() use ($app) { /* ... */ })->bind('url_js');
$app->get('/img', function() use ($app) { /* ... */ })->bind('url_img');

// baseurl
$app->get('/', function() use ($app) { /* ... */ })->bind('url_base');


// run
$app->run();
