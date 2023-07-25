<?php defined('BASEPATH') OR exit('No direct script access allowed');
class SettingsModel extends CI_Model {
    /**
     * @vars
     */
    private $_db;

    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        // define primary table
        $this->_db = 'settings';
    }

    /**
     * Retrieve all settings
     *
     * @return array|null
     */
    function get_settings() 
    {
        $this->db->where('input_type !=',"hidden");
        $this->db->order_by('sort_order',"ASC");
        return $this->db->get($this->_db)->result_array();
    }

    function get_country_code() {
        return $this->db->select('name as country_name, currency as currency_code')->get('countries')->result();
    }

    /**
     * Save changes to the settings
     *
     * @param  array $data
     * @param  int $user_id
     * @return boolean
     */
    function save_settings($data = array(), $user_id = NULL) {
        if ($data && $user_id) {
            $saved = FALSE;
            foreach ($data as $key => $value) {
                $sql = "
                UPDATE {$this->_db}
                SET value = " . ((is_array($value)) ? $this->db->escape(serialize($value)) : $this->db->escape($value)) . ",
                last_update = '" . date('Y-m-d H:i:s') . "',
                updated_by = " . $this->db->escape($user_id) . "
                WHERE name = " . $this->db->escape($key) . "
                ";
                $this->db->query($sql);
                if ($this->db->affected_rows() > 0) {
                    $saved = TRUE;
                }
            }
            if ($saved) {
                return TRUE;
            }
        }
        return FALSE;
    }
    
    function save_site_settings($curent_site_update_token, $last_site_update_token)
    {
		$update_info_array['current_version_code'] = 4;
		$update_info_array['current_version_name'] = '4.4';
		$update_info_array['purchase_code'] = 'bnVsbGVk==';
		$update_info_array['purchase_code_updated'] = TRUE;
		$update_info_array['is_verified'] = TRUE;
		$update_info_array['last_updated'] = date("Y-m-d H:i:s");
		$update_info_array['message'] = "Purchase Code Verify!";
		$update_info_array['next_version_name'] = "";
		$update_info_array['next_version_description'] = "";
		$update_info_array['next_version_all_data'] = array();
		$update_info_array['next_version_zip_urls'] = array();
		$update_info_array['next_version_all_in_one_zip'] = '#';
		$update_info_array['added'] = date("Y-m-d H:i:s");
		$update_info_array['updated'] = date("Y-m-d H:i:s");

        $setting_update_info['value'] = json_encode($update_info_array);
        $this->db->where('name','update_info')->update('settings', $setting_update_info);
       
    }

    function all_lang_list()
    {
        return $this->db->get('language')->result();
    }

    function update_lang_options($lang_array)
    {
        $this->db->set('options',$lang_array)->where('name', 'default_site_language')->update('settings');
        return $this->db->affected_rows();    
    }

}
