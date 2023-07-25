<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Institution extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_js_theme('institution.js');

        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');

        $this->load->model('InstitutionModal');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('Resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/institution'));
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
        $this->set_title(lang('institution_list'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/institution/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add() 
    {
        $logo = NULL;
        $this->form_validation->set_rules('title', lang('title'), 'required|trim|is_unique[institutions.title]');
        $this->form_validation->set_rules('description', lang('description'), 'required|trim');
        $this->form_validation->set_rules('address', lang('address'), 'required|trim');
        $instute_courses_array = $this->input->post('instute_courses',TRUE);
        
        if(empty($instute_courses_array))
        {
	        $this->form_validation->set_rules('instute_courses', lang('instute_courses'), 'required|trim');
        }
        if (empty($_FILES['logo']['name'])) 
        {
            $this->form_validation->set_rules('logo_error', lang('logo'), 'required|trim');
        }
        else
        {
            $config['upload_path'] = "./assets/images/institution/";


            if (!is_dir($config['upload_path'])) 
            {
                mkdir($config['upload_path'], 0775, TRUE);
            }

            $config['allowed_types'] = 'jpg|png|bmp|jpeg';
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('logo')) 
            {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                $this->form_validation->set_rules('logo_error', lang('logo'), 'required|trim');
            }

            $file = $this->upload->data();
            $logo = $file['file_name'];
        
        }

        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $institution_content = array();
            $institution_content['title'] = $this->input->post('title',TRUE);
            $institution_content['description'] = $this->input->post('description',TRUE);
            $institution_content['address'] = $this->input->post('address',TRUE);

            if($logo)
            {                
                $institution_content['logo'] = $logo;
            }

            $institution_content['added'] =  date('Y-m-d H:i:s');

            $institution_id = $this->InstitutionModal->insert_institution($institution_content);

            if($institution_id)
            {      
                if($instute_courses_array && is_array($instute_courses_array))
                {
                	foreach ($instute_courses_array as $course_id) 
                	{
                		$institution_courses_content = array();
                		$institution_courses_content['course_id'] = $course_id;
                		$institution_courses_content['instute_id'] = $institution_id;
    		            $institution_id = $this->InstitutionModal->insert_institution_courses($institution_courses_content);
                	}
                }
          
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));                  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
             redirect(base_url('admin/institution'));

        }
            
        $all_courses = $this->InstitutionModal->get_all_courses();
        $institution_course_array = array(); 

        $this->set_title(lang('add_institution'));
        $data = $this->includes;

        $content_data = array('all_courses' => $all_courses, 'institution_course_array' => $institution_course_array);
        // load views
        $data['content'] = $this->load->view('admin/institution/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($institution_id = NULL) 
    {
        if(empty($institution_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/institution'));
        }

        $institution_data = $this->InstitutionModal->get_institution_by_id($institution_id);

        if(empty($institution_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/institution'));
        }

        $title_unique = $this->input->post('title')  != $institution_data->title ? '|is_unique[institutions.title]' : '';

        $this->form_validation->set_rules('title', lang('title'), 'required|trim'.$title_unique);
        $this->form_validation->set_rules('description', lang('description'), 'required|trim');
        $this->form_validation->set_rules('address', lang('address'), 'required|trim');
        $instute_courses_array = $this->input->post('instute_courses',TRUE);
        
        if(empty($instute_courses_array))
        {
	        $this->form_validation->set_rules('instute_courses', lang('instute_courses'), 'required|trim');
        }


        if (empty($_FILES['logo']['name']) && empty($institution_data->logo)) 
        {
            $this->form_validation->set_rules('logo_error',lang('logo'), 'required|trim');
        }
        
        $logo = NULL;
        if(isset($_FILES['logo']['name']) && $_FILES['logo']['name'])
        {
            $new_name = time().$_FILES["logo"]['name'];
            $config['upload_path'] = "./assets/images/institution/";

            if (!is_dir($config['upload_path'])) 
            {
                mkdir($config['upload_path'], 0775, TRUE);
            }

            $config['allowed_types'] = 'jpg|png|bmp|jpeg';
            $config['file_name'] = $new_name;
            $this->load->library('upload', $config);

            if (!$this->upload->do_upload('logo')) 
            {
                $error = $this->upload->display_errors();
                $this->session->set_flashdata('error', $error);
                $this->form_validation->set_rules('logo_error', lang('logo'), 'required|trim');
            }

            $file = $this->upload->data();
            $logo = $file['file_name'];

        }
        
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $institution_content = array();
            
             
            $institution_content = array();
            $institution_content['title'] = $this->input->post('title',TRUE);
            $institution_content['description'] = $this->input->post('description',TRUE);
            $institution_content['address'] = $this->input->post('address',TRUE);

            if($logo)
            {                
                $institution_content['logo'] = $logo;
            }

            $institution_content['updated'] =  date('Y-m-d H:i:s');

            $status = $this->InstitutionModal->update_institution($institution_id, $institution_content);

            if($status)
            {

            	if($instute_courses_array && is_array($instute_courses_array))
                {	
					$this->InstitutionModal->delete_courses_by_institution_id($institution_id);

                	foreach ($instute_courses_array as $course_id) 
                	{
                		$institution_courses_content = array();
                		$institution_courses_content['course_id'] = $course_id;
                		$institution_courses_content['instute_id'] = $institution_id;
    		            $institution_id = $this->InstitutionModal->insert_institution_courses($institution_courses_content);
                	}
                }

                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }

            redirect(base_url('admin/institution'));
        }

        $this->set_title(lang('update_institution'));
        $data = $this->includes;

        $all_courses = $this->InstitutionModal->get_all_courses();
        $institution_course_array = $this->InstitutionModal->get_courses_by_institution_id($institution_id); 

        $content_data = array('institution_id' => $institution_id, 'institution_data' => $institution_data,'institution_course_array' => $institution_course_array, 'all_courses' => $all_courses);
        // load views
        $data['content'] = $this->load->view('admin/institution/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function delete($institution_id = NULL)
    {
        action_not_permitted();
        $status = $this->InstitutionModal->delete_institution($institution_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/institution'));
    }

    function list() 
    {
        $data = array();
        $list = $this->InstitutionModal->get_institution();

        $no = $_POST['start'];
        foreach ($list as $institution_data) {
            $no++;
            $row = array();
            // p($institution_data);
            $row[] = $no;
            $row[] = ucfirst($institution_data->title);

            $institution_logo_dir = $institution_data->logo ? base_url('assets/images/institution/').$institution_data->logo : base_url('assets/default/default.png'); 
            $institution_logo_img = '<img class="image_preview popup listing_img" src="'.$institution_logo_dir.'">';

            $row[] = $institution_logo_img;
            $button = '<a href="' . base_url("admin/institution/update/". $institution_data->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1">
            <i class="fas fa-pencil-alt"></i>
            </a>';

 
            $button.= '<a href="' . base_url("admin/institution/delete/" . $institution_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->InstitutionModal->count_all(), "recordsFiltered" => $this->InstitutionModal->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }
}