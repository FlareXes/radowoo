<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Membership extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('membership.js');
        // load the language files 
        // load the category model 
        $this->load->model('QuizModel');
        $this->load->model('MembershipModel');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/membership'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);

        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }
    }

    function index() {

        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('membership_list'));

        $data = $this->includes;
        $content_data = array();
        // load views 
        $data['content'] = $this->load->view('admin/membership/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add()
    {   
        $this->form_validation->set_rules('title', 'Title', 'required|trim|is_unique[membership.title]');

        if ($this->form_validation->run() == false)  
        {
            $this->form_validation->error_array();
        }
        else
        {
            $category_id = $this->input->post('category_id',TRUE) > 0 ? $this->input->post('category_id',TRUE) : 0;
            $membership_content = array();
            $membership_content['title'] = $this->input->post('title',TRUE);
            $membership_content['amount'] = $this->input->post('amount',TRUE);
            $membership_content['duration'] = $this->input->post('duration',TRUE);
            $membership_content['description'] = $this->input->post('description',TRUE);
            $membership_content['category_id'] = $category_id;
            
            $membership_id = $this->MembershipModel->insert_membership($membership_content);

            if($membership_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }

            redirect(base_url('admin/membership'));
        }


        $all_category = $this->QuizModel->get_all_category();
        $category_data = array();
        foreach ($all_category as $category_array)
        {
            $category_data[''] = lang('select_category');
            $category_data[$category_array->id] = $category_array->category_title;
        }
        $this->set_title(lang('admin_add_membership'));
        $data = $this->includes;
        $content_data = array('category_data' => $category_data);
        // load views 
        $data['content'] = $this->load->view('admin/membership/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function membership_list() {
        $data = array();
        $list = $this->MembershipModel->get_membership();

        $no = $_POST['start'];
        foreach ($list as $membership) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($membership->title);
            $row[] = ucfirst($membership->duration);
            $row[] = ucfirst($membership->amount);
            $button = '<a href="' . base_url("admin/membership/update/". $membership->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';
                        
            $button.= '<a href="' . base_url("admin/membership/delete/" . $membership->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->MembershipModel->count_all(), "recordsFiltered" => $this->MembershipModel->count_filtered(), "data" => $data,);
        
        //output to json format
        echo json_encode($output);
    }

    function update($membership_id = NULL) 
    {
        if(empty($membership_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('admin/membership'));
        }

        $membership_data = $this->MembershipModel->get_membership_by_id($membership_id);

        if(empty($membership_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('admin/membership'));
        }

        $title_unique = $this->input->post('title')  != $membership_data->title ? '|is_unique[membership.title]' : '';
        $this->form_validation->set_rules('title', 'Title', 'required|trim'.$title_unique);
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $category_id = $this->input->post('category_id',TRUE) > 0 ? $this->input->post('category_id',TRUE) : 0;
            $membership_content = array();
            $membership_content['title'] = $this->input->post('title',TRUE);
            $membership_content['amount'] = $this->input->post('amount',TRUE);
            $membership_content['duration'] = $this->input->post('duration',TRUE);
            $membership_content['description'] = $this->input->post('description',TRUE);
            $membership_content['category_id'] = $category_id;

            $update_status = $this->MembershipModel->update_membership($membership_id, $membership_content);

            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/membership'));
        }

        $this->set_title(lang('admin_edit_membership'));
        $data = $this->includes;
        $all_category = $this->QuizModel->get_all_category();
        $category_data = array();
        foreach ($all_category as $category_array) 
        {
            $category_data[''] = lang('select_category');
            $category_data[$category_array->id] = $category_array->category_title;
        }

        $membership_data = json_decode(json_encode($membership_data),TRUE);
        $content_data = array('membership_id' => $membership_id, 'membership_data' => $membership_data,'category_data' => $category_data);
        // load views
        $data['content'] = $this->load->view('admin/membership/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function delete($membership_id = NULL)
    {
        action_not_permitted();
        $status = $this->MembershipModel->delete_membership($membership_id); 

        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        
        redirect(base_url('admin/membership'));
    }
}