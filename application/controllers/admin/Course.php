<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Course extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_js_theme('course.js');

        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');

        $this->load->model('CourseModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/course'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");

        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_url'));
            return redirect(base_url('admin/dashboard'));
        }
    }

    function index() {        
        $this->set_title(lang('course'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/course/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function add() 
    {
        $this->form_validation->set_rules('title', lang('title'), 'required|trim|is_unique[courses.title]');
        $this->form_validation->set_rules('description', lang('description'), 'required|trim');

        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $course_content = array();

            $course_content['title'] = $this->input->post('title',TRUE);
            $course_content['description'] = $this->input->post('description',TRUE);
            $course_content['added'] =  date('Y-m-d H:i:s');

            $course_id = $this->CourseModel->insert_course($course_content);

            if($course_id)
            {                
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));                  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
             redirect(base_url('admin/course'));

        }
            
        $this->set_title(lang('add_course'));
        $data = $this->includes;

        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/course/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($course_id = NULL) 
    {
        if(empty($course_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/course'));
        }

        $course_data = $this->CourseModel->get_course_by_id($course_id);

        if(empty($course_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/course'));
        }

        $title_unique = $this->input->post('title')  != $course_data->title ? '|is_unique[courses.title]' : '';

        $this->form_validation->set_rules('title', lang('title'), 'required|trim'.$title_unique);
        $this->form_validation->set_rules('description', lang('description'), 'required|trim');
        
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $course_content = array();
            $course_content['title'] = $this->input->post('title',TRUE);
            $course_content['description'] = $this->input->post('description',TRUE);
            $course_content['updated'] =  date('Y-m-d H:i:s');

            $update_status = $this->CourseModel->update_course($course_id, $course_content);

            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }

            redirect(base_url('admin/course'));
        }

        $this->set_title(lang('course_update'));
        $data = $this->includes;

        $content_data = array('course_id' => $course_id, 'course_data' => $course_data);
        // load views
        $data['content'] = $this->load->view('admin/course/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function delete($course_id = NULL)
    {
        action_not_permitted();
        $status = $this->CourseModel->delete_course($course_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/course'));
    }



    function list() 
    {
        $data = array();
        $list = $this->CourseModel->get_courses();

        $no = $_POST['start'];
        foreach ($list as $course_data) {
            $no++;
            $row = array();
            // p($course_data);
            $row[] = $no;
            $row[] = ucfirst($course_data->title);

            $button = '<a href="' . base_url("admin/course/update/". $course_data->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1">
            <i class="fas fa-pencil-alt"></i>
            </a>';

            
            $button.= '<a href="' . base_url("admin/course/delete/" . $course_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-warning btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            
            $button.= '<a href="' . base_url("admin/users/delete_course_related_users/" . $course_data->id) . '" data-toggle="tooltip"  title="'.lang("delete_all_related_users").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->CourseModel->count_all(), "recordsFiltered" => $this->CourseModel->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }



}