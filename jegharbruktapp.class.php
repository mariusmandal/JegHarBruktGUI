<?php

class JHBapp {
	public $title = 'JegHarBrukt.no';
	private $tested_login = false;
	private $is_logged_in = false;
	
	public function __construct() {
		$this->assets = new stdClass();
		$this->assets->css = '/JegHarBruktGUI/web/css/';
		$this->assets->img = '/JegHarBruktGUI/web/img/';
		$this->assets->js = '/JegHarBruktGUI/web/js/';
		
		
		$this->_authorize();
	}

	public function is_logged_in() {
		$this->_api_init();
		if( ! $this->tested_login ) {
			$this->tested_login = true;

			global $app;
			if( $app['session']->get('JHB_u_id') !== '' && '' != $app['session']->get('JHB_hash') ) {
				if( $this->_restore_session() ) {
					$this->is_logged_in = true;
					return true;
				}
			} else {
				// One of two could be set (wtf?), to be sure.
				$this->logout();
				return false;
			}
		}
		return $this->is_logged_in;
	}

	public function login( $username, $password ) {
		$this->_api_init();

		$this->current_user = new user();

		$authentication = $this->current_user->login( $username, $password );
		global $app;
		$app['session']->set('JHB_u_id', $this->current_user->ID);
		$app['session']->set('JHB_hash', $authentication);
	}
	
	public function logout() {
		global $app;
		$app['session']->set('JHB_u_id', false);
		$app['session']->set('JHB_hash', false);
		$app['session']->remove('JHB_u_id');
		$app['session']->remove('JHB_hash');
	}
	
	private function _restore_session() {
		global $app;
		$this->current_user = new user();
		try {
			return $this->current_user->restore_session( $app['session']->get('JHB_u_id'), $app['session']->get('JHB_hash') );
		} catch( Exception $e ) {
			return false;
		}
	}
	
	private function _authorize() {
		$this->api = new stdClass();
		$this->api->key = 'test';
		$this->api->secret = 'test';
		
		$this->unit = 1;
		
		$this->user = new stdClass();
		$this->user->ID = 1;
		$this->user->token = 'system';

	}
	
	public function request( $method, $action, $data ) {
		$this->_api_init();
		$this->request = new stdClass();
		$this->request->method = $method;
		$this->request->object = $action;
		$this->request->data = $data;
		
		return $this->JHBapi->{$this->request->method}( $this->request->object, $this->request->data );
	}
	
	private function _api_init() {
		require_once(API_PATH.'user.class.php');

		$this->_api_authenticate();
		$this->_api_authorize();
		$this->JHBapi->register_modules();
	}
	
	private function _api_authenticate() {
		/// JHB API
		require_once('config.php');
		require_once(API_PATH.'vendor/autoload.php');
		// 
		require_once(API_PATH.'unit.class.php');
		
		// CREATE MAIN APP INSTANCE AND AUTHENTICATE
		require_once(API_PATH.'jegharbrukt.class.php');
		$this->JHBapi = new JHB();
		try {
			$this->JHBapi->authenticate( $this->api->key, $this->api->secret );
		} catch( Exception $e ) {
			return $this->_api_die( $e );
		}
		
		try {
			$this->JHBapi->setUnit( $this->unit );
		} catch( Exception $e ) {
			return $this->_api_die( $e );
		}
	}
	
	private function _api_authorize() {
		try {
			$USER = new user( false, $this->user->ID );
		} catch( Exception $e ) {
			return $this->_api_die( $e );
		}
		
		try {
			$this->JHBapi->authorize( $USER, $this->user->token );
		} catch( Exception $e ) {
			return $this->_api_die( $e );
		}
	}
	
	
	private function _api_die( $e ) {
		$error = new stdClass();
		$error->status = 400;
		$error->developerMessage = 'API ERROR: '. $e->getMessage() . ' ('.$e->getCode().')' ;
		$error->errorCode = $e->getCode();
		if( $error->errorCode < 100 ) {
			$error->userMessage = 'Could not connect to API';
		} elseif( $error->errorCode > 100 ) {
			$error->userMessage = 'API did not retrieve mandatory data';
		}
		
		return $error;
	}
}