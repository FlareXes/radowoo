<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Paragraph extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');
        $this->add_css_theme('select2.min.css');
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_js_theme('paragraph.js');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');
        $this->load->model('ParagraphModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('Resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/paragraph'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");


        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }
        
    }
    function index() {        
        $this->set_title(lang('Paragraph List'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/paragraph/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
    function add() 
    {
        $featured_image = NULL;
        $this->form_validation->set_rules('title', lang('ParagraphTitle'), 'required|trim|is_unique[paragraph.title]');
        $this->form_validation->set_rules('content', lang('Paragraph Content'), 'required|trim');
        $this->form_validation->set_rules('order', lang('Paragraph Order'), 'required|trim');

        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $order = $this->input->post('order',TRUE) ? $this->input->post('order',TRUE) : 0;
            $paragraphcontent = array();
            $paragraphcontent['title'] = $this->input->post('title',TRUE);
            $paragraphcontent['order'] = $order;
            $paragraphcontent['content'] = $this->input->post('content',TRUE);
            $paragraphcontent['added'] =  date('Y-m-d H:i:s');
  
            $paragraph_id = $this->ParagraphModel->insert_paragraph($paragraphcontent);
            if($paragraph_id)
            {                
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));                  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
             redirect(base_url('admin/paragraph'));
        }
            
        $this->set_title(lang('Add Paragraph'));
        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/paragraph/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function update($paragraph_id = NULL) 
    {
        if(empty($paragraph_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/paragraph'));
        }
        $paragraph_data = $this->ParagraphModel->get_paragraph_by_id($paragraph_id);
        if(empty($paragraph_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/paragraph'));
        }
        $title_unique = $this->input->post('title')  != $paragraph_data->title ? '|is_unique[paragraph.title]' : '';

        $this->form_validation->set_rules('title', lang('Title'), 'required|trim'.$title_unique);
        $this->form_validation->set_rules('content', lang('Paragraph Content'), 'required|trim');
        $this->form_validation->set_rules('order', lang('Paragraph Order'), 'required|trim');


        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {

            $order = $this->input->post('order',TRUE) ? $this->input->post('order',TRUE) : 0;
            $paragraphcontent = array();
            $paragraphcontent['title'] = $this->input->post('title',TRUE);
            $paragraphcontent['order'] = $order;
            $paragraphcontent['content'] = $this->input->post('content',TRUE);
            $paragraphcontent['updated'] =  date('Y-m-d H:i:s');

            $update_status = $this->ParagraphModel->update_paragraph($paragraph_id, $paragraphcontent);
            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/paragraph'));
        }
        $this->set_title(lang('Update Paragraph'));
        $data = $this->includes;
        $content_data = array('paragraph_id' => $paragraph_id, 'paragraph_data' => $paragraph_data);
        // load views
        $data['content'] = $this->load->view('admin/paragraph/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function delete($paragraph_id = NULL)
    {
        action_not_permitted();
        $status = $this->ParagraphModel->delete_paragraph($paragraph_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/paragraph'));
    }
    function ajax_list() 
    {
        $data = array();
        $list = $this->ParagraphModel->get_paragraph();
        $no = $_POST['start'];
        foreach ($list as $paragraph_data) {
            $no++;
            $row = array();
            // p($paragraph_data);
            $row[] = $no;
            $row[] = ucfirst($paragraph_data->title);
            $row[] = ucfirst($paragraph_data->order);
            $button = '<a href="' . base_url("admin/paragraph/update/". $paragraph_data->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1">
            <i class="fas fa-pencil-alt"></i>
            </a>';

  
            $button.= '<a href="' . base_url("admin/paragraph/delete/" . $paragraph_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            
            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->ParagraphModel->count_all(), "recordsFiltered" => $this->ParagraphModel->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }
}