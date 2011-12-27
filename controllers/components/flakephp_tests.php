<?php

class FlakephpTestsComponent extends Object{
	
	function initialize(&$controller, $settings = array()) {        
		// saving the controller reference for later use        
		$this->controller =& $controller;   
	}
	
	function killAuthForTests(){
		$debug = Configure::read('debug');
		$authAllow = Configure::read('FlakephpTests.authAllow');
		$authAllow = ($authAllow === NULL || $authAllow === true)? true : false;

		if(!empty($this->controller->Auth) && $authAllow == true){
			
			$this->controller->Auth->allow('*');
		}
		
		$redirectUrl = Configure::read('FlakephpTests.redirectUrl');
		if($debug < 1){
			$redirectUrl = (!empty($redirectUrl))? $redirectUrl : '/';
			$this->controller->redirect('/');
		}
	}
}
