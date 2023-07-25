<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Study_Controller extends Public_Controller {

    /**
     * Constructor  
     */
    function __construct() 
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('HomeModel');
        $this->load->model('Payment_model');
        $this->load->model('MembershipModel');
        $this->load->model('TestModel');
        $this->load->model('StudyModel');
        $this->add_css_theme('quiz_box.css');
        $this->add_css_theme('set2.css');
        $this->add_css_theme('table-main.css');
        $this->load->library('Ratting');
        $this->add_js_theme('study.js');
    }

    function index($purchases_type,$study_material_id = NULL)
    {

        $study_data = $this->StudyModel->get_study_material_and_file_by_id($study_material_id);
        
        if(empty($study_data))
        {
           $this->session->set_flashdata('error', lang('invalid_id')); 
           redirect(base_url());
        }
        
        $study_m_title = $study_data->title;
        $real_url = base_url('study-material/').slugify_string($study_m_title)."-$study_material_id";
        return redirect($real_url);
    }




    function study_details_show($study_material_slug)
    {
        $purchases_type = "material";
        if(empty($study_material_slug))
        {
            $this->session->set_flashdata('error', lang('invalid_uri_arguments!'));
            return redirect(base_url()); 
        }
        if(is_numeric($study_material_slug))
        {
            $study_material_id = $study_material_slug;
        }
        else
        {
            $slug_parts = explode("-", $study_material_slug);
            $study_material_id = end($slug_parts);
        }

        $user_id = (isset($this->user['id']) && $this->user['id']) ? $this->user['id'] : 0;

        $paid_s_m_array = $this->Payment_model->get_user_paid_study_matiral_obj($user_id);

        $study_data = $this->StudyModel->get_study_material_and_file_by_id($study_material_id);

        
        if(empty($study_data))
        {
           $this->session->set_flashdata('error', lang('invalid_uri_arguments')); 
           return redirect(base_url());
        }

        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }        

        if($is_premium_member == FALSE)
        {
            $user_category_membership = get_user_category_membership($user_id,$study_data->category_id);
            if($user_category_membership) 
            {
                $is_premium_member = ($user_category_membership->validity && $user_category_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
            }
        }


        $is_user_enrolled = FALSE;

        if($user_id)
        {
           $is_enrolled =  $this->db->where('user_id',$user_id)->where('study_material_id',$study_material_id)->get('study_material_user_histry')->row();
           if($is_enrolled)
           {
                $is_user_enrolled = TRUE;
           }
           else
           {
            if($this->input->get('enroll'))
            {
                $enroll_data['user_id'] = $user_id;
                $enroll_data['study_material_id'] = $study_material_id;
                $enroll_data['enroll_date'] = date("Y-m-d H:i:s");
                $this->db->insert('study_material_user_histry',$enroll_data);
                $insert_id = $this->db->insert_id();
                if($insert_id)
                {
                    $this->session->set_flashdata('message', lang('enrolled_success')); 
                }
                else
                {
                    $this->session->set_flashdata('error', lang('error_durning_enroll')); 
                }
                return redirect(base_url("study-content/$study_material_slug"));
            }
           }
        }
        else
        {
            $is_user_enrolled = TRUE;
        }


        $study_material_section_data = $this->StudyModel->get_study_material_section_by_study_material_id($study_material_id);
        
        $current_date = date('Y-m-d');
        $study_view_data = $this->StudyModel->get_study_view($study_data->id,$this->input->ip_address(),$current_date);
       
        $comments_exist_or_not = $this->HomeModel->get_comment_through_quizid_userid_reltype($purchases_type,$study_material_id,$user_id);
        $material_comments = $this->HomeModel->get_quiz_comment($study_material_id,$purchases_type);
         
        $total_comments = count($material_comments);


        $average = 0;
        if($study_data->total_rating > 0 && $study_data->rating > 0)
        {
            $average = $study_data->total_rating / $study_data->rating;
        } 

        $average_rating = ($average > 0) ? $average : 0;
        $average_rating =  number_format((float)$average_rating, 1, '.', '');

        $site_phone_number = $this->settings->site_phone_number;

        $rating_star = [0, 0, 0, 0, 0];
        $study_m_user_data = $this->db->where('id',$study_data->user_id)->get('users')->row();
        $savedata = array();
        $savedata['study_material_id'] = $study_data->id;
        $savedata['ip_address'] = $this->input->ip_address();
        $savedata['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
        
        $quiz_data = $this->StudyModel->get_associate_quiz_by_material_id($study_material_id);
        $session_quiz_data = array();
        $session_quiz_question_data = array();
        if($this->session->quiz_session)
        {
            $get_quiz_session = $this->session->quiz_session;
            $session_quiz_data = $get_quiz_session['quiz_data'];
            $session_quiz_question_data = $get_quiz_session['quiz_question_data'];
        }
       

        if(empty($study_view_data))
        {
           $inserted_data = $this->StudyModel->save_study_view_data($savedata);
        }
        $meta_data = array('meta_title' => $study_data->meta_title, 'meta_keyword' => $study_data->meta_keywords, 'meta_description' =>  $study_data->meta_description, 'title' => $study_data->title,'description' => $study_data->description,'image' => "",);

        $this->set_title(lang('front_study_material_detail'));        
        
        $content_data = array('page_title' => lang('front_study_material_detail'),'study_material_id' => $study_material_id,'study_data'=>$study_data,'purchases_type'=>$purchases_type,'comments_exist_or_not'=>$comments_exist_or_not,'material_comments'=>$material_comments,'average'=>$average,'study_m_user_data' => $study_m_user_data,'average_rating' => $average_rating,'site_phone_number' => $site_phone_number,'study_material_section_data' => $study_material_section_data,'paid_s_m_array' => $paid_s_m_array, 'is_premium_member' => $is_premium_member,'is_user_enrolled' => $is_user_enrolled,'quiz_data' => $quiz_data,'session_quiz_data' => $session_quiz_data,'session_quiz_question_data' => $session_quiz_question_data,);

        $data = $this->includes;

        $data['content'] = $this->load->view('study_material_detail_show', $content_data, TRUE);
        $data['meta_data'] = $meta_data;
        $this->load->view($this->template, $data);
    }




    function study_details($study_material_slug, $study_material_content_id = NULL)
    {
        $purchases_type = "material";
        if(empty($study_material_slug))
        {
            $this->session->set_flashdata('error', lang('invalid_uri_arguments!'));
            return redirect(base_url()); 
        }
        if(is_numeric($study_material_slug))
        {
            $study_material_id = $study_material_slug;
        }
        else
        {
            $slug_parts = explode("-", $study_material_slug);
            $study_material_id = end($slug_parts);
        }
        $user_id = (isset($this->user['id']) && $this->user['id']) ? $this->user['id'] : NULL;
        
        if(empty($user_id))
        {
            // $this->session->set_flashdata('error', lang('please_login_first!'));
            // return redirect(base_url("study-content/$study_material_slug")); 
        }


        $study_data = $this->StudyModel->get_study_material_and_file_by_id($study_material_id);
        
        if(empty($study_data))
        {
           $this->session->set_flashdata('error', lang('invalid_uri_arguments')); 
           return redirect(base_url());
        }

        $study_material_section_data = $this->StudyModel->get_study_material_section_by_study_material_id($study_material_id);
        $study_section_ids = array();
        if($study_material_section_data)
        {   
            $study_material_section_data_array = json_decode(json_encode($study_material_section_data), true);
            $study_section_ids = array_column($study_material_section_data_array,"id");
        }

        $s_m_completed_data = $this->StudyModel->get_user_completed_s_m_contant($study_material_id,$user_id);
        $s_m_completed_content_ids = array();
        if($s_m_completed_data && count($s_m_completed_data))
        {
            $s_m_completed_content_ids = array_column($s_m_completed_data,"s_m_contant_id");
        }

        $study_material_content_data = $this->StudyModel->get_study_material_content_by_ids($study_material_id,$study_material_content_id);
        if(empty($study_material_content_data))
        {
            $this->session->set_flashdata('error', lang('invalid_uri_arguments_or_study_material_has_no_contant')); 
            return redirect(base_url("study-content/$study_material_slug"));
        }

        $next_study_material_content_data = false;
        if($study_material_content_data)
        {
            $next_study_material_content_data = $this->StudyModel->get_next_study_material_content_by_ids($study_material_content_data,$study_material_content_data->section_id);
            if(empty($next_study_material_content_data))
            {
                    foreach ($study_section_ids as $array_study_section_id) 
                    {
                        if($array_study_section_id > $study_material_content_data->section_id)
                        {
                            $next_study_material_content_data = $this->StudyModel->get_next_study_material_content_by_ids($study_material_content_data,$array_study_section_id);
                            break;
                        }
                    }
                   
            }
        }


        if($study_data->price > 0 && $study_data->total_file > 0 && $user_id != $study_data->user_id)
        {
            $quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($purchases_type,$study_material_id);

            if(empty($quiz_last_paymetn_status))
            {
                return redirect(base_url("quiz-pay/payment-mode/$purchases_type/$study_material_id"));
            }
        }


        //check study material is premium start
        if($study_data->is_premium == 1 && $user_id != $study_data->user_id)
        {
         

                $is_premium_member = FALSE;
                $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
                if($get_logged_in_user_membership) 
                {
                    $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
                }        

                if($is_premium_member == FALSE)
                {
                    $user_category_membership = get_user_category_membership($user_id,$study_data->category_id);
                    if($user_category_membership) 
                    {
                        $is_premium_member = ($user_category_membership->validity && $user_category_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
                    }
                }   
                if($is_premium_member == FALSE)
                {
                    $this->session->set_flashdata('error', lang('please_take_membership'));
                    return redirect(base_url('membership'));
                }           
        
        }
        //check study material is premium end

        if($user_id)
        {
           $is_enrolled =  $this->db->where('user_id',$user_id)->where('study_material_id',$study_material_id)->get('study_material_user_histry')->row();
           if(empty($is_enrolled))
           {
                $this->session->set_flashdata('error', lang('please_enroll_first'));
                return redirect(base_url("study-content/$study_material_slug"));
           }
        }

        $current_date = date('Y-m-d');
        $study_view_data = $this->StudyModel->get_study_view($study_data->id,$this->input->ip_address(),$current_date);
       
        $comments_exist_or_not = $this->HomeModel->get_comment_through_quizid_userid_reltype($purchases_type,$study_material_id,$user_id);
         $material_comments = $this->HomeModel->get_quiz_comment($study_material_id,$purchases_type);
         
        $total_comments = count($material_comments);


        $average = 0;
        if($study_data->total_rating > 0 && $study_data->rating > 0)
        {
            $average = $study_data->total_rating / $study_data->rating;
        } 

        $average_rating = ($average > 0) ? $average : 5 ;
        $average_rating =  number_format((float)$average_rating, 1, '.', '');

        $site_phone_number = $this->settings->site_phone_number;

        $rating_star = [0, 0, 0, 0, 0];
        $study_m_user_data = $this->db->where('id',$study_data->user_id)->get('users')->row();
        

        $savedata = array();
        $savedata['study_material_id'] = $study_data->id;
        $savedata['ip_address'] = $this->input->ip_address();
        $savedata['user_agent'] = $_SERVER['HTTP_USER_AGENT'];

        if(empty($study_view_data))
        {
           $inserted_data = $this->StudyModel->save_study_view_data($savedata);
        }
        $meta_data = array('meta_title' => $study_data->meta_title, 'meta_keyword' => $study_data->meta_keywords, 'meta_description' =>  $study_data->meta_description, 'title' => $study_data->title,'description' => $study_data->description,'image' => "",);

        $this->set_title(lang('front_study_material_detail'));        
        
        $content_data = array('page_title' => lang('front_study_material_detail'),'study_material_id' => $study_material_id,'study_data'=>$study_data,'study_material_content_data'=>$study_material_content_data,'purchases_type'=>$purchases_type,'comments_exist_or_not'=>$comments_exist_or_not,'material_comments'=>$material_comments,'average'=>$average,'study_m_user_data' => $study_m_user_data,'average_rating' => $average_rating,'site_phone_number' => $site_phone_number,'study_material_section_data' => $study_material_section_data,'s_m_completed_data' => $s_m_completed_data,'s_m_completed_content_ids' => $s_m_completed_content_ids,'next_study_material_content_data'=>$next_study_material_content_data,'study_material_content_id' => $study_material_content_id,'study_material_slug'=>$study_material_slug);

        $data = $this->includes;
        $data['content'] = $this->load->view('study_material_detail', $content_data, TRUE);
        $data['meta_data'] = $meta_data;
        $this->load->view($this->template, $data);
    }


    function complete_sm_contant()
    {
        $response['status'] = 'error';
        $response['action'] = '';
        $response['message'] = lang('something_went_wrong');
        $response['complete_count'] = 0;
        $response['s_m_s_complete_count'] = 0;

        $s_m_contant_id = $this->input->post('s_m_contant_id');
        $s_m_id = $this->input->post('s_m_id');
        $s_m_section_id = $this->input->post('s_m_section_id');
        if(empty($s_m_contant_id) OR empty($s_m_id) OR empty($s_m_section_id))
        {
            echo json_encode($response);
            exit;
        }
        $user_id = (isset($this->user['id']) && $this->user['id']) ? $this->user['id'] : NULL;
        if(empty($user_id))
        {
            $response['message'] = lang('please_login_first');
            echo json_encode($response);
            exit;
        }

        $is_completed = $this->db->where('user_id',$user_id)->where('s_m_id',$s_m_id)->where('s_m_contant_id',$s_m_contant_id)->get('study_material_user_history')->row();

        if($is_completed)
        {
            $this->db->where('user_id',$user_id)->where('s_m_id',$s_m_id)->where('s_m_contant_id',$s_m_contant_id)->delete('study_material_user_history');
            $status =  $this->db->affected_rows();
            if($status)
            {
                $total = $this->db->select('count(id) as total')->where('user_id',$user_id)->where('s_m_id',$s_m_id)->get('study_material_user_history')->row('total');
                $response['status'] = 'success';
                $response['action'] = 'uncomplete';
                $response['message'] = lang('uncomplete_section_contant');
                $response['complete_count'] = $total;
                $s_m_s_complete_arr = get_user_completed_s_m_section_contant($s_m_id,$s_m_section_id,$user_id);
                $s_m_s_complete_count = $s_m_s_complete_arr ? count($s_m_s_complete_arr) : 0;
                $response['s_m_s_complete_count'] = $s_m_s_complete_count;


            }
            else
            {
                $response['message'] = lang('error_during_unmark_completed');
            }
            echo json_encode($response);
            exit;

        }
        else
        {

            $db_data_array = array();
            $db_data_array['s_m_id'] = $s_m_id;
            $db_data_array['s_m_contant_id'] = $s_m_contant_id;
            $db_data_array['s_m_section_id'] = $s_m_section_id;
            $db_data_array['user_id'] = $user_id;
            $db_data_array['comlete_on'] = date("Y-m-d H:i:s");
            $this->db->insert('study_material_user_history',$db_data_array);
            $new_id = $this->db->insert_id();

            if($new_id)
            {
                $total = $this->db->select('count(id) as total')->where('user_id',$user_id)->where('s_m_id',$s_m_id)->get('study_material_user_history')->row('total');
                $response['status'] = 'success';
                $response['action'] = 'complete';
                $response['message'] = lang('complete_section_contant');
                $response['complete_count'] = $total;
                $s_m_s_complete_arr = get_user_completed_s_m_section_contant($s_m_id,$s_m_section_id,$user_id);
                $s_m_s_complete_count = $s_m_s_complete_arr ? count($s_m_s_complete_arr) : 0;
                $response['s_m_s_complete_count'] = $s_m_s_complete_count;

            }
            else
            {
                $response['message'] = lang('error_during_mark_complete');
            }
            echo json_encode($response);
            exit;
        }

        echo json_encode($response);
        exit;
    }

    function complete_sm_contant_and_go_to_next()
    {
        $response['status'] = 'error';
        $response['message'] = lang('something_went_wrong');
        $response['action'] = '';

        $s_m_contant_id = $this->input->post('s_m_contant_id');
        $s_m_id = $this->input->post('s_m_id');
        $s_m_section_id = $this->input->post('s_m_section_id');
        if(empty($s_m_contant_id) OR empty($s_m_id) OR empty($s_m_section_id))
        {
            echo json_encode($response);
            exit;
        }
        $user_id = (isset($this->user['id']) && $this->user['id']) ? $this->user['id'] : NULL;
        if(empty($user_id))
        {
            $response['message']    = lang('please_login_first');
            $response['action']     = 'jump_to_next';
            echo json_encode($response);
            exit;
        }

        $is_completed = $this->db->where('user_id',$user_id)->where('s_m_id',$s_m_id)->where('s_m_contant_id',$s_m_contant_id)->get('study_material_user_history')->row();

        if($is_completed)
        {
            $response['status'] = 'success';
            $response['message'] = lang('already_complete_section_contant');
            echo json_encode($response);
            exit;

        }
        else
        {

            $db_data_array = array();
            $db_data_array['s_m_id'] = $s_m_id;
            $db_data_array['s_m_contant_id'] = $s_m_contant_id;
            $db_data_array['s_m_section_id'] = $s_m_section_id;
            $db_data_array['user_id'] = $user_id;
            $db_data_array['comlete_on'] = date("Y-m-d H:i:s");
            $this->db->insert('study_material_user_history',$db_data_array);
            $new_id = $this->db->insert_id();

            if($new_id)
            {
                $response['status'] = 'success';
                $response['message'] = lang('complete_section_contant');
            }
            else
            {
                $response['message'] = lang('error_during_mark_complete');
            }
            echo json_encode($response);
            exit;
        }

        echo json_encode($response);
        exit;
    }

    function like_study()
    {
        $response['status'] = 'error';
        $response['action'] = '';
        $response['message'] = lang('something_went_wrong');
        $response['like_count'] = 0;


        $user_id = (isset($this->session->userdata('logged_in')['id']) && $this->session->userdata('logged_in')['id']) ? $this->session->userdata('logged_in')['id'] : 0;
    	$user_id = $user_id ? $user_id : 0;
    	$study_id = (isset($_POST['study_id']) && $_POST['study_id']) ? $_POST['study_id'] : 0;
		if($study_id)
		{
	        if($user_id) 
	        {
	            $study_material_like = $this->db->where('study_material_id',$study_id)->where('user_id',$user_id)->get('study_material_like')->row();

	            if($study_material_like)
	            {
		            $delete_data = $this->HomeModel->delete_like_study_through_studyid($study_id, $user_id);
		            $get_count_of_likes = $this->HomeModel->get_count_likes_through_study_id($_POST['study_id']);

	            	$response['action'] = 'unlike';
		            if($delete_data)
		            {
		                $response['status'] = 'success';
		                $response['message'] = lang('unlike');
	        			$response['like_count'] = $get_count_of_likes;
		            }
		            else
		            {
		                $response['message'] = lang('error_durning_unlike');
		            }
	            }
	            else
	            {
		            $save_study_like['study_material_id'] = $study_id;
		            $save_study_like['user_id'] = $user_id;
		            $inserted_data = $this->HomeModel->insert_study_like($save_study_like);
		            $get_count_of_likes = $this->HomeModel->get_count_likes_through_study_id($study_id);

		            $response['action'] = 'like';
		            if($inserted_data)
		            {
	                    $response['status'] = 'success';
		                $response['message'] = lang('like');
	        			$response['like_count'] = $get_count_of_likes;;
		            }
		            else
		            {
		               $response['message'] = lang('error_durning_like');
		            }
			    }
			}
	        else
	        {
		        $response['message'] = lang('please_login_first');
	        }
	    }
        echo json_encode($response);
        exit;
    }



    function like_study_delete()
    {
        $response = [];
        if($this->session->userdata('logged_in')) 
        {
            $study_id = $_POST['study_id'];
            $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
            
            $delete_data = $this->HomeModel->delete_like_study_through_studyid($study_id, $user_id);
            $get_count_of_likes = $this->HomeModel->get_count_likes_through_study_id($_POST['study_id']);
            
            if($delete_data)
            {
                $response['success'] = $get_count_of_likes;
            }
            else
            {
                $response['error'] = 'unsuccessfull';
            }
        }
        else
        {
            $response['status'] = 'redirect';
        }
        echo json_encode($response);
    }

    function submit_rating($rel_type = false)
    {
         $save = $this->ratting->save_ratting($rel_type);
         if($save == true)
         {
            $this->session->set_flashdata('message', lang('ratting_added_successfully'));
            redirect($_SERVER['HTTP_REFERER']); 
         }
         else
         {
            $this->session->set_flashdata('error', lang('eroor_during_ratting_added')); 
            redirect($_SERVER['HTTP_REFERER']); 
         }
    }

    function review_like_insert()
    {

        $response = $this->ratting->insert_review_like();

        echo json_encode($response);
    }

    function review_delete()
    {
       $response = $this->ratting->dislike_review_like();

       echo json_encode($response);   
    }

    public function user_study_data()
    {
        $user_id = (isset($this->user['id']) && $this->user['id']) ? $this->user['id'] : NULL;
        
        if(empty($user_id))
        {
            $this->session->set_flashdata('error', lang('please_login_first'));
            return redirect(base_url());
        }

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');
        $this->add_js_theme('quiz.js');
        $user_study_metrial_data = $this->db->where('user_id',$user_id)->get('study_material_user_histry')->result_array();
        $user_study_metrial_ids = array(); 
        if($user_study_metrial_data)
        {
            $user_study_metrial_ids = array_column($user_study_metrial_data,"study_material_id");
        }
        $sm_data_array = $this->HomeModel->get_study_material_count_by_ids($user_study_metrial_ids);
        $count_sm = count($sm_data_array); 
        $this->load->library('pagination');

        $config['base_url'] = base_url('my-study-data');
        $config['total_rows'] = $count_sm;
        $config['per_page'] = 10;
        $config['uri_segment'] = 3;
        $config['use_page_numbers'] = TRUE;
        $config['page_query_string'] = FALSE;
        $config['reuse_query_string'] = TRUE;
        $config['first_link'] = 'First';
        $config['full_tag_open'] = '<ul class="pagination">';
        $config['full_tag_close'] = '</ul>';
        $config['attributes'] = ['class' => 'page-link'];
        $config['first_link'] = false;
        $config['last_link'] = false;
        $config['first_tag_open'] = '<li class="page-item">';
        $config['first_tag_close'] = '</li>';
        $config['prev_link'] = '&laquo';
        $config['prev_tag_open'] = '<li class="page-item">';
        $config['prev_tag_close'] = '</li>';
        $config['next_link'] = '&raquo';
        $config['next_tag_open'] = '<li class="page-item">';
        $config['next_tag_close'] = '</li>';
        $config['last_tag_open'] = '<li class="page-item">';
        $config['last_tag_close'] = '</li>';
        $config['cur_tag_open'] = '<li class="page-item active"><a href="#" class="page-link">';
        $config['cur_tag_close'] = '<span class="sr-only">(current)</span></a></li>';
        $config['num_tag_open'] = '<li class="page-item">';
        $config['num_tag_close'] = '</li>';
        $this->pagination->initialize($config);
        $pro_per_page = $config['per_page'];
        $page = $this->uri->segment(2) > 0 ? (($this->uri->segment(2) - 1) * $pro_per_page) : $this->uri->segment(2);
        $page_links = $this->pagination->create_links();
        
        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }


        $study_material_data = $this->HomeModel->get_tutors_study_material_per_page($user_study_metrial_ids, $pro_per_page, $page);
        $paid_s_m_array = $this->Payment_model->get_user_paid_study_matiral_obj($user_id);
             

        $page_title = lang('my_study_materials');
        $this->set_title(lang('my_study_materials')); 



        $content_data = array('Page_message' =>  $page_title, 'page_title' => $page_title , 'study_material_data' => $study_material_data, 'pagination' => $page_links,'is_premium_member' => $is_premium_member,'paid_s_m_array'=>$paid_s_m_array);

        $data = $this->includes;
        $data['content'] = $this->load->view('my_study_materials', $content_data, TRUE);
        $this->load->view($this->template, $data);


        
    }



    
}