<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Section extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_js_theme('section.js');

        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');

        $this->load->model('SectionModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('Resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/section'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");
    }

    function index() {        
        $this->set_title(lang('Section List'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/section/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add() 
    {
        $featured_image = NULL;
        $this->form_validation->set_rules('title', lang('Section Title'), 'required|trim|is_unique[section.title]');

        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $section_content = array();
            $section_content['title'] = $this->input->post('title',TRUE);
            $section_content['order'] = $this->input->post('order',TRUE);
            $section_content['added'] =  date('Y-m-d H:i:s');

            $section_id = $this->SectionModel->insert_section($section_content);

            if($section_id)
            {                
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));                  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
             redirect(base_url('admin/section'));

        }
            
        $this->set_title(lang('Add Section'));
        $data = $this->includes;

        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/section/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($section_id = NULL) 
    {
        if(empty($section_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/section'));
        }

        $section_data = $this->SectionModel->get_section_by_id($section_id);

        if(empty($section_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/section'));
        }

        $title_unique = $this->input->post('title')  != $section_data->title ? '|is_unique[section.title]' : '';

        $this->form_validation->set_rules('title', lang('Title'), 'required|trim'.$title_unique);

        
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $section_content = array();
            $section_content['title'] = $this->input->post('title',TRUE);
            $section_content['order'] = $this->input->post('order',TRUE);
            $section_content['updated'] =  date('Y-m-d H:i:s');

            $update_status = $this->SectionModel->update_section($section_id, $section_content);

            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }

            redirect(base_url('admin/section'));
        }

        $this->set_title(lang('Update Secton'));
        $data = $this->includes;

        $content_data = array('section_id' => $section_id, 'section_data' => $section_data);
        // load views
        $data['content'] = $this->load->view('admin/section/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function delete($section_id = NULL)
    {
        action_not_permitted();
        $status = $this->SectionModel->delete_section($section_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/section'));
    }
    

    function ajax_list() 
    {
        $data = array();
        $list = $this->SectionModel->get_sections();

        $no = $_POST['start'];
        foreach ($list as $section_data) {
            $no++;
            $row = array();
            // p($section_data);
            $row[] = $no;
            $row[] = ucfirst($section_data->title);
            $row[] = ucfirst($section_data->order);
            $button = '<a href="' . base_url("admin/section/update/". $section_data->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1">
            <i class="fas fa-pencil-alt"></i>
            </a>';

            $button.= '<a href="' . base_url("admin/section/delete/" . $section_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            
            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->SectionModel->count_all(), "recordsFiltered" => $this->SectionModel->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }
}