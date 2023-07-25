<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Update_model extends CI_Model {

	function save_update_info($curent_site_update_token, $last_site_update_token)
    {
        $update_info_obj = $this->db->where('name','update_info')->get('settings')->row();

        $update_info_json = (isset($update_info_obj->value) && $update_info_obj->value) ? $update_info_obj->value : json_encode(array());
        $update_info_array = json_decode($update_info_json);
        $update_info_array = json_decode(json_encode($update_info_array), true);
        
        if($update_info_array && is_array($update_info_array))
        {
            if($curent_site_update_token != $last_site_update_token)
            {
                $update_info_array['purchase_code_updated'] = TRUE;
                $update_info_array['purchase_code'] = $curent_site_update_token;
                $update_info_array['updated'] = date("Y-m-d H:i:s");
            }
        }
        else
        {
            $update_info_array['current_version_code'] = 4;
            $update_info_array['current_version_name'] = '4.4';
            $update_info_array['purchase_code'] = $curent_site_update_token;
            $update_info_array['purchase_code_updated'] = TRUE;
            $update_info_array['is_verified'] = FALSE;
            $update_info_array['last_updated'] = date("Y-m-d H:i:s");
            $update_info_array['message'] = "Purchase Code Not Verify!";
            $update_info_array['next_version_name'] = "";
            $update_info_array['next_version_description'] = "";
            $update_info_array['next_version_all_data'] = array();
            $update_info_array['next_version_zip_urls'] = array();
            $update_info_array['next_version_all_in_one_zip'] = '#';
            $update_info_array['added'] = date("Y-m-d H:i:s");
            $update_info_array['updated'] = date("Y-m-d H:i:s");
        }

        $setting_update_info['value'] = json_encode($update_info_array);
        
        $this->db->where('name','update_info')->update('settings', $setting_update_info);
       
    }

}