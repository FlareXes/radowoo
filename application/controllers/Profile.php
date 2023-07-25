<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Profile extends Private_Controller {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        // load the users model
        $this->load->model('UsersModel');
        $this->load->model('Payment_model');
        $this->load->model('MembershipModel');
        $this->load->model('InstitutionModal');
        $this->load->library('form_validation');
        $this->add_css_theme('quiz_box.css');
        $this->add_css_theme('set2.css');
        $this->add_css_theme('table-main.css');
        $this->add_js_theme('quiz.js');
        $this->load->library('encrypt');
    }
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Profile Editor
     */
    
    function index() 
    { 
        // validators
 
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', 'User Name', 'required|trim|min_length[5]|max_length[30]|callback__check_username');
        $this->form_validation->set_rules('first_name','First Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', 'Email', 'required|trim|max_length[128]|valid_email|callback__check_email');
        $this->form_validation->set_rules('language', 'Language', 'required|trim');
        $this->form_validation->set_rules('course_id', 'course', 'required|trim');
        $this->form_validation->set_rules('institution_id', 'institution', 'required|trim');
        $this->form_validation->set_rules('password_repeat', 'Password Repeat', 'min_length[5]');
        $this->form_validation->set_rules('password', 'Password', 'min_length[5]|matches[password_repeat]');
        $this->form_validation->set_error_delimiters('', '');

        if ($this->form_validation->run() == TRUE) 
        {
            action_not_permitted();
            // save the changes
            $saved = $this->UsersModel->edit_profile($this->input->post(), $this->user['id']);
            if ($saved) {
                // reload the new user data and store in session
                $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
                $this->user = $this->UsersModel->get_user($user_id);
                unset($this->user['password']);
                unset($this->user['salt']);
                $this->session->set_userdata('logged_in', $this->user);
                $this->session->language = $this->user['language'];
                $this->lang->load('users', $this->user['language']);
                $this->session->set_flashdata('message', lang('front_record_edited_successfully'));
            } else {
                $this->session->set_flashdata('error', lang('front_record_edited_during_error'));
            }
            // reload page and display message
            redirect('profile');
        }

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        //get purchased quiz by user login
        $purchased_quiz = $this->UsersModel->get_purchase_quiz_by_userid($user_id);

        //get like quiz by user login
        $session_quiz_data = array();
        $session_quiz_question_data = array();

        if($this->session->quiz_session)
        {
            $get_quiz_session = $this->session->quiz_session;
            $session_quiz_data = $get_quiz_session['quiz_data'];
            $session_quiz_question_data = $get_quiz_session['quiz_question_data'];
        }

        $like_quiz = $this->UsersModel->get_liked_quiz_by_userid($user_id);
        
        $quiz_data = array();
        foreach ($like_quiz as $like_key => $like_value) 
        {
            $question_count = $this->UsersModel->get_question_count_by_quiz_id($like_value->id);
            if($like_value->number_questions <= $question_count )
            {
              $quiz_data[] = $like_value;
            }
        }


        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }
        

        $paid_quizes_array = $this->Payment_model->get_user_paid_quiz_obj($user_id);
        //get post like by user login
        $like_post = $this->UsersModel->get_like_post_by_loggedin_user($user_id);

        //get user payment status list
        $payment_list = $this->UsersModel->get_payment_status_by_loggedin_user($user_id);

        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }

        // setup page header data
        $this->set_title(lang('user_profile'));
        $this->add_js_theme('quiz.js');

        

        $login_user_data = $this->UsersModel->get_user_by_id($this->user['id']);
        $all_courses_obj = FALSE;
        if($login_user_data->institution_id)
        {
            $courses_ids = $this->InstitutionModal->get_institutions_by_courses_id($login_user_data->institution_id);
            if($courses_ids)
            {
                $all_courses_obj = $this->InstitutionModal->get_all_courses_by_ids($courses_ids);
            }
        }
        else
        {
            $all_courses_obj = $this->InstitutionModal->get_all_courses();
        }

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
        $all_courses[0] = lang('other');
        $all_institutions[0] = lang('other');


        // set content data
        $content_data = array('cancel_url' => base_url(), 'user' => $this->user, 'password_required' => FALSE, 'session_quiz_data' =>$session_quiz_data, 'session_quiz_question_data' => $session_quiz_question_data, 'quiz_data' => $quiz_data,'purchased_quiz'=>$purchased_quiz,'like_post'=>$like_post,'payment_list' => $payment_list,'paid_quizes_array' => $paid_quizes_array,'is_premium_member' => $is_premium_member,'is_premium_member' => $is_premium_member,'get_logged_in_user_membership' => $get_logged_in_user_membership,'all_courses' => $all_courses,'all_institutions' => $all_institutions);

        // load views
        $data = $this->includes;
        
        $data['content'] = $this->load->view('user/profile_update_form', $content_data, TRUE);        

        $this->load->view($this->template, $data);
    }

    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/
    /**
     * Make sure username is available
     *
     * @param  string $username
     * @return int|boolean
     */
    function _check_username($username) {
        if (trim($username) != $this->user['username'] && $this->UsersModel->username_exists($username)) 
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
            
        }
    }

    /**
     * Make sure email is available
     *
     * @param  string $email
     * @return int|boolean
     */
    function _check_email($email) 
    {
        if (trim($email) != $this->user['email'] && $this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email', sprintf(lang('email_exists'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }

    function remove_from_favourite($product_id = NULL) {
        $status = $this->UsersModel->remove_from_fav_product($this->user['id'], $product_id);
        if ($status) {
            $this->session->set_flashdata('message', 'Product Removed From Favourite List');
            return redirect(base_url('profile'));
        } else {
            $this->session->set_flashdata('error', 'Invalid Request');
            return redirect(base_url('profile'));
        }
    }


    function view_payment_detail()
    {
        $id = $_POST['payment_id'];
        $payment_data = $this->Payment_model->get_payment_detail_by_id($id);

        if(empty($payment_data))
        {
            $this->session->set_flashdata('error', lang(' Payment Not Found ... !')); 
            redirect(base_url('profile'));
        }
        $relation_id = $payment_data->relation_id;
        $purchases_type = $payment_data->purchases_type;

        $item_title = NULL;
        $item_price = NULL;
        $description = NULL;
    
        $item_data = array();
        if($purchases_type == 'quiz')
        {
            $item_data =  $this->Payment_model->get_paid_quiz_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;
        }
        elseif($purchases_type == 'material')
        {
            $item_data = $this->Payment_model->get_paid_material_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }
        elseif($purchases_type == 'membership')
        {
            $item_data = $this->Payment_model->get_paid_membership_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }

        if(empty($item_data))
        {
            $this->session->set_flashdata('error', ucfirst($purchases_type).' Details Not Exist... !');
            return redirect(base_url("profile"));
        }   


        $content_data['title'] = lang('invoice');
        $content_data['payment_data'] = $payment_data;
        $content_data['purchases_type'] = $purchases_type;
        $content_data['relation_id'] = $relation_id;
        $content_data['item_title'] = $item_title;
        $content_data['item_price'] = $item_price;
        $content_data['description'] = $description;
        $content_data['item_data'] = $item_data;

        
        $data = $this->includes;
        // load views
        $modal_data = $this->load->view('admin/payment/payment_detail', $content_data, TRUE);
        echo json_encode($modal_data);
    }



    function invoice($payment_id_encrpt_url = NULL)
    {   
        $payment_id = encrypt_decrypt('decrypt',$payment_id_encrpt_url);

        $payment_data = $this->Payment_model->get_payment_detail_by_id($payment_id);

        if(empty($payment_data))
        {
            $this->session->set_flashdata('error', lang(' Payment Not Found ... !')); 
            redirect(base_url('profile'));
        }
        
        $payment_id = $payment_data->id;
        $relation_id = $payment_data->relation_id;
        $purchases_type = $payment_data->purchases_type;

        $item_title = NULL;
        $item_price = NULL;
        $description = NULL;
    
        $item_data = array();
        if($purchases_type == 'quiz')
        {
            $item_data =  $this->Payment_model->get_paid_quiz_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;
        }
        elseif($purchases_type == 'material')
        {
            $item_data = $this->Payment_model->get_paid_material_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }
        elseif($purchases_type == 'membership')
        {
            $item_data = $this->Payment_model->get_paid_membership_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }

        if(empty($item_data))
        {
            $this->session->set_flashdata('error', ucfirst($purchases_type).' Details Not Exist... !');
            return redirect(base_url("profile"));
        }  


        
        $content_data['title'] = lang('invoice');
        $content_data['payment_data'] = $payment_data;
        $content_data['purchases_type'] = $purchases_type;
        $content_data['relation_id'] = $relation_id;
        $content_data['item_title'] = $item_title;
        $content_data['item_price'] = $item_price;
        $content_data['description'] = $description;
        $content_data['item_data'] = $item_data;

        
        $data = $this->includes;

        // load views
        $modal_data = $this->load->view('admin/payment/invoice', $content_data);
    }

}
