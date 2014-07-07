<?php

date_default_timezone_set('Europe/Paris');

error_reporting(E_ALL);
ini_set('display_errors',1);

define('COOKIE_LIFETIME', (60*10)); // 10min

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

$app->register(new Silex\Provider\SessionServiceProvider(null, array(
	'cookie_lifetime' => COOKIE_LIFETIME,
)));

use Silex\Provider\FormServiceProvider;
$app->register(new FormServiceProvider());

// ------------------
// routes and Dispatch
// ------------------
// echo 'baseUrl:'.$app['request']->getBaseUrl();
// echo 'Uri:'.$app['request']->getUri();
// echo 'reqUri:'.$app['request']->getRequestUri();

$app->get('/logout', function() use ($app) {
	$app['session']->invalidate();
	$app['session']->set('isAuthenticated', false);
	return $app->redirect( $app['request']->getBaseUrl() . '/news' );
});

// mca route / default route
use App\Helper;
$app->match('/{controller}/{action}/{ref}', function($controller, $action, $ref) use ($app) {
	$status_code = 200;
	$controller  = str_replace('/', '', $controller);
	$action      = str_replace('/', '', $action);
	if (empty($controller)) { $controller = 'home'; }
	if (empty($action)) { $action = 'index'; }

	$user      = $app['session']->get('user');
	$last_time = $app['session']->get('last_time');
	if (null !== $user) {
		$app['session']->migrate(false, COOKIE_LIFETIME);
	}

	$aEditrActs = array('edit','del','list');
	if ( in_array($controller,array('aa','editr')) 
	  OR in_array($action,$aEditrActs)
	  OR ('account' == $controller && 'login' != $action)
	) {
		$status_code = 401;
// echo 'user: '.$user.'<br/>';
		if (null !== $user) {
			if (Helper\Conf::SITE_ADMIN == $user) {
				$status_code = 200;
			} else {
				$status_code = 403;
			}
		}
		
		if ( 200 != $status_code ) {
			$controller = 'account';
			$action     = 'login';
		}
	}

	// controller::action detection
	$ctrlnamesp = 'App\\Controller\\'.ucfirst($controller);
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
		$ret = call_user_func(array($ctrl,"{$action}Action"));
		// if ('account' == $controller && 'login' == $action && !is_null($ret) ) {
		if ($ret) {
			return $ret;
		}
	} catch (Exception $e) {
		$app->abort(500, $e->getMessage());
	}

	$ctrl->vars('controller', $controller);
	$ctrl->vars('action', $action);
	$ctrl->vars('this_year', date('Y'));
	$ctrl->vars('user', array(
		'name' => $user,
		'role' => $app['session']->get('role','anonymous'),
	));
	
	$layout = $ctrl->vars('layout');
	if (empty($layout)) { 
		if (in_array($action,$aEditrActs)) {
			$layout = 'iframed.twig';
			$ctrl->stylesheet("editr/{$controller}.css");
			$ctrl->stylesheet('editor.css');
		} else {
			$layout = 'skin.twig';
		}
	}
	return $app['twig']->render($layout, $ctrl->vars());
})
->assert('controller', '[a-zA-Z0-9]+/*')
->assert('action', '[a-zA-Z0-9]+/*')
->value('controller', null)
->value('action', null)
->value('ref', null)
;

// static routes
$app->get('/css', function() use ($app) { /* ... */ })->bind('url_css');
$app->get('/js',  function() use ($app) { /* ... */ })->bind('url_js');
$app->get('/img', function() use ($app) { /* ... */ })->bind('url_img');

// baseurl
$app->get('/', function() use ($app) { /* ... */ })->bind('url_base');


// run
$app->run();
