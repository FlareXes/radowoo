<?php
class Ratting
{
	public function __construct()
    {
        $this->CI =& get_instance();
        $this->CI->load->model('HomeModel');
        $this->CI->load->library('form_validation');
    }

    public function save_ratting($rel_type) 
    {
    	$this->CI->form_validation->set_rules('reviewstar','Review Star', 'required|min_length[1]|max_length[5]');
        $save_rating_data = array();
        if($this->CI->session->userdata('logged_in')) 
        {
           if($this->CI->input->post('save')) 
           {
                if ($this->CI->form_validation->run() == TRUE) 
                {
                	$save_rating_data['user_id'] = $this->CI->session->userdata('logged_in')['id'];
                    $save_rating_data['rel_id'] = $this->CI->input->post('quizid');
                    $save_rating_data['review_content'] = $this->CI->input->post('ratingcontent');
                    $save_rating_data['rating'] = $this->CI->input->post('reviewstar');
                    $save_rating_data['rel_type'] = $rel_type;
                    
                    $inserted_data = $this->CI->HomeModel->insert_rating_data($save_rating_data);
                    if($inserted_data)
                    {
                        return true;
                    }
                    else
                    {
                        return false;
                    }
                }
                else
                {
                    $fielderror = $this->CI->form_validation->error_array();
                    $this->CI->session->set_flashdata('error', implode(" ", $fielderror)); 
                    redirect($_SERVER['HTTP_REFERER']);                     
                }
           }
       }
       else 
       {
           $this->CI->session->set_flashdata('error', lang('plz_login_first')); 
           redirect(base_url('login'));
       }
    }

    public function insert_review_like()
    {
    	$response = [];    
        if($this->CI->session->userdata('logged_in')) 
        { 
        	$save_review_like['review_id'] = $_POST['review_id'];
            $save_review_like['user_id'] = $this->CI->session->userdata('logged_in')['id'];
            $save_review_like['rel_type'] = $_POST['rel_type'];

            $inserted_data = $this->CI->HomeModel->insert_review_like($save_review_like);
            $get_count_of_likes = $this->CI->HomeModel->get_count_likes_through_review_id($_POST['review_id'],$_POST['rel_type']);
            
            if($inserted_data)
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

       	return $response;
    }

    public function dislike_review_like()
    {
    	$response = [];
        if($this->CI->session->userdata('logged_in')) 
        {
            $review_id = $_POST['review_id'];
            $delete_data = $this->CI->HomeModel->delete_review_like_through_reviewid_rel_type($review_id,$this->CI->user['id'],$_POST['rel_type']);
            $get_count_of_likes = $this->CI->HomeModel->get_count_likes_through_review_id($_POST['review_id'],$_POST['rel_type']);
            if($delete_data)
            {
                $response['successfull'] = $get_count_of_likes;
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

        return $response;
    }
}  