<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Admin_login_Controller extends Private_login_Controller 
{

    function __construct() 
    {
        parent::__construct();
        $this->load->model('UsersModel');
        $this->load->library('Googleplus');
        $this->add_external_js('https://www.google.com/recaptcha/api.js');
        // $this->add_js_theme('api.js');
    }

    
    function login() 
    {

        if ($this->session->userdata('logged_in')) 
        {   
            $logged_in_user = $this->session->userdata('logged_in');
            if ($logged_in_user['is_admin']) 
            {
                redirect('admin');
            } 
            else 
            {
                redirect(base_url());
            }
        }

        // set form validation rules
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', 'Username', 'required|trim|max_length[256]');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|max_length[72]');

        if ($this->settings->recaptcha_secret_key && $this->settings->recaptcha_site_key && $this->settings->enable_captch_code_login == "YES")
        {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }

        if ($this->form_validation->run() == FALSE)
        {
            $error = $this->form_validation->error_array();
        }
        else
        {
            $this->user_check_login();
            if ($this->session->userdata('redirect')) 
            {
                // redirect to desired page
                $redirect = $this->session->userdata('redirect');
                $this->session->unset_userdata('redirect');
                redirect($redirect);
            } 
            else 
            {
                $logged_in_user = $this->session->userdata('logged_in');
                if ($logged_in_user['is_admin']) {
                    // redirect to admin dashboard
                    redirect('admin');
                }
                 else {
                    // redirect to landing page
                    redirect(base_url());
                }
            }
        }

        $login_url = $this->googleplus->loginURL();
        // setup page header data
        $this->set_title(lang('user_link_register_account'));
        $data = $this->includes;
        $data['login_url'] = $login_url;
        $this->load->view('user/admin_login', $data);
        // $this->load->view($this->template, $data);
    }


    private function user_check_login() 
    {
        // limit number of login attempts
        $password = $this->input->post('password', TRUE);

        $ok_to_login = $this->UsersModel->login_attempts();
        if($ok_to_login) 
        {

            $login = $this->UsersModel->admin_login($this->input->post('username', TRUE), $password);

            if($login && $login !='not-active') 
            {
                   
                $this->session->set_userdata('logged_in', $login);
                
                $update_data['last_access'] = date('Y-m-d H:i:s');
                $this->db->where('id',$login['id'])->update('users',$update_data);

                $get_login_user_membership = $this->UsersModel->get_login_user_membership($login['id'],date('Y-m-d'));
                
                $this->session->set_userdata('membership',$get_login_user_membership);
                
                return TRUE;
            }
            elseif($login == 'not-active' && $this->settings->email_user_activation == 'YES')
            {
                $this->session->set_flashdata("error",'Your Account Is Not Active Yet Plz Active From Link send To Your Mail');
            }
            elseif($login == 'not-active')
            {
                $this->session->set_flashdata("error",'Your Account Is Not Active');
            }
            else
            {
                $this->session->set_flashdata("error",lang('user_error_invalid_login'));
            }
            return redirect(base_url('admin/login'));
        }

        $this->session->set_flashdata("error",sprintf(lang('user_error_too_many_login_attempts'),$this->config->item('login_max_time')));
        return redirect(base_url('admin/login'));
    }


    function logout() 
    {
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('admin/login');
    }

    function recaptcha($str = '')
    {
        if(empty($str))
        {
            $this->form_validation->set_message('recaptcha', 'Please Check Captcha Code First');
            return false;
        }
        return do_recaptcha_validation($str);
    }

}
