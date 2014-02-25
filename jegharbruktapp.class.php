<?php

class mMenu {
	public $top;
	public $submenu;
	public $base = '/JegHarBruktGUI';
	
	public function __construct() {}
	
	public function register($link, $title, $order=100) {
		$this->top[ $order ] = (object) array('link' => $link,
											   'title' => $title,
											   'active' => false
											  );
	}
	
	public function active( $link ) {
		foreach( $this->top as $order => $menu ) {
			if( $menu->link == $link ) {
				$menu->active = true;
				$this->top[ $order ] = $menu;
				break;
			}
		}
	}
}

class JHBapp {
	public $title = 'JegHarBrukt.no';
	private $tested_login = false;
	private $is_logged_in = false;
	public $menu;
	
	public function __construct() {
		$this->assets = new stdClass();
		$this->assets->css = '/JegHarBruktGUI/web/css/';
		$this->assets->img = '/JegHarBruktGUI/web/img/';
		$this->assets->js = '/JegHarBruktGUI/web/js/';
		
		$this->menu = new mMenu();
		
		$this->api = new stdClass();
		$this->api->key = 'test';
		$this->api->secret = 'test';
		
		$this->unit = 1;
		
		$this->_api_init();
	}

	public function is_logged_in() {
		if( ! $this->tested_login ) {
			$this->tested_login = true;

			global $app;
			if( $app['session']->get('JHB_u_id') !== '' && '' != $app['session']->get('JHB_hash') ) {
				if( $this->_restore_session() ) {
					$this->is_logged_in = true;
					$this->_api_authorize();
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
		$this->user = new user();

		$authentication = $this->user->login( $username, $password );
		global $app;
		$app['session']->set('JHB_u_id', $this->user->ID);
		$app['session']->set('JHB_hash', $authentication);
		$this->_api_authorize();
	}
	
	public function logout() {
		global $app;
		$app['session']->set('JHB_u_id', false);
		$app['session']->set('JHB_hash', false);
		$app['session']->remove('JHB_u_id');
		$app['session']->remove('JHB_hash');
	}
	
	public function public_request( $action, $data ) {
		if(!isset($this->JHBapi)) {
			$this->_api_initiate();
			$this->JHBapi->register_modules();
		}
		return $this->JHBapi->PUBLIC_GET( $action, $data );
	}
		
	public function request( $method, $action, $data ) {
		$this->request = new stdClass();
		$this->request->method = $method;
		$this->request->object = $action;
		$this->request->data = $data;
		
		return $this->JHBapi->{$this->request->method}( $this->request->object, $this->request->data );
	}
	
	private function _api_init() {
		$this->_api_initiate();
		$this->_api_authenticate();
#		$this->_api_authorize();
		$this->JHBapi->register_modules();
	}
	
	private function _api_initiate() {
		// JHB API
		require_once('config.php');
		require_once(API_PATH.'vendor/autoload.php');
		// 
		require_once(API_PATH.'unit.class.php');
		
		// CREATE MAIN APP INSTANCE AND AUTHENTICATE
		require_once(API_PATH.'jegharbrukt.class.php');
		$this->JHBapi = new JHB();
	}
	
	private function _api_authenticate() {
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
			$this->JHBapi->authorize( $USER, true );
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
	
	private function _restore_session() {
		global $app;
		$this->user = new user();
		try {
			$result = $this->user->restore_session( $app['session']->get('JHB_u_id'), $app['session']->get('JHB_hash') );
			return $result;
		} catch( Exception $e ) {
			return false;
		}
	}
}