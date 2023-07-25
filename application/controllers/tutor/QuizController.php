<?php defined('BASEPATH') OR exit('No direct script access allowed');
class QuizController extends Tutor_Controller {
    function __construct() {  
        parent::__construct(); 
        
        $this->add_css_theme('all.css', 'admin'); 
        $this->add_css_theme('dropzone.css', 'admin');
        $this->add_css_theme('bootstrap4-toggle.min.css', 'admin');
        $this->add_js_theme('dropzone.js',false,'admin');
        $this->add_js_theme('bootstrap4-toggle.min.js',false, 'admin');

        $this->add_css_theme('select2.min.css', 'admin');
        $this->add_js_theme('bootstrap-notify.min.js', FALSE, 'admin');  
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js', FALSE, 'admin');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css', 'admin');
        
        $this->add_js_theme('jquery-ui.min-1-12.js', TRUE, 'admin');
        $this->add_css_theme('jquery-ui.min-1-12.css', 'admin');

        $this->add_css_theme('sweetalert.css', 'admin');
        $this->add_js_theme('sweetalert-dev.js', FALSE, 'admin');
        
        $this->add_js_theme('quiz.js');

        $this->load->model('QuizModel');
        $this->load->model('QuizQuestionModel');
        $this->load->library('form_validation');
        $this->load->helper('url');
        $this->load->library('resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('tutor/quiz'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");
    }
    function index() 
    {

        $this->set_title(lang('quiz_list'));
        $data = $this->includes;

        $category_data = array(); 
        $all_category = $this->QuizModel->get_all_category();
        

        $content_data = array();
        $content_data['all_category'] = $all_category;
        $data['content'] = $this->load->view('tutor/quiz/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add() 
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
 
        $this->form_validation->set_rules('category_id', 'Category Name', 'required|numeric|trim');
        $this->form_validation->set_rules('title', 'Title', 'required|trim|is_unique[quizes.title]');
        $this->form_validation->set_rules('number_questions', 'Number Of Question', 'required|trim|numeric');
        $this->form_validation->set_rules('duration_min', 'Duration', 'required|trim|numeric');
        $this->form_validation->set_rules('passing_mark', lang('passing_mark'), 'required|trim|numeric');

        $this->form_validation->set_rules('description', 'Description', 'required|trim');


        $is_sheduled_test = $this->input->post('is_sheduled_test',TRUE) ? 1 : 0;
        $start_date_time = strtotime(date("Y-m-d H:i:s"));
        $end_date_time = strtotime(date("Y-m-d H:i:s"));

        if($is_sheduled_test == 1 && isset($_POST['is_sheduled_test']) && $_POST['is_sheduled_test'])
        {
            $this->form_validation->set_rules('start_date_time', lang('Quiz Start Date Time'), 'required|trim');
            $this->form_validation->set_rules('end_date_time', lang('Quiz End Date Time'), 'required|trim');
            $start_date_time = date("Y-m-d H:i:s",strtotime($this->input->post('start_date_time',TRUE)));
            $end_date_time = date("Y-m-d H:i:s",strtotime($this->input->post('end_date_time',TRUE)));

            if($start_date_time > $end_date_time)
            {
                $this->form_validation->set_rules('invalid_date', lang('Quiz Start Date Must Less Then From Quiz End Date'), 'required|trim');
                $this->session->set_flashdata('error', lang('Quiz Start Date Must Less Then From Quiz End Date'));
            }
        }


        $reward_percentage = $this->input->post('reward_percentage',TRUE);
        $negative_marking_percentage = $this->input->post('negative_marking_percentage',TRUE);
        $marks_for_correct_answer = $this->input->post('marks_for_correct_answer',TRUE);
        
        if(is_numeric($reward_percentage) && $reward_percentage <= 100 && $reward_percentage >= 0)
        {
            $reward_percentage = $reward_percentage;
        }
        else
        {
            $this->form_validation->set_rules('reward_percentage_err', 'reward_percentage', 'required|trim|numeric');
        }


        if(is_numeric($negative_marking_percentage) && $negative_marking_percentage <= 100 && $negative_marking_percentage >= 0)
        {
            $negative_marking_percentage = $negative_marking_percentage;
        }
        else
        {
            $this->form_validation->set_rules('negative_marking_percentage_err', 'negative_marking_percentage_err', 'required|trim|numeric');
        }

        if(is_numeric($marks_for_correct_answer) && $marks_for_correct_answer <= 100 && $marks_for_correct_answer > 0)
        {
            $marks_for_correct_answer = $marks_for_correct_answer;
        }
        else
        {
            $this->form_validation->set_rules('marks_for_correct_answer_err', 'marks_for_correct_answer_err', 'required|trim|numeric');
        }


        $helpsheet = NULL; 
        $helpsheet_file_link = NULL;
        $helpsheet_file = NULL;
        if(isset($_FILES['helpsheet']['name']) && $_FILES['helpsheet']['name'])
        {   
            $path = "./assets/images/helpsheet";
            $allowed_formate = 'jpg|png|jpeg';
            $response_file_upload = $this->do_upload_file('helpsheet',$path,$allowed_formate);

            if($response_file_upload['status'] == 'success')
            {
                $helpsheet_file = $response_file_upload['upload_data']['file_name'];
                $helpsheet_file_link = $path.$helpsheet_file;
            }
            else
            {
                $this->session->set_flashdata('error', $response_file_upload['error']); 
                $this->form_validation->set_rules('helpsheet', 'Helpsheet File', $response_file_upload['error']);
            }
        }


        if ($this->form_validation->run() == false)  
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            if($is_sheduled_test == 1)
            {
                $start_date_time = strtotime($start_date_time);
                $end_date_time = strtotime($end_date_time);
            }
            else
            {
                $start_date_time = 0;
                $end_date_time = 33168837600;
            }

            $leader_board = $this->input->post('leader_board',TRUE);
            $leader_board = $leader_board ? 1 : 0; 

            $is_random = $this->input->post('is_random',TRUE); 
            $is_random = $is_random ? 1 : 0;
            $is_random_option = $this->input->post('is_random_option',TRUE);
            $is_random_option = $is_random_option ? 1 : 0;
            $is_paid = $this->input->post('price',TRUE) > 0 ? 1 : 0;
            $is_premium = $this->input->post('is_premium',TRUE) ? 1 : 0; 
            $registered = $this->input->post('is_registered',TRUE) ? 1 : 0;
            $is_registered = ($is_premium == 1) ? 1 : $registered;
            $is_quiz_active = $this->input->post('is_quiz_active',TRUE) ? 1 : 0;
            $hide_correct_answer = $this->input->post('hide_correct_answer',TRUE) ? 1 : 0;
            $is_disable_result = $this->input->post('is_result_disable',TRUE); 
            $is_disable_result = $is_disable_result ? 1 : 0;
            $is_previous_disable = $this->input->post('is_previous_disable',TRUE); 
            $is_previous_disable = $is_previous_disable ? 1 : 0;
            
                        
            $duration_minute = $this->input->post('duration_min',TRUE);
            
            $quiz_content = array();

            $quiz_content['user_id'] = $user_id;
            $quiz_content['category_id'] = $this->input->post('category_id',TRUE);
            $quiz_content['title'] = $this->input->post('title',TRUE);
            $quiz_content['number_questions'] = $this->input->post('number_questions', TRUE);
            $quiz_content['price'] = $this->input->post('price',TRUE);
            $quiz_content['duration_min'] = $duration_minute;
            $quiz_content['description'] = $this->input->post('description',TRUE);
            $quiz_content['quiz_instruction'] = $this->input->post('quiz_instruction',TRUE);
            $quiz_content['featured_image'] = ($this->input->post('featured_image')) ? json_encode($this->input->post('featured_image',TRUE)) : json_encode(array());
            $quiz_content['leader_board'] = $leader_board;
            $quiz_content['is_random'] = $is_random;
            $quiz_content['is_random_option'] = $is_random_option;
            $quiz_content['is_registered'] = $is_registered;
            $quiz_content['attempt'] = $this->input->post('quiz_attempt',TRUE);
            $quiz_content['is_paid'] = $is_paid;
            $quiz_content['deleted'] = 0;
            $quiz_content['deleted'] = 0;
            $quiz_content['added'] =  date('Y-m-d H:i:s');
            $quiz_content['meta_title'] =  $this->input->post('metatitle',TRUE);
            $quiz_content['meta_keywords'] =  $this->input->post('metakeywords',TRUE);
            $quiz_content['meta_description'] =  $this->input->post('metadescription',TRUE);
            $quiz_content['passing'] =  $this->input->post('passing_mark',TRUE);
            $quiz_content['reward_percentage'] =  $reward_percentage;
            $quiz_content['negative_marking_percentage'] =  $negative_marking_percentage;
            $quiz_content['marks_for_correct_answer'] =  $marks_for_correct_answer;
            $quiz_content['difficulty_level'] =  $this->input->post('difficulty_level',TRUE);
            $quiz_content['points_on_correct'] =  $this->input->post('points_on_correct',TRUE);
            $quiz_content['is_premium'] = $is_premium;
            $quiz_content['bonus_points'] = $this->input->post('bonus_points',TRUE);
            $quiz_content['multiple_attemp'] = "YES";
            $quiz_content['evaluation_test'] = $this->input->post('evaluation_test',TRUE);

            $quiz_content['hide_correct_answer'] = $hide_correct_answer;
            $quiz_content['is_sheduled_test'] = $is_sheduled_test;
            $quiz_content['start_date_time'] = $start_date_time;
            $quiz_content['end_date_time'] = $end_date_time;
            $quiz_content['is_quiz_active'] = $is_quiz_active;
            $quiz_content['quiz_grading_id'] = $this->input->post('quiz_grading_id',TRUE);
            $quiz_content['is_disable_result'] = $is_disable_result;
            $quiz_content['quiz_helpsheet'] = $helpsheet_file;
            $quiz_content['is_previous_disable'] = $is_previous_disable;
            

            $quiz_id = $this->QuizModel->insert_quiz($quiz_content);

            if($quiz_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));   
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }

            redirect(base_url('tutor/quiz'));
        }

        $this->load->model('CategoryModel');
        $newarray = $this->CategoryModel->allcategory();
        // $tree = $this->buildTree($newarray);        
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
        $all_user_array = $this->QuizModel->get_all_users();
        foreach ($all_user_array as $user_data_array) 
        {
            $all_user_data[''] = 'Select User';
            $all_user_data[$user_data_array->id] = $user_data_array->first_name.' '.$user_data_array->last_name;
        }



        $all_quiz_gradings_data = array();
        $all_quiz_gradings = $this->QuizModel->all_quiz_gradings($user_id);
        foreach ($all_quiz_gradings as $quiz_grading) 
        {
            $all_quiz_gradings_data['0'] = lang('select_quiz_grading');
            $all_quiz_gradings_data[$quiz_grading->id] = $quiz_grading->title;
        }
        $this->set_title(lang('admin_add_quiz'));
        $data = $this->includes;

        $content_data = array('category_data' => $category_data,'all_user_data'=>$all_user_data,'end_date_time' =>strtotime($end_date_time), 'start_date_time' => strtotime($start_date_time),'all_quiz_gradings_data' => $all_quiz_gradings_data);
        // load views
        $data['content'] = $this->load->view('tutor/quiz/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($quiz_id = NULL) 
    {
        if(empty($quiz_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/quiz'));
        }

        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/quiz'));
        }

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        if($quiz_data->user_id != $user_id)
        {
            $this->session->set_flashdata('error', lang('access_denide')); 
            redirect(base_url('tutor/quiz'));
        }

        $title_unique = $this->input->post('title')  != $quiz_data->title ? '|is_unique[quizes.title]' : '';

        $this->form_validation->set_rules('category_id', 'Category Name', 'required|numeric|trim');
        $this->form_validation->set_rules('title', 'Title', 'required|trim'.$title_unique);
        $this->form_validation->set_rules('number_questions', 'Number Question', 'required|trim|numeric');
        $this->form_validation->set_rules('duration_min', 'Duration', 'required|trim|numeric');
        $this->form_validation->set_rules('passing_mark', lang('passing_mark'), 'required|trim|numeric');
       
        $this->form_validation->set_rules('description', 'Description', 'required|trim');




        $is_sheduled_test = $this->input->post('is_sheduled_test',TRUE) ? 1 : 0;

        if($is_sheduled_test == 1)
        {
            $start_date_time = $quiz_data->start_date_time;
            $end_date_time = $quiz_data->end_date_time;
        }
        else
        {
            $start_date_time = strtotime(date("Y-m-d H:i:s"));
            $end_date_time = strtotime(date("Y-m-d H:i:s"));
        }

        

        if($is_sheduled_test == 1 && isset($_POST['is_sheduled_test']) && $_POST['is_sheduled_test'])
        {
            $this->form_validation->set_rules('start_date_time', lang('Quiz Start Date Time'), 'required|trim');
            $this->form_validation->set_rules('end_date_time', lang('Quiz End Date Time'), 'required|trim');
            $start_date_time = date("Y-m-d H:i:s",strtotime($this->input->post('start_date_time',TRUE)));
            $end_date_time = date("Y-m-d H:i:s",strtotime($this->input->post('end_date_time',TRUE)));

            if($start_date_time > $end_date_time)
            {
                $this->form_validation->set_rules('invalid_date', lang('Quiz Start Date Must Less Then From Quiz End Date'), 'required|trim');
                $this->session->set_flashdata('error', lang('Quiz Start Date Must Less Then From Quiz End Date'));
            }
        }



        $last_featured_image = json_decode($quiz_data->featured_image);
        $last_featured_image = $last_featured_image ? $last_featured_image : array();


        $reward_percentage = $this->input->post('reward_percentage',TRUE);
        $negative_marking_percentage = $this->input->post('negative_marking_percentage',TRUE);
        $marks_for_correct_answer = $this->input->post('marks_for_correct_answer',TRUE);
        
        if(is_numeric($reward_percentage) &&$reward_percentage <= 100 && $reward_percentage >= 0)
        {
            $reward_percentage =$reward_percentage;
        }
        else
        {
            $this->form_validation->set_rules('reward_percentage_err', 'reward_percentage', 'required|trim|numeric');
        }


        if(is_numeric($negative_marking_percentage) &&$negative_marking_percentage <= 100 && $negative_marking_percentage >= 0)
        {
            $negative_marking_percentage = $negative_marking_percentage;
        }
        else
        {
            $this->form_validation->set_rules('negative_marking_percentage_err', 'negative_marking_percentage_err', 'required|trim|numeric');
        }


        if(is_numeric($marks_for_correct_answer) && $marks_for_correct_answer <= 100 && $marks_for_correct_answer > 0)
        {
            $marks_for_correct_answer = $marks_for_correct_answer;
        }
        else
        {
            $this->form_validation->set_rules('marks_for_correct_answer_err', 'marks_for_correct_answer_err', 'required|trim|numeric');
        }


        $helpsheet = NULL; 
        $helpsheet_file_link = NULL;
        $helpsheet_file = $quiz_data->quiz_helpsheet;
        if(isset($_FILES['helpsheet']['name']) && $_FILES['helpsheet']['name'])
        {   
            
            $path = "./assets/images/helpsheet";
            if(file_exists($path.'/'.$quiz_data->quiz_helpsheet))
            {
                unlink($path.'/'.$quiz_data->quiz_helpsheet);
            }
            
            $allowed_formate = 'jpg|png|jpeg';
            $response_file_upload = $this->do_upload_file('helpsheet',$path,$allowed_formate);

            if($response_file_upload['status'] == 'success')
            {
                $helpsheet_file = $response_file_upload['upload_data']['file_name'];
                $helpsheet_file_link = $path.$helpsheet_file;
            }
            else
            {
                $this->session->set_flashdata('error', $response_file_upload['error']); 
                $this->form_validation->set_rules('helpsheet', 'Helpsheet File', $response_file_upload['error']);
            }
        }



        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {

            if($is_sheduled_test == 1)
            {
                $start_date_time = strtotime($start_date_time);
                $end_date_time = strtotime($end_date_time);
            }
            else
            {
                $start_date_time = 0;
                $end_date_time = 33168837600;
            }
            
            $leader_board = $this->input->post('leader_board',TRUE);
            $leader_board = $leader_board ? 1 : 0;

            $is_random = $this->input->post('is_random',TRUE);
            $is_random = $is_random ? 1 : 0;

            $is_random_option = $this->input->post('is_random_option',TRUE);
            $is_random_option = $is_random_option ? 1 : 0;
            $is_paid = $this->input->post('price',TRUE) > 0 ? 1 : 0;
            $is_premium = $this->input->post('is_premium',TRUE) ? 1 : 0;
            $registered = $this->input->post('is_registered',TRUE) ? 1 : 0;
            $is_registered = ($is_premium == 1) ? 1 : $registered;
            $quiz_featured_image = $this->input->post('featured_image') ?  $this->input->post('featured_image',TRUE) : array();
            $featured_image = array_merge($last_featured_image,$quiz_featured_image);
            $price = ($this->input->post('is_premium',TRUE) == 1) ? 0 : $this->input->post('price',TRUE);
            $is_disable_result = $is_disable_result ? 1 : 0;
            $is_paid = $this->input->post('price',TRUE) > 0 ? 1 : 0;
            
            $duration_minute = $this->input->post('duration_min',TRUE);
            $is_quiz_active = $this->input->post('is_quiz_active',TRUE) ? 1 : 0;
            $hide_correct_answer = $this->input->post('hide_correct_answer',TRUE) ? 1 : 0;

            $is_previous_disable = $this->input->post('is_previous_disable',TRUE); 
            $is_previous_disable = $is_previous_disable ? 1 : 0;
            
            $quiz_content = array();

            $quiz_content['user_id'] = $user_id;
            $quiz_content['category_id'] = $this->input->post('category_id',TRUE);
            $quiz_content['title'] = $this->input->post('title',TRUE);
            $quiz_content['number_questions'] = $this->input->post('number_questions', TRUE);
            $quiz_content['price'] = $price;
            $quiz_content['duration_min'] = $duration_minute;
            $quiz_content['description'] = $this->input->post('description',TRUE);
            $quiz_content['quiz_instruction'] = $this->input->post('quiz_instruction',TRUE);
            $quiz_content['featured_image'] = json_encode($featured_image);
            $quiz_content['leader_board'] = $leader_board;
            $quiz_content['is_random'] = $is_random;
            $quiz_content['is_random_option'] = $is_random_option;
            $quiz_content['is_registered'] = $is_registered;
            $quiz_content['attempt'] = $this->input->post('quiz_attempt',TRUE);    
            $quiz_content['is_paid'] = $is_paid;
            $quiz_content['updated'] =  date('Y-m-d H:i:s');
            $quiz_content['meta_title'] =  $this->input->post('metatitle');
            $quiz_content['meta_keywords'] =  $this->input->post('metakeywords');
            $quiz_content['meta_description'] =  $this->input->post('metadescription');
            $quiz_content['passing'] =  $this->input->post('passing_mark',TRUE);
            $quiz_content['reward_percentage'] =  $reward_percentage;
            $quiz_content['negative_marking_percentage'] =  $negative_marking_percentage;
            $quiz_content['marks_for_correct_answer'] =  $marks_for_correct_answer;
            $quiz_content['difficulty_level'] =  $this->input->post('difficulty_level',TRUE);
            $quiz_content['points_on_correct'] =  $this->input->post('points_on_correct',TRUE);
            $quiz_content['is_premium'] = $is_premium;
            $quiz_content['bonus_points'] = $this->input->post('bonus_points',TRUE);
            $quiz_content['multiple_attemp'] = "YES";
            $quiz_content['evaluation_test'] = $this->input->post('evaluation_test',TRUE);

            $quiz_content['hide_correct_answer'] = $hide_correct_answer;
            $quiz_content['is_sheduled_test'] = $is_sheduled_test;
            $quiz_content['start_date_time'] = $start_date_time;
            $quiz_content['end_date_time'] = $end_date_time;
            $quiz_content['is_quiz_active'] = $is_quiz_active;
            $quiz_content['is_disable_result'] = $is_disable_result;
            $quiz_content['quiz_helpsheet'] = $helpsheet_file;
            $quiz_content['is_previous_disable'] = $is_previous_disable;


            $article_update_status = $this->QuizModel->update_quiz($quiz_id, $quiz_content);

            if($article_update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('tutor/quiz'));
        }

        $this->load->model('CategoryModel');
        $newarray = $this->CategoryModel->allcategory();
        // $tree = $this->buildTree($newarray);        
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
        $all_user_array = $this->QuizModel->get_all_users();
        foreach ($all_user_array as $user_data_array) 
        {
            $all_user_data[''] = 'Select User';
            $all_user_data[$user_data_array->id] = $user_data_array->first_name.' '.$user_data_array->last_name;
        }



        $all_quiz_gradings_data = array();
        $all_quiz_gradings = $this->QuizModel->all_quiz_gradings($user_id);
        foreach ($all_quiz_gradings as $quiz_grading) 
        {
            $all_quiz_gradings_data['0'] = lang('select_quiz_grading');
            $all_quiz_gradings_data[$quiz_grading->id] = $quiz_grading->title;
        }


        $this->set_title(lang('update_quiz').': '.$quiz_data->title);
        $data = $this->includes;

        $quiz_data = json_decode(json_encode($quiz_data),TRUE);
        $content_data = array('quiz_id' => $quiz_id, 'quiz_data' => $quiz_data, 'category_data' => $category_data,'all_user_data'=>$all_user_data, 'end_date_time' =>strtotime($end_date_time), 'start_date_time' => strtotime($start_date_time),'all_quiz_gradings_data' => $all_quiz_gradings_data);
        // load views
        $data['content'] = $this->load->view('tutor/quiz/edit_form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    public function copy($quiz_id = NULL) 
    {
        action_not_permitted();
        if(empty($quiz_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/quiz'));
        }

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/quiz'));
        }

        if($quiz_data->user_id != $user_id)
        {
            $this->session->set_flashdata('error', lang('access_denide')); 
            redirect(base_url('tutor/quiz'));
        }

        $quiz_name_count = $this->QuizModel->quiz_name_like_this(NULL,$quiz_data->title);
        $count = $quiz_name_count > 0 ? '-' . $quiz_name_count : '';

        $quiz_content = array();

        $quiz_content['user_id'] = $quiz_data->user_id;
        $quiz_content['category_id'] =  $quiz_data->category_id;
        $quiz_content['title'] = $quiz_data->title.'-copy '.$count;
        $quiz_content['number_questions'] = $quiz_data->number_questions;
        $quiz_content['price'] =  $quiz_data->price;
        $quiz_content['is_paid'] = $quiz_data->is_paid;
        $quiz_content['description'] =  $quiz_data->description;
        $quiz_content['featured_image'] =  json_encode(array());
        $quiz_content['duration_min'] =  $quiz_data->duration_min;
        $quiz_content['leader_board'] =  $quiz_data->leader_board;
        $quiz_content['quiz_instruction'] =  $quiz_data->quiz_instruction;
        $quiz_content['is_random'] = $quiz_data->is_random;
        $quiz_content['is_random_option'] = $quiz_data->is_random_option;
        $quiz_content['is_registered'] = $quiz_data->is_registered;
        $quiz_content['attempt'] = $quiz_data->attempt;  
        $quiz_content['meta_title'] =  $quiz_data->meta_title;
        $quiz_content['meta_keywords'] =  $quiz_data->meta_keywords;
        $quiz_content['meta_description'] =  $quiz_data->meta_description;
        $quiz_content['passing'] =  $quiz_data->passing;
        $quiz_content['reward_percentage'] =  $quiz_data->reward_percentage;
        $quiz_content['negative_marking_percentage'] =  $quiz_data->negative_marking_percentage;
        $quiz_content['marks_for_correct_answer'] =  $quiz_data->marks_for_correct_answer;
        $quiz_content['difficulty_level'] =  $quiz_data->difficulty_level;
        $quiz_content['points_on_correct'] =  $quiz_data->points_on_correct;
        $quiz_content['is_premium'] = $quiz_data->is_premium;
        $quiz_content['bonus_points'] = $quiz_data->bonus_points;
        $quiz_content['multiple_attemp'] = $quiz_data->multiple_attemp;
        $quiz_content['evaluation_test'] = $quiz_data->evaluation_test;
        $quiz_content['is_quiz_active'] = $quiz_data->is_quiz_active;
        $quiz_content['deleted'] =  0;
        $quiz_content['added'] =  date('Y-m-d H:i:s');

        $quiz_content['is_sheduled_test'] = $quiz_data->is_sheduled_test;
        $quiz_content['start_date_time'] = $quiz_data->start_date_time;
        $quiz_content['end_date_time'] = $quiz_data->end_date_time;
        $quiz_content['is_disable_result'] = $quiz_data->is_disable_result;

        $article_new_id = $this->QuizModel->insert_quiz($quiz_content);
        $get_question_by_quiz_id = $this->QuizModel->get_question_by_quiz_id($quiz_id);
        
        if($article_new_id && isset($get_question_by_quiz_id) && $get_question_by_quiz_id)
        {
            $question_data_array = array();
            foreach ($get_question_by_quiz_id as $question_key => $question_value) 
            {
                $question_name_count = @$this->QuizModel->question_name_like_this(NULL,$question_value->title);
                
                $count = $question_name_count > 0 ? '-' . $question_name_count : '';
                $question_content_data = array();
                $question_content_data['quiz_id'] = $article_new_id;
                $question_content_data['title'] = $question_value->title.'-copy '.$count; 
                $question_content_data['solution'] = $question_value->solution;
                $question_content_data['image'] = NULL;
                $question_content_data['solution_image'] = NULL;
                $question_content_data['is_multiple'] = $question_value->is_multiple;
                $question_content_data['choices'] = $question_value->choices;
                $question_content_data['correct_choice'] = $question_value->correct_choice;
                $question_content_data['added'] = date('Y-m-d H:i:s');
                $question_content_data['updated'] = date('Y-m-d H:i:s');
                $question_content_data['addon_type'] = $question_value->addon_type;
                $question_content_data['addon_value'] = $question_value->addon_value;
                $question_content_data['queston_choies_type'] = $question_value->queston_choies_type;
                $question_content_data['question_type_is_match'] = $question_value->question_type_is_match;
                $question_content_data['question_paragraph_id'] = $question_value->question_paragraph_id;
                $question_content_data['question_section_id'] = $question_value->question_section_id;
                $question_content_data['upload_type'] = NULL;
                $question_content_data['render_content'] = $question_value->render_content;
                $question_content_data['helpsheet'] = NULL;
                $question_data_array[] = $question_content_data;
            }

            if($question_data_array)
            {
                $quiz_wise_question_data_status = $this->QuizModel->insert_quiz_copy_question_data($question_data_array);
            }
            
        }


        if($article_new_id)
        {
            $this->session->set_flashdata('message', lang('record_copied_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_copying_record')); 
        } 

        redirect(base_url('tutor/quiz'));
    }

    function delete($quiz_id = NULL)
    {
        action_not_permitted();
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $status = $this->QuizModel->delete_tutor_quiz($quiz_id,$user_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('tutor/quiz'));
    }

    function quiz_upload_file() {
        $image = array();
        $name = $_FILES['file']['name'];
        $config['upload_path'] = "./assets/images/quiz";
        $config['allowed_types'] = 'jpg|png|bmp|jpeg';
        $this->load->library('upload', $config);
        $status = $this->upload->do_upload('file');
        if($status) 
        {
            $file = $this->upload->data();
            $full_path = "./assets/images/quiz/".$file['file_name'];
            $resize_to = "./assets/images/quiz/";
            $thumb = $this->resize_image->resize_to_thumb($full_path,$resize_to.'thumbnail');
            $thumb = $thumb ? lang('thumbnail_resize_success') : lang('thumbnail_resize_error');

            $small = $this->resize_image->resize_to_small($full_path,$resize_to.'small');
            $small = $small ? lang('small_resize_success') : lang('small_resize_errors');

            $medium = $this->resize_image->resize_to_medium($full_path,$resize_to.'medium');
            $medium = $medium ? lang('medium_resize_success') : lang('medium_resize_errors');

            $success = array('status' => true, 'messages' => lang('upload_success'), 'name' => $file['file_name'], 'original_name' => $name,);
            echo json_encode($success);
        } 
        else 
        {
            $image['msg'] = 'error';
            echo json_encode($image);
        }
    }

    function dropzone_quiz_file_remove() {
        $filename = $_POST['filename'];
        $path = "./assets/images/article/$filename";
        if ($path) {
            unlink($path);
            unlink("./assets/images/article/thumbnail/$filename");
            unlink("./assets/images/article/small/$filename");
            unlink("./assets/images/article/medium/$filename");

            $status = json_encode($filename);
            echo xss_clean($status);
            return $status;
        }
        echo false;
        return false;
    }

    function quiz_list() 
    {
        $data = array();
        $list = $this->QuizModel->get_quiz();

        $no = $_POST['start'];
        foreach ($list as $quiz) 
        {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($quiz->title);
            $row[] = ucfirst($quiz->category_title);
            $row[] = ucfirst($quiz->number_questions);
            $row[] = ($quiz->duration_min);

            $checkvalue = ($quiz->is_quiz_active == 1 ? 'checked="checked"' : "");
            
            $status = '<label class="custom-switch mt-2">
                        <input type="checkbox" data-id="'.$quiz->id.'" name="custom-switch-checkbox" class="custom-switch-input togle_switch is_quiz_active_togle" '.$checkvalue.' >
                        <span class="custom-switch-indicator indication"></span>
                     </label>';


            $button = '<a href="' . base_url("tutor/quiz/update/". $quiz->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';

            $button.= '<a href="' . base_url("tutor/quiz/copy/" . $quiz->id) . '" data-toggle="tooltip"  title="'.lang("admin_copy_record").'" class="common_copy_record btn btn-warning mr-1"><i class="far fa-copy"></i></a>';
            
            $quiz_slug_url = get_quiz_detail_page_url_by_id($quiz->id);
            $button.= '<a href="' . $quiz_slug_url . '" data-toggle="tooltip"  title="'.lang("preview").'" class="btn btn-info btn-action mr-1 "><i class="fas fa-eye"></i></a>';

            $button.= '<a href="' . base_url("tutor/quiz/delete/" . $quiz->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $status;
            $row[] = $button;
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->QuizModel->count_all(), "recordsFiltered" => $this->QuizModel->count_filtered(), "data" => $data,);
        
        //output to json format
        echo json_encode($output);
    }

    public function delete_featured_image($quiz_id = NULL) 
    {
        $featured_image_name = $this->input->post('featured_image_name', TRUE);
        if ($quiz_id && $featured_image_name) 
        {
            $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);
            $featured_image_array = json_decode($quiz_data->featured_image);
            $featured_image_array = json_decode(json_encode($featured_image_array), True);
            if (($key = array_search($featured_image_name, $featured_image_array)) !== false) 
            {
                unset($featured_image_array[$key]);
            }
            $updated_image_value = json_encode($featured_image_array);
            $result = $this->QuizModel->update_quiz_images_by_id($quiz_id, $updated_image_value);
            $path = "./assets/images/quiz/$featured_image_name";
            unlink($path);
            unlink("./assets/images/quiz/thumbnail/$featured_image_name");
            unlink("./assets/images/quiz/small/$featured_image_name");
            unlink("./assets/images/quiz/medium/$featured_image_name");
            echo xss_clean($result);
            return $result;
        } 
        else 
        {
            echo false;
            return false;
        }
    }

    function image_resize_library($image_address = null)
    {
        $resize_status = $this->resize_image->resize_to_thumb('./assets/images/quiz/default-1.png', './assets/images/');
        echo ($resize_status) ? lang('file_copy_success') : lang('resize_errors');
    } 

    function questions($quiz_id = NULL)
    {
        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/quiz'));
        }

        $this->add_js_theme('dataTables.buttons.min.js', FALSE, 'admin');
        $this->add_js_theme('buttons.flash.min.js', FALSE, 'admin');
        $this->add_js_theme('jszip.min.js', FALSE, 'admin');
        $this->add_js_theme('pdfmake.min.js', FALSE, 'admin');
        $this->add_js_theme('vfs_fonts.js', FALSE, 'admin');
        $this->add_js_theme('buttons.html5.min.js', FALSE, 'admin');
        $this->add_js_theme('buttons.print.min.js', FALSE, 'admin');
        $this->add_css_theme('buttons.dataTables.min.css', 'admin');


        $this->set_title(lang('admin_questions_list').": ".$quiz_data->title);
        $this->add_js_theme('question.js');
        
        $data = $this->includes;
        $content_data = array('quiz_id' => $quiz_id, 'quiz_data'=> $quiz_data);
        $data['content'] = $this->load->view('tutor/quiz/question_list', $content_data, TRUE);
        $this->load->view($this->template, $data);

    }

    function question_list($quiz_id=false) 
    {
        $data = array();
        $list = $this->QuizModel->get_question($quiz_id); 
        $no = $_POST['start'];
        foreach ($list as $question) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = xss_clean($question->title);
            $correct_answer = json_decode($question->correct_choice);

            if($question->question_type_is_match == "YES")
            {
                $correct_answer = json_decode(json_encode($correct_answer), true);

                $correct_answerarr = isset($correct_answer['arr_is_correct']) ? $correct_answer['arr_is_correct'] : array();
                $answer = implode(", ",$correct_answerarr);
            }
            else
            {
                foreach($correct_answer as $correct_value)
                {
                    $answer = xss_clean($correct_value);
                }
            }
            
           
            $answer = isset($answer) ? $answer : "";
            $row[] = $answer; 
                
            $button = '<a href="' . base_url("tutor/questions/update/". $quiz_id. "/". $question->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';

            $button .= '<a href="' . base_url("tutor/questions/copy/" .$quiz_id ."/" . $question->id) . '" data-toggle="tooltip" title="'.lang("admin_copy_record").'" class="common_copy_record btn btn-warning mr-1"><i class="far fa-copy"></i></a>';

            $button .= '<a href="' . base_url("tutor/questions/delete/" .$quiz_id ."/" . $question->id) . '" data-toggle="tooltip" title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'], 
            "recordsTotal" => $this->QuizModel->count_all_question($quiz_id), 
            "recordsFiltered" => $this->QuizModel->count_filtered_question($quiz_id), 
            "data" => $data
        );

        //output to json format
        echo json_encode($output);
    }


    public function translate_quiz($quiz_id)
    {
        if(empty($quiz_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('tutor/quiz'));
        }

        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('tutor/quiz'));
        }

        $this->form_validation->set_rules('quiz_id', 'Quiz Id', 'required|trim');
        $translated_post_data = $this->input->post('translate');

        if ($this->form_validation->run() == false) 
        {
            $this->form_validation->error_array();
        } 
        else 
        {
            $translatition_content_array =array();
            foreach ($this->input->post('translate') as $lang_id => $content_array) 
            {
                foreach ($content_array as $column_name => $column_value) 
                {
                    $translatition_content = array();
                    $translatition_content['table'] = 'quizes';
                    $translatition_content['forigen_table_id'] = $quiz_id;
                    $translatition_content['lang_id'] = $lang_id;
                    $translatition_content['column'] = $column_name;
                    $translatition_content['value'] = $column_value;
                    $translatition_content_array[] = $translatition_content;
                }
            }

            if($translatition_content_array)
            {
                $translated_data = $this->QuizModel->delete_translated_data($quiz_id);
                $status = $this->QuizModel->insert_translated_data($translatition_content_array);
                if($status)
                {
                    $this->session->set_flashdata('message', lang('quiz_translation_success'));
                }
                else
                {
                    $this->session->set_flashdata('error', lang('quiz_translation_invalid_form_data'));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('quiz_translation_invalid_form_data')); 
            }
            redirect(base_url('tutor/quiz/update/'.$quiz_id));
        }
        $languages = $this->QuizModel->get_languages();

        $translated_data_array = array();
        $translated_db_data = $this->QuizModel->get_translated_data($quiz_id);
        if($translated_db_data)
        {
            foreach ($translated_db_data as $key => $translate_array) 
            {
                $translated_data_array[$translate_array->lang_id][$translate_array->column] = $translate_array->value;
            }
        }

        $translated_data_array =  $translated_post_data ?  $translated_post_data : $translated_data_array;

        if(empty($translated_data_array))
        {
            foreach ($languages as $language) 
            {
                $translated_data_array[$language->id]['title'] = $quiz_data->title;
                $translated_data_array[$language->id]['description'] = $quiz_data->description;
                $translated_data_array[$language->id]['quiz_instruction'] = $quiz_data->quiz_instruction;
            }
        }

        $this->set_title(lang('translate').': '.$quiz_data->title);
        $data = $this->includes;
        $content_data = array('quiz_id' => $quiz_id, 'quiz_data'=> $quiz_data, 'languages'=> $languages,'translated_data_array'=>$translated_data_array);

        $data['content'] = $this->load->view('tutor/quiz/translate_quiz', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    public function summernoteimg()
    {
        $path = '';
        try {

                if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
                {

                    $image = array();
                    $name = $_FILES['image']['name'];
                    $config['upload_path'] = "./assets/images/wysiwyg";
                    $config['allowed_types'] = 'jpg|png|bmp|jpeg';
                    $new_name = time().'_'.$_FILES["image"]['name'];
                    $config['file_name'] = $new_name;

                    $this->load->library('upload', $config);

                    if (!$this->upload->do_upload('image'))
                    {
                        $error = $this->upload->display_errors();
                        $response = array('status' => 0,'path'=> $path,'message' => $error);

                    }
                    else
                    {
                        $img_data = $this->upload->data();
                        $image_file = $img_data['file_name'];
                        $path = base_url('assets/images/wysiwyg/').$image_file;
                        $response = array('status' => 1,'path'=> $path,'message' => 'File Uploded');
                    }

                }
                else
                {
                    $response = array('status' => 0,'path'=> $path,'message' => 'Some Thing Went Wrong..! ');
                }
                echo  json_encode($response);
                return $response;

            } 
            catch (Exception $e) 
            {
                $response = array('status' => 0,'path'=> $path,'message' => $e->getMessage());
                echo json_encode($response);
                return $response;
            }

    }



    function update_quiz_status($id)
    {   
        $quiz_id = $this->input->post('quiz_id');
        $status = $this->input->post('status');
        $status = $status == 1 ? 1 : 0;
        $response['status'] = "error";
        $response['message'] = "Invalid Try";
        
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        if($quiz_id)
        {
            $this->db->where('id',$quiz_id)->where('user_id',$user_id)->update('quizes',array('is_quiz_active'=>$status));
            $response['status'] = "success";
            $response['message'] = lang("Quiz Status Change Successfully !");
        }
        
        echo json_encode($response);
        return json_encode($response);
        exit;
    }

    public function do_upload_file($filename, $path, $allowed_formate)
    {
        if (!is_dir($path)) 
        {
            mkdir($path, 0777, TRUE);
        }
             
        $new_name = time().$_FILES[$filename]['name'];
        $config['upload_path']          = $path;
        $config['allowed_types']        = $allowed_formate;
        $config['max_size']             = 50000000;
        $config['max_width']            = 400000;
        $config['max_height']           = 300000;
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if (!$this->upload->do_upload($filename))
        {
            $respons = array(   'status' => 'error','error' => $this->upload->display_errors());
        }
        else
        {
            $respons = array('status' => 'success','upload_data' => $this->upload->data());
        }
        return $respons;
    }

    function delete_helpsheet($quiz_id = NULL)
    {

        action_not_permitted();
        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);
        
        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('admin/quiz/update/'.$quiz_id));
        }
        
        $path = "./assets/images/helpsheet/$quiz_data->quiz_helpsheet";
        
        if(file_exists($path))
        {
            unlink($path);
        }
        
        $status = $this->QuizModel->update_helpsheet_field($quiz_id); 
        
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }

        redirect(base_url('admin/quiz/update/'.$quiz_id));
    }

}
