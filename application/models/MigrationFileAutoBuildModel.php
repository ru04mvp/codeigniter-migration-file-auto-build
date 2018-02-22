<?php

class MigrationFileAutoBuildModel extends CI_Model {

	public function __construct() {
		parent::__construct();
		$this->db = $this->load->database(DB_NAME, TRUE);
	}

	// 取得所有資料表
	public function get_all_table() {
		$db_setting = json_decode(json_encode($this->db));
		$table_schema = $db_setting->database;

		$this->db->set_dbprefix('');
		$this->db->select('TABLE_NAME , TABLE_COMMENT');
		$this->db->from('information_schema.TABLES', true);
		$this->db->where('TABLE_SCHEMA', $table_schema);
		$result = $this->db->get()->result_array();

		return $result;
	}

	// 取得所有欄位
	public function get_all_columns($table_name = '') {

		$db_setting = json_decode(json_encode($this->db));
		$table_schema = $db_setting->database;

		$this->db->set_dbprefix('');
		$this->db->select('COLUMN_NAME,DATA_TYPE,CHARACTER_MAXIMUM_LENGTH,COLUMN_DEFAULT,COLUMN_COMMENT,COLUMN_KEY,EXTRA');
		$this->db->from('information_schema.COLUMNS');
		$this->db->where('table_name', $table_name);
		$this->db->where('TABLE_SCHEMA', $table_schema);
		$result = $this->db->get()->result_array();

		return $result;
	}

	//
	public function get_database_set_dbprefix() {
		$db_setting = json_decode(json_encode($this->db));
		return $db_setting->dbprefix;
	}
}