<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Users extends Admin_Controller {
    /**
     * @var string
     */
    private $_redirect_url;
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->load->model('UsersModel');
        $this->load->model('UsertutorModal');
        $this->load->model('InstitutionModal');


        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');

        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/users'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");
        // use the url in session (if available) to return to the previous filter/sorted/paginated list
        if ($this->session->userdata(REFERRER)) {
            $this->_redirect_url = $this->session->userdata(REFERRER);
        } else {
            $this->_redirect_url = THIS_URL;
        }
        $this->add_js_theme('users_i18n.js', TRUE);


        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }


    }
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * User list page
     */
    function index() {
        
        // get parameters
        $limit = $this->input->get('limit') ? $this->input->get('limit', TRUE) : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort = $this->input->get('sort') ? $this->input->get('sort', TRUE) : DEFAULT_SORT;
        $dir = $this->input->get('dir') ? $this->input->get('dir', TRUE) : DEFAULT_DIR;
        // get filters
        $filters = array();
        if ($this->input->get('username')) {
            $filters['username'] = $this->input->get('username', TRUE);
        }
        if ($this->input->get('first_name')) {
            $filters['first_name'] = $this->input->get('first_name', TRUE);
        }
        if ($this->input->get('last_name')) {
            $filters['last_name'] = $this->input->get('last_name', TRUE);
        }

        if ($this->input->get('course_id')) 
        {
            $course_id_val = $this->input->get('course_id') == "OTHER" ? 0 : $this->input->get('course_id',TRUE);
            $filters['course_id'] = $course_id_val;
        }
        // build filter string
        $filter = "";
        foreach ($filters as $key => $value) 
        {
            $filter.= "&{$key}={$value}";
        }
        // save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
        // are filters being submitted?
        if ($this->input->post()) {
            if ($this->input->post('clear')) {
                // reset button clicked
                redirect(THIS_URL);
            } else {
                // apply the filter(s)
                $filter = "";
                if ($this->input->post('username')) {
                    $filter.= "&username=" . $this->input->post('username', TRUE);
                }
                if ($this->input->post('first_name')) {
                    $filter.= "&first_name=" . $this->input->post('first_name', TRUE);
                }
                if ($this->input->post('last_name')) {
                    $filter.= "&last_name=" . $this->input->post('last_name', TRUE);
                }

                if ($this->input->post('course_id'))
                {
                    $course_id_val = $this->input->post('course_id',TRUE);
                    $filter.= "&course_id=" . $course_id_val;
                }
                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
            }
        }
        // get list
        $users = $this->UsersModel->get_all($limit, $offset, $filters, $sort, $dir);
        
        $this->pagination->initialize(array('base_url' => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}", 'total_rows' => $users['total'], 'per_page' => $limit));
        // setup page header data
        $this->set_title(lang('users_list'));
        $this->add_js_theme('user_list.js');
        $data = $this->includes;
        // set content data

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
        $all_courses['OTHER'] = lang('other');

        $content_data = array('this_url' => THIS_URL, 'users' => $users['results'], 'total' => $users['total'], 'filters' => $filters, 'filter' => $filter, 'pagination' => $this->pagination->create_links(), 'limit' => $limit, 'offset' => $offset, 'sort' => $sort, 'dir' => $dir,'all_courses'=>$all_courses);
        // load views

        $data['content'] = $this->load->view('admin/users/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function tutor()
    { 
        $this->add_js_theme('users_tutor.js');
        $this->set_title(lang('tutor_requests'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/users/tutor', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }




    function tutor_list() 
    {
        $data = array();
        $list = $this->UsertutorModal->get_user_tutor();

        $no = $_POST['start'];
        foreach ($list as $tutor_data) 
        {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($tutor_data->first_name)." ".ucfirst($tutor_data->last_name);
            $row[] = $tutor_data->username;
            $row[] = $tutor_data->email;
           

            $url = base_url("admin/users/approve_tutor/".$tutor_data->id);
            
            $button = "<a data-user_id='$tutor_data->id' href='$url' class='mr-1 btn btn-warning rounded tutor_status' id='tutor_status' data-toggle='tooltip'  title='".lang('Approve')."'> <i class='fas fa-check-circle'></i>  </a>";

            $button .= "<a data-user_id='$tutor_data->id' href='$url' class=' mr-1 btn btn-danger rounded reject_tutor_account_request' id='reject_tutor_account_request' data-toggle='tooltip'  title='".lang('Reject')."'> <i class='fas fa-times-circle'></i> </a>";

           
            $button .= '<a data-user_id="'.$tutor_data->id.'"  href="' . base_url("admin/users/edit/". $tutor_data->id) . '" data-toggle="tooltip"  title="'.lang('update').'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';

            $button .= '<a data-user_id="'.$tutor_data->id.'" href="javascript:void(0)" data-toggle="tooltip"  title="'.lang('info').'" class="btn btn-info reject_tutor_account_request_info"><i class="fas fa-eye"></i></a>';
            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->UsertutorModal->count_all(), "recordsFiltered" => $this->UsertutorModal->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }

    function approve_tutor($user_id)
    {
            $new_user_data = $this->UsersModel->get_user_by_id($user_id);

            if(empty($new_user_data))
            {
                $this->session->set_flashdata('error', lang('invalid_uri_arguments'));
                return redirect(base_url('admin/users'));
            }
            if($new_user_data->role != "tutor")
            {
                $this->session->set_flashdata('error', lang('invalid_uri_arguments'));
                return redirect(base_url('admin/users'));
            }

            $email_template = get_email_template('admin_approve_tutor_account');
            

            $upstatus['role'] = 'tutor';
            $upstatus['user_request_for_tutor'] = '3';
            $this->db->where('id',$user_id)->update('users',$upstatus);
            
            $this->session->language = 'English';
            // build the validation URL
            $user_full_name = $new_user_data->first_name. " ".$new_user_data->last_name;
            $site_name_with_url = '<a href="'.base_url().'">'.$this->settings->site_name.'</a>';
            $email_msg = "Hello $user_full_name Your Account  Activated Successfully";
            $mail_subject = "Account Activate On ".$this->settings->site_name;
            
            if($email_template)
            {
                $email_msg = str_replace("{new_customer_name}",$user_full_name,$email_template->description);
                $email_msg = str_replace('{contact_firstname}',$new_user_data->first_name,$email_msg);
                $email_msg = str_replace('{contact_lastname}',$new_user_data->last_name,$email_msg);
                $email_msg = str_replace('{contact_email}',$new_user_data->email,$email_msg);
                $email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);
                $email_msg = str_replace("{site_name_with_url}",$site_name_with_url,$email_msg);
                $mail_subject = $email_template->subject;

            }
            

            $mail_to = $new_user_data->email;
            $recipet_name = $new_user_data->first_name;
            $this->load->library('SendMail');
            $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
            $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
            $this->session->set_flashdata('message', lang("Account Activate Successfully"));
            return redirect(base_url('admin/users/tutor'));
               
    }




    function tutor_request_reject($user_id)
    {
            $new_user_data = $this->UsersModel->get_user_by_id($user_id);
            if(empty($new_user_data))
            {
                $this->session->set_flashdata('error', lang('invalid_uri_arguments'));
                return redirect(base_url('admin/users'));
            }
            if($new_user_data->role != "user")
            {
                $this->session->set_flashdata('error', lang('invalid_uri_arguments'));
                return redirect(base_url('admin/users'));
            }

            $email_template = get_email_template('reject_tutor_account_request');
            

            $upstatus['user_request_for_tutor'] = '2';

            $this->db->where('id',$user_id)->update('users',$upstatus);
            
            $this->session->language = 'English';
            // build the validation URL
            $user_full_name = $new_user_data->first_name. " ".$new_user_data->last_name;
            $site_name_with_url = '<a href="'.base_url().'">'.$this->settings->site_name.'</a>';
            $email_msg = "Hello $user_full_name ".lang('your_request_for_tutor_has_been_rejected');
            $mail_subject = "Tutot Account request Rejected On ".$this->settings->site_name;
            
            if($email_template)
            {
                $email_msg = str_replace("{new_customer_name}",$user_full_name,$email_template->description);
                $email_msg = str_replace('{contact_firstname}',$new_user_data->first_name,$email_msg);
                $email_msg = str_replace('{contact_lastname}',$new_user_data->last_name,$email_msg);
                $email_msg = str_replace('{contact_email}',$new_user_data->email,$email_msg);
                $email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);
                $email_msg = str_replace("{site_name_with_url}",$site_name_with_url,$email_msg);
                $mail_subject = $email_template->subject;

            }
            

            $mail_to = $new_user_data->email;
            $recipet_name = $new_user_data->first_name;
            $this->load->library('SendMail');
            $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
            $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
            $this->session->set_flashdata('message', lang("tutor_account_request_rejected"));
            return redirect(base_url('admin/users/tutor'));
               
    }

    public function tutor_account_request_info($user_id)
    {
        $response['status'] = "error";
        $response['msg'] = "";

        $user_data = $this->UsersModel->get_user_by_id($user_id);
        if(empty($user_data))
        {
            echo json_encode($response);
            exit;
        }

        $response['status'] = "success";
        $response['msg'] = "success";
        $response['data'] = json_decode(json_encode($user_data), true);
        echo json_encode($response);
        exit;
    }



    /**
     * Add new user
     */
    function add() 
    {
        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', lang('users input username'), 'required|trim|min_length[5]|max_length[30]|callback__check_username[]');
        $this->form_validation->set_rules('first_name', lang('users input first_name'), 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', lang('users input last_name'), 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', lang('users input email'), 'required|trim|max_length[128]|valid_email|callback__check_email[]');
        $this->form_validation->set_rules('language', lang('users input language'), 'required|trim');

        $this->form_validation->set_rules('course_id', 'course', 'required|trim');
        $this->form_validation->set_rules('institution_id', 'institutions', 'required|trim');

        $this->form_validation->set_rules('status', lang('users input status'), 'required');
        $this->form_validation->set_rules('time_accommodation', lang('time_accommodation'), 'required');
        $this->form_validation->set_rules('role', lang('users input role'), 'required');
        $this->form_validation->set_rules('password', lang('users input password'), 'required|trim|min_length[5]');
        $this->form_validation->set_rules('password_repeat', lang('users input password_repeat'), 'required|trim|matches[password]');
        if ($this->form_validation->run() == TRUE) 
        {
            action_not_permitted();
            // save the new user
            $saved = $this->UsersModel->add_user($this->input->post());
            if ($saved) {
                $this->session->set_flashdata('message', sprintf(lang('admin_record_added_successfully'), $this->input->post('first_name', TRUE) . " " . $this->input->post('last_name', TRUE)));
            } else {
                $this->session->set_flashdata('error', sprintf(lang('admin_error_adding_record'), $this->input->post('first_name', TRUE) . " " . $this->input->post('last_name', TRUE)));
            }
            // return to list and display message
            redirect($this->_redirect_url);
        }
        // setup page header data
        $this->set_title(lang('add_user'));
        $data = $this->includes;
        // set content data



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
        $all_courses[0] = lang('other');
        $all_institutions[0] = lang('other');

        $content_data = array('cancel_url' => $this->_redirect_url, 'user' => NULL, 'password_required' => TRUE,'all_courses' => $all_courses,'all_institutions'=>$all_institutions);
        // load views
        $data['content'] = $this->load->view('admin/users/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
    /**
     * Edit existing user
     *
     * @param  int $id
     */
    function edit($id = NULL) {
        // make sure we have a numeric id
        if (is_null($id) OR !is_numeric($id)) {
            redirect($this->_redirect_url);
        }
        // get the data
        $user = $this->UsersModel->get_user($id);
        
        // if empty results, return to list
        if (!$user) {
            redirect($this->_redirect_url);
        }
        // validators
        $this->form_validation->set_error_delimiters($this->config->item('error_delimeter_left'), $this->config->item('error_delimeter_right'));
        $this->form_validation->set_rules('username', lang('users input username'), 'required|trim|min_length[5]|max_length[30]|callback__check_username[' . $user['username'] . ']');
        $this->form_validation->set_rules('first_name', lang('users input first_name'), 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('last_name', lang('users input last_name'), 'required|trim|min_length[2]|max_length[32]');
        $this->form_validation->set_rules('email', lang('users input email'), 'required|trim|max_length[128]|valid_email|callback__check_email[' . $user['email'] . ']');
        $this->form_validation->set_rules('language', lang('users input language'), 'required|trim');
        $this->form_validation->set_rules('course_id', 'course', 'required|trim');
        $this->form_validation->set_rules('institution_id', 'institutions', 'required|trim');

        $this->form_validation->set_rules('status', lang('users input status'), 'required');
        $this->form_validation->set_rules('role', lang('users role'), 'required');
        $this->form_validation->set_rules('time_accommodation', lang('time_accommodation'), 'required');
        $this->form_validation->set_rules('password', lang('users input password'), 'min_length[5]|matches[password_repeat]');
        $this->form_validation->set_rules('password_repeat', lang('users input password_repeat'), 'matches[password]');
        if ($this->form_validation->run() == TRUE) {
            action_not_permitted();
            // save the changes
            $saved = $this->UsersModel->edit_user($this->input->post());
            if ($saved) {
                $this->session->set_flashdata('message', sprintf(lang('admin_record_updated_successfully'), $this->input->post('first_name', TRUE) . " " . $this->input->post('last_name', TRUE)));
            } else {
                $this->session->set_flashdata('error', sprintf(lang('admin_error_during_update_record'), $this->input->post('first_name', TRUE) . " " . $this->input->post('last_name', TRUE)));
            }
            // return to list and display message 
            redirect($this->_redirect_url);
        }
        // setup page header data
        $this->set_title(lang('admin_edit_user'));
        $data = $this->includes;
        // set content data
        

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

        $content_data = array('cancel_url' => $this->_redirect_url, 'user' => $user, 'user_id' => $id, 'password_required' => FALSE,'all_courses' => $all_courses,'all_institutions' => $all_institutions);
        // load views
        $data['content'] = $this->load->view('admin/users/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
    /**
     * Delete a user
     *
     * @param  int $id
     */
    function delete($id = NULL,$user_to_be_asign = 0) 
    {
        action_not_permitted();
       
        $user = $this->UsersModel->get_user($id);

        if($user['role'] == 'tutor' OR $user['is_admin'] == 1)
        {
            if($user_to_be_asign < 0 OR $user_to_be_asign == 0)
            {
                $this->session->set_flashdata('error', lang('please_select_new_user_to_assign_existing_contant'));
                return redirect(base_url('admin/users'));
            }
        }
        
        // make sure we have a numeric id
        if (!is_null($id) OR !is_numeric($id)) 
        {
            // get user details
            if ($user) {
                // soft-delete the user
                $delete = $this->UsersModel->delete_user($id);
                if($delete) 
                {
                    $this->session->set_flashdata('message', sprintf(lang('admin_record_delete_successfully'), $user['first_name'] . " " . $user['last_name']));
                    if($user['role'] = 'tutor' OR $user['is_admin'] = 1)
                    {
                        $this->assign_admin_or_tutor_related_data_to_other($id,$user_to_be_asign);
                    }

                    $this->delete_user_related_data(array($id),NULL);
                } 
                else 
                {
                    $this->session->set_flashdata('error', sprintf(lang('admin_error_during_delete_record'), $user['first_name'] . " " . $user['last_name']));
                }
            } 
            else 
            {
                $this->session->set_flashdata('error', lang('user_not_exist'));
            }
        } 
        else 
        {
            $this->session->set_flashdata('error', lang('user_id_required'));
        }
        // return to list and display message
        redirect($this->_redirect_url);
    }



    private function backup_before_delete($users_id_array, $course_name)
    {
        $this->load->helper('file');
        $back_up = "";

        foreach ($users_id_array as  $id) 
        {
                
                //custom_field_values
                $values_data_array = $this->db->where('rel_id', $id)->get('custom_field_values')->result_array();
                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `custom_field_values` (`field_name`, `form`, `id`, `rel_id`, `value`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {    $i ++;
                        $comma = $i == $values_data_array_count ? "" : ", ";
                        $back_up .= "('".$value_array['field_name']."','".$value_array['form']."','".$value_array['id']."','".$value_array['rel_id']."','".$value_array['value']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }



                //participants
                $values_data_array = $this->db->where('user_id', $id)->get('participants')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `participants` (`completed`, `correct`, `earned_points`, `guest_name`, `id`, `questions`, `quiz_grading_id`, `quiz_id`, `quiz_levels_data`, `quiz_passing_marks`, `started`, `test_language`, `total_attemp`, `user_id`,  `reward_percentage`,  `negative_marking_percentage`,  `marks_for_correct_answer`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";
                        $back_up .= "('".$value_array['completed']."','".$value_array['correct']."','".$value_array['earned_points']."','".$value_array['guest_name']."','".$value_array['id']."','".$value_array['questions']."','".$value_array['quiz_grading_id']."','".$value_array['quiz_id']."','".$value_array['quiz_levels_data']."','".$value_array['quiz_passing_marks']."','".$value_array['started']."','".$value_array['test_language']."','".$value_array['total_attemp']."','".$value_array['user_id']."','".$value_array['reward_percentage']."','".$value_array['negative_marking_percentage']."','".$value_array['marks_for_correct_answer']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                


                //payments
                $values_data_array = $this->db->where('user_id', $id)->get('payments')->result_array();


                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `payments` (`created`, `customer_id`, `email`, `id`, `invoice_no`, `item_name`, `item_price`, `item_price_currency`, `modified`, `name`, `payer_id`, `payment_gateway`, `payment_status`, `purchases_type`, `quiz_id`, `token_no`, `txn_id`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['created']."','".$value_array['customer_id']."','".$value_array['email']."','".$value_array['id']."','".$value_array['invoice_no']."','".$value_array['item_name']."','".$value_array['item_price']."','".$value_array['item_price_currency']."','".$value_array['modified']."','".$value_array['name']."','".$value_array['payer_id']."','".$value_array['payment_gateway']."','".$value_array['payment_status']."','".$value_array['purchases_type']."','".$value_array['quiz_id']."','".$value_array['token_no']."','".$value_array['txn_id']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }



                //post_like
                $values_data_array = $this->db->where('user_id', $id)->get('post_like')->result_array();


                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `post_like` (`added`, `id`, `post_id`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['added']."','".$value_array['id']."','".$value_array['post_id']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }


                //quiz_like
                $values_data_array = $this->db->where('user_id', $id)->get('quiz_like')->result_array();


                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `quiz_like` (`added`, `id`, `quiz_id`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['added']."','".$value_array['id']."','".$value_array['quiz_id']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                

                //quiz_reviews
                $values_data_array = $this->db->where('user_id', $id)->get('quiz_reviews')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `quiz_reviews` (`added`, `id`, `rating`, `rel_id`, `rel_type`, `review_content`, `status`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['added']."','".$value_array['id']."','".$value_array['rating']."','".$value_array['rel_id']."','".$value_array['rel_type']."','".$value_array['review_content']."','".$value_array['status']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                

                //review_likes
                $values_data_array = $this->db->where('user_id', $id)->get('review_likes')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `review_likes` (`added`, `id`, `rel_type`, `review_id`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['added']."','".$value_array['id']."','".$value_array['rel_type']."','".$value_array['review_id']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                
                

                //study_material_like
                $values_data_array = $this->db->where('user_id', $id)->get('study_material_like')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `study_material_like` (`added`, `id`, `study_material_id`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $value_array['study_material_id'] = $value_array['study_material_id'] ? $value_array['study_material_id'] : 0;
                        $back_up .= "('".$value_array['added']."','".$value_array['id']."','".$value_array['study_material_id']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                


                //study_material_user_history
                $values_data_array = $this->db->where('user_id', $id)->get('study_material_user_history')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `study_material_user_history` (`comlete_on`, `id`, `s_m_contant_id`, `s_m_id`, `s_m_section_id`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['comlete_on']."','".$value_array['id']."','".$value_array['s_m_contant_id']."','".$value_array['s_m_id']."','".$value_array['s_m_section_id']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                



                //user_membership_payment
                $values_data_array = $this->db->where('user_id', $id)->get('user_membership_payment')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `user_membership_payment` (`membership_id`, `payment_id`, `purchased`, `user_id`, `validity`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";

                        $back_up .= "('".$value_array['membership_id']."','".$value_array['payment_id']."','".$value_array['purchased']."','".$value_array['user_id']."','".$value_array['validity']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                




                //user_questions
                $values_data_array = $this->db->where('user_id', $id)->get('user_questions')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `user_questions` (`addon_type`, `addon_value`, `choices`, `correct_choice`, `given_answer`, `id`, `image`, `is_correct`, `participant_id`, `question`, `question_id`, `question_paragraph_text`, `question_section_name`, `question_type_is_match`, `queston_choies_type`, `timestamp`, `upload_type`, `user_id`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";


                        $back_up .= "('".$value_array['addon_type']."','".$value_array['addon_value']."','".$value_array['choices']."','".$value_array['correct_choice']."','".$value_array['given_answer']."','".$value_array['id']."','".$value_array['image']."','".$value_array['is_correct']."','".$value_array['participant_id']."','".$value_array['question']."','".$value_array['question_id']."','".$value_array['question_paragraph_text']."','".$value_array['question_section_name']."','".$value_array['question_type_is_match']."','".$value_array['queston_choies_type']."','".$value_array['timestamp']."','".$value_array['upload_type']."','".$value_array['user_id']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }

                





                //users
                $values_data_array = $this->db->where('id', $id)->get('users')->result_array();

                $values_data_array_count = count($values_data_array);

                if($values_data_array_count)
                {
                    $back_up .= "INSERT INTO `users` (`auth_id`, `course_id`, `institution_id`, `created`, `deleted`, `email`, `first_name`, `id`, `image`, `is_admin`, `language`, `last_access`, `last_name`, `login_from`, `password`, `role`, `salt`, `status`, `time_accommodation`, `token`, `updated`, `username`, `validation_code`) VALUES ";
                    $i = 0;
                    foreach ($values_data_array as  $value_array) 
                    {   
                        $i++;
                        $comma = $i == $values_data_array_count ? "" : ", ";


                        $back_up .= "('".$value_array['auth_id']."','".$value_array['course_id']."','".$value_array['institution_id']."','".$value_array['created']."','".$value_array['deleted']."','".$value_array['email']."','".$value_array['first_name']."','".$value_array['id']."','".$value_array['image']."','".$value_array['is_admin']."','".$value_array['language']."','".$value_array['last_access']."','".$value_array['last_name']."','".$value_array['login_from']."','".$value_array['password']."','".$value_array['role']."','".$value_array['salt']."','".$value_array['status']."','".$value_array['time_accommodation']."','".$value_array['token']."','".$value_array['updated']."','".$value_array['username']."','".$value_array['validation_code']."')$comma"; 
                    }
                    $back_up .= "; \n \n \n";
                }
                
        }

        // $this->db->insert_batch('mytable', $values_data_array); 


        $path = "./assets/backup/archive/";
        if(!is_dir($path))
        {
            mkdir($path, 0775, TRUE);
        }
        $file_name = $course_name ? "COURSE_".slugify_string($course_name)."_".count($users_id_array)."_USER_DATA_"."_" : 'USER_'.$id.'_DATA_';

        write_file($path.$file_name.date('Y-m-d-h-i-s')."-".rand().'.sql', $back_up);
        return true;       
    }


    private function delete_user_related_data($users_id_array,$course_name = NULL)
    {
        $this->backup_before_delete($users_id_array , $course_name);

        foreach ($users_id_array as  $id) 
        {         
            $this->db->where('rel_id', $id)->delete('custom_field_values');
            $this->db->where('user_id', $id)->delete('participants');
            $this->db->where('user_id', $id)->delete('payments');
            $this->db->where('user_id', $id)->delete('post_like');
            $this->db->where('user_id', $id)->delete('quiz_reviews');
            $this->db->where('user_id', $id)->delete('review_likes');
            $this->db->where('user_id', $id)->delete('study_material_like');
            $this->db->where('user_id', $id)->delete('study_material_user_history');
            $this->db->where('user_id', $id)->delete('user_membership_payment');
            $this->db->where('user_id', $id)->delete('user_questions');
            $this->db->where('id', $id)->delete('users');


            $quiz_id_array = $this->get_quiz_id_by_user_id($id);
            $material_id_array = $this->get_study_material_by_user($id);
            if($material_id_array)
            {            
                $this->delete_study_related_data($material_id_array);
            }

            if($quiz_id_array)
            {
                $this->db->where_in('id', $quiz_id_array)->delete('quizes');
                $this->db->where_in('quiz_id', $quiz_id_array)->delete('package_quizes'); 
                $this->db->where_in('quiz_id', $quiz_id_array)->delete('quiz_count'); 
                $this->db->where_in('quiz_id', $quiz_id_array)->delete('quiz_like'); 
                $this->db->where_in('forigen_table_id', $quiz_id_array)->where('table','quizes')->delete('translations'); 
                

                $questions_ids = $this->get_questions_ids_by_quiz_ids($quiz_id_array);           
                if($questions_ids)
                {
                    $this->db->where_in('id', $questions_ids)->delete('questions');
                    $this->db->where_in('forigen_table_id', $questions_ids)->where('table','questions')->delete('translations'); 
                }


                
                $reviews_ids =  $this->get_reviews_ids_by_quiz_ids($quiz_id_array);           
                if($reviews_ids)
                {
                    $this->db->where_in('id', $reviews_ids)->delete('quiz_reviews'); 
                    $this->db->where_in('review_id', $reviews_ids)->delete('review_likes');
                }

                
                $participants_ids =  $this->get_participants_ids_by_quiz_ids($quiz_id_array);           
                if($participants_ids)
                {
                    $this->db->where_in('id', $participants_ids)->delete('participants');
                    $this->db->where_in('participant_id', $participants_ids)->delete('user_questions');
                }

            }
        }

        return true;

    }


    function delete_course_related_users($course_id)
    {
        action_not_permitted();
        $users = $this->db->where('course_id',$course_id)->get('users')->result_array();

        
        $this->load->model('CourseModel');
        $course_data = $this->CourseModel->get_course_by_id($course_id);
        $status = $this->CourseModel->delete_course($course_id);
        if($status && $course_data) 
        {
            if($users)
            {
                $users_id_array = array_column($users,"id");
                $this->delete_user_related_data($users_id_array, $course_data->title);
            }

            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/course'));
    }



    private function assign_admin_or_tutor_related_data_to_other($id,$assign_to)
    {
        $quiz_id_array = $this->get_quiz_id_by_user_id($id);
        $material_id_array = $this->get_study_material_by_user($id);
        
        if($quiz_id_array)
        {
            $this->db->set('user_id', $assign_to)->where('user_id', $id)->update('quizes');
        }

        if($material_id_array)
        {            
            $this->db->set('user_id', $assign_to)->where('user_id', $id)->update('study_material');
        }


        return true;

    }

    function get_quiz_id_by_user_id($id) 
    {
        $quiz_array = $this->db->where('user_id', $id)->get('quizes')->result_array();
        $quiz_ids = array();
        if($quiz_array)
        {
            $quiz_ids = array_column($quiz_array,"id");
        }
        return $quiz_ids;
    }

    function get_study_material_by_user($id)
    {
        $data_array = $this->db->where('user_id', $id)->get('study_material')->result_array();
        $material_id_array = array();
        if($data_array)
        {
            $material_id_array = array_column($data_array,"id");
        }
        return $material_id_array;
    }


    function get_questions_ids_by_quiz_ids($quiz_id_array) 
    {
        $questions_array = $this->db->where_in('quiz_id', $quiz_id_array)->get('questions')->result_array();
        $questions_ids = array();
        if($questions_array)
        {
            $questions_ids = array_column($questions_array,"id");
        }
        return $questions_ids;
    }


    function get_participants_ids_by_quiz_ids($quiz_id_array) 
    {
        $participants_array = $this->db->where_in('quiz_id', $quiz_id_array)->get('participants')->result_array();
        $participants_ids = array();
        if($participants_array)
        {
            $participants_ids = array_column($participants_array,"id");
        }
        return $participants_ids;
    }


    function get_reviews_ids_by_quiz_ids($quiz_id_array) 
    {
        $reviews_array = $this->db->where_in('rel_id', $quiz_id_array)->where('rel_type',"quiz")->get('quiz_reviews')->result_array();
        $reviews_ids = array();
        if($reviews_array)
        {
            $reviews_ids = array_column($reviews_array,"id");
        }
        return $reviews_ids;
    }

    function delete_study_related_data($material_id_array)
    {
        if($material_id_array)
        {
            $this->db->where_in('study_material_id', $material_id_array)->delete('package_study_material'); 

            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_content'); 
            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_like'); 
            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_view'); 

        }
        return TRUE;
    }






    /**
     * Export list to CSV
     */
    function export() {
        // get parameters
        $sort = $this->input->get('sort') ? $this->input->get('sort', TRUE) : DEFAULT_SORT;
        $dir = $this->input->get('dir') ? $this->input->get('dir', TRUE) : DEFAULT_DIR;
        // get filters
        $filters = array();
        if ($this->input->get('username')) {
            $filters['username'] = $this->input->get('username', TRUE);
        }
        if ($this->input->get('first_name')) {
            $filters['first_name'] = $this->input->get('first_name', TRUE);
        }
        if ($this->input->get('last_name')) {
            $filters['last_name'] = $this->input->get('last_name', TRUE);
        }
        // get all users
        $users = $this->UsersModel->get_all(0, 0, $filters, $sort, $dir);
        if ($users['total'] > 0) 
        {
            // manipulate the output array
            foreach ($users['results'] as $key => $user) {
                unset($users['results'][$key]['password']);
                unset($users['results'][$key]['deleted']);
                unset($users['results'][$key]['salt']);
                unset($users['results'][$key]['image']);
                unset($users['results'][$key]['validation_code']);
                unset($users['results'][$key]['token']);
                unset($users['results'][$key]['auth_id']);
                if ($user['status'] == 0) 
                {
                    $users['results'][$key]['status'] = lang('admin_inactive');
                } else {
                    $users['results'][$key]['status'] = lang('admin_active');
                }
            }
            // export the file

            array_to_csv($users['results'], "users");
        } else {
            // nothing to export
            $this->session->set_flashdata('error', lang('core_error_no_results'));
            redirect($this->_redirect_url);
        }
        exit;
    }
    /**************************************************************************************
     * PRIVATE VALIDATION CALLBACK FUNCTIONS
     **************************************************************************************/
    /**
     * Make sure username is available
     *
     * @param  string $username
     * @param  string|null $current
     * @return int|boolean
     */
    function _check_username($username, $current) 
    {
        if (trim($username) != trim($current) && $this->UsersModel->username_exists($username)) 
        {
            $this->form_validation->set_message('_check_username', sprintf(lang('username_exist'), $username));
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
     * @param  string|null $current
     * @return int|boolean
     */
    function _check_email($email, $current) {
        if (trim($email) != trim($current) && $this->UsersModel->email_exists($email)) {
            $this->form_validation->set_message('_check_email', sprintf(lang('user_email_exist'), $email));
            return FALSE;
        } else {
            return $email;
        }
    }

    function user_quiz_history($user_id)
    {
        $this->set_title(lang('users_quiz_history_list'));
        $data = $this->includes;
        // set content data
        $content_data = array('this_url' => THIS_URL,'user_id' => $user_id,);
        // load views
        $data['content'] = $this->load->view('admin/users/user_quiz_list', $content_data, TRUE);
        $this->load->view($this->template, $data);        
    }

    function user_quiz_history_list($user_id = false)
    {
        $data = array();
        $list = $this->UsersModel->get_quiz_history($user_id); 

        $no = $_POST['start'];
        foreach ($list as $quiz_history) {
            $no++;
            $row = array();
            $row[] = xss_clean($quiz_history->quiz_title);
            $row[] = json_decode($quiz_history->questions);
            $total_attemp = $quiz_history->total_attemp - $quiz_history->correct;
            $total_attemp = $total_attemp ? $total_attemp : 0;
            $row[] = $total_attemp; 
            $row[] = $quiz_history->correct;
            $wrong = $total_attemp - $quiz_history->correct; 
            $row[] = $wrong;                
            $date_of_quiz = date( "d M Y", strtotime($quiz_history->started));
            $row[] = $date_of_quiz;                
            $button = '<a href="' . base_url("admin/users/summary/". $quiz_history->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1" title="'.lang("quiz_summary").'"><i class="fa fa-eye"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'], 
            "recordsTotal" => $this->UsersModel->count_all($user_id), 
            "recordsFiltered" => $this->UsersModel->count_filtered($user_id), 
            "data" => $data
        );

        //output to json format
        echo json_encode($output);        
    }

    function summary($participant_id = NULL)
    {
        $this->add_external_css(base_url('assets/themes/quizzy/css/table-main.css'));

        $participant_data = $this->UsersModel->get_participant_by_id($participant_id);

        if(empty($participant_data))
        {
            return redirect(base_url('404_override'));
        }

        $this->load->model('TestModel');
        $user_question_data = $this->TestModel->get_user_question_by_participant_id($participant_id);

        
        if(empty($user_question_data))
        {
            $this->session->set_flashdata('error', 'Test Not complet ');
            return redirect(base_url('admin'));
        }

        $quiz_id = $participant_data['quiz_id'];
        $quiz_data = $this->UsersModel->get_quiz_by_id($quiz_id);
        
        $correct = $participant_data['correct'] ? $participant_data['correct'] : 0;
        $total_question = $participant_data['questions'] ? $participant_data['questions'] : 0;
        $total_attemp = $participant_data['total_attemp'] ? $participant_data['total_attemp'] : 0;

        $this->set_title(lang('test_result').": ".$quiz_data->title);
        $this->add_external_js(base_url("/{$this->settings->themes_folder}/quizzy/js/Chart.min.js"));   
        $this->add_external_js(base_url("/{$this->settings->themes_folder}/quizzy/js/autogrow.js"));
        $data = $this->includes;
        $content_data = array('Page_message' => lang('test_result'), 'page_title' => lang('test_result'),'correct' => $correct,'total_question' => $total_question, 'total_attemp' => $total_attemp, 'quiz_data' => $quiz_data, 'participant_data' => $participant_data, 'user_question_data' => $user_question_data );
        $data['content'] = $this->load->view('result', $content_data, TRUE);
        $this->load->view($this->template, $data);      
    }

    function backup() {        
        $this->set_title(lang('users_or_courses_backups'));
        $data = $this->includes;
        $path = "./assets/backup/archive/";
        $backups_array = directory_map($path);

        $content_data = array('backups_array' => $backups_array);

        $data['content'] = $this->load->view('admin/users/backup', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function remove_backup($file_name)
    {
        $path = "./assets/backup/archive/";
        if(is_file($path.$file_name))
        {
            unlink($path.$file_name);
            $this->session->set_flashdata('message', lang("course_backup_file_unlink_successfuly"));
        } 
        else
        {
            $this->session->set_flashdata('error', lang("error_during_unlink_course_backup_file...!"));
        }
        redirect(base_url('admin/users/backup')); 
    }


    function download_backup($file_name)
    {
        $this->load->helper('download');
        $path = "./assets/backup/archive/";
        if(is_file($path.$file_name))
        {
            force_download($path.$file_name,NULL);
        } 
        else
        {
            $this->session->set_flashdata('error', lang("course_backup_file_dose_not_exist...!"));
            redirect(base_url('admin/users/backup')); 

        }
    }

    function get_user_custom_field()
    {
        $response = array();
        $response['status'] = '';
        $response['data'] = '';
        $response['message'] = '';
        $user_id = $this->input->post('user_id');
        $get_custom_field_by_user_id = $this->UsersModel->get_custom_field_by_id($user_id);
        
        if($get_custom_field_by_user_id)
        {
            $response['status'] = 'success';
            $content_data['custom_field'] = $get_custom_field_by_user_id;
            $response['data'] = $this->load->view('admin/users/user_custom_field', $content_data, TRUE);
        }
        
        echo json_encode($response);
    }
}
