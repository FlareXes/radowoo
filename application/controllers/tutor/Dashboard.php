<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Dashboard extends Tutor_Controller 
{

    function __construct() 
    {
        parent::__construct();
        $this->load->model('DashboardModel');
    }

    function index() 
    {
        
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        // setup page header data
        $this->set_title(lang('dashboard'));
        $data = $this->includes;
        $data['products'] = 400;
        $data['market'] = 30;
        $data['brands'] = 20;
        $data['category'] = 0;
        $data['users'] = 0;
        $data['pages'] = 0;
        $data['quiz'] = $this->db->select('count(*) as count')->where('is_quiz_active',1)->where('user_id',$user_id)->get('quizes')->row('count');
        $data['blog_post'] = 0;
        $data['payment_total'] = 0;
        $data['langues_count'] = 0;
        
        $data['content'] = $this->load->view('tutor/dashboard', $data, TRUE);
        $this->load->view($this->template, $data);
        
    }
}