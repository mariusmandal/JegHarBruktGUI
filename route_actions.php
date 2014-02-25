<?php

$app->match('action/logout/', function() use( $JHBapp, $app) {
	$JHBapp->logout();
	return $app->redirect('/JegHarBruktGUI/');
});

$app->post('action/login/', function() use( $JHBapp, $app ) {
	try{
		$JHBapp->login( $_POST['username'], $_POST['password'] );
	} catch( Exception $e ) {
		$app['session']->getFlashBag()->add('message', $e->getMessage());
		return $app->redirect('/JegHarBruktGUI/login/');
	}
	return $app->redirect('/JegHarBruktGUI/');
});

/* USER SIGN UP */
$app->post('/action/signup/', function() use ($JHBapp, $app) {	
	try {
		$user = $JHBapp->request('POST','user',$_POST);
	} catch( Exception $e ) {
		switch( $e->getCode() ) {
			case 408:
				$message = 'Kan ikke opprette bruker med tomt brukernavn.';
				break;
			case 406:
				$message = 'Brukernavn opptatt. Kunne ikke opprette bruker.';
				break;
			case 404:
				$message = 'Kan ikke opprette bruker med tomt passord.';
				break;
			case 403:
				$message = 'Kan ikke opprette bruker uten gyldig e-postadresse.';
				break;
			default:
				$message = 'Kunne ikke opprette bruker.';
				break;
		}
		$app->abort(400, $message .( $app['debug'] ? ' </p><h4>Debug:</h4><p>'. $e->getMessage() : '.' ) );
	}
		
	if( 'user' != get_class( $user ) ) {
		$app->abort(400, 'Kunne ikke opprette bruker, ukjent feil oppsto');
	}
	
	try{
		$JHBapp->login( $_POST['username'],  $_POST['password'] );
	} catch( Exception $e ) {
		$app->abort(400, $e);
	}
	
	//die('Should redirect to frontpage');
	return $app->redirect('/JegHarBruktGUI/');
});
