<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Template_Controller extends Admin_Controller {
	function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('email_template.js');
        // load the language files  
        // load the category model
        $this->load->model('TemplateModel');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/template'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);


        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }
    }

    function index() {
    	$this->set_title(lang('email_template_list'));
        
    	$data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/email/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function email_template_list() 
    {
        $data = array();
        $list = $this->TemplateModel->get_email_template();
        $no = $_POST['start'];
        foreach ($list as $template) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($template->subject);
            $button = '<a href="' . base_url("admin/template/update/". $template->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';
                        
            //$button.= '<a href="' . base_url("admin/template/delete/" . $template->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->TemplateModel->count_all(), "recordsFiltered" => $this->TemplateModel->count_filtered(), "data" => $data,);
        
        //output to json format
        echo json_encode($output);
    }
      

    function update($email_template_id = NULL)
    {
        if(empty($email_template_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('admin/template'));
        }

        $email_template_data = $this->TemplateModel->get_email_template_by_id($email_template_id);

        if(empty($email_template_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('admin/template'));
        }

        $subject_unique = $this->input->post('subject')  != $email_template_data->subject ? '|is_unique[email_template.subject]' : '';
        $this->form_validation->set_rules('subject', 'Subject', 'required|trim'.$subject_unique);
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $template_content = array();
            $template_content['subject'] = $this->input->post('subject',TRUE);
            $template_content['description'] = $this->input->post('description',TRUE);

            $update_status = $this->TemplateModel->update_email_template($email_template_id, $template_content);

            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/template'));
        }

        $this->set_title(lang('admin_edit_email_template'));
        $data = $this->includes;

        $email_template_data = json_decode(json_encode($email_template_data),TRUE);
        $content_data = array('email_template_id' => $email_template_id, 'email_template_data' => $email_template_data,);
        // load views
        $data['content'] = $this->load->view('admin/email/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }  
}
