<?php
/**
 * Takes Security.salt hash token and flag in GET params, then if conditions are met, switches any model(s) (that extend this class) 
 * database to the test database. Useful for testing with mobile apps that rely on a central server for testing. Debug
 * must be set to debugActivationLevel Config variable in bootsterap or defaults to level 2. see config/bootstrap.php
 * for details
 * 
 * Note: For Global use, simply extend the app/AppModel to this class.
 * 
 * usage: pass in value in app/core.php security.salt and a GET param : 'useTestDb=1' to trigger use of test db
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * PHP versions 4 and 5
 *
 * created by: Brandon Plasters aka bmilesp
 * 
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       default
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 *
 */

abstract class TestDbSwitcherModel extends Model {
		
	function __construct($id = false, $table = null, $ds = null) {
		$debugActivationLevel = Configure::read('FlakephpTests.debugActivationLevel');
		$debugActivationLevel = (!empty($debugActivationLevel))? $debugActivationLevel : 2;
		$debug = Configure::read('debug');
		if($debug == $debugActivationLevel){
			$securitySalt = Configure::read('Security.salt');
			if(!empty($_GET['useTestDb']) 
				&& !empty($_GET['securitySalt'])
				&& $_GET['securitySalt'] == $securitySalt)
			{
				$this->useDbConfig = 'test';
			}
		}
		parent::__construct($id, $table, $ds);
		
	}
		
}
	