<?php defined('BASEPATH') OR exit('No direct script access allowed');
class GradingController extends Admin_Controller 
{
    
    function __construct() 
    {  
        parent::__construct(); 
        $this->add_css_theme('all.css'); 
        $this->add_css_theme('bootstrap4-toggle.min.css');  
        $this->add_js_theme('dropzone.js');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');
        $this->add_js_theme('bootstrap-notify.min.js');  
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');
        
        $this->add_js_theme('jquery-ui.min-1-12.js', TRUE);
        $this->add_css_theme('jquery-ui.min-1-12.css');

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');
        $this->add_js_theme('quizgrading.js');

        $this->load->model('GradingModel');
        
        $this->load->library('form_validation');
        
        $this->load->helper('url');
        $this->load->library('resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/quiz'));
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



    function index() 
    {
        $this->set_title(lang('quiz_grading'));
        $data = $this->includes;    
        $content_data = array();
        $data['content'] = $this->load->view('admin/grading/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }



    function add() 
    {
 
        $this->form_validation->set_rules('title', 'title', 'required|trim');

        $level_names_array = array();
        $level_marks_array = array();
        $level_common_array = array();
        $no_of_level_row = 1;

        $this->form_validation->set_rules('quiz_level_name[]', 'Level Names', 'required|trim');
        $this->form_validation->set_rules('quiz_level_marks[]', 'Level Marks', 'required|numeric|trim');

        $level_names_array = $this->input->post('quiz_level_name') ? $this->input->post('quiz_level_name') : array();
        $level_names_array = is_array($level_names_array) ? $level_names_array : array();
        $level_marks_array = $this->input->post('quiz_level_marks') ? $this->input->post('quiz_level_marks') : array();
        $level_marks_array = is_array($level_marks_array) ? $level_marks_array : array();

        if(count($level_marks_array) != count($level_names_array))
        {
            $this->session->set_flashdata('error', lang('something_wennt_wrong')); 
            $this->form_validation->set_rules('error', 'Level Marks', 'required');
        }

        $no_of_level_row = count($level_marks_array);

        foreach ($level_marks_array as $key => $value) 
        {   $is_numeric = is_numeric($value);
            if($is_numeric == FALSE OR $value > 100)
            {
                $this->session->set_flashdata('error', lang('level_marks_should_be_number_or_less_then_hundrad')); 
                $this->form_validation->set_rules('error', 'Level Marks', 'required');
            }
            $level_common_array[$value] = isset($level_names_array[$key]) ? $level_names_array[$key] : "-";
        }
        

        
        if ($this->form_validation->run() == false)  
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $quiz_levels_data = array('level_names_array'=>$level_names_array,'level_marks_array'=>$level_marks_array,'level_common_array' => $level_common_array);

            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
                        
            $qgrading_content = array();
            $qgrading_content['user_id'] = $this->user['id'];
            $qgrading_content['title'] = $this->input->post('title',TRUE);
            $qgrading_content['data'] = json_encode($quiz_levels_data);
            
            $grading_id = $this->GradingModel->insert_grading($qgrading_content);

            if($grading_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }

            redirect(base_url('admin/quiz-grading'));
        }

        $this->set_title(lang('add_quiz_grading'));
        $data = $this->includes;

        $content_data = array('level_names_array' => $level_names_array, 'level_marks_array' => $level_marks_array,'no_of_level_row' => $no_of_level_row);
        $data['content'] = $this->load->view('admin/grading/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }



    function update($grading_id) 
    {
        if(empty($grading_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('admin/quiz-grading'));
        }

        $grading_data = $this->GradingModel->get_grading_by_id($grading_id);

        if(empty($grading_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('admin/quiz-grading'));
        }



        $level_names_array = array();
        $level_marks_array = array();
        $level_common_array = array();
        $no_of_level_row = 1;

        $this->form_validation->set_rules('quiz_level_name[]', 'Level Names', 'required|trim');
        $this->form_validation->set_rules('quiz_level_marks[]', 'Level Marks', 'required|numeric|trim');

        $level_names_array = $this->input->post('quiz_level_name') ? $this->input->post('quiz_level_name') : array();
        $level_names_array = is_array($level_names_array) ? $level_names_array : array();
        $level_marks_array = $this->input->post('quiz_level_marks') ? $this->input->post('quiz_level_marks') : array();
        $level_marks_array = is_array($level_marks_array) ? $level_marks_array : array();

        if(count($level_marks_array) != count($level_names_array))
        {
            $this->session->set_flashdata('error', lang('something_wennt_wrong')); 
            $this->form_validation->set_rules('error', 'Level Marks', 'required');
        }
        $no_of_level_row = count($level_marks_array);

        foreach ($level_marks_array as $key => $value) 
        {   $is_numeric = is_numeric($value);
            if($is_numeric == FALSE OR $value > 100)
            {
                $this->session->set_flashdata('error', lang('level_marks_should_be_number_or_less_then_hundrad')); 
                $this->form_validation->set_rules('error', 'Level Marks', 'required');
            }
            $level_common_array[$value] = isset($level_names_array[$key]) ? $level_names_array[$key] : "-";
        }


        if(empty($level_names_array))
        {
            $db_quiz_levels_data = json_decode($grading_data->data);
            $db_quiz_levels_data = json_decode(json_encode($db_quiz_levels_data), true);

            $level_names_array = isset($db_quiz_levels_data['level_names_array']) ? $db_quiz_levels_data['level_names_array'] : array();
            $level_names_array = is_array($level_names_array) ? $level_names_array : array();
            
            $level_marks_array = isset($db_quiz_levels_data['level_marks_array']) ? $db_quiz_levels_data['level_marks_array'] : array();
            $level_marks_array = is_array($level_marks_array) ? $level_marks_array : array();
            
            $level_common_array = isset($db_quiz_levels_data['level_common_array']) ? $db_quiz_levels_data['level_common_array'] : array();
            $level_marks_array = is_array($level_marks_array) ? $level_marks_array : array();

        }

        $no_of_level_row = count($level_marks_array);



        if ($this->form_validation->run() == false)  
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $quiz_levels_data = array('level_names_array'=>$level_names_array,'level_marks_array'=>$level_marks_array,'level_common_array' => $level_common_array);

            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
                        
            $qgrading_content = array();
            $qgrading_content['user_id'] = $this->user['id'];
            $qgrading_content['title'] = $this->input->post('title',TRUE);
            $qgrading_content['data'] = json_encode($quiz_levels_data);
            

            $update_status = $this->GradingModel->update_grading_by_id($grading_id, $qgrading_content);

            if($update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/quiz-grading'));
        }

       
        $this->set_title(lang('update_quiz_grading').': '.$grading_data->title);
        $data = $this->includes;

        $grading_data = json_decode(json_encode($grading_data),TRUE);


        $content_data = array('grading_id' => $grading_id, 'grading_data' => $grading_data,'level_names_array' => $level_names_array, 'level_marks_array' => $level_marks_array,'no_of_level_row' => $no_of_level_row,);

        $data['content'] = $this->load->view('admin/grading/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }



    function delete($grading_id)
    {
        action_not_permitted();
        $status = $this->GradingModel->delete_grading_by_id($grading_id); 

        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/quiz-grading'));
    }



    function ajax_list() 
    {
        $data = array();
        $list = $this->GradingModel->get_grading();

        $no = $_POST['start'];
        foreach ($list as $db_data) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($db_data->title);
           

            $button = '<a href="' . base_url("admin/quiz-grading-update/". $db_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';            
            $button.= '<a href="' . base_url("admin/quiz-grading-delete/" . $db_data->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->GradingModel->count_all(), "recordsFiltered" => $this->GradingModel->count_filtered(), "data" => $data,);
        
        //output to json format
        echo json_encode($output);
    }

}
