<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Search_Controller extends Public_Controller {

    /**
     * Constructor 
     */
    function __construct() 
    {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('HomeModel');
        $this->load->model('Payment_model');
        $this->load->model('TestModel');
        $this->load->model('MembershipModel');
        $this->add_css_theme('quiz_box.css');
        $this->add_css_theme('set2.css');
        $this->load->library('Ratting');
        $this->add_js_theme('perfect-scrollbar.min.js');
        $this->add_css_theme('table-main.css');
        $this->add_css_theme('perfect-scrollbar.css');
    }
    
    public function index($search_itesm = NULL)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        $users_like = "";
        $searching_for = "";
        $header_search_value = "";
        if($this->input->post('header_search_type',true))
        {
            $searching_for = $this->input->post('header_search_type',true);
            $header_search_value = $this->input->post('header_search_value',true);
        }

        if($searching_for OR $header_search_value)
        {
            return redirect(base_url("search/".$searching_for."?query=".$header_search_value));
        }
        if(empty($search_itesm))
        {
            return redirect(base_url("search/quiz"));
        }

        if($search_itesm != "quiz" AND $search_itesm != "study" AND $search_itesm != "category" AND $search_itesm != "tutor")
        {
            $this->session->set_flashdata('error', lang('invalid uri arguments'));
            return redirect(base_url());
        }

        $find_like = $this->input->get('query');

        $filter_category = $this->input->get('category',TRUE);
        $filter_purchage_type = $this->input->get('purchage_type',TRUE);
        $filter_rating = $this->input->get('rating',TRUE);
        $filter_duration = $this->input->get('duration');

        if($filter_purchage_type)
        {
            if($filter_purchage_type != "paid" && $filter_purchage_type != "premium" && $filter_purchage_type != "free")
            {
                $this->session->set_flashdata('error', lang('invalid_filter_uri_arguments'));
                return redirect(base_url());
            }
        }

        if($filter_rating)
        {
            if($filter_rating != 1 && $filter_rating != 2 && $filter_rating != 3 && $filter_rating != 4 && $filter_rating != 5)
            {
                $this->session->set_flashdata('error', lang('invalid_filter_uri_arguments'));
                return redirect(base_url());
            }
        }


        if($filter_duration)
        {
            if($filter_duration != "5min" && $filter_duration != "10min" && $filter_duration != "20min" && $filter_duration != "30min" && $filter_duration != "1hour" && $filter_duration != "6hour" && $filter_duration != "1day" && $filter_duration != "unlimited")
            {
                $this->session->set_flashdata('error', lang('invalid_filter_uri_arguments'));
                return redirect(base_url());
            }
        }
        $filter_minute_from = 0;
        $filter_minute_to = 0;
        if($filter_duration)
        {
            if($filter_duration == "5min")
            {
                $filter_minute_from = 1;
                $filter_minute_to = 5;
            }
            else if($filter_duration == "10min")
            {
                $filter_minute_from = 6;
                $filter_minute_to = 10;
            }
            else if($filter_duration == "20min")
            {
                $filter_minute_from = 11;
                $filter_minute_to = 20;
            }
            else if($filter_duration == "30min")
            {
                $filter_minute_from = 21;
                $filter_minute_to = 30;
            }
            else if($filter_duration == "1hour")
            {
                $filter_minute_from = 31;
                $filter_minute_to = 60;
            }
            else if($filter_duration == "6hour")
            {
                $filter_minute_from = 61;
                $filter_minute_to = 360;
            }
            else if($filter_duration == "1day")
            {
                $filter_minute_from = 361;
                $filter_minute_to = 1440;
            }
            else if($filter_duration == "unlimited")
            {
                $filter_minute_from = 1441;
                $filter_minute_to = 144000000000000000;
            }
        }

        $serch_url = base_url("search/$search_itesm?query=$find_like");
        $session_quiz_data = array();
        $session_quiz_question_data = array();

        if($this->session->quiz_session)
        {
            $get_quiz_session = $this->session->quiz_session;
            $session_quiz_data = $get_quiz_session['quiz_data'];
            $session_quiz_question_data = $get_quiz_session['quiz_question_data'];
        }

        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }
        
        $paid_quizes_array = $this->Payment_model->get_user_paid_quiz_obj($user_id);
        $paid_s_m_array = $this->Payment_model->get_user_paid_study_matiral_obj($user_id);

        $filtered_db_record = false;
        $all_category_data = $this->HomeModel->get_category();

        if($search_itesm == "category")
        {
            if($find_like)
            {
                $filtered_db_record = $this->db->like('category_title',$find_like);
            }
            else
            {
                $this->db->where('parent_category',0);
            }
            $this->db->where('category_status',1);
            $this->db->where('category_is_delete',0);
            $filtered_db_record = $this->db->limit(20)->get('category')->result();
        }
        else if ($search_itesm == "study")
        {
            if($filter_category)
            {
                $f_category_data = $this->HomeModel->get_category_by_slug($filter_category);
                $filter_category_id = 0;
                if($f_category_data)
                {
                    $filter_category_id = $f_category_data->id;
                }
            }

            if($filter_purchage_type)
            {
                if($filter_purchage_type != "paid" && $filter_purchage_type != "paid" && $filter_purchage_type != "paid")
                {
                    $filter_category_id = $f_category_data->id;
                }
            }


            if($filter_rating)
            {
                $filter_rating_to = $filter_rating;
                $filter_rating_from = $filter_rating -1;;
                

                $fiter_sm_rating_data = $this->db->select('quiz_reviews.*,rel_id as quiz_id,avg(rating) as avg_rating')
                ->where('rel_type','material')->where('status',1)
                ->having('avg_rating >',$filter_rating_from)
                ->having('avg_rating  <=',$filter_rating_to)
                ->group_by('rel_id')->get('quiz_reviews')->result_array();

                $fiter_sm_rating_ids[] = 0; 
                if($fiter_sm_rating_data)
                {
                    foreach ($fiter_sm_rating_data as $fiter_sm_rating_array) 
                    {
                        $fiter_sm_rating_ids[] = $fiter_sm_rating_array['quiz_id'];
                    }
                }
            }


            if($filter_duration)
            {
                $filter_sm_duration_data = $this->db->select('study_material_content.*,sum(duration) as total_duration')
                ->having('total_duration  >',$filter_minute_from)
                ->having('total_duration <=',$filter_minute_to)
                ->group_by('study_material_id')
                ->get('study_material_content')
                ->result_array();

                $filter_sm_duration_id_arr[] = 0; 
                if($filter_sm_duration_data)
                {
                    $filter_sm_duration_id_arr = array_column($filter_sm_duration_data,"study_material_id");
                }
            }



            $this->db->select("study_material.*,
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'pdf') as total_pdf, 

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'video') as total_audio, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id) as total_file, 

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND (type = 'video' OR type = 'vimeo-embed-code' OR type = 'youtube-embed-code')) as total_video,

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'doc') as total_doc, 

                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'image') as total_images, 
                (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = 'content') as total_other, 


                (select CONCAT(first_name, last_name) from users where users.id = study_material.user_id) as full_name, 
                (SELECT id FROM study_material_like where study_material_id = study_material.id AND user_id = '".$user_id."') as like_id,
                (SELECT count(id) FROM study_material_like where study_material_id = study_material.id) as total_like, 
                (SELECT count(id) FROM study_material_view where study_material_id = study_material.id) as total_view, 
                (select count(type) from study_material_content where study_material_content.study_material_id = study_material.id) as type,
                (select count(id) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as rating,
                (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'material'."') as total_rating")
            ->where('study_material.status',1);

            if($find_like)
            {
                $this->db->like('title',$find_like);
            }
            
            if($filter_category)
            {
                $this->db->where('category_id',$filter_category_id);
            }
            if($filter_purchage_type)
            {
                if($filter_purchage_type == "paid")
                {
                    $this->db->where('price >',0);
                    $this->db->where('is_premium',0);
                }
                if($filter_purchage_type == "premium")
                {
                    $this->db->where('is_premium',1);
                }

                if($filter_purchage_type == "free")
                {
                    $this->db->where('is_premium !=',1);
                    $this->db->where('price < ',1);
                }
            }

            if($filter_rating)
            {
                $this->db->where_in('id',$fiter_sm_rating_ids);
            }

            if($filter_duration)
            {
                $this->db->where_in('id',$filter_sm_duration_id_arr);
            }

            $filtered_db_record = $this->db->limit(20)->get('study_material')->result(); 

        }
        else if ($search_itesm == "quiz")
        {

                if($filter_category)
                {
                    $f_category_data = $this->HomeModel->get_category_by_slug($filter_category);
                    $filter_category_id = 0;
                    if($f_category_data)
                    {
                        $filter_category_id = $f_category_data->id;
                    }
                }


                if($filter_rating)
                {
                    $filter_rating_to = $filter_rating;
                    $filter_rating_from = $filter_rating -1;;
                    

                    $fiter_quiz_rating_data = $this->db->select('quiz_reviews.*,rel_id as quiz_id,avg(rating) as avg_rating')
                    ->where('rel_type','quiz')->where('status',1)
                    ->having('avg_rating >',$filter_rating_from)
                    ->having('avg_rating  <=',$filter_rating_to)
                    ->group_by('rel_id')->get('quiz_reviews')->result_array();

                    $fiter_quiz_rating_ids[] = 0; 
                    if($fiter_quiz_rating_data)
                    {
                        foreach ($fiter_quiz_rating_data as $fiter_quiz_rating_array) 
                        {
                            $fiter_quiz_rating_ids[] = $fiter_quiz_rating_array['quiz_id'];
                        }
                    }
                }




                $this->db->select("quizes.*,
                (select count(id) from questions where questions.quiz_id = quizes.id) as total_question, 
                (select first_name from users where users.id = quizes.user_id) as first_name , 
                (select last_name from users where users.id = quizes.user_id) as last_name, 
                (SELECT count(id) FROM quiz_count where quiz_id = quizes.id) as total_view,
                (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = '".$user_id."') as like_id,
                (SELECT count(id) FROM quiz_like where quiz_id = quizes.id) as total_like,
                (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as rating,
                (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = '".'quiz'."') as total_rating")
                ->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
                ->where(time().' BETWEEN start_date_time AND end_date_time')
                ->where('is_quiz_active',1);
                if($find_like)
                {
                    $this->db->like('title',$find_like);
                }

                if($filter_category)
                {
                    $this->db->where('category_id',$filter_category_id);
                }


                if($filter_purchage_type == "paid")
                {
                    $this->db->where('price >',0);
                    $this->db->where('is_premium',0);
                }
                if($filter_purchage_type == "premium")
                {
                    $this->db->where('is_premium',1);
                }

                if($filter_purchage_type == "free")
                {
                    $this->db->where('is_premium !=',1);
                    $this->db->where('price < ',1);
                }

                if($filter_rating)
                {
                    $this->db->where_in('id',$fiter_quiz_rating_ids);
                }                

                if($filter_duration)
                {
                    if($filter_duration == "unlimited")
                    {
                        $this->db->where("duration_min = 0 OR duration_min > 1440");
                    }
                    else
                    {   
                        $this->db->where('duration_min >',$filter_minute_from);
                        $this->db->where('duration_min <=',$filter_minute_to);
                    }
                }

                $filtered_db_record = $this->db->limit(20)->get('quizes')->result(); 
            
        }
        else if($search_itesm == "tutor")
        {
            $this->db->group_start();
            $this->db->like('first_name', $find_like);
            $this->db->or_like('last_name',$find_like);
            $this->db->or_like('username',$find_like);
            $this->db->group_end();
            $this->db->where("(role='tutor' OR role='admin')");
            $this->db->where('status','1');
            $this->db->where('deleted','0');
            $users_like = $this->db->get('users')->result();
            $filtered_db_record = $users_like;
        }

        $this->set_title(lang("search"), $this->settings->site_name);

        $content_data = array();
        $content_data['Page_message'] = lang("search");
        $content_data['filtered_db_record'] = $filtered_db_record;
        $content_data['search_itesm'] = $search_itesm;
        $content_data['search_itesm'] = $search_itesm;
        $content_data['find_like'] = $find_like;
        $content_data['user_id'] = $user_id;
        $content_data['session_quiz_question_data'] = $session_quiz_question_data;
        $content_data['session_quiz_data'] = $session_quiz_data;
        $content_data['is_premium_member'] = $is_premium_member;
        $content_data['paid_quizes_array'] = $paid_quizes_array;
        $content_data['paid_s_m_array'] = $paid_s_m_array;
        $content_data['all_category_data'] = $all_category_data;
        $content_data['serch_url'] = $serch_url;
        $content_data['filter_category'] = $filter_category;
        $content_data['filter_purchage_type'] = $filter_purchage_type;
        $content_data['filter_rating'] = $filter_rating;
        $content_data['filter_duration'] = $filter_duration;
        $content_data['users_like'] = $users_like;

        $data = $this->includes;
        $data['content'] = $this->load->view('search', $content_data, TRUE);        
        $this->load->view($this->template, $data);
    }

    public function find_tutor_quiz($username)
    {
        $this->db->where('username',$username);
        $this->db->where("(role='tutor' OR role='admin')");
        $this->db->where('status','1');
        $this->db->where('deleted','0');
        $user_data = $this->db->get('users')->row();
        if(empty($user_data))
        {
            $this->session->set_flashdata('error', lang('invalid uri arguments'));
            return redirect(base_url());
        }
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $session_quiz_data = array();
        $session_quiz_question_data = array();

        if($this->session->quiz_session)
        {
            $get_quiz_session = $this->session->quiz_session;
            $session_quiz_data = $get_quiz_session['quiz_data'];
            $session_quiz_question_data = $get_quiz_session['quiz_question_data'];
        }

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');
        $this->add_js_theme('quiz.js');

        $quiz_data_array = $this->HomeModel->get_quiz_by_tutors_id($user_data->id);

        $count_quiz = count($quiz_data_array); 
        $this->load->library('pagination');

        $config['base_url'] = base_url('tutor-quiz/') . $username;
        $config['total_rows'] = $count_quiz;
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
        $page = $this->uri->segment(3) > 0 ? (($this->uri->segment(3) - 1) * $pro_per_page) : $this->uri->segment(3);
        $page_links = $this->pagination->create_links();

        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }
        
        $paid_quizes_array = $this->Payment_model->get_user_paid_quiz_obj($user_id);
          
        $quiz_data = $this->HomeModel->get_tutors_quiz_per_page($user_data->id, $pro_per_page, $page);
                
        $page_title = lang('tutor_quiz');
        $this->set_title(lang('tutor_quiz')); 

        $content_data = array('Page_message' =>  $page_title, 'page_title' => $page_title , 'user_data' => $user_data, 'quiz_data' => $quiz_data,'session_quiz_data' => $session_quiz_data, 'session_quiz_question_data' => $session_quiz_question_data, 'pagination' => $page_links,'is_premium_member' => $is_premium_member,'paid_quizes_array'=>$paid_quizes_array);

        $data = $this->includes;
        $data['content'] = $this->load->view('tutor_quiz', $content_data, TRUE);
        $this->load->view($this->template, $data);


    }
    public function find_tutor_study_material($username)
    {
        $this->db->where('username',$username);
        $this->db->where("(role='tutor' OR role='admin')");
        $this->db->where('status','1');
        $this->db->where('deleted','0');
        $user_data = $this->db->get('users')->row();
        if(empty($user_data))
        {
            $this->session->set_flashdata('error', lang('invalid uri arguments'));
            return redirect(base_url());
        }

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js');
        $this->add_js_theme('quiz.js');

        $sm_data_array = $this->HomeModel->get_category_study_material($user_data->id);
        $count_quiz = count($sm_data_array); 
        $this->load->library('pagination');

        $config['base_url'] = base_url('tutor-study-materia/') . $username;
        $config['total_rows'] = $count_quiz;
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
        $page = $this->uri->segment(3) > 0 ? (($this->uri->segment(3) - 1) * $pro_per_page) : $this->uri->segment(3);
        $page_links = $this->pagination->create_links();

        
        $is_premium_member = FALSE;
        $get_logged_in_user_membership = $this->MembershipModel->get_user_membership($user_id);
        if($get_logged_in_user_membership) 
        {
            $is_premium_member = ($get_logged_in_user_membership->validity && $get_logged_in_user_membership->validity >= date('Y-m-d')) ? TRUE : FALSE;
        }
        
          

        $study_material_data = $this->HomeModel->get_tutors_study_material_per_page($user_data->id, $pro_per_page, $page);
        $paid_s_m_array = $this->Payment_model->get_user_paid_study_matiral_obj($user_id);
                
        $page_title = lang('tutor_study_materials');
        $this->set_title(lang('study_materials')); 

        $content_data = array('Page_message' =>  $page_title, 'page_title' => $page_title , 'user_data' => $user_data, 'study_material_data' => $study_material_data, 'pagination' => $page_links,'is_premium_member' => $is_premium_member,'paid_s_m_array'=>$paid_s_m_array);

        $data = $this->includes;
        $data['content'] = $this->load->view('tutor_study_materials', $content_data, TRUE);
        $this->load->view($this->template, $data);


        
    }

}