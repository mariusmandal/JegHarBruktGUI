<?php
$app->get('/ajax/{action}/{id}/', function($action, $id) use($JHBapp, $app) {
	
	return json_encode( $JHBapp->public_request( $action, $id ) );
});
