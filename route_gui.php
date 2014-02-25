<?php
$app->get('/', function() use($JHBapp, $app) {
	if( $JHBapp->is_logged_in() ) {
		return $app['twig']->render('welcome.twig.html', (array) $JHBapp );
	} else {
		return $app['twig']->render('homepage.twig.html', (array) $JHBapp );
	}
});


$app->get('/login/', function() use($JHBapp, $app) {
	return $app['twig']->render('login.twig.html', (array) $JHBapp);
});