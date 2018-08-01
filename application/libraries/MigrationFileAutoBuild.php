<?php if (!defined('BASEPATH')) {exit('No direct script access allowed');}

/**
 *
 */
class MigrationFileAutoBuild {
    public $CI;

    function __construct() {
        $this->CI = &get_instance();
    }

    public function build($db_set_dbprefix = '') {
        //
        $this->CI->load->model('MigrationFileAutoBuildModel');
        //
        $tables = $this->CI->MigrationFileAutoBuildModel->get_all_table();
        //
        foreach ($tables as $tableIndex => $table) {

            $table_name = str_replace($db_set_dbprefix, "", $table['TABLE_NAME']);

            // log_message('error', '## 第' . ($tableIndex + 1) . '張資料表: ' . $table['TABLE_NAME']);
            $columns = $this->CI->MigrationFileAutoBuildModel->get_all_columns($table['TABLE_NAME']);
            $table_prekey = '';
            $prekey = false;

            // 初始化檔案內容
            $thefile = fopen(BASEPATH . '../application/migrations/' . date('Ymd') . '0000' . sprintf("%02d", $tableIndex) . '_add_' . $table_name . ".php", "w");
            $file_cont = '';

            echo '#### ' . date('Ymd') . '00000' . $tableIndex . '_add_' . $table_name . '<br>';
            $file_cont .= '<?php' . PHP_EOL;
            $file_cont .= 'defined("BASEPATH") OR exit("No direct script access allowed");' . PHP_EOL;
            $file_cont .= '// Migration 應用語法自動化產生 - ' . date('Y-m-d H:i:s') . ' Power By Jake@jbravo.com.tw' . PHP_EOL;
            $file_cont .= (!empty($table['TABLE_COMMENT'])) ? '// ' . $table['TABLE_COMMENT'] . PHP_EOL : '';
            $file_cont .= 'class Migration_Add_' . $table_name . ' extends CI_Migration {' . PHP_EOL;
            $file_cont .= 'public function up() {';
            $file_cont .= "// 設定資料表欄位" . PHP_EOL;
            $file_cont .= '$this->dbforge->add_field(array(' . PHP_EOL;
            foreach ($columns as $columnIndex => $column) {
                // log_message('error', '## => 第' . ($columnIndex + 1) . '個欄位: ' . json_encode($column));

                $php_str = '';
                // 組合字串
                $php_str .= "'" . $column['COLUMN_NAME'] . "' => array(";
                $php_str .= "'type' => '" . $column['DATA_TYPE'] . "',";
                if ($column['DATA_TYPE'] != 'text' and !empty($column['CHARACTER_MAXIMUM_LENGTH'])) {
                    $php_str .= "'CONSTRAINT' => '" . $column['CHARACTER_MAXIMUM_LENGTH'] . "',";
                }
                $php_str .= "'comment' => '" . $column['COLUMN_COMMENT'] . "',";
                if ($column['EXTRA'] == 'auto_increment') {
                    $php_str .= "'auto_increment' => TRUE,";
                }
                if (((string) $column['COLUMN_DEFAULT']) != '') {
                    $php_str .= "'default' => '" . $column['COLUMN_DEFAULT'] . "',";
                }
                $php_str .= "'null' => false,";
                $php_str .= '),';
                // 完成內容
                $file_cont .= $php_str . PHP_EOL;

                // 偵測是否有鍵值
                if ($column['COLUMN_KEY'] == 'PRI') {
                    if ($prekey) {
                        $table_prekey .= '$this->dbforge->add_key("' . $column['COLUMN_NAME'] . '");' . PHP_EOL;
                    } else {
                        $prekey = true;
                        $table_prekey .= '$this->dbforge->add_key("' . $column['COLUMN_NAME'] . '",TRUE);' . PHP_EOL;
                    }
                }
            }
            $file_cont .= "));" . PHP_EOL;

            $file_cont .= "// 設定資料表key值" . PHP_EOL;
            $file_cont .= $table_prekey;
            $file_cont .= "// 建立資料表" . PHP_EOL;
            $file_cont .= '$this->dbforge->create_table("' . $table_name . '");' . PHP_EOL;

            $file_cont .= "// 建立預設內容 (自動重新建立資料表時可自訂預設內容)" . PHP_EOL;
            $file_cont .= '####################################################' . PHP_EOL;
            $file_cont .= '##' . PHP_EOL;
            $file_cont .= '##' . PHP_EOL;
            $file_cont .= '##' . PHP_EOL;
            $file_cont .= "}" . PHP_EOL;
            $file_cont .= 'public function down() {' . PHP_EOL;
            $file_cont .= "// 移除資料表" . PHP_EOL;
            $file_cont .= '$this->dbforge->drop_table("' . $table_name . '");' . PHP_EOL;
            $file_cont .= "}" . PHP_EOL;
            $file_cont .= "}" . PHP_EOL;

            fwrite($thefile, $file_cont);
            fclose($thefile);
        }

    }

}
