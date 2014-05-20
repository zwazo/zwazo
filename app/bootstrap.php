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
$app->get('/{module}/{controller}/{action}', function($module, $controller, $action) use ($app) {
	$content = <<<EOB
module: {$module}
controller: {$controller}
action: {$action}
EOB;

	return $app['twig']->render('skin.twig', array(
        'content' => $content,
    ));
})
->assert('module', '[a-zA-Z0-9]+/*')
->assert('controller', '[a-zA-Z0-9]+/*')
->assert('action', '[a-zA-Z0-9]+/*')
->value('module', null)
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