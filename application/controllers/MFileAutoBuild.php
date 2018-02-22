<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MFileAutoBuild extends CI_Controller {

	public function __construct() {
		parent::__construct();
		$this->load->helper("Common_Helper", "", false);

	}

	public function index() {
		//
		$this->load->model('MigrationFileAutoBuildModel');
		//
		$db_set_dbprefix = $this->MigrationFileAutoBuildModel->get_database_set_dbprefix();
		//
		$this->load->library('MigrationFileAutoBuild');
		//
		$this->migrationfileautobuild->build($db_set_dbprefix);
	}

}