<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Custom_fields extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('jquery-ui.min-1.12.1.css');
        $this->add_js_theme('jquery-ui-1.12.js');

        $this->add_css_theme('select2.min.css');


        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_js_theme('customfield.js');

        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');

        $this->load->model('CustomfieldModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('Resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/custom_fields'));
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
        $this->set_title(lang('admin_custom_fields_list'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/custom_fields/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add() 
    {
        $customfields = $this->db->where('form','registration')->order_by('field_order', 'asc')->get('custom_fields')->result();

        $this->form_validation->set_rules('field_label', lang('admin_field_label'), 'required|trim');
        $this->form_validation->set_rules('form', lang('admin_field_form'), 'required|trim');
        $this->form_validation->set_rules('field_help_text', lang('admin_field_help_text'), 'trim');
        $this->form_validation->set_rules('field_placeholder', lang('admin_field_placeholder'), 'trim');
        $this->form_validation->set_rules('width', lang('admin_field_width'), 'required|trim');
        $this->form_validation->set_rules('field_type', lang('admin_field_type'), 'required|trim');
        $this->form_validation->set_rules('field_order', lang('admin_field_order'), 'required|trim');
        // if($customfields && $_POST)
        // {
        //     foreach ($customfields as  $customfield) 
        //     {
        //         if($customfield->is_required == 1 && empty($this->input->post($customfield->field_name)))
        //         {
        //             if($customfield->field_type == 'file')
        //             {
        //                 if(isset($_FILES[$customfield->field_name]['name']))
        //                 {
        //                     $response = $this->upload_file($customfield->field_name);
        //                     if($response['status'] == true)
        //                     {
        //                         $cf_files_name[$customfield->field_name] = $response['name'];
        //                     }
        //                     else
        //                     {
        //                         $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
        //                         $this->session->set_flashdata('error', $customfield->field_label.' '. $response['msg']);
        //                     }
        //                 }
        //                 else
        //                 {
        //                     $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
        //                     $this->session->set_flashdata('error', $customfield->field_label.' Is Required');
        //                 }
        //             }
        //             else
        //             {
        //                 $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
        //             }
        //         }
        //     }
        // }

        if ($this->input->post('field_type',TRUE) == 'select' OR $this->input->post('field_type',TRUE) == 'checkbox')
        {
            $this->form_validation->set_rules('field_options[]', lang('admin_field_options'), 'required|trim');
        }

        $option_array = $this->input->post('field_options',TRUE) ? $this->input->post('field_options',TRUE) : array();
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {

            $c_f_content = array();
            $c_f_content['field_name'] = 'cf_'.date('Ymdhism');
            $c_f_content['field_label'] = $this->input->post('field_label',TRUE);
            $c_f_content['form'] = $this->input->post('form',TRUE);
            $c_f_content['field_help_text'] = $this->input->post('field_help_text',TRUE);
            $c_f_content['field_placeholder'] = $this->input->post('field_placeholder',TRUE);
            $c_f_content['width'] =  $this->input->post('width');
            $c_f_content['field_type'] =  $this->input->post('field_type');
            $c_f_content['field_order'] =  $this->input->post('field_order');
            $c_f_content['is_required'] =  $this->input->post('is_required');
            $c_f_content['status'] =  $this->input->post('status');
            $c_f_content['field_options'] = json_encode($option_array);
            $c_f_content['added'] = date('Y-m-d H:i:s');
            $c_f_content['deleted'] = 0;

            $c_f_id = $this->CustomfieldModel->insert_custom_fields($c_f_content);

            if($c_f_id)
            {        


            // if($customfields)
	            // {
	            //     $customfield_val_array = [];
	            //     foreach ($customfields as  $customfield) 
	            //     {
	            //         $customfield_val = [];
	            //         if($customfield->field_type == 'checkbox')
	            //         {
	            //             $value = $this->input->post("$customfield->field_name") && is_array($this->input->post("$customfield->field_name")) ? $this->input->post("$customfield->field_name") : array();
	            //             $value = json_encode($value);
	            //         }
	            //         else if($customfield->field_type == 'file')
	            //         {
	            //             $value = isset($cf_files_name[$customfield->field_name]) ? $cf_files_name[$customfield->field_name] : NULL;
	            //         }
	            //         else
	            //         {
	            //             $value = $this->input->post("$customfield->field_name") ? $this->input->post("$customfield->field_name") : NULL;
	            //         }

	            //         $customfield_val['field_name'] = $customfield->field_name;
	            //         $customfield_val['rel_id'] = $c_f_id;
	            //         $customfield_val['value'] = $value;
	            //         $customfield_val_array[] = $customfield_val;
	            //     }

	            //     if($customfield_val_array)
	            //     {
	            //         $this->db->insert_batch('custom_field_values', $customfield_val_array); 
	            //     }
            // }


                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));                  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
             redirect(base_url('admin/custom_fields'));

        }
            
        $this->set_title(lang('admin_add_custom_fields'));
        $data = $this->includes;
        $content_data = array();
        $content_data['customfields'] = $customfields;
        $content_data['option_array'] = $option_array;
        // load views
        $data['content'] = $this->load->view('admin/custom_fields/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($c_f_id = NULL) 
    {
        $customfields = $this->db->select('custom_fields.*,(select value from custom_field_values where custom_field_values.field_name = custom_fields.field_name AND rel_id = '.$c_f_id.') as value')->where('form','registration')->order_by('field_order', 'asc')->get('custom_fields')->result();

        if(empty($c_f_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/custom_fields'));
        }

        $custom_field_data = $this->CustomfieldModel->get_custom_fields_by_id($c_f_id);

        if(empty($custom_field_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/custom_fields'));
        }

        $this->form_validation->set_rules('field_label', lang('admin_field_label'), 'required|trim');
        $this->form_validation->set_rules('form', lang('admin_field_form'), 'required|trim');
        $this->form_validation->set_rules('field_help_text', lang('admin_field_help_text'), 'trim');
        $this->form_validation->set_rules('field_placeholder', lang('admin_field_placeholder'), 'trim');
        $this->form_validation->set_rules('width', lang('admin_field_width'), 'required|trim');
        $this->form_validation->set_rules('field_type', lang('admin_field_type'), 'required|trim');
        $this->form_validation->set_rules('field_order', lang('admin_field_order'), 'required|trim');

        if ($this->input->post('field_type',TRUE) == 'select' OR $this->input->post('field_type',TRUE) == 'checkbox')
        {
            $this->form_validation->set_rules('field_options[]', lang('admin_field_options'), 'required|trim');
        }

        $option_array = $this->input->post('field_options',TRUE) ? $this->input->post('field_options',TRUE) : array();


	    // if($customfields && $_POST)
	        // {
	        //     foreach ($customfields as  $customfield) 
	        //     {
	        //         if($customfield->is_required == 1 && empty($this->input->post($customfield->field_name)))
	        //         {
	        //             if($customfield->field_type == 'file')
	        //             {
	        //                 if(isset($_FILES[$customfield->field_name]['name']))
	        //                 {
	        //                     $response = $this->upload_file($customfield->field_name);
	        //                     if($response['status'] == true)
	        //                     {
	        //                         $cf_files_name[$customfield->field_name] = $response['name'];
	        //                     }
	        //                     else
	        //                     {
	        //                         $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
	        //                         $this->session->set_flashdata('error', $customfield->field_label.' '. $response['msg']);
	        //                     }
	        //                 }
	        //                 else if(empty($customfield->value))
	        //                 {
	        //                     $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
	        //                     $this->session->set_flashdata('error', $customfield->field_label.' Is Required');
	        //                 }
	        //                 else
	        //                 {
	        //                     $cf_files_name[$customfield->field_name] = $customfield->value;
	        //                 }
	        //             }
	        //             else
	        //             {
	        //                 $this->form_validation->set_rules($customfield->field_name, $customfield->field_label, 'required|trim');
	        //             }
	        //         }
	        //     }
	    // }

        
        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            
            $c_f_content = array();
            $c_f_content['field_label'] = $this->input->post('field_label',TRUE);
            $c_f_content['form'] = $this->input->post('form',TRUE);
            $c_f_content['field_help_text'] = $this->input->post('field_help_text',TRUE);
            $c_f_content['field_placeholder'] = $this->input->post('field_placeholder',TRUE);
            $c_f_content['width'] =  $this->input->post('width');
            $c_f_content['field_type'] =  $this->input->post('field_type');
            $c_f_content['field_order'] =  $this->input->post('field_order');
            $c_f_content['is_required'] =  $this->input->post('is_required');
            $c_f_content['status'] =  $this->input->post('status');
            $c_f_content['field_options'] = json_encode($option_array);
            $c_f_content['updated'] =  date('Y-m-d H:i:s');

            $page_update_status = $this->CustomfieldModel->update_custom_fields($c_f_id, $c_f_content);

            if($page_update_status)
            {

                // if($customfields)
                // {
                //     $customfield_val_array = [];
                //     foreach ($customfields as  $customfield) 
                //     {
                //         $customfield_val = [];
                //         if($customfield->field_type == 'checkbox')
                //         {
                //             $value = $this->input->post("$customfield->field_name") && is_array($this->input->post("$customfield->field_name")) ? $this->input->post("$customfield->field_name") : array();
                //             $value = json_encode($value);
                //         }
                //         else if($customfield->field_type == 'file')
                //         {
                //             $value = isset($cf_files_name[$customfield->field_name]) ? $cf_files_name[$customfield->field_name] : NULL;
                //         }
                //         else
                //         {
                //             $value = $this->input->post("$customfield->field_name") ? $this->input->post("$customfield->field_name") : NULL;
                //         }

                //         $customfield_val['field_name'] = $customfield->field_name;
                //         $customfield_val['rel_id'] = $c_f_id;
                //         $customfield_val['value'] = $value;
                //         $customfield_val_array[] = $customfield_val;
                //     }

                //     if($customfield_val_array)
                //     { 
                //         $this->db->where('rel_id',$c_f_id)->delete('custom_field_values'); 
                //         $this->db->insert_batch('custom_field_values', $customfield_val_array); 
                //     }
                // }

                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }

            redirect(base_url('admin/custom_fields'));
        }
        if(empty($option_array))
        {
            $option_array = json_decode($custom_field_data->field_options);
        }

        $this->set_title(lang('admin_custom_fields_update'));
        $data = $this->includes;
      
        $content_data['c_f_id'] = $c_f_id;        
        $content_data['customfields'] = $customfields;        
        $content_data['c_f_data'] = $custom_field_data;        
        $content_data['option_array'] = $option_array;        
        // load views
        $data['content'] = $this->load->view('admin/custom_fields/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }


    function upload_file($file_name) 
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



    public function copy($c_f_id = NULL) 
    {
        action_not_permitted();
        if(empty($c_f_id))
        {
           $this->session->set_flashdata('error', lang('invalid_url')); 
           redirect(base_url('admin/custom_fields'));
        }

        $custom_field_data = $this->CustomfieldModel->get_custom_fields_by_id($c_f_id);

        if(empty($custom_field_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/custom_fields'));
        }

        $page_name_count = $this->CustomfieldModel->custom_fields_name_like_this($custom_field_data->title);
        $slug_count = $this->CustomfieldModel->page_slug_like_this($title_slug, NULL);
        
        $page_name_count = $page_name_count > 0 ? "-".$page_name_count : '';
        
        $c_f_content = array();

        $c_f_content['title'] = $custom_field_data->title.'-copy'.$page_name_count;
        $c_f_content['slug'] = $custom_field_data->slug;
        $c_f_content['content'] = $custom_field_data->content;
        $c_f_content['content'] = $custom_field_data->on_menu;
        $c_f_content['featured_image'] = NULL;
        $c_f_content['added'] =  date('Y-m-d H:i:s');

        $page_new_id = $this->CustomfieldModel->insert_custom_fields($c_f_content);
       
        if($page_new_id)
        {
          $this->session->set_flashdata('message', lang('record_copied_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_copying_record')); 
        } 

        redirect(base_url('admin/custom_fields'));
    }

    function delete($c_f_id = NULL)
    {
        action_not_permitted();
        $status = $this->CustomfieldModel->delete_customfield($c_f_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/custom_fields'));
    }

    function custom_field_list() 
    {
        $data = array();
        $list = $this->CustomfieldModel->get_fields();

        $no = $_POST['start'];
        foreach ($list as $custom_field_data) {
            $no++;
            $row = array();
            // p($custom_field_data);
            $row[] = $no;
            $row[] = ucfirst($custom_field_data->field_label);
            $row[] = ucfirst($custom_field_data->field_type);
            $button = '<a href="' . base_url("admin/custom_fields/update/". $custom_field_data->id) . '" data-toggle="tooltip" title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1">
            <i class="fas fa-pencil-alt"></i>
            </a>';

            $buttonn= '<a href="' . base_url("admin/custom_fields/copy/" . $custom_field_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_copy_record").'" class="common_copy_record btn btn-warning mr-1"><i class="far fa-copy"></i></a>';
            $button.= '<a href="' . base_url("admin/custom_fields/delete/" . $custom_field_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->CustomfieldModel->count_all(), "recordsFiltered" => $this->CustomfieldModel->count_filtered(), "data" => $data,);
        //output to json format
        echo json_encode($output);
    }
}