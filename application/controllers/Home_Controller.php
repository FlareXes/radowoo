<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Home_Controller extends Public_Controller {
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->add_css_theme('sweetalert.css');
        $this->add_js_theme('sweetalert-dev.js'); 
        $this->add_css_theme('set2.css');
        $this->add_css_theme('slick.min.css');
        $this->add_css_theme('slick-theme.min.css');
        $this->add_js_theme('slick.min.js');
        $this->add_js_theme('home.js');
        $this->load->library('form_validation');
        $this->load->model('HomeModel');
        $this->load->model('MembershipModel');
        $this->load->model('Payment_model');
        $this->add_css_theme('quiz_box.css');
        

    }
    
    function index() 
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0; 

        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $category_data = $this->HomeModel->get_category();
        
        $testimonial_data = $this->HomeModel->get_testmonials();
        $sponser_data = $this->HomeModel->get_sponsers();

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

        $get_page_content = '';

        if($this->settings->pages_list)
        {
            $get_page_content = $this->db->select('content')->where('slug',$this->settings->pages_list)->get('pages')->row();
        }
        
        $paid_quizes_array = $this->Payment_model->get_user_paid_quiz_obj($user_id);
        
        $paid_s_m_array = $this->Payment_model->get_user_paid_study_matiral_obj($user_id);
          
        $latest_quiz_data = $this->HomeModel->get_latest_quiz(4,'quizes.added','DESC');
        
        $popular_quiz_data = $this->HomeModel->get_latest_quiz(4,'total_view','DESC');

        $upcoming_quiz_data = $this->HomeModel->get_upcoming_quiz();
        //p($upcoming_quiz_data);
       $latest_study_material_data = $this->HomeModel->get_latest_study_material(4,'study_material.added');
        
        $popular_study_material_data = $this->HomeModel->get_latest_study_material(4,'total_view');
       
        $content_data = array('Page_message' => lang('welcome_to_online_quiz'), 'page_title' => lang('home'),'category_data' => $category_data,'testimonial_data'=>$testimonial_data,'latest_quiz_data' => $latest_quiz_data, 'popular_quiz_data' => $popular_quiz_data,'session_quiz_data' => $session_quiz_data, 'session_quiz_question_data' => $session_quiz_question_data,'sponser_data'=>$sponser_data,'latest_study_material_data'=>$latest_study_material_data,'popular_study_material_data'=>$popular_study_material_data,'is_premium_member' => $is_premium_member,'paid_quizes_array'=>$paid_quizes_array,'paid_s_m_array' => $paid_s_m_array, 'upcoming_quiz_data' => $upcoming_quiz_data,'get_page_content'=>$get_page_content,);

        $data = $this->includes;
        $data['content'] = $this->load->view('home', $content_data, TRUE);

        $this->load->view($this->template, $data);
    }

}