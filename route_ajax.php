<?php
$app->get('/ajax/{action}/{id}/', function($action, $id) use($JHBapp, $app) {
	
	return json_encode( $JHBapp->request('GET', $action, $id ) );
});
