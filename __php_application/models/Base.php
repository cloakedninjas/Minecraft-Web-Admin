<?php

class Application_Model_Base {

	/**
	 *
	 * @var Zend_Db_Adapter_Pdo_Sqlite
	 */
	protected $_db;

	protected $_schema;

	/**
	 * True if the object was populated using load()
	 * @var boolean
	 */
	protected $_loaded;

	public function __construct($id=null) {
		if ($id !== null) {
			$this->load($id);
		}
	}

	protected function load($id=null) {
		if ($id !== null) {
			$this->id = intval($id);
		}

		if (!isset($this->id)) {
			throw new Exception('Cannot load ' . get_class($this) . '. Missing an ID');
		}

		if ($this->_db === null) {
			$this->defineDb();
		}

		$obj = $this->_db->fetchRow('SELECT * FROM ' . $this->_table_name . ' WHERE id = ?', $this->id, Zend_Db::FETCH_OBJ);

		if ($obj !== false) {
			foreach (get_object_vars($obj) as $key => $value) {
				$this->$key = $value;
			}

			$this->_loaded = true;
		}
	}

	protected function save() {
		if ($this->_db === null) {
			$this->defineDb();
		}

		$data = array();

		foreach ($this->_schema as $field => $def) {
			if ($field == 'id') {
				continue;
			}
			$data[$field] = $this->$field;
		}

		if ($this->_loaded) {
			$this->_db->update($this->_table_name, $data, 'id = ' . $this->id);
		}
		else {
			$this->_db->insert($this->_table_name, $data);
			$this->id = $this->_db->lastInsertId();
		}

	}

	protected function defineDb() {
		$this->_db = Zend_Registry::get('db');
		$this->_schema = $this->_db->describeTable($this->_table_name);
	}


}

