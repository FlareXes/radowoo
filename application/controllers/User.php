<?php defined('BASEPATH') OR exit('No direct script access allowed');
class User extends Public_Controller {
    /**
     * Constructor
     */
    function __construct() 
    {
        parent::__construct();
        // load the users model
        $this->load->model('UsersModel');
        $this->load->model('InstitutionModal');
        $this->load->helper('core_helper');
        $this->load->library('encryption');
        if($this->settings->facebook_app_id!='' && $this->settings->facebook_app_secret!='') {
            $this->load->library('facebook');
        }
        
        @$this->load->library('Googleplus');
        $this->add_external_js('//www.google.com/recaptcha/api.js');
        // $this->add_js_theme('api.js');
        $this->add_js_theme('social_login.js');
        $this->load->library('encrypt');

    }
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Default
     */
    function index() {
        return redirect(base_url('login'));
    }
    /**
     * Validate login credentials
     */
    function login() 
    {
        if ($this->session->userdata('logged_in')) 
        {   
            $logged_in_user = $this->session->userdata('logged_in');
            if ($logged_in_user['is_admin']) 
            {
                redirect('admin');
            }
            else if($logged_in_user['role'] == "tutor")
            {
                redirect('tutor');
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
        // $this->form_validation->set_rules('password', 'Password', 'required|trim|max_length[72]|callback__check_login');

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
                if ($logged_in_user['is_admin']) 
                {
                    // redirect to admin dashboard
                    redirect('admin');
                }
                else if($logged_in_user['role'] == "tutor")
                {
                    redirect('tutor');
                }
                else 
                {
                    // redirect to landing page
                    redirect(base_url());
                }
            }

        }

        

        $login_url = $this->googleplus->loginURL();
        $content_data['login_url'] = $login_url;
        // setup page header data
        $this->set_title(lang('user_link_register_account'));
        $data = $this->includes;
        // load views
        $data['content'] = $this->load->view('user/login', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    /**
     * Logout
     */
    function logout() {
        $this->session->unset_userdata('logged_in');
        $this->session->sess_destroy();
        redirect('login');
    }
    /**
     * Registration Form
     */
    function register($is_tutor = 'user') 
    {

        if($is_tutor == "tutor")
        {
            $customfields = $this->db->where('form','tutor_registration')->order_by('field_order', 'asc')->get('custom_fields')->result();
        }
        else if($is_tutor == "user")
        {
            $customfields = $this->db->where('form','registration')->order_by('field_order', 'asc')->get('custom_fields')->result();
        }
        else
        {
            $this->session->set_flashdata('error', lang('invalid_uri_arguments'));
            return redirect(base_url('user/register'));
        }
        
        if($customfields && $_POST)
        {
            foreach ($customfields as  $customfield) 
            {
                if($customfield->is_required == 1 && empty($this->input->post($customfield->field_name)))
                {
                    if($customfield->field_type == 'file')
                    {
                        if(isset($_FILES[$customfield->field_name]['name']))
                        {
                            $response = $this->upload_file($customfield->field_name);
                            if($response['status'] == true)
                            {
                                $cf_files_name[$customfield->field_name] = $response['name'];
                            }
                            else
                            {
                                $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
                                $this->session->set_flashdata('error', $customfield->field_label.' '. $response['msg']);
                            }
                        }
                        else
                        {
                            $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
                            $this->session->set_flashdata('error', $customfield->field_label.' Is Required');
                        }
                    }
                    else
                    {
                        $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
                    }
                }
            }
        }

        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', 'Username', 'required|trim|min_length[5]|max_length[30]|callback__check_username');
        $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[256]|valid_email|callback__check_email');
        $this->form_validation->set_rules('language', 'Language', 'required|trim');
        $this->form_validation->set_rules('course_id', lang('course'), 'required|trim');
        $this->form_validation->set_rules('institution_id', lang('institution'), 'required|trim');
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('password_repeat', 'Repeat Password', 'required|trim|matches[password]');
        
        if ($this->settings->recaptcha_secret_key && $this->settings->recaptcha_site_key && $this->settings->enable_captch_code_login == "YES") 
        {
            $this->form_validation->set_rules('g-recaptcha-response', 'Captcha', 'callback_recaptcha');
        }


        if ($this->form_validation->run() == TRUE) 
        {
            // save the changes 
            $user_register_post_data = $this->input->post();
            $user_request_for_tutor = ($this->input->post('tutor_request') && $this->input->post('tutor_request') == 1) ? 1 : 0;
            if($user_request_for_tutor == 1)
            {
                $user_register_post_data['role'] = 'tutor'; 
            }
            else
            {
                $user_register_post_data['role'] = $is_tutor;
            }
                
            $user_register_response = $this->UsersModel->create_profile($user_register_post_data);

            if($user_register_response && is_array($user_register_response))
            {
                $validation_code = $user_register_response['validation_code'];
                $new_user_id = $user_register_response['new_user_id'];

                if($customfields) 
                {
                    $customfield_val_array = [];
                    foreach ($customfields as  $customfield) 
                    {
                        $customfield_val = [];
                        if($customfield->field_type == 'checkbox')
                        {
                            $value = $this->input->post("$customfield->field_name") && is_array($this->input->post("$customfield->field_name")) ? $this->input->post("$customfield->field_name") : array();
                            $value = json_encode($value);
                        }
                        else if($customfield->field_type == 'file')
                        {
                            $value = isset($cf_files_name[$customfield->field_name]) ? $cf_files_name[$customfield->field_name] : NULL;
                        }
                        else
                        {
                            $value = $this->input->post("$customfield->field_name") ? $this->input->post("$customfield->field_name") : NULL;
                        }

                        $customfield_val['form'] = 'registration';
                        $customfield_val['field_name'] = $customfield->field_name;
                        $customfield_val['rel_id'] = $new_user_id;
                        $customfield_val['value'] = $value;
                        $customfield_val_array[] = $customfield_val;
                    }

                    if($customfield_val_array)
                    {
                        $this->db->insert_batch('custom_field_values', $customfield_val_array); 
                    }
                }

                $new_user_data = $this->UsersModel->get_user_by_id($new_user_id);
                if($new_user_data->role == "tutor")
                {
                    $email_template = get_email_template('register_new_tutor_account');
                }
                else
                {
                    $email_template = get_email_template('verify_account');
                }


                $this->session->language = 'English';
                // build the validation URL
                $encrypted_email = sha1($this->input->post('email', TRUE));
                $user_full_name = $this->input->post('first_name', TRUE). " ".$this->input->post('last_name', TRUE);
                $site_name_with_url = '<a href="'.base_url().'">'.$this->settings->site_name.'</a>';
                $validation_url = base_url('user/validate') . "?e={$encrypted_email}&c={$validation_code}";
                $account_verify_link = '<a href="'.$validation_url.'" title="Activate Account">Click Here</a>';

                $email_msg = "Hello $user_full_name You Are  Register Successfully";
                $mail_subject = "Register On ".$this->settings->site_name;
                

                
                if($email_template)
                {
                    $email_msg = str_replace("{new_customer_name}",$user_full_name,$email_template->description);
                    $email_msg = str_replace('{contact_firstname}',$this->input->post('first_name', TRUE),$email_msg);
                    $email_msg = str_replace('{contact_lastname}',$this->input->post('last_name', TRUE),$email_msg);
                    $email_msg = str_replace('{contact_email}',$this->input->post('email', TRUE),$email_msg);
                    $email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);
                    $email_msg = str_replace("{site_name_with_url}",$site_name_with_url,$email_msg);
                    $email_msg = str_replace("{account_verify_link}",$account_verify_link,$email_msg);
                    $email_msg = str_replace("{verify_link}",'<a href="'.$account_verify_link.'" title="Activate Account">Click Here</a>',$email_msg);
                    $mail_subject = $email_template->subject;

                }
                


                $mail_to = $this->input->post('email', TRUE);
                $recipet_name = $this->input->post('first_name', TRUE);
                $this->load->library('SendMail');

               

                if($new_user_data->role == "tutor")
                {
                    $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
                    $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
                    $this->session->set_flashdata('message', lang("Congratulation Register Successfully"));
                    return redirect(base_url('user/register'));
                }
                else
                {
                    if($this->settings->email_user_activation == 'YES')
                    {
                        $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
                        $mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
                        
                        if($mail_status)
                        {
                            $this->session->set_flashdata('message', lang("Congratulation Register Successfully"));
                        }
                        else
                        {
                            $this->session->set_flashdata('error', lang('Register Successfully ! Mail Send Error'));
                        }
                    }
                    else
                    {
                        $this->session->set_flashdata('message', lang("Congratulation Register Successfully"));
                    }
                }
            } 
            else 
            {
                $this->session->set_flashdata('error', lang('user_error_register_failed'));
                return redirect(base_url('user/register'));
            }
            // redirect home and display message
            return redirect(base_url('login'));
        }
        $all_courses_obj = $this->InstitutionModal->get_all_courses();

        $all_courses = array();
        $all_courses[''] = lang('select_course');
        if($all_courses_obj)
        {
            foreach ($all_courses_obj as $courses_obj) 
            {
                $all_courses[$courses_obj->id] = $courses_obj->title;
            }
        }

        $all_instute_obj = $this->InstitutionModal->get_all_institutions();

        $all_institutions = array();
        $all_institutions[''] = lang('select_institution');
        if($all_instute_obj)
        {
            foreach ($all_instute_obj as $instute_obj) 
            {
                $all_institutions[$instute_obj->id] = $instute_obj->title;
            }
        }
        // $all_courses[0] = lang('other');
        // $all_institutions[0] = lang('other');

        // setup page header data
        $this->set_title(lang('front_register'));
        $data = $this->includes;
        // set content data
        $content_data = array('cancel_url' => base_url(),'is_tutor' => $is_tutor, 'user' => NULL, 'password_required' => TRUE,'customfields'=>$customfields,'all_courses' => $all_courses,'all_institutions' => $all_institutions);
        // load views
        $data['content'] = $this->load->view('user/profile_form', $content_data, TRUE);
        $this->load->view($this->template, $data); 
    }

    /**
     * Validate new account
     */
    function validate() {
        // get codes
        $encrypted_email = $this->input->get('e');
        $validation_code = $this->input->get('c');
        // validate account
        $validated = $this->UsersModel->validate_account($encrypted_email, $validation_code);
        if ($validated) {
            $this->session->set_flashdata('message', lang('user_msg_validate_success'));
        } else {
            $this->session->set_flashdata('error', lang('user_error_validate_failed'));
        }
        redirect(base_url('login'));
    }

    /**
     * Forgot password
     */
    function forgot() {
        // validators

        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[256]|valid_email|callback__check_email_exists');
        if ($this->form_validation->run() == TRUE) {
            // save the changes
            $results = $this->UsersModel->reset_password_by_token($this->input->post('email',TRUE));
            
            if($results) 
            {
                $key = uniqid(rand(),1);
                $token = md5($key."_EMAIL_".$results->email);
                $token_data = array();
                $token_data['token']         = $token;
                $token_data['updated']       = date('Y-m-d H:i:s');

                $update_status = $this->UsersModel->update_user_token_by_email($results->email, $token_data);
                if($update_status)
                {
                    $email_template = get_email_template('reset-password');
                    $reset_url = base_url('user/reset-my-password/').$token;
                    $password_reset_link_is_here = '<a href="'.$reset_url.'" title="Reset Password">Reset Password</a>';
                    $site_name_with_link = '<a href="'.base_url().'">'.$this->settings->site_name.'</a>';
                    $user_full_name = $results->first_name. " ".$results->last_name;
                    $email_msg = 'Click Below Link To Reset Your Password '. $password_reset_link_is_here;
                    $mail_subject = "Reset Password";

                    if($email_template)
                    {
                        $email_msg = str_replace("{account_holder_name}",$user_full_name,$email_template->description);
                        $email_msg = str_replace("{password_reset_link_is_here}",$password_reset_link_is_here,$email_msg);
                        $email_msg = str_replace("{site_name_with_link}",$site_name_with_link,$email_msg);
                        $email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);

                        $email_msg = str_replace("{site_email_address}",$this->settings->site_email,$email_msg);
                        $mail_subject = $email_template->subject;
                    }
                    

                    $mail_to = $results->email;
                    $recipet_name = $results->first_name;
                    
                    if($this->settings->email_user_activation == 'YES')
                    {
                        $this->load->library('SendMail');
                        $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
                        $mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);

                        if($mail_status)
                        {
                            $this->session->set_flashdata("message",lang('forgot_email_send')." ".$results->first_name);
                        }
                        else
                        {
                            $this->session->set_flashdata("error",lang('email_encountered_an_error')." ".$results->first_name." !");
                        }
                    }
                }
                else
                {
                     $this->session->set_flashdata("error","Sorry Password Update Error ".$results->first_name." !");
                }
            } 
            else 
            {
                $this->session->set_flashdata('error', lang('user_error_password_reset_failed'));
            }
            redirect(base_url('login'));
        }

        // setup page header data
        $this->set_title(lang('front_forgot'));
        $data = $this->includes;
        // set content data
        $content_data = array('cancel_url' => base_url(), 'user' => NULL);
        // load views
        $data['content'] = $this->load->view('user/forgot_form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/
    /**
     * Verify the login credentials
     *
     * @param  string $password
     * @return boolean
     */
    function user_check_login() 
    {
        // limit number of login attempts
        $password = $this->input->post('password', TRUE);

        $ok_to_login = $this->UsersModel->login_attempts();
        if($ok_to_login) 
        {

            $login = $this->UsersModel->login($this->input->post('username', TRUE), $password);

            if ($login && $login !='not-active') 
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
            return redirect(base_url('login'));
        }

        $this->session->set_flashdata("error",sprintf(lang('user_error_too_many_login_attempts'),$this->config->item('login_max_time')));
        return redirect(base_url('login'));
    }



    function _check_login($password) 
    {
        // limit number of login attempts
        $ok_to_login = $this->UsersModel->login_attempts();
        if ($ok_to_login) {

            $login = $this->UsersModel->login($this->input->post('username', TRUE), $password);

            if ($login && $login !='not-active') 
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
                $this->form_validation->set_message('_check_login', 'Your Account Is Not Active Yet Plz Active From Link send To Your Mail');
                return FALSE;
            }
            elseif($login == 'not-active')
            {
                $this->form_validation->set_message('_check_login', 'Your Account Is Not Active');
                return FALSE;   
            }
            else
            {
                $this->form_validation->set_message('_check_login', lang('user_error_invalid_login'));
                return FALSE;
            }
        }
        $this->form_validation->set_message('_check_login', sprintf(lang('user_error_too_many_login_attempts'), $this->config->item('login_max_time')));
        return FALSE;
    }

    /**
     * Make sure username is available
     *
     * @param  string $username
     * @return int|boolean
     */
    function _check_username($username) {
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

    /**
     * Make sure email is available
     *
     * @param  string $email
     * @return int|boolean
     */
    function _check_email($email) {
        if ($this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email', sprintf(lang('email_exists'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }

    /**
     * Make sure email exists
     *
     * @param  string $email
     * @return int|boolean
     */
    function _check_email_exists($email) {
        if (!$this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email_exists', sprintf(lang('user_error_email_not_exists'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }

    public function reset_password_form($token = NULL)
    {
        if(empty($token))
        {
            $this->session->set_flashdata("error","Invalid Link Or Link Has Been Expired !");
            return redirect(base_url('LOGIN'));
        }

        $user_data = $this->UsersModel->check_is_valid_user($token);

        if(empty($user_data))
        {
            $this->session->set_flashdata("error","Invalid Link Plz Try Again Latter !");
            return redirect(base_url('login'));
        }

        $email = $user_data->email;
        $action = base_url('user/reset-my-password/').$token;
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('password', 'Password', 'required|trim|min_length[5]');
        $this->form_validation->set_rules('password_repeat', 'Password Repeat', 'required|trim|matches[password]');
            
        if($this->form_validation->run() == TRUE) 
        {
            if($this->input->post('password') == $this->input->post('password_repeat'))
            {
                $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                $password = hash('sha512', $this->input->post('password',TRUE) . $salt);

                $data['password']             = $password;
                $data['salt']                 = $salt;
                $data['token']                = NULL;
                $data['updated']              = date('Y-m-d H:i:s');

                $update_status = $this->UsersModel->update_user_token_by_email($email,$data);
                if($update_status)
                {
                    $this->session->set_flashdata("message",lang("Password Update Successfully Now You Can Login with New Password"));
                    return redirect(base_url('login'));
                }
                else
                {
                    $this->session->set_flashdata("error",lang("Password Update Error Plz Try Again ...!"));
                    return redirect($action);
                }
            } 
            else 
            {
                $this->session->set_flashdata("error",lang("Confirm Password Does Not Match....!"));
                return redirect($action);
            }
        }
        else
        {
            $this->set_title(lang('Reset Password'));
            $data = $this->includes;
            $content_data = array('cancel_url' => base_url(), 'user' => NULL, 'action' => $action);
            $data['content'] = $this->load->view('user/reset_password_form', $content_data, TRUE);
            $this->load->view($this->template, $data);
        }
    }


    public function check_fb_login() 
    {

        $response['status'] = 'error';               
        $response['msg'] = 'Not Allowed....!';  

        if ($this->input->post('user_id')) 
        {
            $post = $this->input->post();
            $user_name = $this->split_name($post['user_name']);
            $img = FCPATH.'assets/images/user_image/'.$post['user_id'].'.png';
            file_put_contents($img, file_get_contents($post['user_picture']));

            $user['username'] = slugify_string($post['user_name']);
            $user['password'] = 'facebook';
            $user['salt'] = 'facebook';
            $user['first_name'] = $user_name['first_name'];
            $user['last_name'] = $user_name['last_name'];
            $user['email'] = $post['user_email'];
            $user['image'] = $post['user_id'].'.png';
            $user['language'] = 'en';
            $user['is_admin'] = '0';
            $user['status'] = '1';
            $user['deleted'] = '0';
            $user['validation_code'] = NULL;
            $user['created'] = date('Y-m-d H:i:s');
            $user['updated'] = date('Y-m-d H:i:s');
            $user['token'] = NULL;
            $user['auth_id'] = $post['user_id'];
            $user['login_from'] = 'facebook';

            $login_status = $this->social_login($user);

            if($login_status['status'] == TRUE)
            {
                $this->session->set_flashdata("message","facebook Login Successfully ... ! ");
                // $user['user_picture']                
                $response['status'] = 'success';               
                $response['msg'] = $login_status['msg'];                       
                $response['url'] = $login_status['url'];                         
            }
            else
            {
                $response['status'] = 'error';               
                $response['msg'] = $login_status['msg'];                       
                $response['url'] = $login_status['url'];           
            }
        }
        else
        {
            $this->session->set_flashdata("error","Sorry Facebook Login Fail....!");
            $response['status'] = 'error';               
            $response['msg']    = 'Sorry Facebook Login Fail....!';  
            $response['url']    = base_url('login'); 
        }

        echo  json_encode($response);
        exit;
    }


    public function google_login() 
    {
        if (isset($_GET['code'])) 
        {

            try
            {
                $this->googleplus->getAuthenticate();
                $post = $this->googleplus->getUserInfo();
            }
            catch(Exception $e)
            {
                $this->session->set_flashdata("error","Sorry Exception Occurred During Google Login ....! ");
                return redirect(base_url('login'));
            }
            
            
            $user_name = $this->split_name($post['name']);
            $img = FCPATH.'assets/images/user_image/'.$post['id'].'.png';
            file_put_contents($img, file_get_contents($post['picture']));

            $user['username'] = slugify_string($post['name']);
            $user['password'] = 'google';
            $user['salt'] = 'google';
            $user['first_name'] = $post['given_name'];
            $user['last_name'] = $post['family_name'];
            $user['email'] = $post['email'];
            $user['image'] = $post['id'].'.png';
            $user['language'] = $post['locale'];
            $user['is_admin'] = '0';
            $user['status'] = '1';
            $user['deleted'] = '0';
            $user['validation_code'] = NULL;
            $user['token'] = NULL;
            $user['auth_id'] = $post['id'];
            $user['login_from'] = 'google';

            $login_status = $this->social_login($user);

            if($login_status['status'] == TRUE)
            {
                if($login_status['msg'])
                {
                    $this->session->set_flashdata("message",$login_status['msg']);
                }
                return redirect($login_status['url']);                   
            }
            else
            {
                if($login_status['msg'])
                {
                    $this->session->set_flashdata("error",$login_status['msg']);
                }
                return redirect($login_status['url']);           
            }

        }
        else
        {
            $this->session->set_flashdata("error","Sorry Google Login Fail....!");
            return redirect(base_url('login'));
        }
    }

    private function social_login($user_dara) 
    {
        $social_login['status'] = TRUE;
        $social_login['msg'] = '';
        $social_login['url'] = base_url();
        
        if ($this->session->userdata('logged_in')) 
        {
            $logged_in_user = $this->session->userdata('logged_in');
            if ($logged_in_user['is_admin']) 
            {
                $social_login['msg'] = '';
                $social_login['url'] = base_url('admin');
            } 
            else 
            {
                $social_login['msg'] = '';
                $social_login['url'] = base_url();
                
            }
            return $social_login;
        }

        $check_user_data = $this->check_social_login($user_dara);

        if ($check_user_data['status'] == TRUE) 
        {
            if ($this->session->userdata('redirect')) 
            {
                // redirect to desired page
                $redirect = $this->session->userdata('redirect');
                $this->session->unset_userdata('redirect');

                $social_login['msg'] = $check_user_data['message'];
                $social_login['url'] = $redirect;

                // redirect($redirect);
            } 
            else 
            {
                $logged_in_user = $this->session->userdata('logged_in');

                if ($logged_in_user['is_admin']) 
                {
                    $social_login['msg'] = $check_user_data['message'];
                    $social_login['url'] = base_url('admin');
                    redirect('admin');
                } 
                else 
                {
                    $social_login['msg'] = $check_user_data['message'];
                    $social_login['url'] = base_url();
                }
            }
            return $social_login;
        }

        $social_login['msg'] = $check_user_data['message'];
        $social_login['url'] = base_url('login');
        $social_login['status'] = FALSE;
        return $social_login;
    }


    private function check_social_login($user_dara) 
    {
        $login_chek_response['status'] = FALSE;
        $login_chek_response['message'] = '';

        $ok_to_login = $this->UsersModel->login_attempts();
        if ($ok_to_login) 
        {
            $login = $this->UsersModel->social_login($user_dara);
            if ($login && $login !='not-active') 
            {
                $this->session->set_userdata('logged_in', $login);
                $login_chek_response['status'] = TRUE;
                $login_chek_response['message'] = '';
                return $login_chek_response;
            }
            elseif($login == 'not-active')
            {
                $login_chek_response['status'] = FALSE;
                $login_chek_response['message'] = 'Your Account Is Not Active Yet Plz Active From Link send To Your Mail';
                return $login_chek_response;
            }
            else
            {
                $login_chek_response['status'] = FALSE;
                $login_chek_response['message'] = lang('user_error_invalid_login');
                return $login_chek_response;
            }
        }

        $login_chek_response['status'] = FALSE;
        $login_chek_response['message'] = sprintf(lang('user_error_too_many_login_attempts'), $this->config->item('login_max_time'));
        return $login_chek_response;
    }



    private function split_name($name) 
    {
        $name = trim($name);
        $last_name = (strpos($name, ' ') === false) ? '' : preg_replace('#.*\s([\w-]*)$#', '$1', $name);
        $first_name = trim( preg_replace('#'.$last_name.'#', '', $name ) );
        return array('first_name' => $first_name, 'last_name' => $last_name);
    }

    function otp_login()
    {
        
        $this->form_validation->set_rules('mobile_no', 'Mobile No', 'required|trim|max_length[10]');
        if ($this->form_validation->run() == TRUE) {
            $otp_detail = array();
            $mobile_no = $this->input->post('mobile_no');
            $otp_detail['phone_no'] = $mobile_no;
            
            $curl = curl_init();

            curl_setopt_array($curl, array(
              CURLOPT_URL => "http://2factor.in/API/V1/3ce8981e-4765-11ea-9fa5-0200cd936042/SMS/$mobile_no/AUTOGEN",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
              CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            $response = json_decode($response);
            
            if(strtolower($response->Status) == "success")
            {
                $check_mobile_no = $this->UsersModel->mobile_exists($mobile_no);
                $otp_detail['auth_id'] = $response->Details;
                if(!empty($check_mobile_no))
                {   
                    $otp_detail['updated'] = date('Y-m-d H:i:s');
                    $updated_record = $this->UsersModel->update_otp_data($check_mobile_no->id,$otp_detail);
                    if($updated_record)
                    {
                        $encrypted_id = encrypt_decrypt('encrypt',$check_mobile_no->id);          
                        return redirect(base_url('login-otp/'.$encrypted_id));
                    }
                    
                }
                else
                {

                    $salt = hash('sha512', uniqid(mt_rand(1, mt_getrandmax()), TRUE));
                    $password = hash('sha512', date("YmdHis").rand(). $salt);
                    $validation_code = sha1(microtime(TRUE) . mt_rand(10000, 90000));

                    $otp_detail['created'] = date('Y-m-d H:i:s');
                    $otp_detail['updated'] = date('Y-m-d H:i:s');
                    $otp_detail['login_from'] = 'otp';
                    $otp_detail['username'] = 'user_'.$mobile_no."_".rand();
                    $otp_detail['first_name'] = 'user';
                    $otp_detail['last_name'] = $mobile_no;
                    $otp_detail['password'] = $password;
                    $otp_detail['salt'] = $salt;
                    $otp_detail['validation_code'] = $validation_code;
                    $inserted_id = $this->UsersModel->insert_otp_detail($otp_detail);
                    if($inserted_id)
                    {
                        $encrypted_id = encrypt_decrypt('encrypt',$inserted_id);          
                        return redirect(base_url('login-otp/'.$encrypted_id));
                    }
                }  
            } 
            else 
            {
              echo "cURL Error #:" . $err;
            }
        }
        // setup page header data
        $content_data = array();
        $this->set_title(lang('user_link_register_account'));
        $data = $this->includes;
        // load views
        $data['content'] = $this->load->view('user/login_otp', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function match_otp($login_id = FALSE)
    {

        $login_id = encrypt_decrypt('decrypt',$login_id);

        $user_data =  $this->UsersModel->get_user_token_by_id($login_id);
        $category_data = $this->UsersModel->get_categories();
        $auth_id = $user_data['auth_id'];
        if(empty($user_data['category_of_user'])) 
        { 
            $this->form_validation->set_rules('category_of_user', 'category_of_user', 'required|trim');
        }   
        
        $this->form_validation->set_rules('otp', 'OTP', 'required|trim|min_length[2]|max_length[32]');
        if ($this->form_validation->run() == TRUE) 
        {

            $otp_value = $this->input->post('otp');
           
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => "http://2factor.in/API/V1/3ce8981e-4765-11ea-9fa5-0200cd936042/SMS/VERIFY/$auth_id/$otp_value",
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => "",
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 30,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => "GET",
              CURLOPT_POSTFIELDS => "",
              CURLOPT_HTTPHEADER => array(
                "content-type: application/x-www-form-urlencoded"
              ),
            ));

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);
            $response = json_decode($response);
            
            if(strtolower($response->Status) == 'success')
            {
                if(empty($user_data['category_of_user']))
                {
                    $otp_detail = array();
                    $category_of_user = $this->input->post('category_of_user'); 
                    $otp_detail['category_of_user'] = $category_of_user;
                    $otp_detail['updated'] = date('Y-m-d H:i:s');
                    $updated_record = $this->UsersModel->update_otp_data($user_data['id'],$otp_detail);
                    $user_data =  $this->UsersModel->get_user_token_by_id($login_id);

                }

                $this->session->set_userdata('logged_in', $user_data);
                
                $update_data['last_access'] = date('Y-m-d H:i:s');
                $this->db->where('id',$user_data['id'])->update('users',$update_data);

                $get_login_user_membership = $this->UsersModel->get_login_user_membership($user_data['id']);

                $validity['validity'] = (isset($get_login_user_membership['validity']) && !empty($get_login_user_membership['validity']) ? $get_login_user_membership['validity'] : FALSE);
                
                $this->session->set_userdata('membership',$validity);
                return redirect(base_url());   
            }
            else 
            {
                $this->session->set_flashdata('error',lang('invalid_otp'));
                return redirect($_SERVER['HTTP_REFERER']);  
                //echo "cURL Error #:" . $err;
            }
        }

        $content_data = array('user_data'=>$user_data,'category_data'=>$category_data);
        $this->set_title(lang('user_link_register_account'));
        $data = $this->includes;
        // load views
        $data['content'] = $this->load->view('user/submit_otp', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }



    public function upload_file($file_name) 
    {
        $image = array();
        $name = $_FILES[$file_name]['name'];
        $new_name = time().$name;
        $config['upload_path'] = "./assets/images/custom_fields";
        $config['allowed_types'] = 'jpg|png|bmp|jpeg';
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
        $status = $this->upload->do_upload($file_name);
        if($status) 
        {
            $file = $this->upload->data();
            $upolded_filename = $file['file_name'];
            return  array('status' => true, 'name' => $file['file_name'], 'original_name' => $name,);
        } 
        else 
        {
            return  array('status' => false, 'original_name' => $name,'msg' => $this->upload->display_errors());
        }
        return  array('status' => false, 'msg' => 'File Upload Failed..!');
    }



    public function recaptcha($str = '')
    {
        if(empty($str))
        {
            $this->form_validation->set_message('recaptcha', 'Please Check Captcha Code First');
            return false;
        }
        return do_recaptcha_validation($str);
    }



    function activate()
    {

        $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[256]|valid_email|callback__check_email_exists');
        if ($this->form_validation->run() == TRUE) 
        {
            
            $email = $this->input->post('email',TRUE);
            $user_data = $this->db->where("email",$email)->get('users')->row();
            if(empty($user_data))
            {
                $this->session->set_flashdata('error', lang('Sorry User Email Address Is Not Exist !'));
                redirect(base_url('login'));
            }

            if($user_data->role == "tutor")
            {
                $this->session->set_flashdata('error', lang('tutor can not self activate.'));
                redirect(base_url('login'));
            }
            $status = $this->send_user_account_activation_mail($user_data->id);
            if($status) 
            {
                 $this->session->set_flashdata("message",lang("Account Activation Link Has Been Send To Your Email Address !"));
            } 
            else 
            {
                $this->session->set_flashdata('error', lang('Account Activation Email Send Error Plesae Try Again After Some Time'));
            }
            redirect(base_url('login'));
        }

        // setup page header data
        $this->set_title(lang('Account Activation'));
        $data = $this->includes;
        // set content data
        $content_data = array('cancel_url' => base_url('login'), 'user' => NULL);
        // load views
        $data['content'] = $this->load->view('user/activation_form', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }



    private function send_user_account_activation_mail($user_id)
    {
        if(empty($user_id))
        {
            $this->session->set_flashdata('error', lang('Something Went Wrong Please Try Again Later !'));
            redirect(base_url('login'));
        }

        $user_data = $this->db->where("id",$user_id)->get("users")->row();
        if(empty($user_data))
        {
            $this->session->set_flashdata('error', lang('Sorry User Is Not Exist !'));
            redirect(base_url('login'));
        }
        $validation_code = $user_data->validation_code;
        if(empty($user_data->validation_code))
        {
            $validation_code = sha1(microtime(TRUE) . mt_rand(10000, 90000));
            $user_data = $this->db->where("id",$user_id)->update("users",array('validation_code'=>$validation_code));
        }

        if($user_data->deleted == 1 OR $user_data->deleted == "1")
        {
            $this->session->set_flashdata('error', lang('Sorry Your Account Has Been Deleted By Admin !'));
            redirect(base_url('login'));
        }

        if($user_data->status == 1 OR $user_data->status == "1")
        {
            $this->session->set_flashdata('error', lang('Sorry Your Account Is Alredy Activated !'));
            redirect(base_url('login'));
        }

        // build the validation URL
        $encrypted_email = sha1($user_data->email);
        $validation_url = base_url('user/validate') . "?e={$encrypted_email}&c={$validation_code}";
        $email_template = get_email_template('verify_account');
        $site_name = $this->settings->site_name;        
        $user_full_name = $user_data->first_name. " ".$user_data->last_name;
        $site_name_with_url = '<a href="'.base_url().'">'.$site_name.'</a>';

        $email_msg = '';
        
        if($email_template)
        {
            $email_msg = str_replace("{user_full_name}",$user_full_name,$email_template->description);            
            $email_msg = str_replace("{account_verify_link}",'<a href="'.$validation_url.'" title="Activate Account">Click Here</a>',$email_msg);
            $email_msg = str_replace("{your_site_name}",$site_name,$email_msg);
            $email_msg = str_replace("{site_name_with_url}",$site_name_with_url,$email_msg);
            $mail_subject = $email_template->subject;
        }
        
        $mail_to = $user_data->email;
        $recipet_name = $user_full_name;
        $this->load->library('SendMail');
        $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
        $mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);

        if($mail_status)
        {
            return TRUE;
        }
        return FALSE;

    }

    function get_course_instute($course_id)
    {
        
        $all_institutions = array();
        $all_institutions[''] = lang('select_institution');

        if($course_id > 0)
        {
            $institutions_ids = $this->InstitutionModal->get_institutions_by_courses_id($course_id);
            if($institutions_ids)
            {
                $all_instute_obj = $this->InstitutionModal->get_all_institutions_by_ids($institutions_ids);
            }
        }
        else
        {
            $all_instute_obj = $this->InstitutionModal->get_all_institutions();
            
        }

        if($all_instute_obj)
        {
            foreach ($all_instute_obj as $instute_obj) 
            {
                $all_institutions[$instute_obj->id] = $instute_obj->title;
            }
        }
        $all_institutions[0] = lang('other');
        $options = "";
        foreach ($all_institutions as $id => $title) 
        {
            $options .= "<option value='".$id."'>".$title."</option>";
        }

        $data['options'] = $options;
        echo  json_encode($data);
        exit;
    }


    function get_courses_by_instute_id($institution_id)
    {
        $all_courses = array();
        $all_courses[''] = lang('select_courses');

        if($institution_id > 0)
        {
            $courses_ids = $this->InstitutionModal->get_courses_by_institution_id($institution_id);
            if($courses_ids)
            {
                $all_courses_obj = $this->InstitutionModal->get_all_courses_by_ids($courses_ids);
            }
        }
        else
        {
            $all_courses_obj = $this->InstitutionModal->get_all_institutions();
            
        }

        if($all_courses_obj)
        {
            foreach ($all_courses_obj as $course_obj) 
            {
                $all_courses[$course_obj->id] = $course_obj->title;
            }
        }
        // $all_courses[0] = lang('other');
        $options = "";
        foreach ($all_courses as $id => $title) 
        {
            $options .= "<option value='".$id."'>".$title."</option>";
        }

        $data['options'] = $options;
        echo  json_encode($data);
        exit;
    }

    public function tutor_request()
    {

        $logged_in_user = false;
        $user_id = false;
        if($this->session->userdata('logged_in')) 
        {   
            $logged_in_user = $this->session->userdata('logged_in');
            $user_id = $logged_in_user['id'];
            if ($logged_in_user['is_admin']) 
            {
                $this->session->set_flashdata('error', lang('you_are_admin'));
                redirect('admin');
            }
            else if($logged_in_user['role'] == "tutor")
            {
                $this->session->set_flashdata('error', lang('you_are_already_tutor'));
                redirect('tutor');
            }

        }
        else
        {
            $this->session->set_flashdata('error', lang('please_login_first'));
            redirect('login');
        }


        $this->form_validation->set_rules('user_qualification_experience', lang('qualification_experience'), 'required|trim');
        if ($this->form_validation->run() == TRUE) 
        {
            $db_data = array();
            $db_data['user_qualification_experience'] = $this->input->post('user_qualification_experience');
            $db_data['user_request_for_tutor'] = 1;
            $this->db->where('id',$user_id)->update('users',$db_data);
            $this->session->set_flashdata('error', lang('your_request_for_tutor_has_been_send_successfully'));
            redirect('tutor-request');
        }

        $user_data = $this->UsersModel->get_user_by_id($user_id);

        // setup page header data
        $this->set_title(lang('request_for_tutor'));
        $data = $this->includes;
        // set content data
        $content_data = array('logged_in_user' => $logged_in_user, 'user_id' => $user_id,'user_data' =>$user_data);
        // load views

        $data['content'] = $this->load->view('user/request_for_tutor', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }

}
