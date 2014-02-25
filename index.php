<?php
#session_start();
require_once('vendor/autoload.php');
require_once('config.php');
require_once('jegharbruktapp.class.php');

global $app;
$app = new Silex\Application();
// REGISTER DEPENDENCIES
$app->register(new Silex\Provider\TwigServiceProvider(),
	array('twig.path' =>dirname(__FILE__).'/app/frontend',
		  #'twig.options' => array('cache' => APP_PATH. 'tmp/twig.cache')
		  ));
$app->register(new Silex\Provider\SessionServiceProvider());

$app['debug'] = APP_DEBUG;


$JHBapp = new JHBapp();
$JHBapp->is_logged_in();

// REGISTER ERROR HANDLER
$app->error(function (\Exception $e, $code) use ($JHBapp, $app) {
	if ($app['debug'] && $code != 400) {
		return;
	}

	$JHBapp->message = $e->getMessage();
	return $app['twig']->render('error.twig.html', (array) $JHBapp );
});


// REGISTER ROUTING
require_once('route_gui.php');
require_once('route_actions.php');
require_once('route_ajax.php');

// RUN APP
$app->run();