<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Study_Material extends Tutor_Controller {
    function __construct() 
    {
        parent::__construct();
        $this->add_css_theme('all.css','admin');
        $this->add_js_theme('jquery-ui.min.js',false,'admin');
        $this->add_js_theme('jquery.multi-select.min.js',false,'admin');
        $this->add_js_theme('studymaterial.js');

        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js',false,'admin');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css','admin');
        // load the language files 
        // load the category model
        $this->load->model('StudyModel');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('tutor/study'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");
        $this->add_css_theme('sweetalert.css','admin')->add_js_theme('sweetalert-dev.js',false,'admin')->add_js_theme('bootstrap-notify.min.js',false,'admin');
        $contant_type_arr = array();
        $contant_type_arr['video'] = "Video";
        $contant_type_arr['audio'] = "Audio";
        $contant_type_arr['image'] = "Image";
        $contant_type_arr['pdf'] = "Pdf";
        $contant_type_arr['doc'] = "Doc";
        $contant_type_arr['youtube-embed-code'] = "Youtube Embed Code";
        $contant_type_arr['vimeo-embed-code'] = "Vimeo Embed Code";
        $contant_type_arr['content'] = "Content / Embed Code";
        $this->contant_types = $contant_type_arr;
        
    }


    function index()
    {
       $this->set_title(lang('admin_study_material_list'));

        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('tutor/study/material_list', $content_data, TRUE);
        $this->load->view($this->template, $data);       
    }

    function add($study_material_id = false) 
    { 
        $this->add_external_js("//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"); 
        $this->add_external_js(base_url("/assets/themes/admin/js/mediaupload.js")); 

        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $category_data = array(); 
        $this->load->model('CategoryModel');
        $newarray = $this->CategoryModel->allcategory();
        $all_quiz_array = $this->StudyModel->get_all_quiz();
        $res =  generatePageTree($newarray, 0, 0, '|--> &nbsp; ');
        $newCats = explode("||~LB~||", $res);

        foreach($newCats as $cat) {
            if(trim($cat) !='' ) {
                $nArray = explode('|~CB~|', $cat);

                $catArray[$nArray[1]] = array(
                    'depth' => $nArray[0], 
                    'id' => $nArray[1], 
                    'name' => get_parent_category_with_comma($nArray[1], ' >> '), 
                    'slug' => $nArray[3], 
                    'icon' => $nArray[4], 
                    'image' => $nArray[5], 
                    'status' => $nArray[6]
                );
            }
        }

        foreach ($catArray as $category_array) 
        {
            $category_data[''] = lang('select_category');
            $category_data[$category_array['id']] = $category_array['name'];
        }

        $all_user_data = array();
        $all_user_array = $this->StudyModel->get_all_users();
        foreach ($all_user_array as $user_data_array) 
        {
            $all_user_data[''] = 'Select User';
            $all_user_data[$user_data_array->id] = $user_data_array->first_name.' '.$user_data_array->last_name;
        }



        $upload_file_name = NULL;
        $file_link = NULL;

        if($this->input->post('title'))
        {
            // if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
            // {   
            //     $allowed_formate = 'gif|jpg|png|bmp|jpeg';
            //     $allowed_max_upload_size = 26000000;
            //     $path = "./assets/images/studymaterial";
            //     $material_file_upload = $this->do_upload_file('image',$path,$allowed_formate,$allowed_max_upload_size);
            //     if($material_file_upload['status'] == 'success')
            //     {
            //         $upload_file_name = $material_file_upload['upload_data']['file_name'];
            //         $file_link = $path.$upload_file_name;
            //     }
            //     else
            //     {
            //         $this->session->set_flashdata('error', $material_file_upload['error']); 
            //         $this->form_validation->set_rules('image', 'Matiral File', 'required');
            //     }
                
            // }
            // else
            // {
            //     $this->form_validation->set_message('error', 'Please upload File First.');
            //     $this->form_validation->set_rules('image', 'Matiral File ', 'required');
            // }
        }

        // $this->form_validation->set_rules('user_id', 'User Name', 'required|numeric|trim');
        $this->form_validation->set_rules('category_id', 'Category Name', 'required|numeric|trim');
        $this->form_validation->set_rules('title', 'Title', 'required|trim|is_unique[study_material.title]');
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('media_file', lang('media_file'), 'required|trim');
        $media_file = $this->input->post('media_file',TRUE);
        if($media_file)
        {
            $ext = pathinfo($media_file, PATHINFO_EXTENSION);
            $allowed_formate = array('gif', 'jpg', 'png', 'bmp','jpeg');              
            if(in_array($ext, $allowed_formate) == FALSE)
            {
                $this->form_validation->set_rules('media_file_err', lang('media_file'), 'required|trim');
            }
        }

        if ($this->form_validation->run() == false)  
        {

            // if($upload_file_name && $file_link && file_exists($file_link))
            // {
            //     unlink($file_link);
            // }
           $errors =  $this->form_validation->error_array();
        }
        else
        {
            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
            $is_registered = $this->input->post('is_registered',TRUE) ? 1 : 0;
            $is_premium = $this->input->post('is_premium',TRUE) ? 1 : 0;
            $status = $this->input->post('status',TRUE) ? 1 : 0;
            $price = $this->input->post('price',TRUE) ? $this->input->post('price',TRUE) : 0;
            $price = $is_premium == 1 ? 0 : $price;

            $study_material_content = array();
            $study_material_content['user_id'] = $user_id;
            $study_material_content['category_id'] = $this->input->post('category_id',TRUE);
            $study_material_content['title'] = $this->input->post('title',TRUE);
            $study_material_content['status'] = $status;
            $study_material_content['is_premium'] = $is_premium;
            $study_material_content['price'] = $price;
            $study_material_content['description'] = $this->input->post('description',TRUE);
            $study_material_content['is_registered'] = $is_registered;
            $study_material_content['meta_title'] =  $this->input->post('metatitle',TRUE);
            $study_material_content['meta_keywords'] =  $this->input->post('metakeywords',TRUE);
            $study_material_content['meta_description'] =  $this->input->post('metadescription',TRUE);
            // $study_material_content['image'] =  $upload_file_name;
            $study_material_content['media_file'] =  $this->input->post('media_file',TRUE);

            $study_material_id = $this->StudyModel->insert_study_material($study_material_content);
            $associate_quiz = $this->input->post('associate_quiz');
            foreach($associate_quiz as $a_key => $asso_quiz_value)
            {
                $data_to_save = array(
                    'study_material_id' => $study_material_id,
                    'quiz_id' => $asso_quiz_value
                );
                $this->StudyModel->insert_study_quiz($data_to_save);
            }
            if($study_material_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }

            redirect(base_url('tutor/study'));
        }

        $this->set_title(lang('admin_add_study_material'));
        $data = $this->includes;
        
        $this->load->helper('url');
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $username = isset($this->user['username']) ? $this->user['username'] : rand();
        $media_path =  FCPATH . '/media/'.$user_id."_".$username."/";
        $media_url =  base_url().'media/'.$user_id."_".$username."/";
        $connector = site_url('tutor/media/upload');

        $content_data = array('category_data'=>$category_data,'all_user_data'=>$all_user_data,'study_material_id'=>$study_material_id,'media_path' => $media_path, 'media_url' => $media_url,'connector' => $connector,'all_quiz_array' => $all_quiz_array,);
        // load views
        $data['content'] = $this->load->view('tutor/study/studymaterial_form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function study_material_update($study_material_id = NULL)
    {
        $this->add_external_js("//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"); 
        $this->add_external_js(base_url("/assets/themes/admin/js/mediaupload.js")); 

        if(empty($study_material_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/study'));
        }

        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }

        $study_quiz = $this->StudyModel->get_study_quiz($study_material_id);
        $quiz_ids = array_column($study_quiz, 'quiz_id');

        $category_data = array(); 
        $this->load->model('CategoryModel');
        $newarray = $this->CategoryModel->allcategory();
        $res =  generatePageTree($newarray, 0, 0, '|--> &nbsp; ');
        $newCats = explode("||~LB~||", $res);

        foreach($newCats as $cat) {
            if(trim($cat) !='' ) {
                $nArray = explode('|~CB~|', $cat);

                $catArray[$nArray[1]] = array(
                    'depth' => $nArray[0], 
                    'id' => $nArray[1], 
                    'name' => get_parent_category_with_comma($nArray[1], ' >> '), 
                    'slug' => $nArray[3], 
                    'icon' => $nArray[4], 
                    'image' => $nArray[5], 
                    'status' => $nArray[6]
                );
            }
        }

        foreach ($catArray as $category_array) 
        {
            $category_data[''] = lang('select_category');
            $category_data[$category_array['id']] = $category_array['name'];
        }

        $all_user_data = array();
        $all_user_array = $this->StudyModel->get_all_users();
        foreach ($all_user_array as $user_data_array) 
        {
            $all_user_data[''] = 'Select User';
            $all_user_data[$user_data_array->id] = $user_data_array->first_name.' '.$user_data_array->last_name;
        }

        $all_quiz_array = $this->StudyModel->get_all_quiz();

        $upload_file_name = NULL;
        $file_link = NULL;

        // if($this->input->post('title'))
        // {
        //     if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
        //     {   
        //         $allowed_formate = 'gif|jpg|png|bmp|jpeg';
        //         $allowed_max_upload_size = 26000000;
        //         $path = "./assets/images/studymaterial";
        //         $material_file_upload = $this->do_upload_file('image',$path,$allowed_formate,$allowed_max_upload_size);
        //         if($material_file_upload['status'] == 'success')
        //         {
        //             $upload_file_name = $material_file_upload['upload_data']['file_name'];
        //             $file_link = $path.$upload_file_name;
        //         }
        //         else
        //         {
        //             $this->session->set_flashdata('error', $material_file_upload['error']); 
        //             $this->form_validation->set_rules('image', 'Matiral File', 'required');
        //         }
                
        //     }
        //     else
        //     {
        //         if(empty($study_material_data->title))
        //         {
        //             $this->form_validation->set_message('error', 'Please upload File First.');
        //             $this->form_validation->set_rules('image', 'Matiral File ', 'required');                    
        //         }
        //     }
        // }

        $media_file = $this->input->post('media_file',TRUE);
        if($media_file)
        {
            $ext = pathinfo($media_file, PATHINFO_EXTENSION);
            $allowed_formate = array('gif', 'jpg', 'png', 'bmp','jpeg');              
            if(in_array($ext, $allowed_formate) == FALSE)
            {
                $this->form_validation->set_rules('media_file_err', lang('media_file'), 'required|trim');
            }
        }


        $title_unique = $this->input->post('title')  != $study_material_data->title ? '|is_unique[study_material.title]' : '';
        // $this->form_validation->set_rules('user_id', 'User Name', 'required|numeric|trim');
        $this->form_validation->set_rules('category_id', 'Category Name', 'required|numeric|trim');
        $this->form_validation->set_rules('title', 'Title', 'required|trim'.$title_unique);
        $this->form_validation->set_rules('description', 'Description', 'required|trim');
        $this->form_validation->set_rules('media_file', lang('media_file'), 'required|trim');

        if ($this->form_validation->run() == false) 
        {
            // if($upload_file_name && $file_link && file_exists($file_link))
            // {
            //     unlink($file_link);
            // }
            $this->form_validation->error_array();
        } 
        else 
        {
            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
            $is_registered = $this->input->post('is_registered',TRUE) ? 1 : 0;
            $is_premium = $this->input->post('is_premium',TRUE) ? 1 : 0;
            $status = $this->input->post('status',TRUE) ? 1 : 0;
            $price = $this->input->post('price',TRUE) ? $this->input->post('price',TRUE) : 0;
            $price = $is_premium == 1 ? 0 : $price;

            $study_material_content = array();
            $study_material_content['user_id'] = $login_user_id;
            $study_material_content['category_id'] = $this->input->post('category_id',TRUE);
            $study_material_content['title'] = $this->input->post('title',TRUE);
            $study_material_content['status'] = $status;
            $study_material_content['is_premium'] = $is_premium;
            $study_material_content['price'] = $price;

            $study_material_content['description'] = $this->input->post('description',TRUE);
            $study_material_content['is_registered'] = $is_registered;
            $study_material_content['meta_title'] =  $this->input->post('metatitle',TRUE);
            $study_material_content['meta_keywords'] =  $this->input->post('metakeywords',TRUE);
            $study_material_content['meta_description'] =  $this->input->post('metadescription',TRUE);
            $study_material_content['media_file'] =  $this->input->post('media_file',TRUE);
            // if($upload_file_name)
            // {
            //     $study_material_content['image'] =  $upload_file_name;
            // }

            $study_update_status = $this->StudyModel->update_study_material($study_material_id, $study_material_content);
            $this->StudyModel->delete_study_quiz($study_material_id);
            $associate_quiz = $this->input->post('associate_quiz');
            
            foreach($associate_quiz as $a_key => $asso_quiz_value)
            {
                $data_to_save = array(
                    'study_material_id' => $study_material_id,
                    'quiz_id' => $asso_quiz_value
                );
                $this->StudyModel->insert_study_quiz($data_to_save);
            }    
            
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            
                redirect(base_url('tutor/study'));
        }

        $this->set_title(lang('admin_edit_study_material'));
        $data = $this->includes;

        $this->load->helper('url');
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $username = isset($this->user['username']) ? $this->user['username'] : rand();
        $media_path =  FCPATH . '/media/'.$user_id."_".$username."/";
        $media_url =  base_url().'media/'.$user_id."_".$username."/";
        $connector = site_url('tutor/media/upload');


        $content_data = array('category_data'=>$category_data,'all_user_data'=>$all_user_data,'study_material_id'=>$study_material_id,'study_material_data'=>$study_material_data,'media_path' => $media_path, 'media_url' => $media_url,'connector' => $connector,'study_quiz' =>$study_quiz,'all_quiz_array' => $all_quiz_array,'quiz_ids' => $quiz_ids,);
        // load views
        $data['content'] = $this->load->view('tutor/study/studymaterial_form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function study_material_list() 
    {
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $data = array();
        $list = $this->StudyModel->get_study_material($login_user_id);

        $no = $_POST['start'];
        foreach ($list as $study_material) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($study_material->title);
            $row[] = ucfirst($study_material->category_title);
            $button = '<a href="' . base_url("tutor/study/update/". $study_material->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';

            $button.= '<a href="' . base_url("tutor/study/copy/" . $study_material->id) . '" data-toggle="tooltip"  title="'.lang("admin_copy_record").'" class="common_copy_record btn btn-warning mr-1"><i class="far fa-copy"></i></a>';
            

            $sm_slug_url = get_study_material_detail_page_url_by_id($study_material->id);

            $button.= '<a href="' . $sm_slug_url . '" data-toggle="tooltip"  title="'.lang("Preview").'" class="btn btn-info btn-action mr-1 "><i class="fas fa-eye"></i></a>';



            $button.= '<a href="' . base_url("tutor/study/delete-study-material/" . $study_material->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->StudyModel->count_all($login_user_id), "recordsFiltered" => $this->StudyModel->count_filtered($login_user_id), "data" => $data,);
        
        //output to json format
        echo json_encode($output);
    }

    function copy($study_material_id = NULL)
    {
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        action_not_permitted();
        if(empty($study_material_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/study'));
        }
        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }

        $study_material_name_count = $this->StudyModel->study_material_name_like_this(NULL,$study_material_data->title);
        $count = $study_material_name_count > 0 ? '-' . $study_material_name_count : '';       

        $study_material_content = array();
        $study_material_content['user_id'] = $study_material_data->user_id;
        $study_material_content['category_id'] = $study_material_data->category_id;
        $study_material_content['title'] = $study_material_data->title.'-copy'.$count;
        $study_material_content['price'] = $study_material_data->price;
        $study_material_content['status'] = $study_material_data->status;
        $study_material_content['is_premium'] = $study_material_data->is_premium;
        $study_material_content['description'] = $study_material_data->description;
        $study_material_content['is_registered'] = $study_material_data->is_registered;
        $study_material_content['meta_title'] =  $study_material_data->meta_title;
        $study_material_content['meta_keywords'] =  $study_material_data->meta_keywords;
        $study_material_content['meta_description'] =  $study_material_data->meta_description;
        $study_material_content['media_file'] =  $study_material_data->media_file;

        $study_material_id = $this->StudyModel->insert_study_material($study_material_content);
        if($study_material_id)
        {
            $this->session->set_flashdata('message', lang('record_copied_successfully'));   
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_copying_record')); 
        }

        redirect(base_url('tutor/study'));       
    }   

    function study_material_delete($study_material_id = NULL)
    {
        action_not_permitted();
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $status = $this->StudyModel->delete_study_material($study_material_id,$login_user_id); 

        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('tutor/study'));
    }

    function section($study_material_id = FALSE)
    {
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        
        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }



        $this->form_validation->set_rules('title', 'Title', 'required|trim');

        if($this->form_validation->run() == false)  
        {
            $this->form_validation->error_array();
            $this->session->set_flashdata('error', lang('required_fields_missing')); 
            redirect(base_url('tutor/study/material-file/'.$study_material_id));
        }
        else
        {

            $section_name_count = $this->StudyModel->study_section_name_like_this($id = NULL, $this->input->post('title', TRUE),$study_material_id);
            $count = $section_name_count > 0 ? '-' . $section_name_count : '';
            $max_order = $this->db->where('study_material_id',$study_material_id)->order_by('order','desc')->get('study_section')->row('order');
            $section_order = ($max_order == 0 OR empty($max_order)) ? 1 : $max_order + 1;

            $study_section_content = array();
            $study_section_content['order'] = $section_order;
            $study_section_content['study_material_id'] = $study_material_id;
            $study_section_content['title'] = $this->input->post('title',TRUE);
            $study_section_content['slug'] = slugify_string ($this->input->post('title',TRUE). $count);
            $study_section_content['added'] = date("Y-m-d H:i:s");
           
            $study_section_id = $this->db->insert('study_section',$study_section_content);
            if($study_material_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
            redirect(base_url('tutor/study/material-file/'.$study_material_id));
        }
    }


    function section_update($study_section_id)
    {
        $study_section_data = $this->db->where('id',$study_section_id)->get('study_section')->row();
        if(empty($study_section_data))
        {
            $this->session->set_flashdata('error', lang('something_went_wrong')); 
            redirect(base_url('tutor/study'));
        }
        $study_material_id = $study_section_data->study_material_id;


        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }


        $this->form_validation->set_rules('title', 'Title', 'required|trim');

        if ($this->form_validation->run() == false)  
        {
            $this->form_validation->error_array();
            $this->session->set_flashdata('error', lang('required_fields_missing')); 
            redirect(base_url('tutor/study/material-file/'.$study_material_id));

        }
        else
        {
            $section_name_count = $this->StudyModel->study_section_name_like_this($study_section_id, $this->input->post('title', TRUE),$study_material_id);
            $count = $section_name_count > 0 ? '-' . $section_name_count : '';

            $section_order = $study_section_data->order;
            if(empty($section_order) OR $section_order == 0)
            {
                $max_order = $this->db->where('study_material_id',$study_material_id)->order_by('order','desc')->get('study_section')->row('order');
                $section_order = ($max_order == 0 OR empty($max_order)) ? 1 : $max_order+1;
            }



            $study_section_content = array();
            $study_section_content['order'] = $section_order;
            $study_section_content['title'] = $this->input->post('title',TRUE);
            $study_section_content['slug'] = slugify_string ($this->input->post('title',TRUE). $count);
            $study_section_content['updated'] = date("Y-m-d H:i:s");
           
            $this->db->where('id',$study_section_id)->update('study_section',$study_section_content);
            $study_section_status =  $this->db->affected_rows();
            if($study_section_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));   
            }
            else
            {
                $this->session->set_flashdata('error', lang('error_during_update_record')); 
            }
            redirect(base_url('tutor/study/material-file/'.$study_section_data->study_material_id));
        }
    }

    function section_delete($study_section_id)
    {
        $study_section_data = $this->db->where('id',$study_section_id)->get('study_section')->row();
        if(empty($study_section_data))
        {
            $this->session->set_flashdata('error', lang('something_went_wrong')); 
            return redirect(base_url('tutor/study'));
        }
        $study_material_id = $study_section_data->study_material_id;
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }

        $this->db->where('id',$study_section_id)->delete('study_section');
        $study_section_status =  $this->db->affected_rows();
        if($study_section_status)
        {
            $this->db->where('section_id',$study_section_id)->delete('study_material_content');
            $this->session->set_flashdata('message', lang('record_deleted_successfully'));   
        }
        else
        {
            $this->session->set_flashdata('error', lang('error_during_delete_record')); 
        }
        redirect(base_url('tutor/study/material-file/'.$study_section_data->study_material_id));
    }

    function change_section_position()
    {   
        $position_array = $this->input->post('position');
        $response['status'] = "error";
        $response['message'] = lang("Invalid Try");
        
        if(count($position_array))
        {
            foreach ($position_array as $items) 
            {
               $this->db->where('id',$items[0])->update('study_section',array('order'=>$items[1]));
            }

            $response['status'] = "success";
            $response['message'] = lang("Order Change Successfully !");
        }
        
        echo json_encode($response);
        return json_encode($response);
        exit;
    }



    function material_file($study_material_id = NULL)
    {
        action_not_permitted();

        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        if(empty($study_material_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/study'));
        }
        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }

        $study_section_data = $this->db->where('study_material_id',$study_material_id)->order_by('order','asc')->get('study_section')->result();

        $this->add_css_theme('sweetalert.css','admin')->add_js_theme('sweetalert-dev.js',false,'admin')->add_js_theme('bootstrap-notify.min.js',false,'admin')->set_title(lang('admin_study_material_file_list'));

        $data = $this->includes;
        $content_data['study_material_id'] =$study_material_id;
        $content_data['study_section_data'] =$study_section_data;
        // load views
        $data['content'] = $this->load->view('tutor/study/material_file_list', $content_data, TRUE);
        $this->load->view($this->template, $data);       
    }

    function add_material_file($study_material_id = NULL,$contant_type)
    {

        action_not_permitted();

        $this->add_external_js("//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"); 
        $this->add_external_js(base_url("/assets/themes/admin/js/mediaupload.js")); 

        $valid_uri = (isset($this->contant_types[$contant_type]) && $this->contant_types[$contant_type]) ? $contant_type : NULL;
        
        if(empty($valid_uri))
        {
            $this->session->set_flashdata('error', lang('invalid_uri_arguments')); 
            redirect(base_url('tutor/study'));
        }
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }
        $study_material_section_data = $this->StudyModel->get_study_material_section_by_study_material_id($study_material_id);


        if(empty($study_material_section_data))
        {
            $this->session->set_flashdata('error', lang('please_add_Section_first')); 
            redirect(base_url("tutor/study/material-file/".$study_material_data->id));
        }
        

        $this->form_validation->set_rules('title', 'Title', 'required|trim|is_unique[study_material_content.title]');


        $material_upload_file = NULL; 
        $upload_file_name = NULL;
        $path_filename = NULL;
        $file_link = NULL;

        if($this->input->post('title'))
        {
            $media_file = $this->input->post('media_path',TRUE);
            if($contant_type == "image" OR $contant_type == "audio" OR $contant_type == "video" OR $contant_type == "pdf" OR $contant_type == "doc" )
            {
                if($media_file)
                {
                    $ext = pathinfo($media_file, PATHINFO_EXTENSION);
                    
                    if($contant_type == "image")
                    {
                        $allowed_formate =  array('gif','jpg','png','bmp','jpeg');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_image', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "audio")
                    {
                        $allowed_formate = array('ogg','aac','mp3','mpeg','amr');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_audio', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "video")
                    {
                        $allowed_formate = array('flv','wma','avi','wmv','mp4','wav','mov','avchd','webm','mkv');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_video', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "pdf")
                    {
                        $allowed_formate = array('pdf');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_pdf', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "doc")
                    {
                        $allowed_formate = array('doc','docx','odt','rtf','xps','csv','ods','xls','xlsx','ppt','pptx');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_doc', lang('media_file'), 'required|trim');
                        }
                    }
                   
                }
                else
                {
                    $this->form_validation->set_rules('media_path', lang('media_path'), 'required|trim');
                }
            }
            else
            {
                $this->form_validation->set_rules('embed_code_contant', lang('embed_code_or_contant'), 'required|trim');
            }

            $this->form_validation->set_rules('title', lang('title'), 'required|trim');
            $this->form_validation->set_rules('section_id', lang('section_name'), 'required');
            $this->form_validation->set_rules('duration', lang('duration'), 'required');

            if ($this->form_validation->run() == false)  
            {
                $error = $this->form_validation->error_array();
                // p($error);
            }
            else
            {
                $embed_code_contant = $this->input->post('embed_code_contant',TRUE);
                $section_id = $this->input->post('section_id',TRUE);
                $duration = $this->input->post('duration',TRUE);
                
                if(empty($embed_code_contant) && empty($media_file))
                {
                    $this->session->set_flashdata('error', lang('something_went_wrong')); 
                    redirect(base_url('tutor/study/material-file/'.$study_material_id));
                }

                $value = $media_file;
                if($embed_code_contant)
                {
                   $value = $embed_code_contant;
                }

                $file_size = isset($_FILES['file']['size']) ? $_FILES['file']['size'] : 0;

                $max_order = $this->db->where('study_material_id',$study_material_id)->where('section_id',$section_id)->order_by('material_order','desc')->get('study_material_content')->row('material_order');
                $section_order = ($max_order == 0 OR empty($max_order)) ? 1 : $max_order + 1;

                $study_material_content = array();
                $study_material_content['study_material_id'] = $study_material_id;
                $study_material_content['section_id'] = $section_id;
                $study_material_content['material_order'] = $section_order;
                $study_material_content['title']    = $this->input->post('title',TRUE);
                $study_material_content['type']     = $contant_type;
                $study_material_content['value']    = $value;
                $study_material_content['duration']    = $duration;
                $study_material_content['size']    = $file_size;
                $study_material_content['is_media_file']    = 1;

                $study_material_content_id = $this->StudyModel->insert_study_material_content($study_material_content);
                if($study_material_content_id)
                {
                    $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
                }
                else
                {
                    $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
                }

                redirect(base_url('tutor/study/material-file/'.$study_material_id));
                
            } 
        }

        $this->set_title(lang('admin_add_study_material_file'));
        $data = $this->includes;

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $username = isset($this->user['username']) ? $this->user['username'] : rand();
        $media_path =  FCPATH . '/media/'.$user_id."_".$username."/";
        $media_url =  base_url().'media/'.$user_id."_".$username."/";
        $connector = site_url('tutor/media/upload');

        $content_data['study_material_section_data'] = $study_material_section_data;
        $content_data['study_material_id'] = $study_material_id;
        $content_data['contant_type'] = $contant_type;
        $content_data['user_id'] = $user_id;
        $content_data['username'] = $username;
        $content_data['media_path'] = $media_path;
        $content_data['media_url'] = $media_url;
        $content_data['connector'] = $connector;

        // load views
        $data['content'] = $this->load->view('tutor/study/material_file_form', $content_data, TRUE);
        $this->load->view($this->template, $data);        
    }



    function update_study_material_content($study_material_content_id)
    {

        $this->add_external_js("//cdnjs.cloudflare.com/ajax/libs/require.js/2.3.2/require.min.js"); 
        $this->add_external_js(base_url("/assets/themes/admin/js/mediaupload.js")); 

        $study_material_content_data = $this->StudyModel->get_material_content_by_id($study_material_content_id);
        
        if(empty($study_material_content_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('tutor/study'));
        }

        $study_material_id = $study_material_content_data->study_material_id;
        $study_material_section_id = $study_material_content_data->section_id;


        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        if(empty($study_material_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/study'));
        }
        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }


        $study_material_section_data = $this->StudyModel->get_study_material_section_by_study_material_id($study_material_id);


        if(empty($study_material_section_data))
        {
            $this->session->set_flashdata('error', lang('please_add_Section_first')); 
            redirect(base_url("tutor/study/material-file/".$study_material_data->id));
        }


        $material_upload_file = NULL; 
        $upload_file_name = NULL;
        $path_filename = NULL;
        $file_link = NULL;
        $contant_type = $study_material_content_data->type;
        $last_contant_value = $study_material_content_data->value;

        if($this->input->post('title'))
        {
            $media_file = $this->input->post('media_path',TRUE);
            if($contant_type == "image" OR $contant_type == "audio" OR $contant_type == "video" OR $contant_type == "pdf" OR $contant_type == "doc" )
            {
                if($media_file)
                {
                    $ext = pathinfo($media_file, PATHINFO_EXTENSION);
                    
                    if($contant_type == "image")
                    {
                        $allowed_formate =  array('gif','jpg','png','bmp','jpeg');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_image', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "audio")
                    {
                        $allowed_formate = array('ogg','aac','mp3','mpeg','amr');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_audio', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "video")
                    {
                        $allowed_formate = array('flv','wma','avi','wmv','mp4','wav','mov','avchd','webm','mkv');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_video', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "pdf")
                    {
                        $allowed_formate = array('pdf');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_pdf', lang('media_file'), 'required|trim');
                        }
                    }

                    if($contant_type == "doc")
                    {
                        $allowed_formate = array('doc','docx','odt','rtf','xps','csv','ods','xls','xlsx','ppt','pptx');
                        if(in_array($ext, $allowed_formate) == FALSE)
                        {
                            $this->form_validation->set_rules('media_path_doc', lang('media_file'), 'required|trim');
                        }
                    }
                   
                }
                else
                {
                    $this->form_validation->set_rules('media_path', lang('media_path'), 'required|trim');
                }
            }
            else
            {
                $this->form_validation->set_rules('embed_code_contant', lang('embed_code_or_contant'), 'required|trim');
            }



            $this->form_validation->set_rules('title', 'Title', 'required|trim');
            $this->form_validation->set_rules('section_id', lang('section_name'), 'required');
            $this->form_validation->set_rules('duration', lang('duration'), 'required');



            if ($this->form_validation->run() == false)  
            {
                $this->form_validation->error_array();
            }
            else
            {
                
                $embed_code_contant = $this->input->post('embed_code_contant',TRUE);
                $section_id = $this->input->post('section_id',TRUE);
                $duration = $this->input->post('duration',TRUE);
                
                if(empty($embed_code_contant) && empty($media_file))
                {
                    $this->session->set_flashdata('error', lang('something_went_wrong')); 
                    redirect(base_url('tutor/study/material-file/'.$study_material_id));
                }

                $value = $embed_code_contant;

                if($contant_type == "image" OR $contant_type == "audio" OR $contant_type == "video" OR $contant_type == "pdf" OR $contant_type == "doc" )
                {
                    if($media_file)
                    {
                        $value = $media_file;
                    }
                }
                
                $file_size = isset($_FILES['file']['size']) ? $_FILES['file']['size'] : NULL;
                $file_size = $file_size ? $file_size : $study_material_content_data->size;


                $section_order = $study_material_content_data->material_order;
                if(empty($section_order) OR $section_order == 0)
                {
                    $max_order = $this->db->where('study_material_id',$study_material_id)->where('section_id',$section_id)->order_by('material_order','desc')->get('study_material_content')->row('material_order');
                    $section_order = ($max_order == 0 OR empty($max_order)) ? 1 : $max_order + 1;
                }

                $study_material_content = array();
                $study_material_content['section_id'] = $section_id;
                $study_material_content['title'] = $this->input->post('title',TRUE);
                $study_material_content['value'] = $value;
                $study_material_content['material_order'] = $section_order;
                $study_material_content['size'] = $file_size;
                $study_material_content['duration'] = $duration;
                if($media_file)
                {
                    $study_material_content['is_media_file'] = 1;
                }

                $study_material_content_status = $this->StudyModel->update_study_material_content($study_material_content_id,$study_material_content);

                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));   

                redirect(base_url('tutor/study/material-file/'.$study_material_id));           
            }    
        }   

        $this->set_title(lang('admin_edit_study_material_file'));
        $data = $this->includes;


        $this->load->helper('url');
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $username = isset($this->user['username']) ? $this->user['username'] : rand();
        $media_path =  FCPATH . '/media/'.$user_id."_".$username."/";
        $media_url =  base_url().'media/'.$user_id."_".$username."/";
        $connector = site_url('tutor/media/upload');

        $content_data = array('study_material_id'=>$study_material_id,'study_material_content_id'=>$study_material_content_id,'study_material_content_data'=>$study_material_content_data,'contant_type' => $contant_type,'study_material_section_data'=>$study_material_section_data,'media_path' => $media_path, 'media_url' => $media_url,'connector' => $connector);
        // load views
        $data['content'] = $this->load->view('tutor/study/material_file_form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }





    function do_upload_file($filename, $path,$allowed_formate,$allowed_max_upload_size)
    {
        if (!is_dir($path)) 
        {
            mkdir($path, 0777, TRUE);
        }
        $max_server_upload_limit = convertBytes(ini_get('upload_max_filesize'));
        if($max_server_upload_limit < $allowed_max_upload_size)
        {
            $allowed_max_upload_size = $max_server_upload_limit;
        }
        
        $new_name = time().$_FILES[$filename]['name'];
        $config['upload_path']          = $path;
        $config['allowed_types']        = $allowed_formate;
        $config['max_size']             = $allowed_max_upload_size;
        $config['max_width']            = 400000;
        $config['max_height']           = 300000;
        $config['file_name']            = $new_name;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($filename))
        {
            $respons = array('status' => 'error','error' => strip_tags($this->upload->display_errors()));
        }
        else
        {
            $respons = array('status' => 'success','upload_data' => $this->upload->data());
        }
        return $respons;
    }


    function countPages($path) 
    {
      $pdftext = file_get_contents($path);
      $num = preg_match_all("/\/Page\W/", $pdftext, $dummy);
      return $num;
    }

    function material_file_list($study_material_id = NULL)
    {
        $data = array();
        $list = $this->StudyModel->get_study_material_content($study_material_id);
        $no = $_POST['start'];
        foreach ($list as $study_material_content) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($study_material_content->title);
            $row[] = ucfirst($study_material_content->type);
            $button = '<a href="' . base_url("tutor/study/update-material-content/" . $study_material_id ."/". $study_material_content->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';            

            $button.= '<a href="' . base_url("tutor/study/delete-material-content/" . $study_material_id ."/". $study_material_content->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->StudyModel->count_all_content($study_material_id), "recordsFiltered" => $this->StudyModel->count_filtered_content($study_material_id), "data" => $data,);
        
        //output to json format
        echo json_encode($output);           
    }


    function delete_study_material_content($study_material_content_id)
    {
        $study_content_data = $this->StudyModel->get_file_by_study_and_content_id($study_material_content_id);

        if(empty($study_content_data))
        {
            $this->session->set_flashdata('error', lang('something_went_wrong')); 
            redirect(base_url('tutor/study'));
        }


        $filename = $study_content_data->file_name;
        $study_material_id = $study_content_data->study_material_id;



        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        if(empty($study_material_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/study'));
        }
        $study_material_data = $this->StudyModel->get_study_material_by_id($study_material_id,$login_user_id);

        if(empty($study_material_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }




        $path = "./assets/uploads/study_material/$filename";

        if(file_exists($path))
        {
            unlink($path);
        }  

        $status = $this->StudyModel->delete_material_content($study_material_id,$study_material_content_id);
        if ($status) 
        {
          $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('tutor/study/material-file/'.$study_content_data->study_material_id));
    }

    function change_section_contant_position()
    {   
        $position_array = $this->input->post('position');
        $response['status'] = "error";
        $response['message'] = lang("Invalid Try");
        if(count($position_array))
        {
            foreach ($position_array as $items) 
            {
               $this->db->where('id',$items[0])->update('study_material_content',array('material_order'=>$items[1]));
            }

            $response['status'] = "success";
            $response['message'] = lang("Order Change Successfully !");
        }
        
        echo json_encode($response);
        return json_encode($response);
        exit;
    }





}