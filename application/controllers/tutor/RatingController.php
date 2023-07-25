<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class RatingController extends Tutor_Controller { 
     function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css','admin');
        $this->add_js_theme('jquery.multi-select.min.js',false,'admin');
        $this->add_js_theme('rating.js');
        // load the rating model
        $this->load->model('RatingModel');
        $this->load->model('QuizModel');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('tutor/rating'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");
    }

    function index($rating_for, $rating_relation_id = NULL) 
    {
        $relation_data = NULL;
        $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        if($rating_for == "quiz")
        {
            $relation_data = $this->QuizModel->get_quiz_by_id($rating_relation_id,$login_user_id);
        }
        if($rating_for == "material")
        {
            $relation_data = $this->db->where('id',$rating_relation_id)->where('user_id',$login_user_id)->get('study_material')->row();
        }

        if(empty($relation_data))
        {
            $this->session->set_flashdata('error', lang('something_went_wrong'));
            return redirect(base_url('admin'));
        }

        $this->add_css_theme('sweetalert.css','admin')->add_js_theme('sweetalert-dev.js',false,'admin')->add_js_theme('bootstrap-notify.min.js',false,'admin')->set_title('Rating List'.": ".$relation_data->title);
        


        $data = $this->includes;
        $content_data = array('quiz_id' => $rating_relation_id,'rating_relation_id' => $rating_relation_id ,'rating_for' => $rating_for);
        // load views
        $data['content'] = $this->load->view('tutor/rating/list', $content_data, TRUE);
        $this->load->view($this->template, $data); 
    }

    function rating_list()
    {

        $rating_relation_id = $_POST['rating_relation_id'];
        $rating_for = $_POST['rating_for'];

        $list = $this->RatingModel->get_rating($rating_for, $rating_relation_id);

        $data = array();
        $no = $_POST['start'];
        foreach ($list as $rating) {
            
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = xss_clean($rating->first_name. " ".$rating->last_name);
            $row[] = ucfirst($rating->review_content);
            $row[] = ucfirst($rating->rating);
            $checkvalue = ($rating->status == 0 ? "" : 'checked="checked"');
            $row[] = '<label class="custom-switch mt-2">
                <input type="checkbox" data-id="' . $rating->id . '" name="custom-switch-checkbox" class="custom-switch-input togle_switch" ' . $checkvalue . '>
                <span class="custom-switch-indicator indication"></span>
                </label>';
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->RatingModel->count_all($rating_for, $rating_relation_id), "recordsFiltered" => $this->RatingModel->count_filtered($rating_for, $rating_relation_id), "data" => $data);
        //output to json format
        echo json_encode($output);
    }

    function update_status()
    {
        
        
        $id = $_POST['rating_id'];
        $status = $_POST['status'];
        
        $this->RatingModel->update_status($id, $status);
        $success = array('status' => $status, 'messages' => lang('admin Rating Status Updated Successfully'));
        echo json_encode($success);
    }

    function approve()
    {
        
        $this->add_css_theme('sweetalert.css','admin')->add_js_theme('sweetalert-dev.js',false,'admin')->add_js_theme('bootstrap-notify.min.js',false,'admin')->set_title('Approve Rating List');
        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('tutor/rating/approvelist', $content_data, TRUE);
        $this->load->view($this->template, $data);        
    }

    function approve_rating_list()
    {
        $list = $this->rating_model->get_approve_rating();        
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $rating) {
            $no++;
            $row = array();
            $row[] = $rating->username;
            $row[] = ucfirst($rating->review_content);
            $row[] = ucfirst($rating->rating);
            $checkvalue = ($rating->status == 0 ? "" : 'checked="checked"');
            $row[] = '<label class="custom-switch mt-2">
                <input type="checkbox" data-id="' . $rating->id . '" name="custom-switch-checkbox" class="custom-switch-input togle_switch" ' . $checkvalue . '>
                <span class="custom-switch-indicator indication"></span>
                </label>';
            $data[] = $row;
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->rating_model->approve_count_all(), "recordsFiltered" => $this->rating_model->approve_count_filtered(), "data" => $data);
        //output to json format
        echo json_encode($output);
    }

}
