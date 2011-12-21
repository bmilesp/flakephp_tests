<?php

class FlakephpTestsController extends FlakephpTestsAppController{
	
	public $uses = array();
	
	function beforeFilter(){
		parent::beforeFilter();
		
		$this->data['LoadCakeFixtures'] = array(
			'app.match', 'app.played_hand', 'app.user', 'app.played_letter', 
			'app.letter', 'app.letter_set', 'app.available_letter', 
			'app.map', 'app.mapped_tile', 'app.matches_user', 'app.matchstatus' 
		);
		
		if (!empty($this->data['LoadCakeFixtures'])){
			$this->fixtures = $this->data['LoadCakeFixtures'];
		}
		$this->loadCakeTestData();
	}
	
	function index(){
		$this->autoRender = false;
		$returnData = array();
		if(!empty($this->_fixtures)){
			foreach($this->_fixtures as $fixture){
				$returnData[$fixture->table] = ClassRegistry::init($fixture->name)->find('all',array('recursive' => -1));
			}
		}
		Configure::write('debug', 0);
		echo json_encode($returnData);
	}
	
	function afterFilter(){
		$this->deleteCakeTestData();
	}
	
	
}
