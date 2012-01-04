<?php
require_once CAKE_TESTS_LIB . 'cake_test_fixture.php';
class FlakephpTestsAppController extends AppController{

	var $components = array('FlakephpTests.FlakephpTests');

	function beforeFilter(){
		$this->FlakephpTests->killAuthForTests();
	}
	
	
	/**
	 * data string CakePHP format for a test url eg: model.post
	 */
	protected function loadCakeTestData(){
		$this->_initDb();
		$this->_loadFixtures();
		
		if (isset($this->_fixtures) && isset($this->db)) {
			Configure::write('Cache.disable', true);
			$cacheSources = $this->db->cacheSources;
			$this->db->cacheSources = false;
			$sources = $this->db->listSources();
			$this->db->cacheSources = $cacheSources;

			//if (!$this->dropTables) {
			//	return;
			//}
			foreach ($this->_fixtures as $fixture) {
				$table = $this->db->config['prefix'] . $fixture->table;
				if (in_array($table, $sources)) {
					$fixture->drop($this->db);
					$fixture->create($this->db);
					$fixture->insert($this->db);
				} elseif (!in_array($table, $sources)) {
					//removes fields in models that have primaryKey = false
					foreach($fixture->fields as $key=>$field){
						if(is_numeric($key)){
							unset($fixture->fields[$key]);
						}
					} 
					$fixture->create($this->db);
					$fixture->insert($this->db);
				}
			}
		}
	}
	
	protected function deleteCakeTestData(){
		if (isset($this->_fixtures) && isset($this->db)) {
			//if ($this->dropTables) {
				foreach (array_reverse($this->_fixtures) as $fixture) {
					$fixture->drop($this->db);
				}
			//}
			$this->db->sources(true);
			Configure::write('Cache.disable', false);
		}

		if (class_exists('ClassRegistry')) {
			ClassRegistry::flush();
		}
	}
	
	function _loadFixtures() {
		if (!isset($this->fixtures) || empty($this->fixtures)) {
			return;
		}

		if (!is_array($this->fixtures)) {
			$this->fixtures = array_map('trim', explode(',', $this->fixtures));
		}

		$this->_fixtures = array();

		foreach ($this->fixtures as $index => $fixture) {
			$fixtureFile = null;

			if (strpos($fixture, 'core.') === 0) {
				$fixture = substr($fixture, strlen('core.'));
				foreach (App::core('cake') as $key => $path) {
					$fixturePaths[] = $path . 'tests' . DS . 'fixtures';
				}
			} elseif (strpos($fixture, 'app.') === 0) {
				$fixture = substr($fixture, strlen('app.'));
				$fixturePaths = array(
					TESTS . 'fixtures',
					VENDORS . 'tests' . DS . 'fixtures'
				);
			} elseif (strpos($fixture, 'plugin.') === 0) {
				$parts = explode('.', $fixture, 3);
				$pluginName = $parts[1];
				$fixture = $parts[2];
				$fixturePaths = array(
					App::pluginPath($pluginName) . 'tests' . DS . 'fixtures',
					TESTS . 'fixtures',
					VENDORS . 'tests' . DS . 'fixtures'
				);
			} else {
				$fixturePaths = array(
					TESTS . 'fixtures',
					VENDORS . 'tests' . DS . 'fixtures',
					TEST_CAKE_CORE_INCLUDE_PATH . DS . 'cake' . DS . 'tests' . DS . 'fixtures'
				);
			}

			foreach ($fixturePaths as $path) {
				if (is_readable($path . DS . $fixture . '_fixture.php')) {
					$fixtureFile = $path . DS . $fixture . '_fixture.php';
					break;
				}
			}

			if (isset($fixtureFile)) {
				require_once($fixtureFile);
				$fixtureClass = Inflector::camelize($fixture) . 'Fixture';
				$this->_fixtures[$this->fixtures[$index]] =& new $fixtureClass($this->db);
				$this->_fixtureClassMap[Inflector::camelize($fixture)] = $this->fixtures[$index];
			}
		}

		if (empty($this->_fixtures)) {
			unset($this->_fixtures);
		}
	}
	
	function _initDb() {
		App::import('Core', 'ConnectionManager');
		$testDbAvailable = in_array('test', array_keys(ConnectionManager::enumConnectionObjects()));

		$_prefix = null;

		if ($testDbAvailable) {
			// Try for test DB
			restore_error_handler();
			@$db =& ConnectionManager::getDataSource('test');
			//set_error_handler('simpleTestErrorHandler');
			$testDbAvailable = $db->isConnected();
		}

		// Try for default DB
		if (!$testDbAvailable) {
			$db =& ConnectionManager::getDataSource('default');
			$_prefix = $db->config['prefix'];
			$db->config['prefix'] = 'test_suite_';
		}

		ConnectionManager::create('test_suite', $db->config);
		$db->config['prefix'] = $_prefix;

		// Get db connection
		$this->db =& ConnectionManager::getDataSource('test_suite');
		$this->db->cacheSources  = false;

		ClassRegistry::config(array('ds' => 'test_suite'));
	}
	
}
?>