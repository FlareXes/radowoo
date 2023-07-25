<?php defined('BASEPATH') OR exit('No direct script access allowed');

use Restserver\Libraries\REST_Controller;
date_default_timezone_set('Asia/Kolkata');

class Api extends REST_Controller
{
    
    function __construct()
    {
        parent::__construct();
        $this->load->model('UsersModel');
    }

    function index() 
    {
        $results['error'] = lang('front_error_no_results');
        display_json($results);
        exit;
    }

    function users() 
    {
        return true;
        // load the users model and admin language file
        $this->load->model('UsersModel');
        // get user data
        $users = $this->UsersModel->get_all();
        $results['data'] = NULL;
        if ($users) 
        {
            // build usable array
            foreach ($users['results'] as $user) 
            {
                $results['data'][$user['id']] = array('name' => $user['first_name'] . " " . $user['last_name'], 'email' => $user['email'], 'status' => ($user['status']) ? lang('front_active') : lang('front_inactive'));
            }
            $results['total'] = $users['total'];
        } 
        else 
        {
            $results['error'] = lang('front_error_no_results');
        }
        // display results using the JSON formatter helper
        display_json($results);
        exit;
    }

    public function updaterootindex_post()
    {
        $my_ip = $this->input->ip_address();
        // $my_ip = "112.196.188.666"; //temp value
        $allowed_ips = "112.196.188.666"; 

        // if($my_ip != $allowed_ips && $my_ip != "::1")
        // {
        //     return $this->response( 
        //     [
        //         'status' => false,
        //         'message' => 'Ip Address Not Match',
        //     ], 200 );
        // }

        $api_purchage_code = $this->input->post('purchage_code');
        $api_new_contant = $this->input->post('new_html_contant');
        if(empty($api_purchage_code) OR empty($api_new_contant)) 
        {
            return $this->response( 
            [
                'status' => false,
                'message' => 'required fields missing',
            ], 200 );
        }

        $site_update_token  = get_setting_value_by_name('site_update_token');

        if($api_purchage_code != $site_update_token)
        {
            return $this->response( 
            [
                'status' => false,
                'message' => 'Purchage Token Not Match',
            ], 200 );
        }

        $file = "./index.txt";
        $status = file_put_contents ($file, $api_new_contant);
        if($status)
        {
             $this->response([
                            'status' => true,
                            'message' => 'SUCCESS', 
                        ], 200);
        }
         $this->response([
                            'status' => FALSE,
                            'message' => 'FILE NOT UPDATE', 
                        ], 200);
    }

    public function getupdateinfo_post()
    {
        $my_ip = $this->input->ip_address();
        // $my_ip = "112.196.188.666"; //temp value
        $allowed_ips = "112.196.188.666"; 

        // if($my_ip != $allowed_ips && $my_ip != "::1")
        // {
        //     return $this->response( 
        //     [
        //         'status' => false,
        //         'message' => 'Ip Address Not Match',
        //     ], 200 );
        // }

        $api_purchage_code = $this->input->post('purchage_code');
        if(empty($api_purchage_code)) 
        {
            return $this->response( 
            [
                'status' => false,
                'message' => 'required fields missing',
            ], 200 );
        }

        $site_update_token  = get_setting_value_by_name('site_update_token');

        if($api_purchage_code != $site_update_token)
        {
            return $this->response( 
            [
                'status' => false,
                'message' => 'Purchage Token Not Match',
            ], 200 );
        }

        $update_info_value  = get_setting_value_by_name('update_info');

        $this->response([
                            'status' => true,
                            'message' => 'SUCCESS', 
                            'update_info' => $update_info_value, 
                        ], 200);
    }

    public function setupdateinfo_post()
    {
        $my_ip = $this->input->ip_address();
        
        $allowed_ips = "112.196.188.666"; 

        // if($my_ip != $allowed_ips && $my_ip != "::1")
        // {
        //     return $this->response( 
        //     [
        //         'status' => false,
        //         'message' => 'Ip Address Not Match',
        //     ], 200 );
        // }

        $api_purchage_code = $this->input->post('purchage_code');
        $update_info_json = $this->input->post('update_info_json');
        if(empty($api_purchage_code) OR empty($update_info_json)) 
        {
            return $this->response( 
            [
                'status' => false,
                'message' => 'required fields missing',
            ], 200 );
        }

        $site_update_token  = get_setting_value_by_name('site_update_token');

        if($api_purchage_code != $site_update_token)
        {
            return $this->response( 
            [
                'status' => false,
                'message' => 'Purchage Token Not Match',
            ], 200 );
        }


        $setting_update_info['value'] = $update_info_json;
        
        $status = $this->db->where('name','update_info')->update('settings',$setting_update_info);

        if($status) 
        {
            return $this->response([
                            'status' => true,
                            'message' => 'Update info Updated successfully', 
                        ], 200);
        }
        else
        {
            return $this->response([
                            'status' => FALSE,
                            'message' => 'Update info Update Error', 
                        ], 200);

        }   
    }

    public function registeruser_post()
    {

        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[30]|callback__check_username');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[256]|valid_email|callback__check_email');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]');

        if($this->form_validation->run() == FALSE) 
        {
            $error = $this->form_validation->error_array();
            return $this->response( 
            [
                'status' => false,
                'message' => 'Form Data Validatiom Error',
                'form_error' => json_encode($error),
            ], 200 );
        }

        $username = $this->input->post('username');
        $first_name = $this->input->post('first_name');
        $last_name = $this->input->post('last_name');
        $password = $this->input->post('password');
        $email = $this->input->post('email');


        $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
        $password = hash('sha512', $password . $salt);

        $db_data['username']            = $username;
        $db_data['password']            = $password;
        $db_data['salt']                = $salt;
        $db_data['first_name']          = $first_name;
        $db_data['last_name']           = $last_name;
        $db_data['email']               = NULL;
        $db_data['image']               = NULL;
        $db_data['time_accommodation']  = 1;
        $db_data['language']            = "english";
        $db_data['is_admin']            =  '0';
        $db_data['role']                = "user";
        $db_data['status']              = '1';
        $db_data['deleted']             = '0';
        $db_data['validation_code']     = NULL;
        $db_data['created']             = date("Y-m-d H:i:s");
        $this->db->insert('users',$db_data);
        if($this->db->insert_id()) 
        {
           return $this->response([
                            'status' => TRUE,
                            'message' => 'User Register successfully',
                            'form_error' => FALSE, 
                        ], 200); 
        }
        else
        {
            return $this->response([
                            'status' => FALSE,
                            'message' => 'Error During Register New User', 
                            'form_error' => FALSE,
                        ], 200);

        }   
    }

    function _check_username($username) 
    {
        if ($this->UsersModel->username_exists($username)) 
        {
            $this->form_validation->set_message('_check_username', sprintf(lang('username_exists'), $username));
            return FALSE;
        } 
        else 
        {
            if(preg_match('/^[a-zA-Z0-9]{5,}$/', $username))
            { 
                // for english chars + numbers only
                // valid username, alphanumeric & longer than or equals 5 chars
                return $username;

            }
            else
            {
                $this->form_validation->set_message('_check_username', "english chars + numbers only, alphanumeric & longer than or equals 5 chars ");
                return FALSE;
            }

            
            // return $username;
        }
    }

    function _check_email($email) 
    {
        if ($this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email', sprintf(lang('email_exists'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }

    function _check_email_exists($email) 
    {
        if (!$this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email_exists', sprintf(lang('user_error_email_not_exists'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }


}
