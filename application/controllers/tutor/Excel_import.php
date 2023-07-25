<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Rap2hpoutre\FastExcel\FastExcel;
use App\User;

class Excel_import extends Tutor_Controller {
    /**
     * @var string
     */

    private $_redirect_url;

    protected $brands = [];

    protected $markets = [];
    protected $groups = [];
    protected $custom_fields = [];
    
    /**
     * Constructor
     */

    function __construct() {

        parent::__construct();
        $this->load->model('ImportModel');
        $this->load->model('QuizModel');
        $this->load->model('StudyModel');

        $contant_type_arr = array();
        $contant_type_arr[''] = lang('select_study_material_contant_type');
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
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * Message list page
     */
    function index($post_quiz_id = NULL) 
    {


        $quiz_data = $this->QuizModel->get_quiz_by_id($post_quiz_id);

        
        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', lang('invalid_id')); 
            redirect(base_url('tutor/quiz'));
        }

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        if($quiz_data->user_id != $user_id)
        {
            $this->session->set_flashdata('error', lang('access_denide')); 
            redirect(base_url('tutor/quiz'));
        }


        
        $this->form_validation->set_rules('quiz_id', lang('admin_excel_quiz_name'), 'required|numeric|trim');
        if(empty($_FILES["excel_file"]['name']))
        {            
            $this->form_validation->set_rules('excel_file', lang('admin_excel_file'), 'required');
        }

        if ($this->form_validation->run() == false OR empty($this->input->post('quiz_id'))) 
        {
            $this->form_validation->error_array();
        }
        else
        {

            $image = time().'-'.$_FILES["excel_file"]['name'];
            
            $config['upload_path']      = "./assets/excel";
            $config['allowed_types']    = 'xlsx|csv|xls';
            $config['file_name']        = $image;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('excel_file')) 
            {
                
                $error =  $this->upload->display_errors();

                $this->session->set_flashdata('error', $error);
                return redirect(base_url('tutor/quiz/import/'.$post_quiz_id));
            }
            else
            {
                
                $file = $this->upload->data();
                $content['category_image'] = $file['file_name'];
            }
            $quiz_id = $this->input->post('quiz_id',TRUE); 
            $over_write = $this->input->post('over_write',TRUE);
            $over_write = $over_write ? 1 : 0;


            $import = $this->import_Excel_data($file['file_name'], $quiz_id,$over_write);

            if($import)
            { 

                $this->session->set_flashdata('message', $import['insert_count'].' '.lang('record_import_successfully').' '.$import['skip_count'].' '.lang('row_skip_during_import'));

                return redirect(base_url('tutor/quiz/import/'.$post_quiz_id));                
            }
            else
            {
                $this->session->set_flashdata('error',lang('data_import_error'));
                return redirect(base_url('tutor/quiz/import/'.$post_quiz_id));        
            }
        }
       
        $quiz_name_array = array();
        $all_quiz = $this->ImportModel->get_all_quiz();
        foreach ($all_quiz as $quiz_array) 
        {
            $quiz_name_array[''] = 'Select Quiz';
            $quiz_name_array[$quiz_array->id] = $quiz_array->title;
        }
        

        $this->set_title(lang('admin_import_quiz_questions_excel').": ".$quiz_data->title);
        $data = $this->includes;
        
        $content_data = array('quiz_name_array' => $quiz_name_array,'post_quiz_id'=>$post_quiz_id);
        // load views
        $data['content'] = $this->load->view('tutor/import/form', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }

    private function import_Excel_data($file_name, $quiz_id,$over_write) 
    {
      
        $file_dir = "./assets/excel/".$file_name;

        $questions_array = array();
        try
        {

           $questions_array = (new FastExcel)->import($file_dir);
           
        }
        catch (Exception $e)
        {
           
            $error =  lang('unable_to_read_this_file_formate');
            
            $this->session->set_flashdata('error', $error);
            return redirect(base_url('tutor/excel_import'));

        }
        $questions_array = $questions_array ? $questions_array : array();
        $questions_array = json_decode(json_encode($questions_array), true);

        $question_content_array = array();

        $i = 0;
        $insert_count = 0;
        $skip_count = 0;
        foreach ($questions_array as $product_detail_data) 
        {   
            $i++;
            if(empty($product_detail_data['title']))
            {
                break;
            }

            if($i === 1)
            {
                $excel_is_multiple = isset($product_detail_data['is_multiple']) ? 'SUCCESS' : NULL;
                if(empty($product_detail_data['choices']) OR empty($product_detail_data['correct_choice']) OR empty($excel_is_multiple ))
                {
                    $this->session->set_flashdata('error', lang('invalid_file_formate'));
                    return redirect(base_url('tutor/quiz/import/'.$post_quiz_id));
                }
            }

            $is_multiple = $product_detail_data['is_multiple'] == 1 ? 1 : 0;

            if(is_array($product_detail_data['choices']))
            {
                if(isset($product_detail_data['choices']['date']))
                {
                   $product_detail_data['choices'] = date('d M Y',strtotime($product_detail_data['choices']['date'])); 
                }
                else
                {
                    $product_detail_data['choices'] = implode(',', $product_detail_data['choices']);
                }
            }

            $excel_choices_array = explode('||', $product_detail_data['choices']);
            $excel_choices_array = $excel_choices_array ? $excel_choices_array : array();
            $choices_array = array();
            if($excel_choices_array)
            foreach ($excel_choices_array as $key => $choice_value) 
            {
                if($choice_value)
                {
                    $choices_array[] = trim($choice_value);                    
                }
            }

            if(is_array($product_detail_data['correct_choice']))
            {
                if(isset($product_detail_data['correct_choice']['date']))
                {
                   $product_detail_data['correct_choice'] = date('d M Y',strtotime($product_detail_data['correct_choice']['date'])); 
                }
                else
                {
                    $product_detail_data['correct_choice'] = implode(',', $product_detail_data['correct_choice']);
                }
            }

            $excel_correct_choice = explode('||', $product_detail_data['correct_choice']);
            $excel_correct_choice = $excel_correct_choice ? $excel_correct_choice : array();

            $correct_choice = array();
           
            foreach ($excel_correct_choice as $key => $correct_value) 
            {
                if($correct_value)
                {   
                    $correct_choice[] = trim($correct_value);
                }
            }


            if($product_detail_data['title'] && $choices_array && $correct_choice)
            {
                $insert_count++;
                $question_content['quiz_id'] = $quiz_id;
                $question_content['title'] = $product_detail_data['title'];
                $question_content['is_multiple'] = $is_multiple;
                $question_content['choices'] = json_encode($choices_array);
                $question_content['correct_choice'] = json_encode($correct_choice);
                $question_content['image'] = NULL;
                $question_content['solution'] = $product_detail_data['solution'];
                $question_content['deleted'] = 0;
                $question_content['added'] =  date('Y-m-d H:i:s');
                
                $question_content_array[] = $question_content;

            }
            else
            {
                $skip_count++;
            }
        
        }  //end foreach

        if($over_write == 1)
        {            
            $this->ImportModel->delete_question_by_quiz_id($quiz_id);
        }
        
        $status = $this->ImportModel->insert_bulk_question($question_content_array);
          
        $respone['insert_count']   = $insert_count;     
        $respone['skip_count']   = $skip_count;  
        return $respone;
    }

    
    function study_data_import($post_s_m_id = NULL) 
    {
        $study_data = $this->StudyModel->get_study_material_by_id($post_s_m_id);
        if(empty($study_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/study'));
        }

        $study_section_data = $this->db->where('study_material_id',$post_s_m_id)->order_by('order','asc')->get('study_section')->result();

        $all_study_section_name = array();
        foreach ($study_section_data as $study_section_data_obj) 
        {
            $all_study_section_name[''] = lang('Select Study Material Section');
            $all_study_section_name[$study_section_data_obj->id] = $study_section_data_obj->title;
        }

        $this->form_validation->set_rules('s_m_id', lang('study_material_name'), 'required|numeric|trim');
        // $this->form_validation->set_rules('s_m_section_id', lang('study_material_section_name'), 'required|numeric|trim');
        // $this->form_validation->set_rules('s_m_contant_type', lang('study_material_contant_type'), 'required|trim');
        if(empty($_FILES["excel_file"]['name']))
        {            
            $this->form_validation->set_rules('excel_file', lang('admin_excel_file'), 'required');
        }

        if ($this->form_validation->run() == false OR empty($this->input->post('s_m_id'))) 
        {
            $this->form_validation->error_array();
        }
        else
        {

            $image = time().'-'.$_FILES["excel_file"]['name'];
            $path = "./assets/excel/study_material";
            if (!is_dir($path)) 
            {
                mkdir($path, 0775, TRUE);
            }
            
            $config['upload_path']      = $path;
            $config['allowed_types']    = 'xlsx|csv|xls';
            $config['file_name']        = $image;

            $this->load->library('upload', $config);
            if (!$this->upload->do_upload('excel_file')) 
            {
                
                $error =  $this->upload->display_errors();

                $this->session->set_flashdata('error', $error);
                return redirect(base_url('tutor/study-data/import/'.$post_s_m_id));
            }
            else
            {
                
                $file = $this->upload->data();
                // $content['category_image'] = $file['file_name'];
            }
            $s_m_id = $this->input->post('s_m_id',TRUE); 
            // $s_m_section_id = $this->input->post('s_m_section_id',TRUE); 
            // $s_m_contant_type = $this->input->post('s_m_contant_type',TRUE); 
            $over_write = $this->input->post('over_write',TRUE);
            $over_write = $over_write ? 1 : 0;


            $import = $this->import_study_material_Excel_data($file['file_name'], $s_m_id);

            if($import)
            { 

                $this->session->set_flashdata('message', $import['insert_count'].' '.lang('record_import_successfully').' '.$import['skip_count'].' '.lang('row_skip_during_import'));

                return redirect(base_url('tutor/study-data/import/'.$post_s_m_id));                
            }
            else
            {
                $this->session->set_flashdata('error',lang('data_import_error'));
                return redirect(base_url('tutor/study-data/import/'.$post_s_m_id));        
            }
        }

        $all_study_material_name = array();
        $study_material_name_obj = $this->ImportModel->get_all_study_material_name();
        foreach ($study_material_name_obj as $study_material_name_array) 
        {
            $all_study_material_name[''] = lang('Select Study Material');
            $all_study_material_name[$study_material_name_array->id] = $study_material_name_array->title;
        }
        

        $this->set_title(lang('import_study_data').": ".$study_data->title);
        $data = $this->includes;
        
        $content_data = array('all_study_material_name' => $all_study_material_name,'post_s_m_id'=>$post_s_m_id,'study_data'=>$study_data,'all_study_section_name' => $all_study_section_name);
        // load views
        $data['content'] = $this->load->view('tutor/import/study_data/form', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }

    private function import_study_material_Excel_data($file_name, $s_m_id) 
    {
      
        $file_dir = "./assets/excel/study_material/".$file_name;

        $study_data_excel_array = array();
        try
        {
           $study_data_excel_array = (new FastExcel)->import($file_dir);
        }
        catch (Exception $e)
        {
            $error =  lang('unable_to_read_this_file_formate');
            $this->session->set_flashdata('error', $error);
            return redirect(base_url('tutor/study-data/import/'.$s_m_id));        
        }

        $study_data_excel_array = $study_data_excel_array ? $study_data_excel_array : array();
        $study_data_excel_array = json_decode(json_encode($study_data_excel_array), true);

        $question_content_array = array();

        $i = 0;
        $insert_count = 0;
        $skip_count = 0;
        foreach ($study_data_excel_array as $study_data_contant_array) 
        {   
            $section_name = (isset($study_data_contant_array['SECTION-NAME']) && trim($study_data_contant_array['SECTION-NAME'])) ? trim($study_data_contant_array['SECTION-NAME']) : NULL;

            if(empty($section_name))
            {
                break;
            }

            $s_m_content_type = isset($study_data_contant_array['CONTANT-TYPE']) ? $study_data_contant_array['CONTANT-TYPE'] : "none";
            $s_m_content_title = isset($study_data_contant_array['CONTANT-TITLE']) ? $study_data_contant_array['CONTANT-TITLE'] : NULL;
            $duration_in_minutes = isset($study_data_contant_array['CONTENT-DURATION-IN-MINUTES']) ? $study_data_contant_array['CONTENT-DURATION-IN-MINUTES'] : NULL;
            $uploaded_file_name = isset($study_data_contant_array['FILE-NAME']) ? $study_data_contant_array['FILE-NAME'] : NULL;
            $youtube_embade_code = isset($study_data_contant_array['YOUTUBE-EMBED-CODE']) ? $study_data_contant_array['YOUTUBE-EMBED-CODE'] : NULL;
            $vimeo_embade_code = isset($study_data_contant_array['VIMEO-EMBED-CODE']) ? $study_data_contant_array['VIMEO-EMBED-CODE'] : NULL;
            $contant_or_embade_code = isset($study_data_contant_array['CONTENT-OR-EMBED-CODE']) ? $study_data_contant_array['CONTENT-OR-EMBED-CODE'] : NULL;

            $section_id = "";



            $study_section_data = $this->db->where('study_material_id',$s_m_id)->order_by('order','asc')->get('study_section')->result();

            foreach ($study_section_data as $study_section_data_obj) 
            {

                if(strtolower(trim($study_section_data_obj->title)) == strtolower($section_name))
                {
                    $section_id = $study_section_data_obj->id;
                    break;
                }
            }


            if(empty($section_id))
            {

                $section_name_count = $this->StudyModel->study_section_name_like_this($id = NULL, $section_name,$s_m_id);
                $count = $section_name_count > 0 ? '-' . $section_name_count : '';
                $max_order = $this->db->where('study_material_id',$s_m_id)->order_by('order','desc')->get('study_section')->row('order');
                $section_order = ($max_order == 0 OR empty($max_order)) ? 1 : $max_order + 1;

                $study_section_content = array();
                $study_section_content['order'] = $section_order;
                $study_section_content['study_material_id'] = $s_m_id;
                $study_section_content['title'] = $section_name;
                $study_section_content['slug'] = slugify_string ($section_name. $count);
                $study_section_content['added'] = date("Y-m-d H:i:s");
               
                $this->db->insert('study_section',$study_section_content);
                $section_id = $this->db->insert_id();

            }

            if(empty($section_id))
            {
                $error =  lang('invalid_section_name');
                $this->session->set_flashdata('error', $error);
                return redirect(base_url('tutor/study-data/import/'.$s_m_id));       
            }


            $valid_contant_types = (isset($this->contant_types[$s_m_content_type]) && $this->contant_types[$s_m_content_type]) ? strtolower(trim($s_m_content_type)) : NULL;
        
            if(empty($valid_contant_types))
            {
                $this->session->set_flashdata('error', lang('invalid_contant_type')); 
                return redirect(base_url('tutor/study-data/import/'.$s_m_id));  
            }

            $study_data_contant_db_array = array();


            if($valid_contant_types == "image" OR $valid_contant_types == "audio" OR $valid_contant_types == "video" OR $valid_contant_types == "pdf" OR $valid_contant_types == "doc" )
            {
                $value = $uploaded_file_name;
            }
            else if($valid_contant_types == 'youtube-embed-code')
            {
                $value = $youtube_embade_code;
            }
            else if($valid_contant_types == 'vimeo-embed-code')
            {
                $value = $vimeo_embade_code;
            }
            else if($valid_contant_types == 'content')
            {
                $value = $contant_or_embade_code;
            }
            else
            {
                $value = "";
            }


            $max_order = $this->db->where('study_material_id',$s_m_id)->where('section_id',$section_id)->order_by('material_order','desc')->get('study_material_content')->row('material_order');
            $study_material_content_order = ($max_order == 0 OR empty($max_order)) ? 1 : $max_order + 1;

            $study_material_content = array();
            $study_material_content['study_material_id'] = $s_m_id;
            $study_material_content['section_id'] = $section_id;
            $study_material_content['material_order'] = $study_material_content_order;
            $study_material_content['title']    = $s_m_content_title;
            $study_material_content['type']     = $s_m_content_type;
            $study_material_content['value']    = $value;
            $study_material_content['duration']    = $duration_in_minutes;
            $study_material_content['size']    = 0;

            $study_material_content_id = $this->StudyModel->insert_study_material_content($study_material_content);
          
            $insert_count++;
        
        }  //end foreach
 
        $respone['insert_count']   = $insert_count;     
        $respone['skip_count']   = $skip_count;  
        return $respone;
    }

    

    public function bulk_import()
    { 
        $this->session->set_flashdata('error', lang('Invalid Uri Request... !'));
        return redirect(base_url('tutor'));
    }

}
