<?php defined('BASEPATH') OR exit('No direct script access allowed');
class QuestionController extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_css_theme('dropzone.css');
        $this->add_css_theme('bootstrap4-toggle.min.css');
        $this->add_js_theme('dropzone.js');
        $this->add_js_theme('bootstrap4-toggle.min.js');

        $this->add_css_theme('select2.min.css');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');
        $this->add_js_theme('question.js');

        $this->load->model('QuestionModel');
        $this->load->model('QuizModel');
        $this->load->library('form_validation');
        $this->load->helper("My_custom_field_helper");
        $this->load->helper('url');
        $this->load->library('resize_image');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/questions'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc"); 
    }
    function index() {
        $this->set_title(lang('admin_questions_list'));
        $data = $this->includes;
        $content_data = array();
        $data['content'] = $this->load->view('admin/questions/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function add($quiz_id = false) 
    {
        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);
        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', "Invalid Quiz Id"); 
            return redirect(base_url("admin/quiz"));
        }

        $question_image = NULL; 
        $question_file_link = NULL;
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
        {   
            $path = "./assets/images/questions";
            $allowed_formate = 'gif|jpg|png|bmp|jpeg';

            $upload_type = $this->input->post('upload_type',TRUE);
            if($upload_type == "audio")
            {
                $allowed_formate = 'ogg|wav|mp3|mpeg|m4a';
            }
            $response_file_upload = $this->do_upload_file('image',$path,$allowed_formate);

            if($response_file_upload['status'] == 'success')
            {
                $question_image = $response_file_upload['upload_data']['file_name'];
                $question_file_link = $path.$question_image;
            }
            else
            {
                $this->session->set_flashdata('error', $response_file_upload['error']); 
                $this->form_validation->set_rules('image_upload_error', 'Question Image', 'required');
            }
        }


        $solution_image = NULL; 
        $solution_file_link = NULL;
        if(isset($_FILES['solutionimage']['name']) && $_FILES['solutionimage']['name'])
        {   
           
            $path = "./assets/images/questions/solution";
            $allowed_formate = 'gif|jpg|png|bmp|jpeg';
            $response_file_upload = $this->do_upload_file('solutionimage',$path,$allowed_formate);

            if($response_file_upload['status'] == 'success')
            {
                $solution_image = $response_file_upload['upload_data']['file_name'];
                $solution_file_link = $path.$solution_image;
            }
            else
            {
                $this->session->set_flashdata('error', $response_file_upload['error']); 
                $this->form_validation->set_rules('solutionimage_error', 'Solution Image', 'required');
            }
        }
        

        $this->form_validation->set_rules('question_type_is_match', 'Question Choices Type', 'required|trim');




        $question_type_is_match = $this->input->post('question_type_is_match') ? $this->input->post('question_type_is_match') : "NO";
        $choices_array = array();
        $arr_is_correct = array();
        $display_order_array =  array();
        $araay_to_display_match = array();

        if($question_type_is_match == "YES")
        {
            $this->form_validation->set_rules('mark_choices[]', 'Choices', 'required|trim');
            $this->form_validation->set_rules('mark_is_correct[]', 'Question Choice 2', 'required|trim');
            $this->form_validation->set_rules('is_correct_display_order[]', 'Choice 2 Display Order', 'required|trim');
            
            $choices_array = $this->input->post('mark_choices') ? $this->input->post('mark_choices') : array();
            $arr_is_correct = $this->input->post('mark_is_correct') ? $this->input->post('mark_is_correct') : array();
            $display_order_array = $this->input->post('is_correct_display_order') ? $this->input->post('is_correct_display_order') : array();


            if($choices_array && $arr_is_correct && $display_order_array)
            {
                foreach ($arr_is_correct as $arr_is_correct_key => $arr_is_correct_value) 
                {
                    if(isset($display_order_array[$arr_is_correct_key]) && $display_order_array[$arr_is_correct_key] != "" && isset($arr_is_correct[$display_order_array[$arr_is_correct_key]]))
                    {
                        $araay_to_display_match[$arr_is_correct_key] = $arr_is_correct[$display_order_array[$arr_is_correct_key]];
                    }
                    else
                    {
                        $this->form_validation->set_rules('invalid_dispaly_indexes', 'Invalid Choice 2 Display Order', 'required|trim');
                        break;
                    }
                    
                }
                
            }

            if(count($araay_to_display_match) != count($choices_array))
            {
                $this->form_validation->set_rules('invalid_dispaly_indexes', 'Invalid Choice 2 Display Order', 'required|trim');
            }
        }
        else
        {
            $this->form_validation->set_rules('choices[]', 'Choices', 'required|trim');
            $choices_array = $this->input->post('choices') ? $this->input->post('choices') : array();
            $choices_array = (isset($_POST['choices']) && $_POST['choices']) ? $_POST['choices'] : array();
            $arr_is_correct = $this->input->post('is_correct') ? $this->input->post('is_correct') : array();
            
            if(empty($arr_is_correct) && $_POST)
            {
                $this->form_validation->set_rules('is_correct[]', 'One Correct Choices', 'required|trim');
                $this->session->set_flashdata('error', lang('Please Choose One Correct Choices..! '));
            }
        }
        

        $this->form_validation->set_rules('title', 'Title', 'required|trim|is_unique[questions.title]');  

        $count_of_option = count($choices_array);
        $no_of_option = $count_of_option > 0 ? $count_of_option - 1 : 0;

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
            $errors = $this->form_validation->error_array();
            
            $err_is_correct = (isset($errors['is_correct_display_order[]']) OR isset($errors['is_correct[]'])) ? TRUE : FALSE;
            if($err_is_correct)
            {
                $this->session->set_flashdata('error', lang('Cross Choice All Fields Are Required'));
            }

            if(isset($errors['invalid_dispaly_indexes']) && $err_is_correct == FALSE)
            {
                $this->session->set_flashdata('error', lang('Invalid Choice 2 Display Order Index'));
            }

            if($question_image && file_exists($question_file_link))
            {
                unlink($question_file_link);
            }

            if($solution_image && file_exists($solution_file_link))
            {
                unlink($solution_file_link);
            }
        } 
        else 
        {
            action_not_permitted();

            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
            $count_is_correct = is_array($arr_is_correct) ?  count($arr_is_correct) : 1;
            $is_multiple = $count_is_correct > 1 ? 1 : 0;
            
            $correct_choice = array();

            if($question_type_is_match == "YES")
            {
                $correct_choice['choices_array'] = $choices_array;
                $correct_choice['arr_is_correct'] = $arr_is_correct;
                $correct_choice['display_order_array'] = $display_order_array;
                $correct_choice['araay_to_display_match'] = $araay_to_display_match;
            }
            else
            {
                foreach ($choices_array as $key => $option_value) 
                {
                    if(isset($arr_is_correct[$key]) && $arr_is_correct[$key] && $option_value)
                    {
                        $correct_choice[$key] = $option_value;
                    }
                }
            }

            $queston_choies_type = $count_of_option > 1 ? "choices" : "text";
            $title = $this->input->post('title') ? $this->input->post('title') : '';
            $title = $_POST['title'] ? $_POST['title'] : '';
            $question_content = array();
            $question_content['quiz_id'] = $quiz_id;
            $question_content['title'] = $title;
            $question_content['is_multiple'] = $is_multiple;
            $question_content['choices'] = json_encode($choices_array);
            $question_content['correct_choice'] = json_encode($correct_choice);
            $question_content['solution_image'] = $solution_image;
            $question_content['solution'] = $this->input->post('solution',TRUE);
            $question_content['addon_type'] = $this->input->post('addon_type',TRUE);
            $question_content['addon_value'] = $this->input->post('addon_value',TRUE);
            $question_content['question_paragraph_id'] = $this->input->post('question_paragraph_id',TRUE);
            $question_content['question_section_id'] = $this->input->post('question_section_id',TRUE);
            $question_content['queston_choies_type'] = $queston_choies_type;
            $question_content['question_type_is_match'] = $question_type_is_match;
            $question_content['added'] =  date('Y-m-d H:i:s');
            $question_content['render_content'] =  $this->input->post('render_content',TRUE);
            $question_content['helpsheet'] = $helpsheet_file;

            if($question_image)
            {
                $question_content['image'] = $question_image;
                $question_content['upload_type'] = $upload_type;
            }


            $question_id = $this->QuestionModel->insert_question($question_content);

            if($question_id)
            {
                $this->session->set_flashdata('message', lang('admin_record_added_successfully'));  
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_adding_record')); 
            }
            
            redirect(base_url('admin/quiz/questions/'.$quiz_id));
        }

        $quiz_name_array = array();
        $all_quiz = $this->QuestionModel->get_all_quiz();
        foreach ($all_quiz as $quiz_array) 
        {
            $quiz_name_array[''] = lang('select_quiz');
            $quiz_name_array[$quiz_array->id] = $quiz_array->title;
        }
        
        $pragraph_data = $this->db->order_by('order','asc')->get('paragraph')->result();
        $section_data = $this->db->get('section')->result();

        $this->set_title(lang('admin_add_question').": ".$quiz_data->title);
        $data = $this->includes;

        $content_data = array('quiz_name_array' => $quiz_name_array,'choices_array' => $choices_array, 'no_of_option' => $no_of_option, 'arr_is_correct' => $arr_is_correct,'quiz_id'=>$quiz_id,'quiz_data' => $quiz_data,'pragraph_data' => $pragraph_data,'section_data' => $section_data,'display_order_array'=>$display_order_array,'question_type_is_match' => $question_type_is_match);
        // load views
        $data['content'] = $this->load->view('admin/questions/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function update($quiz_id = NULL, $question_id = NULL) 
    {
        $question_data = $this->QuestionModel->get_question_by_id($question_id);
        
        if(empty($question_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/questions'));
        }
        
        $quiz_id = $question_data->quiz_id;
        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);
        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', "Invalid Quiz Id"); 
            return redirect(base_url("admin/quiz"));
        }


        $question_image = NULL; 
        $question_file_link = NULL;
        if(isset($_FILES['image']['name']) && $_FILES['image']['name'])
        {   
            $path = "./assets/images/questions";
            $allowed_formate = 'gif|jpg|png|bmp|jpeg';

            $upload_type = $this->input->post('upload_type',TRUE);
            if($upload_type == "audio")
            {
                $allowed_formate = 'ogg|wav|mp3|mpeg|m4a';
            }
            $response_file_upload = $this->do_upload_file('image',$path,$allowed_formate);

            if($response_file_upload['status'] == 'success')
            {
                $question_image = $response_file_upload['upload_data']['file_name'];
                $question_file_link = $path.$question_image;
            }
            else
            {
                $this->session->set_flashdata('error', $response_file_upload['error']); 
                $this->form_validation->set_rules('image_upload_error', 'Question Image', 'required');
            }
        }


        $solution_image = NULL; 
        $solution_file_link = NULL;
        if(isset($_FILES['solutionimage']['name']) && $_FILES['solutionimage']['name'])
        {   
            $path = "./assets/images/questions/solution";
            $allowed_formate = 'gif|jpg|png|bmp|jpeg';
            $response_file_upload = $this->do_upload_file('solutionimage',$path,$allowed_formate);

            if($response_file_upload['status'] == 'success')
            {
                $solution_image = $response_file_upload['upload_data']['file_name'];
                $solution_file_link = $path.$solution_image;
            }
            else
            {
                $this->session->set_flashdata('error', $response_file_upload['error']); 
                $this->form_validation->set_rules('solutionimage_error', 'Solution Image', 'required');
            }
        }

        $questions_title = $this->input->post('title');
        $title_unique = $questions_title != $question_data->title ? '|is_unique[questions.title]' : '';
        $this->form_validation->set_rules('title', 'Title', 'required|trim'.$title_unique);    

        $question_type_is_match = $this->input->post('question_type_is_match');
        $choices_array = array();
        $arr_is_correct = array();
        $display_order_array =  array();
        $araay_to_display_match = array();

        if($question_type_is_match == "YES")
        {
            $this->form_validation->set_rules('mark_choices[]', 'Choices', 'required|trim');
            $this->form_validation->set_rules('mark_is_correct[]', 'Question Choice 2', 'required|trim');
            $this->form_validation->set_rules('is_correct_display_order[]', 'Choice 2 Display Order', 'required|trim');
            
            $choices_array = $this->input->post('mark_choices') ? $this->input->post('mark_choices') : array();
            $arr_is_correct = $this->input->post('mark_is_correct') ? $this->input->post('mark_is_correct') : array();
            $display_order_array = $this->input->post('is_correct_display_order') ? $this->input->post('is_correct_display_order') : array();


            if($choices_array && $arr_is_correct && $display_order_array)
            {
                foreach ($arr_is_correct as $arr_is_correct_key => $arr_is_correct_value) 
                {
                    if(isset($display_order_array[$arr_is_correct_key]) && $display_order_array[$arr_is_correct_key] != "" && isset($arr_is_correct[$display_order_array[$arr_is_correct_key]]))
                    {
                        $araay_to_display_match[$arr_is_correct_key] = $arr_is_correct[$display_order_array[$arr_is_correct_key]];
                    }
                    else
                    {
                        $this->form_validation->set_rules('invalid_dispaly_indexes', 'Invalid Choice 2 Display Order', 'required|trim');
                        break;
                    }
                    
                }
                
            }

            if(count($araay_to_display_match) != count($choices_array))
            {
                $this->form_validation->set_rules('invalid_dispaly_indexes', 'Invalid Choice 2 Display Order', 'required|trim');
            }
        }
        else
        {
            $this->form_validation->set_rules('choices[]', 'Choices', 'required|trim');
            $choices_array = $this->input->post('choices') ? $this->input->post('choices') : array();
            $choices_array = (isset($_POST['choices']) && $_POST['choices']) ? $_POST['choices'] : array();
            
            $arr_is_correct = $this->input->post('is_correct') ? $this->input->post('is_correct') : array();

            if(empty($arr_is_correct) && $_POST)
            {
                $this->form_validation->set_rules('is_correct[]', 'One Correct Choices', 'required|trim');
                $this->session->set_flashdata('error', lang('Please Choose One Correct Choices..! '));
            }
        }
     
        if(empty($choices_array))
        {
            $choices_array = json_decode($question_data->choices);
            $arr_is_correct = json_decode($question_data->correct_choice);
            $question_type_is_match = $question_data->question_type_is_match;
            if($question_data->question_type_is_match == "YES")
            {
                $display_order_array = isset($arr_is_correct->display_order_array) ? $arr_is_correct->display_order_array : array();
                $araay_to_display_match = isset($arr_is_correct->araay_to_display_match) ? $arr_is_correct->araay_to_display_match : array();
                $arr_is_correct = isset($arr_is_correct->arr_is_correct) ? $arr_is_correct->arr_is_correct : array();

            }

        }
        
        $arr_is_correct = json_decode(json_encode($arr_is_correct), true);
        $choices_array = json_decode(json_encode($choices_array), true);
        $display_order_array = json_decode(json_encode($display_order_array), true);
        $count_of_option = count($choices_array);
        $no_of_option = $count_of_option > 0 ? $count_of_option - 1 : 0;
        $question_type_is_match = $question_type_is_match ? $question_type_is_match : "NO";

        $helpsheet = NULL; 
        $helpsheet_file_link = NULL;
        $helpsheet_file = $question_data->helpsheet;
        if(isset($_FILES['helpsheet']['name']) && $_FILES['helpsheet']['name'])
        {   
            $path = "./assets/images/helpsheet";
            if($question_data->helpsheet && file_exists($path))
            {
                unlink($path.'/'.$question_data->helpsheet);
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

        if($this->form_validation->run() == false) 
        {
            $errors = $this->form_validation->error_array();
            
            $err_is_correct = (isset($errors['is_correct_display_order[]']) OR isset($errors['is_correct[]'])) ? TRUE : FALSE;
            if($err_is_correct)
            {
                $this->session->set_flashdata('error', lang('Cross Choice All Fields Are Required'));
            }

            if(isset($errors['invalid_dispaly_indexes']) && $err_is_correct == FALSE)
            {
                $this->session->set_flashdata('error', lang('Invalid Choice 2 Display Order Index'));
            }


            if($question_image && file_exists($question_file_link))
            {
                unlink($question_file_link);
            }

            if($solution_image && file_exists($solution_file_link))
            {
                unlink($solution_file_link);
            }            
        }  
        else 
        {

            action_not_permitted();
            $question_content = array();
            
            $user_id = $this->user['id'] ? $this->user['id'] : NULL;
            $count_is_correct = is_array($arr_is_correct) ?  count($arr_is_correct) : 1;
            $is_multiple = $count_is_correct > 1 ? 1 : 0;

            $correct_choice = array();

            if($question_type_is_match == "YES")
            {
                $correct_choice['choices_array'] = $choices_array;
                $correct_choice['arr_is_correct'] = $arr_is_correct;
                $correct_choice['display_order_array'] = $display_order_array;
                $correct_choice['araay_to_display_match'] = $araay_to_display_match;
            }
            else
            {
                foreach ($choices_array as $key => $option_value) 
                {
                    if(isset($arr_is_correct[$key]) && $arr_is_correct[$key])
                    {
                        $correct_choice[$key] = $option_value;
                    }
                }
            }

            $queston_choies_type = $count_of_option > 1 ? "choices" : "text";
            $title = $this->input->post('title') ? $this->input->post('title') :'';
            $title = $_POST['title'] ? $_POST['title'] : '';
            $question_content['quiz_id'] = $quiz_id;
            $question_content['title'] = $title;
            $question_content['is_multiple'] = $is_multiple;
            $question_content['choices'] = json_encode($choices_array);
            $question_content['correct_choice'] = json_encode($correct_choice);
            $question_content['solution'] = $this->input->post('solution',TRUE);
            $question_content['addon_type'] = $this->input->post('addon_type',TRUE);
            $question_content['addon_value'] = $this->input->post('addon_value',TRUE);
            $question_content['question_paragraph_id'] = $this->input->post('question_paragraph_id',TRUE);
            $question_content['question_section_id'] = $this->input->post('question_section_id',TRUE);
            $question_content['queston_choies_type'] = $queston_choies_type;
            $question_content['question_type_is_match'] = $question_type_is_match;
            $question_content['updated'] =  date('Y-m-d H:i:s');
            $question_content['render_content'] =  $this->input->post('render_content',TRUE);
            $question_content['helpsheet'] = $helpsheet_file;
            //p($question_content);
            if($question_image)
            {
                $question_content['image'] = $question_image;
                $question_content['upload_type'] = $upload_type;
            }
            
            if($solution_image)
            {
                $question_content['solution_image'] = $solution_image;
            }


            $question_update_status = $this->QuestionModel->update_question($question_id, $question_content);

            if($question_update_status)
            {
                $this->session->set_flashdata('message', lang('admin_record_updated_successfully'));
            }
            else
            {
                $this->session->set_flashdata('error', lang('admin_error_during_update_record')); 
            }
            redirect(base_url('admin/quiz/questions/'.$quiz_id));
        }


        $quiz_name_array = array();
        $all_quiz = $this->QuestionModel->get_all_quiz();
        foreach ($all_quiz as $quiz_array) 
        {
            $quiz_name_array[''] = lang('select_quiz');
            $quiz_name_array[$quiz_array->id] = $quiz_array->title;
        }

        $pragraph_data = $this->db->order_by('order','asc')->get('paragraph')->result();
        $section_data = $this->db->get('section')->result();

        $this->set_title(lang('admin_update_question'));
        $data = $this->includes;

        $question_data = json_decode(json_encode($question_data),TRUE);

        $content_data = array('question_id' => $question_id, 'quiz_name_array' => $quiz_name_array,'choices_array' => $choices_array,'question_data' => $question_data,'no_of_option' => $no_of_option, 'arr_is_correct' => $arr_is_correct,'quiz_id' => $quiz_id,'quiz_data' => $quiz_data,'pragraph_data' => $pragraph_data,'section_data' => $section_data,'display_order_array'=>$display_order_array,'question_type_is_match' => $question_type_is_match);
        // load views
        $data['content'] = $this->load->view('admin/questions/form', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    public function copy($quiz_id = NULL,$question_id = NULL) 
    {
        action_not_permitted();
        $question_data = $this->QuestionModel->get_question_by_id($question_id);

        if(empty($question_data))
        {
           $this->session->set_flashdata('error', lng('invalid_footer_link_id')); 
           redirect(base_url('admin/quiz/questions/'.$quiz_id));
        }

        $question_name_count = $this->QuestionModel->question_name_like_this(NULL,$question_data->title);
        $count = $question_name_count > 0 ? '-' . $question_name_count : '';
        
        $question_content = array();

        $question_content['quiz_id']        = $question_data->quiz_id;
        $question_content['title']          = $question_data->title.'-copy '.$count;
        $question_content['is_multiple']    =  $question_data->is_multiple;
        $question_content['choices']        = $question_data->choices;
        $question_content['correct_choice'] =  $question_data->correct_choice;
        $question_content['image']          =  NULL;
        $question_content['upload_type']    =  NULL;
        $question_content['addon_type'] = $question_data->addon_type;
        $question_content['addon_value'] = $question_data->addon_value;
        $question_content['question_paragraph_id'] =  $question_data->question_paragraph_id;
        $question_content['question_section_id'] =  $question_data->question_section_id;
        $question_content['question_type_is_match'] =  $question_data->question_type_is_match;
        $question_content['queston_choies_type'] =  $question_data->queston_choies_type;
        $question_content['added']          =  date('Y-m-d H:i:s');

        $question_new_id = $this->QuestionModel->insert_question($question_content);

        if($question_new_id)
        {
          $this->session->set_flashdata('message', lang('record_copied_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_copying_record')); 
        } 
        redirect(base_url('admin/quiz/questions/'.$quiz_id));
    }

    function delete($quiz_id = NULL,$question_id = NULL)
    {
        action_not_permitted();
        $status = $this->QuestionModel->delete_question($question_id,$quiz_id);
        if ($status) 
        {
          $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));  
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('admin/quiz/questions/'.$quiz_id));   
    }

    function quiz_upload_file() 
    {
        $image = array();
        $name = $_FILES['file']['name'];
        $config['upload_path'] = "./assets/images/quiz";
        $config['allowed_types'] = 'jpg|png|bmp|jpeg';
        $this->load->library('upload', $config);
        $status = $this->upload->do_upload('file');
        if ($status) 
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

    function dropzone_quiz_file_remove() 
    {
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

    function question_list() 
    {
        $data = array();
        $list = $this->QuestionModel->get_question(); 
        $no = $_POST['start'];
        foreach ($list as $question) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = mb_convert_encoding($question->quiz_name, 'UTF-8', 'UTF-8');
            $row[] = mb_convert_encoding($question->title, 'UTF-8', 'UTF-8');
            $button = '<a href="' . base_url("admin/questions/update/". $question->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-pencil-alt"></i></a>';

            $button .= '<a href="' . base_url("admin/questions/copy/" . $question->id) . '" data-toggle="tooltip" title="'.lang("admin_copy_record").'" class="common_copy_record btn btn-warning mr-1"><i class="far fa-copy"></i></a>';

            $button .= '<a href="' . base_url("admin/questions/delete/" . $question->id) . '" data-toggle="tooltip" title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'], 
            "recordsTotal" => $this->QuestionModel->count_all(), 
            "recordsFiltered" => $this->QuestionModel->count_filtered(), 
            "data" => $data
        );

        //output to json format
        echo json_encode($output);
    }

    public function delete_image($question_id = NULL) 
    {
        $image_name = $this->input->post('image_name', TRUE);
        if ($question_id && $image_name) 
        {
            $question_data = $this->QuestionModel->get_quiz_by_id($question_id);
            $image_array = json_decode($question_data->image);
            $image_array = json_decode(json_encode($image_array), True);
            if (($key = array_search($image_name, $image_array)) !== false) 
            {
                unset($image_array[$key]);
            }
            $updated_image_value = json_encode($image_array);
            $result = $this->QuestionModel->update_quiz_images_by_id($question_id, $updated_image_value);
            $path = "./assets/images/quiz/$image_name";
            unlink($path);
            unlink("./assets/images/quiz/thumbnail/$image_name");
            unlink("./assets/images/quiz/small/$image_name");
            unlink("./assets/images/quiz/medium/$image_name");
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

        echo $resize_status ? lang('file_copy_success') : lang('resize_errors');        
    } 

    public function do_upload_image($filename, $path)
    {

        $new_name = time().$_FILES[$filename]['name'];
        $config['upload_path']          = $path;
        $config['allowed_types']        = 'jpg|png|jpeg|gif';
        $config['max_size']             = 50000;
        $config['max_width']            = 4000;
        $config['max_height']           = 3000;
        $config['file_name'] = $new_name;
        $this->load->library('upload', $config);
        $this->upload->initialize($config);

        if ( ! $this->upload->do_upload($filename))
        {
            $respons = array(   'status' => 'error',
                                'error' => $this->upload->display_errors()
                            );

           
        }
        else
        {
            $respons = array('status' => 'success',
                             'upload_data' => $this->upload->data(),
                            );
        }
 
        return $respons;
    }


    public function translate_questions($question_id)
    {
        if(empty($question_id))
        {
            $this->session->set_flashdata('error', lang('invalid_url')); 
            redirect(base_url('admin/questions'));
        }

        $question_data = $this->QuestionModel->get_question_by_id($question_id);

        if(empty($question_data))
        {
            $this->session->set_flashdata('error', lang('admin_invalid_id')); 
            redirect(base_url('admin/questions'));
        }

        $this->form_validation->set_rules('question_id', 'Question Id', 'required|trim');
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
                    $translatition_content['table'] = 'questions';
                    $translatition_content['forigen_table_id'] = $question_id;
                    $translatition_content['lang_id'] = $lang_id;
                    $translatition_content['column'] = $column_name;
                    if($column_name == 'title')
                    {
                        $translatition_content['value'] = $column_value;
                    }
                    else
                    {
                        $column_value = json_decode(json_encode($column_value), true);
                        $translatition_content['value'] = json_encode($column_value);
                    }
                    $translatition_content_array[] = $translatition_content;
                }
            }

            if($translatition_content_array)
            {
                $translated_data = $this->QuestionModel->delete_translated_data($question_id);
                $status = $this->QuestionModel->insert_translated_data($translatition_content_array);
                if($status)
                {
                    $this->session->set_flashdata('message', lang('question_translation_success'));
                }
                else
                {
                    $this->session->set_flashdata('error', lang('question_translation_invalid_form_data'));
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('question_translation_invalid_form_data'));
            }
            redirect(base_url('admin/questions/update/'.$question_data->quiz_id.'/'.$question_id));
        }
        $languages = $this->QuestionModel->get_languages();

        $translated_data_array = array();
        $translated_db_data = $this->QuestionModel->get_translated_data($question_id);

        if($translated_db_data)
        {
            foreach ($translated_db_data as $key => $translate_array) 
            {
                if($translate_array->column =='title')
                {
                    $translated_data_array[$translate_array->lang_id][$translate_array->column] = $translate_array->value;
                }
                else
                {
                    $column_value = json_decode($translate_array->value);
                    $column_value = json_decode(json_encode($column_value), true);
                    $translated_data_array[$translate_array->lang_id][$translate_array->column] = $column_value;
                }
            }
        }

        $translated_data_array =  $translated_post_data ?  $translated_post_data : $translated_data_array;
        if(empty($translated_data_array))
        { 
            $choices_array = json_decode($question_data->choices);
            $correct_choice_array = json_decode($question_data->correct_choice);
            foreach ($languages as $language) 
            {
                $translated_data_array[$language->id]['title'] = $question_data->title;
                foreach ($choices_array as $key => $choices) 
                {
                    $translated_data_array[$language->id]['choices'][$key] = $choices;
                }

                foreach ($correct_choice_array as $key => $correct_choice) 
                {
                    $translated_data_array[$language->id]['correct_choice'][$key] = $correct_choice;
                }

                $translated_data_array[$language->id]['description'] = '';
                $translated_data_array[$language->id]['quiz_instruction'] = '';
            }
        }
        
        $this->set_title(lang('translate').': '.$question_data->title);
        $data = $this->includes;

        $content_data = array('question_id' => $question_id, 'question_data'=> $question_data, 'languages'=> $languages,'translated_data_array'=>$translated_data_array);

        $data['content'] = $this->load->view('admin/questions/translate_question', $content_data, TRUE);
        // p('fvfv');
        $this->load->view($this->template, $data);
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

    function delete_helpsheet($quiz_id = NULL, $question_id = NULL)
    {
        action_not_permitted();
        $question_data = $this->QuestionModel->get_question_by_id($question_id);
       
        if(empty($question_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/questions/update/'.$quiz_id.'/'.$question_id));
        }
        
        $quiz_id = $question_data->quiz_id;
        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', "Invalid Quiz Id"); 
            return redirect(base_url("admin/quiz"));
        }
        
        $path = "./assets/images/helpsheet/$question_data->helpsheet";
        
        unlink($path);
       
        $status = $this->QuestionModel->update_helpsheet_field_in_question($quiz_id,$question_id); 
        
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }

        redirect(base_url('admin/questions/update/'.$quiz_id.'/'.$question_id));
    }

    function delete_upload_file_type($quiz_id = NULL, $question_id = NULL)
    {
        action_not_permitted();
        $question_data = $this->QuestionModel->get_question_by_id($question_id);
       
        if(empty($question_data))
        {
           $this->session->set_flashdata('error', lang('admin_invalid_id')); 
           redirect(base_url('admin/questions/update/'.$quiz_id.'/'.$question_id));
        }
        
        $quiz_id = $question_data->quiz_id;
        $quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
            $this->session->set_flashdata('error', "Invalid Quiz Id"); 
            return redirect(base_url("admin/quiz"));
        }
        
        $path = "./assets/images/questions/$question_data->image";
      
        unlink($path);
       
       
        $status = $this->QuestionModel->update_file_type_field_in_question($quiz_id,$question_id); 
        
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }

        redirect(base_url('admin/questions/update/'.$quiz_id.'/'.$question_id));
    }
}
