<?php

define('ROOT_APP', __DIR__ );
define('ROOT_VENDOR', __DIR__.'/../vendor');

require_once ROOT_VENDOR.'/autoload.php';

$app = new Silex\Application();

// mca route / default route
$app->get('/{module}/{controller}/{action}', function($module, $controller, $action) use ($app) {
	$output = <<<EOB
module: {$module}
controller: {$controller}
action: {$action}
EOB;

	// return nl2br($output);
	return $app['twig']->render('skin.twig', array(
        'content' => $output,
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

// Errors
use Symfony\Component\HttpFoundation\Response;
$app->error(function (\Exception $e, $code) use ($app) {
	// switch ($code) {
        // case 404:
            // $message = 'The requested page could not be found.';
			// break;
        // default:
            // $message = 'We are sorry, but something went terribly wrong.';
    // }
	return $app['twig']->render('error.twig', array(
		'code'      => $code,
		// 'exception' => $e,
	));
});

// Services
$app->register(new Silex\Provider\UrlGeneratorServiceProvider());
$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => ROOT_APP.'/views',
));
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
    'locale_fallbacks' => array('en'),
));
use Symfony\Component\Translation\Loader\YamlFileLoader;
$app['translator'] = $app->share($app->extend('translator', function($translator, $app) {
    $translator->addLoader('yaml', new YamlFileLoader());

    $translator->addResource('yaml', ROOT_APP.'/locales/en.yml', 'en');
    // $translator->addResource('yaml', ROOT_APP.'/locales/de.yml', 'de');
    // $translator->addResource('yaml', ROOT_APP.'/locales/fr.yml', 'fr');

    return $translator;
}));


// run
$app->run();