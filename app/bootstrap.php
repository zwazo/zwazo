<?php

define('ROOT_APP', __DIR__ );
define('ROOT_VENDOR', __DIR__.'/../vendor');

require_once ROOT_VENDOR.'/autoload.php';

$app = new Silex\Application();

// ------------------
// Services
// ------------------
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => ROOT_APP.'/views',
));

// translation service
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
use Symfony\Component\Translation\Loader\YamlFileLoader;
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', ROOT_APP.'/locales/en.yml', 'en');
    // $translator->addResource('yaml', ROOT_APP.'/locales/fr.yml', 'fr');

    return $translator;
}));

// ------------------
// Routes
// ------------------

// mca route / default route
$app->get('/{controller}/{action}', function($controller, $action) use ($app) {
	$controller = str_replace('/', '', $controller);
	$action     = str_replace('/', '', $action);
	if (empty($controller)) { $controller = 'home'; }
	if (empty($action)) { $action = 'index'; }

	if (file_exists( ROOT_APP.'/controllers/'.ucfirst($controller).'Controller.php' )) {
		include_once ROOT_APP.'/controllers/'.ucfirst($controller).'Controller.php';
		if (class_exists('Zwazo\Ctrl\Home')) {
			$ctrl = new Zwazo\Ctrl\Home($app);
		}
	}

	if (isset($ctrl) && method_exists($ctrl,"{$action}Action")) {
		$content = call_user_func_array (array($ctrl,"{$action}Action"),array(
			
		));
	} else {
		$app->abort(404, 'Page Not Found');
	}

	return $app['twig']->render('skin.twig', array(
        'content' => $content,
    ));
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

// ------------------
// Errors display
// ------------------
use Symfony\Component\HttpFoundation\Response;
$app->error(function (\Exception $e, $code) use ($app) {
	
	if (404 == $code) {
		return $app['twig']->render('error404.twig', array(
			'code'    => $code,
		));
	} else 
		return $app['twig']->render('error.twig', array(
			'code'    => $code,
		));
});

// run
$app->run();