<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Membership_Controller extends Public_Controller {
    /** 
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->load->library('form_validation');
        $this->load->model('MembershipModel');
    }

    function index()
    {
        $membership_data = $this->MembershipModel->get_membership_data();

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;

        $user_membership_history = $this->MembershipModel->get_user_membership($user_id);
        $this->set_title(lang('admin_membership'));
        $content_data = array('membership_data'=>$membership_data,'user_id' => $user_id,'user_membership_history' => $user_membership_history);
        
        $data = $this->includes;
        $data['content'] = $this->load->view('membership_list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }
}