<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Test_Controller extends Public_Controller {

    /**
     * Constructor
     */
    function __construct() 
    {
        parent::__construct(); 
        $this->load->model('TestModel');
        $this->load->model('Payment_model');
        $this->add_js_theme('Chart.min.js');
        $this->add_js_theme('autogrow.js');
        $this->add_js_theme('test.js');
        $this->add_js_theme('jquery.simple.timer.js');
        $this->add_js_theme('dojo.js');
        $this->add_js_theme('notify.min.js');
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->load->library('form_validation'); 
        $this->load->library('session');
        $this->add_js_theme('perfect-scrollbar.min.js');
        $this->add_css_theme('table-main.css');
        $this->add_css_theme('perfect-scrollbar.css');        
    }

    function set_test_session($quiz_id = NULL) 
    { 

        if(isset($this->session->quiz_session) && $this->session->quiz_session)
        {
            $session_quiz_id = $this->session->quiz_session['quiz_data']['id'];
            return redirect(base_url("test/$session_quiz_id/1"));
        }

        $leader_bord_user_name = $this->session->leader_bord_user_name;
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0; 

        if(empty($leader_bord_user_name) && $user_id <= 0)
        {
           $this->session->set_flashdata('error', lang('user_required')); 
           redirect(base_url(''));
        }
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
        $loged_in_user_data = $this->db->where('id',$user_id)->get('users')->row();
        $quiz_data = $this->TestModel->get_quiz_by_id($quiz_id);

        if(empty($quiz_data))
        {
           $this->session->set_flashdata('error', lang('invalid_id')); 
           redirect(base_url());
        }

        if($quiz_data->is_registered == 1 && empty($user_id))
        {
            $this->session->set_flashdata('error', 'Plz Login First');
            return redirect(base_url("quiz-detail/quiz/$quiz_id"));    
        }

        $find_no_of_question_particular_quiz = $this->TestModel->find_no_of_question($quiz_id);
        
        if(empty($find_no_of_question_particular_quiz))
        {
            $this->session->set_flashdata('error', lang('this_quiz_have_no_question'));
            return redirect(base_url());
        }
        
        if($quiz_data->is_sheduled_test == 1) 
        {
            $start_date_time_code = $quiz_data->start_date_time;
            $end_date_time_code = $quiz_data->end_date_time;

            if($end_date_time_code < strtotime(date("Y-m-d H:i:s")))
            {
                $this->session->set_flashdata('error', lang('Sorry This Quiz Has Been Expired  ..!'));
                return redirect(base_url("quiz-detail/quiz/$quiz_id"));   
            }

            if($start_date_time_code > strtotime(date("Y-m-d H:i:s")))
            {
                $this->session->set_flashdata('error', lang('This Quiz Start From '.date("Y-m-d H:i",$start_date_time_code)));
                return redirect(base_url("quiz-detail/quiz/$quiz_id"));   
            }
        }
        $is_admin = (isset($loged_in_user_data->is_admin) && $loged_in_user_data->is_admin == 1 ) ? TRUE : FALSE; 

        if($is_admin == FALSE  && $login_user_id != $quiz_data->user_id)
        {
            $check_quiz_multiple_attemp_by_category = $this->TestModel->check_quiz_multiple_attemp_by_category($quiz_data->category_id,$user_id);

            foreach ($check_quiz_multiple_attemp_by_category as $multiple_attemp_array) 
            {

                if($multiple_attemp_array->id == $quiz_id && $multiple_attemp_array->attempt > 0)
                {   
                    // $is_passed = $this->TestModel->is_quiz_already_given_or_pass($quiz_id, $user_id);
                    $quiz_passing_count = $this->TestModel->number_of_time_quiz_pass_by_user($quiz_id, $user_id);
                    if($quiz_passing_count >= $multiple_attemp_array->attempt)
                    {
                        $this->session->set_flashdata('error', lang('Sorry You Already Pass This Quiz So Multiple Attemp Is Not Allowed ..!'));
                        return redirect(base_url("quiz-detail/quiz/$quiz_id"));   
                    }
                }
            }

            if($quiz_data->difficulty_level > 1)
            {

                $quiz_child_difficulty_level = $this->TestModel->get_quiz_with_child_difficulty_level($quiz_data->category_id,$quiz_data->difficulty_level,$user_id);

                foreach ($quiz_child_difficulty_level as $child_difficulty_level) 
                {
                    $correct = $child_difficulty_level->correct > 0 ? $child_difficulty_level->correct : 0;
                    $test_has_questions = $child_difficulty_level->test_has_questions > 0 ? $child_difficulty_level->test_has_questions : 1;
                    $user_test_percentage =  ($correct/$test_has_questions)*100;
                    if($child_difficulty_level->quiz_passing_percentage > $user_test_percentage OR empty($child_difficulty_level->correct))
                    {
                        $this->session->set_flashdata('error', lang('category_related_quiz_clear'));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }
                }

                $get_quiz_difficulty_result = $this->TestModel->get_quiz_with_difficulty_level_result($quiz_data->id,$user_id);
                foreach ($get_quiz_difficulty_result as $difficulty_result_array) 
                {

                    if( empty($user_is_prime) && $difficulty_result_array->difficulty_level == 3 && $difficulty_result_array->id == $quiz_id)
                    {
                        $this->session->set_flashdata('error', lang('Sorry You Are Not Premiu Member..!'));
                        return redirect($_SERVER['HTTP_REFERER']);
                    }

                    $correct = $difficulty_result_array->correct > 0 ? $difficulty_result_array->correct : 0;
                    $test_has_questions = $difficulty_result_array->test_has_questions > 0 ? $difficulty_result_array->test_has_questions : 1;
                    
                    $user_test_percentage =  ($correct/$test_has_questions)*100;
                }    
            }


            $test_taken = $this->TestModel->get_test_taken($quiz_id,$user_id);
            if($quiz_data->is_registered == 1 && $quiz_data->attempt > 0 && ($quiz_data->attempt == $test_taken['count']))
            {
                $this->session->set_flashdata('error', lang('test_already_given'));
                return redirect(base_url("quiz-detail/quiz/$quiz_id"));    
            }
        } 


        if($quiz_data->price > 0  && $login_user_id != $quiz_data->user_id)
        {
            $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status('quiz',$quiz_id);
            if(empty($quiz_last_paymetn_status))
            {
                $this->session->set_flashdata('error', lang('You Need To Buy This Quiz  Before Start Quiz ...')); 
                return redirect(base_url("quiz-detail/quiz/$quiz_id"));  
            }
        }

        if($quiz_data->is_premium == 1  && $login_user_id != $quiz_data->user_id)
        {
            $get_logged_in_user_membership = $this->TestModel->get_user_membership($user_id);
            if($get_logged_in_user_membership) 
            {
                $membership_session = $this->session->userdata('membership');
                $this->session->unset_userdata('membership');
                $membership_session['validity'] = $get_logged_in_user_membership->validity;
                $this->session->set_userdata(array('membership'=>$membership_session));
                
                if($this->session->userdata('membership') && $this->session->userdata('membership')['validity'] < date('Y-m-d'))
                {
                    $this->session->set_flashdata('error', lang('renew_your_membership'));
                    return redirect(base_url("quiz-detail/quiz/$quiz_id"));    
                }
            }
            else
            {
                $this->session->set_flashdata('error', lang('please_take_membership'));
                return redirect(base_url("quiz-detail/quiz/$quiz_id"));    
            }
        }

        $time_accommodation = (isset($loged_in_user_data->time_accommodation) && $loged_in_user_data->time_accommodation > 0) ? $loged_in_user_data->time_accommodation : 0;

        $quiz_data = json_decode(json_encode($quiz_data), true);
        $is_random = $quiz_data['is_random'];
        $quiz_time = $quiz_data['duration_min'];
        if($time_accommodation > 0)
        {
           $quiz_time = round(($quiz_time * $time_accommodation) * 60);
           // $new_time_accommodation = round($quiz_time * $time_accommodation);
           // $quiz_time = $quiz_time + $new_time_accommodation;
        }
        else
        {
            $quiz_time = round($quiz_time * 60);
        }

        $duration_min = ($quiz_data['duration_min'] > 0) ? date('Y-m-d H:i:s',strtotime("+$quiz_time seconds")) : date("Y-m-d H:i:s", strtotime('+24 hours'));

        $number_questions = $quiz_data['number_questions'];

        if($is_random == 1)
        { 
            $quiz_question_data = $this->TestModel->get_random_question_by_quiz_id($quiz_id,$number_questions);
        }
        else
        {
            $quiz_question_data = $this->TestModel->get_question_by_quiz_id($quiz_id,$number_questions);
        }
        
        $quiz_question_data = json_decode(json_encode($quiz_question_data), true);
               
        if(empty($quiz_question_data))
        {
            $this->session->set_flashdata('error', lang('there_no_question_in_this_quiz'));
            return redirect(base_url());
        }
        $temp_quiz_question_data = array();
        $lang_id = get_language_data_by_language($this->session->userdata('language'));
        foreach ($quiz_question_data as $key => $quiz_question_val_array) 
        {
            $new_quiz_question_data = array();
            $translated_qus_title = get_translated_column_value($lang_id,'questions',$quiz_question_val_array['id'],'title');
            $translated_qus_title = $translated_qus_title ? $translated_qus_title : $quiz_question_val_array['title'];

            $translated_qus_choices = get_translated_column_value($lang_id,'questions',$quiz_question_val_array['id'],'choices');
            $translated_qus_choices = $translated_qus_choices ? $translated_qus_choices : $quiz_question_val_array['choices'];
            
            if($quiz_question_val_array['is_random_option'] == 1)
            {
                $decode_choices = json_decode($translated_qus_choices);
                shuffle($decode_choices);
                $decode_choices = $decode_choices ? $decode_choices : array();
                $translated_qus_choices = json_encode($decode_choices);   
            }

            $translated_qus_correct_choice = get_translated_column_value($lang_id,'questions',$quiz_question_val_array['id'],'correct_choice');
            $translated_qus_correct_choice = $translated_qus_correct_choice ? $translated_qus_correct_choice : $quiz_question_val_array['correct_choice'];

            $question_type_is_match = $quiz_question_val_array['question_type_is_match'];

            



            $question_section_name = $quiz_question_val_array['question_section_name'] ? $quiz_question_val_array['question_section_name'] : "GENRAL QUESTIONS";
            

            $new_quiz_question_data['id'] = $quiz_question_val_array['id'];
            $new_quiz_question_data['quiz_id'] = $quiz_question_val_array['quiz_id'];
            $new_quiz_question_data['title'] = $translated_qus_title;
            $new_quiz_question_data['image'] = $quiz_question_val_array['image'];
            $new_quiz_question_data['is_multiple'] = $quiz_question_val_array['is_multiple'];
            $new_quiz_question_data['choices'] = $translated_qus_choices;
            $new_quiz_question_data['correct_choice'] = $translated_qus_correct_choice;
            $new_quiz_question_data['added'] = $quiz_question_val_array['added'];
            $new_quiz_question_data['updated'] = $quiz_question_val_array['updated'];
            $new_quiz_question_data['deleted'] = $quiz_question_val_array['deleted'];
            $new_quiz_question_data['addon_type'] = $quiz_question_val_array['addon_type'];
            $new_quiz_question_data['addon_value'] = $quiz_question_val_array['addon_value'];
            $new_quiz_question_data['upload_type'] = $quiz_question_val_array['upload_type'];
            
            $new_quiz_question_data['queston_choies_type'] = $quiz_question_val_array['queston_choies_type'];
            $new_quiz_question_data['question_paragraph_text'] = $quiz_question_val_array['question_paragraph_text'];
            $new_quiz_question_data['question_paragraph_id'] = $quiz_question_val_array['question_paragraph_id'];
            $new_quiz_question_data['question_section_id'] = $quiz_question_val_array['question_section_id'];
            $new_quiz_question_data['question_section_order'] = $quiz_question_val_array['question_section_order'];
            $new_quiz_question_data['question_section_name'] = $question_section_name;
            $new_quiz_question_data['question_type_is_match'] = $question_type_is_match;
            $new_quiz_question_data['render_content'] = $quiz_question_val_array['render_content'];
            $new_quiz_question_data['question_helpsheet'] = $quiz_question_val_array['helpsheet'];
            
            $temp_quiz_question_data[] = $new_quiz_question_data;
        }

        $quiz_question_data = $temp_quiz_question_data;
        if($this->session->quiz_session)
        { 
            $session_quiz_id = $this->session->quiz_session['quiz_data']['id'];
            return redirect(base_url("test/$session_quiz_id/1"));
        }
        else
        {
            $start_time = date('Y-m-d H:i:s');
            $start_time_data = date('Y-m-d H:i:s');
            $end_time = $duration_min;
            
            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
            $participants_content = array(); 
            $participants_content['user_id'] = $user_id;
            $participants_content['guest_name'] = $this->session->leader_bord_user_name;
            $participants_content['quiz_id'] = $quiz_data['id'];
            $participants_content['questions'] = COUNT($quiz_question_data);
            $participants_content['correct'] = 0;
            $participants_content['reward_percentage'] = $quiz_data['reward_percentage'];
            $participants_content['negative_marking_percentage'] = $quiz_data['negative_marking_percentage'];
            $participants_content['marks_for_correct_answer'] = $quiz_data['marks_for_correct_answer'];
            $participants_content['started'] = $start_time;
            $participants_content['completed'] = NULL;
            $participants_content['test_language'] = $this->session->userdata('language');
            $participants_content['quiz_passing_marks'] = $quiz_data['passing'];
            $participants_content['quiz_grading_id'] = $quiz_data['quiz_grading_id'];
            $participants_content['quiz_levels_data'] = json_encode(array());
            
            if($quiz_data['quiz_grading_id'])
            {
                $quiz_levels_data = $this->db->where('id',$quiz_data['quiz_grading_id'])->get('quiz_grading')->row('data');
                if($quiz_levels_data)
                {
                    $participants_content['quiz_levels_data'] = $quiz_levels_data;
                }
            }

            $participants_content['earned_points'] = 0;
            $participant_id = $this->TestModel->insert_participant($participants_content); 
            $participants_content['end_time'] = $end_time;               
            $participants_content['participant_id'] = $participant_id;               

            if($quiz_question_data && $this->user['id'])
            {                  
                foreach ($quiz_question_data as $question_key => $quiz_question_array) 
                {

                    $answer = array();
                    if(isset($quiz_question_array['status']) && $quiz_question_array['status'] != "mark")
                    {
                        $answer = isset($quiz_question_array['answer']) ? $quiz_question_array['answer'] : array();
                    }
                    $question_id = isset($quiz_question_array['id']) ? $quiz_question_array['id'] : NULL;
                    $question_title = isset($quiz_question_array['title']) ? $quiz_question_array['title'] : NULL;
                    $question_img = isset($quiz_question_array['image']) ? $quiz_question_array['image'] : NULL;

                    $upload_type = isset($quiz_question_array['upload_type']) ? $quiz_question_array['upload_type'] : NULL;
                    
                    $addon_type = isset($quiz_question_array['addon_type']) ? $quiz_question_array['addon_type'] : NULL;
                    $addon_value = isset($quiz_question_array['addon_value']) ? $quiz_question_array['addon_value'] : NULL;


                    $queston_choies_type = isset($quiz_question_array['queston_choies_type']) ? $quiz_question_array['queston_choies_type'] : NULL;

                    $question_paragraph_text = isset($quiz_question_array['question_paragraph_text']) ? $quiz_question_array['question_paragraph_text'] : NULL;
                    $question_section_name = isset($quiz_question_array['question_section_name']) ? $quiz_question_array['question_section_name'] : "GENRAL QUESTIONS";
                    $question_type_is_match = isset($quiz_question_array['question_type_is_match']) ? $quiz_question_array['question_type_is_match'] : "NO";



                    $choices = isset($quiz_question_array['choices']) ? $quiz_question_array['choices'] : '[""]';
                    $correct_choice = isset($quiz_question_array['correct_choice']) ? $quiz_question_array['correct_choice'] : '[""]';
                    
                    $correct_choice = json_decode($correct_choice);
                    $is_correct = 0;
                    $correct_ans = 0;

                    $user_questions = array();
                    $user_questions['user_id'] = $user_id;
                    $user_questions['participant_id'] = $participant_id;
                    $user_questions['question_id'] = $question_id;
                    $user_questions['question'] = $question_title;
                    $user_questions['image'] = $question_img;
                    $user_questions['given_answer'] = json_encode($answer);
                    $user_questions['is_correct'] = $is_correct;
                    $user_questions['choices'] = $choices;
                    $user_questions['correct_choice'] = json_encode($correct_choice);

                    $user_questions['upload_type'] = $upload_type;
                    $user_questions['question_paragraph_text'] = $question_paragraph_text;
                    $user_questions['addon_type'] = $addon_type;
                    $user_questions['addon_value'] = $addon_value;
                    $user_questions['queston_choies_type'] = $queston_choies_type;
                    $user_questions['question_section_name'] = $question_section_name;
                    $user_questions['question_type_is_match'] = $question_type_is_match;
                    
                    $user_questions['timestamp'] = date('Y-m-d H:i:s');
                    $user_questions_array[] = $user_questions;
                    $user_question_id = $this->TestModel->insert_user_questions($user_questions);
                    $quiz_question_data[$question_key]['user_question_id'] = $user_question_id;
                }
            }

            $quiz_session = array(
                'quiz_data'=>$quiz_data,
                'quiz_question_data'=>$quiz_question_data,
                'participants_content'=>$participants_content,
                );
                
            $this->session->set_userdata('quiz_session', $quiz_session);

        }

        //save view detail 
        if($this->session->quiz_session['quiz_data'])
        {
            $savedata = array();
            $savedata['quiz_id'] = $this->session->quiz_session['quiz_data']['id'];
            $savedata['ip_address'] = $this->input->ip_address();
            $savedata['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $inserted_data = $this->TestModel->save_quiz_view_data($savedata);
        }

        return redirect(base_url("test/$quiz_id/1"));
    }

    function test($quiz_id = NULL, $question_id = NULL) 
    {
        $quiz_data = $this->TestModel->get_quiz_by_id($quiz_id);
        if(empty($quiz_data))
        {
           $this->session->set_flashdata('error', lang('invalid_id')); 
           redirect(base_url('Invalid Uri Arguments'));
        }
        
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : NULL;

        if($quiz_data->price > 0  && $login_user_id != $quiz_data->user_id)
        {
            $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status('quiz',$quiz_id);
            //$quiz_last_paypal_status = $this->Payment_model->get_quiz_last_paypal_status($quiz_id); 
            if(empty($quiz_last_paymetn_status))
            {
                $this->session->set_flashdata('error', lang('You Need To Buy This Quiz  Before Start Quiz ...')); 
                return redirect(base_url("quiz-detail/quiz/$quiz_id")); 
            }
        }


        $session_quiz_id = isset($this->session->quiz_session['quiz_data']['id']) ? $this->session->quiz_session['quiz_data']['id'] : NULL;
        if(empty($this->session->quiz_session) OR empty($session_quiz_id))
        {
            $this->session->set_flashdata('error', lang('schedule_quiz_first'));
            return redirect(base_url("instruction/quiz/$quiz_id"));
        }

            
        if($session_quiz_id != $quiz_id)
        {
            return redirect(base_url("test/$session_quiz_id/$question_id"));
        }
        $total_question = count($this->session->quiz_session['quiz_question_data']);

        if(empty($question_id) OR $total_question < 1)
        {
            redirect(base_url('Invalid Uri Arguments'));
            return redirect(base_url());
        }
        

        if($total_question < $question_id)
        {
            redirect(base_url('Invalid Quiz Data'));
            return redirect(base_url());
        }

        $tes_over = $this->session->quiz_session['participants_content']['end_time'];
        if($tes_over < date('Y-m-d H:i:s'))
        {
            return redirect(base_url('test_result/'.$session_quiz_id));
        } 

        $added_time = $this->session->quiz_session['participants_content']['started']; 

        $dt = new DateTime($added_time);
        $minutes_to_add = $this->session->quiz_session['quiz_data']['duration_min'];
        $time = new DateTime($added_time);
        $time->add(new DateInterval('PT' . $minutes_to_add . 'M'));
        $expire_time = $this->session->quiz_session['participants_content']['end_time'];

        $expire_time = strtotime($expire_time);
        $current_time = strtotime(date('Y-m-d H:i:s'));
        $left_time = $expire_time - $current_time;
        $answer = $this->input->POST('answer') ? $this->input->POST('answer') : array();
        $answer = (isset($_POST['answer']) && is_array($_POST['answer'])) ? $_POST['answer'] : array();
        
        $q_number = $question_id-1;
        $question_url_id = $question_id > 1 ? $question_id-1 : $question_id;
        
        $next_question = $question_id < $total_question ? $question_id+1 : $question_id;
        $last_question = $total_question == $question_id ? 'YES' : "NO";
        $is_first_question = $question_id == 1 ? 'YES' : "NO";
        if($this->input->POST('mark_or_next_quiz'))
        {
            $this->set_question_status($q_number,'mark_or_next_quiz',$answer,$next_question);
            return redirect(base_url("test/$quiz_id/$next_question"));
        }
        if($this->input->POST('mark_for_answer_and_next'))
        {
            $this->set_question_status($q_number,'mark_for_answer_and_next',$answer,$next_question);
            return redirect(base_url("test/$quiz_id/$next_question"));
        }
        if($this->input->POST('save_or_next_quiz'))
        {
            $this->set_question_status($q_number,'save_or_next_quiz',$answer,$next_question);
            return redirect(base_url("test/$quiz_id/$next_question"));
        }
        
        if($this->input->POST('submit_test'))
        {
             return redirect(base_url("result/$quiz_id"));
        }
        elseif ($this->input->POST('preview_quiz')) 
        {
            $this->set_question_status($q_number,'preview_quiz','',$question_url_id);
            return redirect(base_url("test/$quiz_id/$question_url_id"));

        }
        elseif ($this->input->POST('next_quiz')) 
        {
            $this->set_question_status($q_number,'next_quiz','',$next_question);
            return redirect(base_url("test/$quiz_id/$next_question"));

        }
        elseif ($this->input->POST('submit_quiz')) 
        {
             return redirect(base_url("result/$quiz_id"));
        }
        else
        {  
            $this->set_question_status($q_number,'visited','',$next_question);
        }

        $running_quiz_question_data = $this->session->quiz_session['quiz_question_data'];
        $runn_total_question = count($this->session->quiz_session['quiz_question_data']);
        $runn_attemp = 0;
        $runn_mark = 0;
        $runn_mark_answer = 0;
        $runn_answered = 0;
        $runn_visited = 0;
        
        foreach ($running_quiz_question_data as $runn_quiz_question_array) 
        {
            if(isset($runn_quiz_question_array['answer']))
            {   
                $runn_attemp++;
            }

            if(isset($runn_quiz_question_array['status']) && $runn_quiz_question_array['status'] == 'answer')
            {   
                $runn_answered++;
            }

            if(isset($runn_quiz_question_array['status']) && $runn_quiz_question_array['status'] == 'mark')
            {   
                $runn_mark++;
            }

            if(isset($runn_quiz_question_array['status']) && $runn_quiz_question_array['status'] == 'mark-answer')
            {   
                $runn_mark_answer++;
            }

            if(isset($runn_quiz_question_array['status']) && $runn_quiz_question_array['status'] == 'visited')
            {   
                $runn_visited++;
            }
        }
        $get_quiz_session = $this->session->quiz_session;

        $quiz_data = $get_quiz_session['quiz_data']; 
        $quiz_question_data = $get_quiz_session['quiz_question_data'];
        $last_question_nbr = count($quiz_question_data);
        $question_data = $get_quiz_session['quiz_question_data'][$q_number];
        $runn_not_visited = $runn_total_question - $runn_attemp - $runn_visited; 
        
        $is_evaluation_test = $get_quiz_session['quiz_data']['evaluation_test'];
        $current_question_json = $get_quiz_session['quiz_question_data'][$question_id - 1]['correct_choice'];

        $current_question_answers_arr = json_decode($current_question_json);
        
        $current_question_answers_arr = json_decode(json_encode($current_question_answers_arr), true);
        $is_last_question_answerd = (isset($question_data['status']) && $question_data['status'] == 'answer') ? 'YES' : 'NO';
       
        $current_question_answers_string = "";
        $current_question_answers_string_keys = "";
        
        if(isset($question_data['question_type_is_match']))
        {
            if($question_data['question_type_is_match'] == "YES")
            {      

                $current_question_answers_arr_is_correct = isset($current_question_answers_arr['arr_is_correct']) ? $current_question_answers_arr['arr_is_correct'] : array();
                
                $current_question_display_order_array = isset($current_question_answers_arr['display_order_array']) ? $current_question_answers_arr['display_order_array'] : array();
                  
                $current_question_answers_string = implode(',', $current_question_answers_arr_is_correct);
                
                $current_question_answers_string_keys = implode(',',$current_question_display_order_array);
                
            }
            else
            {
                if(is_array($current_question_answers_arr))
                {
                    $current_question_answers_string = implode(',', $current_question_answers_arr);
                }
                else
                {
                    $current_question_answers_string = $current_question_answers_arr;
                }
            }
        }

        $quiz_last_attempted_question_url = $this->get_quiz_last_attempted_question_url();
        $this->set_title("Test", $this->settings->site_name);


        $content_data = array('Page_message' => $quiz_data['title'], 'page_title' => $quiz_data['title'], 'quiz_data' => $quiz_data, 'quiz_question_data' => $quiz_question_data, 'question_data' => $question_data, 'left_time' => $left_time, 'question_url_id' => $question_url_id, 'question_id' => $question_id,'runn_attemp'=>$runn_attemp, 'runn_mark' => $runn_mark,'runn_mark_answer'=>$runn_mark_answer, 'runn_answered' => $runn_answered, 'runn_visited' => $runn_visited, 'runn_not_visited' => $runn_not_visited,'last_question'=>$last_question,'is_last_question_answerd'=>$is_last_question_answerd,'is_first_question'=>$is_first_question,'is_evaluation_test' => $is_evaluation_test,'current_question_json'=> $current_question_json,'quiz_id' => $quiz_id,'current_question_answers_string_keys'=> $current_question_answers_string_keys, 'current_question_answers_string' => $current_question_answers_string,'quiz_last_attempted_question_url'=>$quiz_last_attempted_question_url,'last_question_nbr' => $last_question_nbr);

        $data = $this->includes;
        $data['content'] = $this->load->view('test', $content_data, TRUE);

        $this->load->view($this->template, $data);
    }

    public function get_quiz_last_attempted_question_url()
    {
        $get_quiz_session = $this->session->quiz_session;

        $quiz_data = $get_quiz_session['quiz_data'];

        $session_quiz_id = $quiz_data['id'];        
        $quiz_question_data = $get_quiz_session['quiz_question_data'];
        $last_question = count($quiz_question_data) > 0 ? count($quiz_question_data) : 1;
        $url =  base_url("test/$session_quiz_id/$last_question");
        $i = 0; 
        foreach ($quiz_question_data as $key => $quiz_question_array) 
        {
            $i++;
            if(isset($quiz_question_array['status']) && $quiz_question_array['status'])
            {
                $url =  base_url("test/".$session_quiz_id."/".$i);
            }
        }
        return $url;
    }

    public function set_question_status($q_number = NULL, $action = NULL, $answer = NULL, $go_to_question_number = 0)
    {
        $go_to_question_number = $go_to_question_number == 0 ? $q_number + 1 : $go_to_question_number;
        $get_quiz_session = $this->session->quiz_session;
        $question_session_array = $get_quiz_session['quiz_question_data'][$q_number];
        $quiz_id = $question_session_array['quiz_id'];
        $status_of_ques = isset($get_quiz_session['quiz_question_data'][$q_number]['status']) ? $get_quiz_session['quiz_question_data'][$q_number]['status'] : "";

        if($this->input->post() && $status_of_ques != "visited" && $get_quiz_session['quiz_data']['is_previous_disable'] == 1)
        {
            $url = $this->get_quiz_last_attempted_question_url();
            $this->session->set_flashdata('error', lang('answer_already_submited_or_visited'));
            return redirect($url);
        }


        if(empty($status_of_ques) && $action == 'visited')
        {
            $get_quiz_session['quiz_question_data'][$q_number]['status'] = 'visited';
            $quiz_session = $get_quiz_session;
            $this->session->set_userdata('quiz_session', $quiz_session);
        }

        if(empty($status_of_ques) && $action == 'preview_quiz')
        {
            $get_quiz_session['quiz_question_data'][$q_number]['status'] = 'preview_quiz';
            $quiz_session = $get_quiz_session;
            $this->session->set_userdata('quiz_session', $quiz_session);
        }

        if(empty($status_of_ques) && $action == 'next_quiz')
        {
            $get_quiz_session['quiz_question_data'][$q_number]['status'] = 'visited';
            $quiz_session = $get_quiz_session;
            $this->session->set_userdata('quiz_session', $quiz_session);
        }

        if($action == 'mark_or_next_quiz')
        {
            $get_quiz_session['quiz_question_data'][$q_number]['status'] = 'mark';
            $get_quiz_session['quiz_question_data'][$q_number]['mark'] = 1;
            $get_quiz_session['quiz_question_data'][$q_number]['answer'] = $answer;
            $quiz_session = $get_quiz_session;
            $this->session->set_userdata('quiz_session', $quiz_session);
        }

        if($action == 'mark_for_answer_and_next')
        {
            $get_quiz_session['quiz_question_data'][$q_number]['status'] = 'mark-answer';
            $get_quiz_session['quiz_question_data'][$q_number]['mark'] = 1;
            $get_quiz_session['quiz_question_data'][$q_number]['answer'] = $answer;
            $quiz_session = $get_quiz_session;
            $this->session->set_userdata('quiz_session', $quiz_session);
        }

        if($action == 'save_or_next_quiz')
        {
            $get_quiz_session['quiz_question_data'][$q_number]['status'] = 'answer';
            $get_quiz_session['quiz_question_data'][$q_number]['mark'] = 0;
            $get_quiz_session['quiz_question_data'][$q_number]['answer'] = $answer;
            $quiz_session = $get_quiz_session;
            $this->session->set_userdata('quiz_session', $quiz_session);
        }
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        if($user_id)
        {
            $this->temp_test_result($quiz_id);
        }
        return TRUE;
    }


    private function temp_test_result($quiz_id) 
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $right_answer = 0;
        $total_question =0;
        $correct = 0;
        $correct_ans = 0;
        $total_attemp = 0;

        if($this->session->quiz_session)
        {  
            $quiz_question_data = $this->session->quiz_session['quiz_question_data'];
            $total_question = count($this->session->quiz_session['quiz_question_data']);
            $participant_id = $this->session->quiz_session['participants_content']['participant_id'];

            foreach ($quiz_question_data as $quiz_question_array) 
            {

                $answer = array();
                if(isset($quiz_question_array['status']) && $quiz_question_array['status'] != "mark")
                {
                    $answer = isset($quiz_question_array['answer']) ? $quiz_question_array['answer'] : array();
                }
                $question_id = isset($quiz_question_array['id']) ? $quiz_question_array['id'] : NULL;
                $question_title = isset($quiz_question_array['title']) ? $quiz_question_array['title'] : NULL;
                $question_img = isset($quiz_question_array['image']) ? $quiz_question_array['image'] : NULL;

                $upload_type = isset($quiz_question_array['upload_type']) ? $quiz_question_array['upload_type'] : NULL;
                
                $addon_type = isset($quiz_question_array['addon_type']) ? $quiz_question_array['addon_type'] : NULL;
                $addon_value = isset($quiz_question_array['addon_value']) ? $quiz_question_array['addon_value'] : NULL;

 
                $queston_choies_type = isset($quiz_question_array['queston_choies_type']) ? $quiz_question_array['queston_choies_type'] : NULL;

                $question_paragraph_text = isset($quiz_question_array['question_paragraph_text']) ? $quiz_question_array['question_paragraph_text'] : NULL;
                $question_section_name = isset($quiz_question_array['question_section_name']) ? $quiz_question_array['question_section_name'] : "GENRAL QUESTIONS";
                $question_type_is_match = isset($quiz_question_array['question_type_is_match']) ? $quiz_question_array['question_type_is_match'] : "NO";

                $user_question_id = isset($quiz_question_array['user_question_id']) ? $quiz_question_array['user_question_id'] : 0;

                $choices = isset($quiz_question_array['choices']) ? $quiz_question_array['choices'] : '[""]';
                $correct_choice = isset($quiz_question_array['correct_choice']) ? $quiz_question_array['correct_choice'] : '[""]';
                
                $correct_choice = json_decode($correct_choice);
                $is_correct = 0;
                $correct_ans = 0; 

                if($question_type_is_match == "YES")
                {
                    $choices_array =  json_decode($choices);
                    $actual_correct_choices = json_decode(json_encode($correct_choice), true);

                    $display_order_array = isset($correct_choice->display_order_array) ? $correct_choice->display_order_array : array();
                    $araay_to_display_match = isset($correct_choice->araay_to_display_match) ? $correct_choice->araay_to_display_match : array();
                    $arr_is_correct = isset($correct_choice->arr_is_correct) ? $correct_choice->arr_is_correct : array();
                    
                    $arr_is_correct = json_decode(json_encode($arr_is_correct), true);
                    $araay_to_display_match = json_decode(json_encode($araay_to_display_match), true);
                    $display_order_array = json_decode(json_encode($display_order_array), true);
                    $choices_array = json_decode(json_encode($choices_array), true);


                    $new_arr_display_match_index = array();
                    $index = 0;
                    foreach ($araay_to_display_match as $key => $value) 
                    {
                        $index++; 
                        $new_arr_display_match_index[$index] = $araay_to_display_match[$key];
                    }

                    $submit_answer_array = array();

                    foreach ($answer as $key => $value) 
                    {
                        $submit_answer_array[$key] = isset($new_arr_display_match_index[$value]) ? $new_arr_display_match_index[$value] : "";
                    }

                    $actual_correct_choices['submit_answer_array'] = $submit_answer_array;
                    $is_correct_match =  json_encode($submit_answer_array) == json_encode($arr_is_correct) ? TRUE : FALSE;
                    $correct_ans = $is_correct_match == TRUE ? 1 : 0;
                    $correct_choice = $actual_correct_choices;
                }
                else
                {

                    if($quiz_question_array['queston_choies_type'] != 'text')
                    {
                        foreach ($answer as $key => $answer_value) 
                        {

                            foreach ($correct_choice as  $db_correct_value) 
                            {
                                if($correct_ans != 1)
                                {
                                   
                                   $correct_ans = (trim(strtolower($answer_value)) == trim(strtolower($db_correct_value))) ? 1 : 0;
                                   
                                }
                            }
                        }
                    }
                    else
                    {
                        foreach ($answer as $key => $answer_value) 
                        {
                            
                            foreach ($correct_choice as  $db_correct_value) 
                            {
                                
                                if($correct_ans != 1)
                                {
                                    
                                    $db_correct_value = trim(strip_tags($db_correct_value));
                                    
                                    $correct_ans = (trim(strtolower($answer_value)) == trim(strtolower($db_correct_value))) ? 1 : 0;
                                }
                            }
                        }
                    }

                }

                if ($correct_ans == 1)  
                {
                    $is_correct = 1;
                    $right_answer++;
                }
                if($answer)
                {
                    $total_attemp++;
                }

                $user_questions = array();
                $user_questions['user_id'] = $user_id;
                $user_questions['participant_id'] = $participant_id;
                $user_questions['question_id'] = $question_id;
                $user_questions['question'] = $question_title;
                $user_questions['image'] = $question_img;
                $user_questions['given_answer'] = json_encode($answer);
                $user_questions['is_correct'] = $is_correct;
                $user_questions['choices'] = $choices;
                $user_questions['correct_choice'] = json_encode($correct_choice);

                $user_questions['upload_type'] = $upload_type;
                $user_questions['question_paragraph_text'] = $question_paragraph_text;
                $user_questions['addon_type'] = $addon_type;
                $user_questions['addon_value'] = $addon_value;
                $user_questions['queston_choies_type'] = $queston_choies_type;
                $user_questions['question_section_name'] = $question_section_name;
                $user_questions['question_type_is_match'] = $question_type_is_match;
                
                $user_questions['timestamp'] = date('Y-m-d H:i:s');
                $user_questions_array[] = $user_questions;
                
                $this->TestModel->update_user_question_by_id($user_questions,$user_question_id); 
            }

            $participants_content = array();
            $participants_content['correct'] = $right_answer;
            $participants_content['total_attemp'] = $total_attemp;
            $participants_content['completed'] = date('Y-m-d H:i:s');

            $bonus_points = $this->session->quiz_session['quiz_data']['bonus_points'];
            $points_on_correct = $this->session->quiz_session['quiz_data']['points_on_correct'];
            if($participants_content['correct'] == $total_question)
            {
                $correct_answer_point = $participants_content['correct'] * $points_on_correct;
                $all_enrned_point = $correct_answer_point + $bonus_points;
                $participants_content['earned_points'] = $all_enrned_point;
            }
            else
            {
                $correct_answer_point = $participants_content['correct'] * $points_on_correct;
                $participants_content['earned_points'] = $correct_answer_point;
            }
            
            $update_participant = $this->TestModel->update_participant($quiz_id, $participants_content,$participant_id);
            $correct = $right_answer;
            return true;
        }
       return false;
    }

    public function test_result($quiz_id = NULL)
    {

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $right_answer = 0;
        $total_question =0;
        $correct = 0;
        $correct_ans = 0;
        $total_attemp = 0;

        if($this->session->quiz_session)
        { 
            $quiz_question_data = $this->session->quiz_session['quiz_question_data'];
            $total_question = count($this->session->quiz_session['quiz_question_data']);
            $participant_id = $this->session->quiz_session['participants_content']['participant_id'];
            $this->db->where('participant_id',$participant_id)->delete('user_questions');

            foreach ($quiz_question_data as $quiz_question_array) 
            {
                $answer = array();
                if(isset($quiz_question_array['status']) && $quiz_question_array['status'] != "mark")
                {
                    $answer = isset($quiz_question_array['answer']) ? $quiz_question_array['answer'] : array();
                }
                $question_id = isset($quiz_question_array['id']) ? $quiz_question_array['id'] : NULL;
                $question_title = isset($quiz_question_array['title']) ? $quiz_question_array['title'] : NULL;
                $question_img = isset($quiz_question_array['image']) ? $quiz_question_array['image'] : NULL;

                $upload_type = isset($quiz_question_array['upload_type']) ? $quiz_question_array['upload_type'] : NULL;
                
                $addon_type = isset($quiz_question_array['addon_type']) ? $quiz_question_array['addon_type'] : NULL;
                $addon_value = isset($quiz_question_array['addon_value']) ? $quiz_question_array['addon_value'] : NULL;


                $queston_choies_type = isset($quiz_question_array['queston_choies_type']) ? $quiz_question_array['queston_choies_type'] : NULL;

                $question_paragraph_text = isset($quiz_question_array['question_paragraph_text']) ? $quiz_question_array['question_paragraph_text'] : NULL;
                $question_section_name = isset($quiz_question_array['question_section_name']) ? $quiz_question_array['question_section_name'] : "GENRAL QUESTIONS";
                $question_type_is_match = isset($quiz_question_array['question_type_is_match']) ? $quiz_question_array['question_type_is_match'] : "NO";



                $choices = isset($quiz_question_array['choices']) ? $quiz_question_array['choices'] : '[""]';
                $correct_choice = isset($quiz_question_array['correct_choice']) ? $quiz_question_array['correct_choice'] : '[""]';
                
                $correct_choice = json_decode($correct_choice);
                $is_correct = 0;
                $correct_ans = 0; 

                if($question_type_is_match == "YES")
                {
                    $choices_array =  json_decode($choices);
                    $actual_correct_choices = json_decode(json_encode($correct_choice), true);

                    $display_order_array = isset($correct_choice->display_order_array) ? $correct_choice->display_order_array : array();
                    $araay_to_display_match = isset($correct_choice->araay_to_display_match) ? $correct_choice->araay_to_display_match : array();
                    $arr_is_correct = isset($correct_choice->arr_is_correct) ? $correct_choice->arr_is_correct : array();
                    
                    $arr_is_correct = json_decode(json_encode($arr_is_correct), true);
                    $araay_to_display_match = json_decode(json_encode($araay_to_display_match), true);
                    $display_order_array = json_decode(json_encode($display_order_array), true);
                    $choices_array = json_decode(json_encode($choices_array), true);


                    $new_arr_display_match_index = array();
                    $index = 0;
                    foreach ($araay_to_display_match as $key => $value) 
                    {
                        $index++; 
                        $new_arr_display_match_index[$index] = $araay_to_display_match[$key];
                    }

                    $submit_answer_array = array();

                    foreach ($answer as $key => $value) 
                    {
                        $submit_answer_array[$key] = isset($new_arr_display_match_index[$value]) ? $new_arr_display_match_index[$value] : "";
                    }

                    $actual_correct_choices['submit_answer_array'] = $submit_answer_array;
                    $is_correct_match =  json_encode($submit_answer_array) == json_encode($arr_is_correct) ? TRUE : FALSE;
                    $correct_ans = $is_correct_match == TRUE ? 1 : 0;
                    $correct_choice = $actual_correct_choices;
                }
                else
                {
                    if($quiz_question_array['queston_choies_type'] != 'text')
                    {
                        foreach ($answer as $key => $answer_value) 
                        {
                            foreach ($correct_choice as  $db_correct_value) 
                            {
                                if($correct_ans != 1)
                                {
                                   $correct_ans = trim(strtolower($answer_value)) == trim(strtolower($db_correct_value)) ? 1 : 0;
                                }
                            }
                        }
                    }
                    else
                    {
                        foreach ($answer as $key => $answer_value) 
                        {
                            
                            foreach ($correct_choice as  $db_correct_value) 
                            {
                                
                                if($correct_ans != 1)
                                {
                                    
                                    $db_correct_value = trim(strip_tags($db_correct_value));
                                
                                    $correct_ans = (trim(strtolower($answer_value)) == trim(strtolower($db_correct_value))) ? 1 : 0;
                                }
                            }
                        }
                    }

                }

                if ($correct_ans == 1)  
                {
                    $is_correct = 1;
                    $right_answer++;
                }
                if($answer)
                {
                    $total_attemp++;
                }

            
                $user_questions = array();
                $user_questions['user_id'] = $user_id;
                $user_questions['participant_id'] = $participant_id;
                $user_questions['question_id'] = $question_id;
                $user_questions['question'] = $question_title;
                $user_questions['image'] = $question_img;
                $user_questions['given_answer'] = json_encode($answer);
                $user_questions['is_correct'] = $is_correct;
                $user_questions['choices'] = $choices;
                $user_questions['correct_choice'] = json_encode($correct_choice);

                $user_questions['upload_type'] = $upload_type;
                $user_questions['question_paragraph_text'] = $question_paragraph_text;
                $user_questions['addon_type'] = $addon_type;
                $user_questions['addon_value'] = $addon_value;
                $user_questions['queston_choies_type'] = $queston_choies_type;
                $user_questions['question_section_name'] = $question_section_name;
                $user_questions['question_type_is_match'] = $question_type_is_match;
                
                $user_questions['timestamp'] = date('Y-m-d H:i:s');
                $user_questions_array[] = $user_questions;
                $user_question_id = $this->TestModel->insert_user_questions($user_questions); 
            }

            $participants_content = array();
            $participants_content['correct'] = $right_answer;
            $participants_content['total_attemp'] = $total_attemp;
            $participants_content['completed'] = date('Y-m-d H:i:s');

            $bonus_points = $this->session->quiz_session['quiz_data']['bonus_points'];
            $points_on_correct = $this->session->quiz_session['quiz_data']['points_on_correct'];
            if($participants_content['correct'] == $total_question)
            {
                $correct_answer_point = $participants_content['correct'] * $points_on_correct;
                $all_enrned_point = $correct_answer_point + $bonus_points;
                $participants_content['earned_points'] = $all_enrned_point;
            }
            else
            {
                $correct_answer_point = $participants_content['correct'] * $points_on_correct;
                $participants_content['earned_points'] = $correct_answer_point;
            }

            $this->session->unset_userdata('quiz_session');
            $this->session->unset_userdata('leader_bord_user_name');
            
            $update_participant = $this->TestModel->update_participant($quiz_id, $participants_content,$participant_id);
            $correct = $right_answer;

            $encrypted_participant_id = encrypt_decrypt('encrypt',$participant_id);

            return redirect(base_url('my/test/summary/'.$encrypted_participant_id));
        }
        else
        {
            return redirect(base_url('my/history'));
        }

        $this->set_title(lang('test_result'), $this->settings->site_name);

        $content_data = array('Page_message' => lang('Ttest_result'), 'page_title' => lang('test_result'),'correct' => $correct,'total_question' => $total_question, 'total_attemp' => $total_attemp);

        $data = $this->includes;
        $data['content'] = $this->load->view('result', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function test_submit_request()
    {
        if($this->session->quiz_session)
        {
            $quiz_question_data = $this->session->quiz_session['quiz_question_data'];
            $total_question = count($this->session->quiz_session['quiz_question_data']);
            $attemp = 0;

            foreach ($quiz_question_data as $quiz_question_array) 
            {
                if(isset($quiz_question_array['answer']))
                {
                    $attemp++;
                }
            }

            $respons['attemp'] = $attemp;
            $respons['status'] = 'success';
            $respons['msg'] = 'Test Submit Request !';
        }
        else
        {
            $respons['status'] = 'error';
            $respons['msg'] = 'Invalid Test Submit Request !';
        } 

        echo json_encode($respons);
        return json_encode($respons);
    }

    function test_summary($participant_id)
    { 
        $participant_id = encrypt_decrypt('decrypt',$participant_id);
        $participant_data = $this->TestModel->get_participant_by_id($participant_id);
        
        if(empty($participant_data))
        {
            redirect(base_url('Invalid Quiz Data'));
            return redirect(base_url()); 
        }

        $quiz_id = $participant_data['quiz_id'];
        if(isset($this->session->quiz_session) && $this->session->quiz_session)
        {
            return redirect(base_url("result/$quiz_id"));
        }
        
        $user_question_data = $this->TestModel->get_user_question_by_participant_id($participant_id);
        
        if(empty($user_question_data))
        {
            $this->session->set_flashdata('error', 'Test Not complet ');
            return redirect(base_url());
        }
        
        $quiz_data = $this->TestModel->get_quiz_by_id($quiz_id);
        $category_data = $this->TestModel->get_category_by_id($quiz_data->category_id);
        $parent_category_data = $this->TestModel->get_category_by_id($category_data->parent_category);

        $correct = $participant_data['correct'] ? $participant_data['correct'] : 0;
        $total_question = $participant_data['questions'] ? $participant_data['questions'] : 0;
        $total_attemp = $participant_data['total_attemp'] ? $participant_data['total_attemp'] : 0;

        $this->set_title(lang('test_result'), $this->settings->site_name);

        $content_data = array('Page_message' => lang('test_result'), 'page_title' => lang('test_result'),'correct' => $correct,'total_question' => $total_question, 'total_attemp' => $total_attemp, 'quiz_data' => $quiz_data, 'participant_data' => $participant_data, 'user_question_data' => $user_question_data ,'category_data'=>$category_data,'parent_category_data'=>$parent_category_data,'participant_id'=>$participant_id,);
        $data = $this->includes;
        $data['content'] = $this->load->view('result', $content_data, TRUE);

        $this->load->view($this->template, $data);
    }

    function send_report()
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $this->form_validation->set_rules('report_option', 'Choose any report option', 'required');
        $this->form_validation->set_rules('any_info', 'Any more information', 'required');
        if ($this->form_validation->run() == false)  
        {
            $validation_error = $this->form_validation->error_array();
            $this->session->set_flashdata('error', $validation_error['report_option']); 
            return redirect($this->input->post('page_url'));
        } 
        else 
        {
            $report_content = array();
            $report_content['user_id'] = $user_id;
            $report_content['question_id'] = $this->input->post('question_id');
            $report_content['report_option'] = $this->input->post('report_option');
            $report_content['other_info'] = $this->input->post('any_info');
            $report_content['added'] = date('Y-m-d H:i:s');

            $report_id = $this->db->insert('question_report_problem',$report_content);

            if($report_id)
            {
                $this->session->set_flashdata('message', lang('your_reort_problem_send_to_administration')); 
            }
            else
            {
                $this->session->set_flashdata('error', lang('error_sending_report_problem'));
            }

            return redirect($this->input->post('page_url'));
        }
    }

}
