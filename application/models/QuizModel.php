<?php defined('BASEPATH') OR exit('No direct script access allowed');
class QuizModel extends CI_Model 
{

    var $table = 'quizes';
    var $column_order = array(null, 'title', 'category_title', 'number_questions', 'duration_min','is_quiz_active' , NULL);
    var $column_search = array('title', 'category_title','number_questions','duration_min','is_quiz_active');
    var $order = array('quizes.id' => 'DESC');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query() 
    {
        $this->db->from($this->table);
        $this->db->join('category', 'quizes.category_id = category.id', 'left');
        $this->db->join('users', 'users.id = quizes.user_id', 'left');
        
        $logged_in_user = $this->session->userdata('logged_in');
        if($logged_in_user['role'] == "tutor")
        {
            $this->db->where('quizes.user_id', $logged_in_user['id']);
        }

        if($this->input->get('category'))
        {
            $category_filter = $this->input->get('category');
            $this->db->where('category.id', $category_filter);
        }

        $i = 0;
        foreach ($this->column_search as $item) {
            // if datatable send POST for search
            if ($_POST['search']['value']) {
                // first loop
                if ($i === 0) {
                    // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } else {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                // last loop
                if (count($this->column_search) - 1 == $i) {
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
        // here order processing
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order)) {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order) ]);
        }
    }

    function get_quiz() 
    {
        $this->_get_datatables_query();  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('quizes.*,category.category_title')
        // ->where('is_quiz_active',1)
        ->order_by('quizes.id', 'desc')
        ->get();
        return $query->result();
        
    }
    
    function count_filtered() 
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function count_all() {
        $this->db->from($this->table);

        $logged_in_user = $this->session->userdata('logged_in');
        if($logged_in_user['role'] == "tutor")
        {
            $this->db->where('quizes.user_id', $logged_in_user['id']);
        }
        return $this->db->count_all_results();
    }

    

    function get_all_category() {
        return $this->db->where('category_is_delete',0)->where('category_status',1)->order_by('category_title','asc')->get('category')->result();
    }

    function get_all_users() {
        return $this->db->where('deleted','0')->where('status','1')->where('role !=','user')->order_by('first_name','asc')->get('users')->result();
    }

    function get_languages() {
        return $this->db->select('id,lang')->order_by('id','asc')->get('language')->result();
    }


    function all_quiz_gradings($user_id) 
    {
        $this->db->select('id,title');
        if($user_id)
        {
            $this->db->where('id',$user_id);
        }
        return $this->db->order_by('id','asc')->get('quiz_grading')->result();
    }

    function get_translated_data($quiz_id)
    {
         return $this->db->where('table','quizes')->where('forigen_table_id',$quiz_id)->order_by('id','asc')->get('translations')->result();
    }

    function delete_translated_data($quiz_id)
    {
         return $this->db->where('table','quizes')->where('forigen_table_id',$quiz_id)->delete('translations');
    }

    function insert_translated_data($data) 
    {
     return $this->db->insert_batch('translations', $data);
    }

    function insert_quiz($data) 
    {
        $this->db->insert('quizes', $data);
        return $this->db->insert_id();
    }

    function batch_insert_quiz_fields($data) 
    {
     return $this->db->insert_batch('quizes', $data);
    }

 function quiz_name_like_this($id, $title) 
 {
    $this->db->like('title', $title);
    if ($id) 
    {
        $this->db->where('id !=', $id);
        $this->db->where('id <', $id);
    }
    return $this->db->count_all_results('quizes');
}

function get_quiz_by_id($quiz_id,$login_user_id = NULL)
{
    $this->db->where('id',$quiz_id);
    if($login_user_id)
    {
       $this->db->where('user_id',$login_user_id); 
    }

    $logged_in_user = $this->session->userdata('logged_in');
    if($logged_in_user['role'] == "tutor")
    {
        $this->db->where('quizes.user_id', $logged_in_user['id']);
    }

    return $this->db->get('quizes')->row();
}

function update_quiz($quiz_id, $data,$login_user_id = NULL) 
{
    $this->db->set($data)->where('id', $quiz_id);
    //  $this->db->where('is_quiz_active',1)

    if($login_user_id)
    {
       $this->db->where('user_id',$login_user_id); 
    }

    $logged_in_user = $this->session->userdata('logged_in');
    if($logged_in_user['role'] == "tutor")
    {
        $this->db->where('quizes.user_id', $logged_in_user['id']);
    }


    $this->db->update('quizes');
    return $this->db->affected_rows();
}

function delete_quiz($quiz_id,$login_user_id = NULL) 
{
    $this->db->where('id', $quiz_id);
    if($login_user_id)
    {
       $this->db->where('user_id',$login_user_id); 
    }

    $logged_in_user = $this->session->userdata('logged_in');
    if($logged_in_user['role'] == "tutor")
    {
        $this->db->where('quizes.user_id', $logged_in_user['id']);
    }


    $this->db->delete('quizes');

    $status = $this->db->affected_rows();
    if($status)
    {
        $quiz_id_array = array($quiz_id);
        $this->delete_quiz_related_data($quiz_id_array);
    }
   return $status; 
}



function delete_tutor_quiz($quiz_id,$login_user_id) 
{

    $this->db->where('id', $quiz_id)->where('user_id', $login_user_id)->delete('quizes');
    $status = $this->db->affected_rows();
    if($status)
    {
        $quiz_id_array = array($quiz_id);
        $this->delete_quiz_related_data($quiz_id_array,$login_user_id);
    }
   return $status; 
}






    function delete_quiz_related_data($quiz_id_array,$login_user_id = NULL)
    {

        if($quiz_id_array)
        {
            $this->db->where_in('quiz_id', $quiz_id_array)->delete('package_quizes'); 
            $this->db->where_in('quiz_id', $quiz_id_array)->delete('quiz_count'); 
            $this->db->where_in('quiz_id', $quiz_id_array)->delete('quiz_like'); 
            $this->db->where_in('forigen_table_id', $quiz_id_array)->where('table','quizes')->delete('translations'); 
            

            
            
            $questions_ids = $this->get_questions_ids_by_quiz_ids($quiz_id_array);           
            if($questions_ids)
            {
                $this->db->where_in('id', $questions_ids)->delete('questions');
                $this->db->where_in('forigen_table_id', $questions_ids)->where('table','questions')->delete('translations'); 
            }


            
            $reviews_ids =  $this->get_reviews_ids_by_quiz_ids($quiz_id_array);           
            if($reviews_ids)
            {
                $this->db->where_in('id', $reviews_ids)->delete('quiz_reviews'); 
                $this->db->where_in('review_id', $reviews_ids)->delete('review_likes');
            }

            
            $participants_ids =  $this->get_participants_ids_by_quiz_ids($quiz_id_array);           
            if($participants_ids)
            {
                $this->db->where_in('id', $participants_ids)->delete('participants');
                $this->db->where_in('participant_id', $participants_ids)->delete('user_questions');
            }

        }

        return true;

    }




    function get_quiz_id_by_user_id($id) 
    {
        $quiz_array = $this->db->where('user_id', $id)->where('is_quiz_active',1)->get('quizes')->result_array();
        $quiz_ids = array();
        if($quiz_array)
        {
            $quiz_ids = array_column($quiz_array,"id");
        }
        return $quiz_ids;
    }


    function get_questions_ids_by_quiz_ids($quiz_id_array) 
    {
        $questions_array = $this->db->where_in('quiz_id', $quiz_id_array)->get('questions')->result_array();
        $questions_ids = array();
        if($questions_array)
        {
            $questions_ids = array_column($questions_array,"id");
        }
        return $questions_ids;
    }


    function get_participants_ids_by_quiz_ids($quiz_id_array) 
    {
        $participants_array = $this->db->where_in('quiz_id', $quiz_id_array)->get('participants')->result_array();
        $participants_ids = array();
        if($participants_array)
        {
            $participants_ids = array_column($participants_array,"id");
        }
        return $participants_ids;
    }


    function get_reviews_ids_by_quiz_ids($quiz_id_array) 
    {
        $reviews_array = $this->db->where_in('rel_id', $quiz_id_array)->where('rel_type',"quiz")->get('quiz_reviews')->result_array();
        $reviews_ids = array();
        if($reviews_array)
        {
            $reviews_ids = array_column($reviews_array,"id");
        }
        return $reviews_ids;
    }






function update_quiz_images_by_id($quiz_id, $updated_image_value) {
    $this->db->set('featured_image', $updated_image_value)->where('is_quiz_active',1)->where('id', $quiz_id)->update('quizes');
    return $this->db->affected_rows();
}

var $table_question = 'questions';
var $column_order_question = array(null, 'questions.correct_choice', 'questions.title', NULL);
var $column_search_question = array('questions.title', 'questions.correct_choice');
var $order_question = array('questions.id' => 'DESC');

private function _get_question_datatables_query() {
    $this->db->from($this->table_question);
    $this->db->join('quizes', 'quizes.id = questions.quiz_id', 'inner');
    //$this->db->where('is_quiz_active',1);
    $i = 0;
    foreach ($this->column_search_question as $item) {
        // if datatable send POST for search
        if ($_POST['search']['value']) {

            // first loop
            if ($i === 0) {

                // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.
                $this->db->group_start();

                $this->db->like($item, $_POST['search']['value']);
            } else {
                $this->db->or_like($item, $_POST['search']['value']);
            }
            // last loop
            if (count($this->column_search_question) - 1 == $i) {
                // close bracket
                $this->db->group_end();
            }
        }
        $i++;
    }
    // here order processing
    if (isset($_POST['order'])) {
        $this->db->order_by($this->column_order_question[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
    } else if (isset($this->column_order_question)) {
        $order = $this->column_order_question;
        $this->db->order_by(key($order), $order[key($order) ]);
    }
}

function count_filtered_question($quiz_id) {
    $this->_get_question_datatables_query();
    $query = $this->db->where('quiz_id',$quiz_id)->get();
    return $query->num_rows();
}

function count_all_question($quiz_id) {
    $this->db->from($this->table_question)->where('quiz_id', $quiz_id);
    return $this->db->count_all_results();
}

function get_question($quiz_id) {
    $this->_get_question_datatables_query();
    if ($_POST['length'] != - 1) 
        $this->db->limit($_POST['length'], $_POST['start']);
    $query = $this->db->select('questions.*,quizes.title as quiz_name')->where('quiz_id',$quiz_id)
    ->order_by('questions.id', 'asc')
    ->get();
    return $query->result();
}

function update_helpsheet_field($quiz_id)
{
    $this->db->set('quiz_helpsheet',NULL)->where('id', $quiz_id)->update('quizes');
    return $this->db->affected_rows();
}

function get_question_by_quiz_id($quiz_id)
{
    return $this->db->where('quiz_id',$quiz_id)->get('questions')->result();
}

function question_name_like_this($id, $title) 
 {
    $this->db->like('title', $title);
    if ($id) 
    {
        $this->db->where('id !=', $id);
        $this->db->where('id <', $id);
    }
    return $this->db->count_all_results('questions');
}

function insert_quiz_copy_question_data($data)
{
   return $this->db->insert_batch('questions', $data); 
}

}