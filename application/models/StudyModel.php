<?php defined('BASEPATH') OR exit('No direct script access allowed');
class StudyModel extends CI_Model {
	
	function get_all_category() {
        return $this->db->where('category_is_delete',0)->where('category_status',1)->order_by('category_title','asc')->get('category')->result();
    }

    function get_all_study_material() 
    {
        return $this->db->where('status',1)->order_by('id','asc')->get('study_material')->result();
    }

    function get_all_users() {
        return $this->db->where('deleted','0')->where('status','1')->where('role !=','user')->order_by('first_name','asc')->get('users')->result();
    }

    function insert_study_material($data)
    {
    	$this->db->insert('study_material',$data);
    	return $this->db->insert_id();
    }

    var $table = 'study_material';
    var $column_order = array(null, 'title', 'category_title', NULL);
    var $column_search = array('title', 'category_title');
    var $order = array('study_material.id' => 'DESC');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query($login_user_id) 
    {
        $this->db->from($this->table);
        if($login_user_id)
        {
            $this->db->where('user_id',$login_user_id);   
        }
        $this->db->join('category', 'study_material.category_id = category.id', 'left');
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

    function get_study_material($login_user_id = NULL) 
    {
        $this->_get_datatables_query($login_user_id);  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('study_material.*,category_title')
        ->order_by('study_material.id', 'desc')
        ->get();
        return $query->result();
        
    }
    
    function count_filtered($login_user_id = NULL) {
        $this->_get_datatables_query($login_user_id);
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function count_all($login_user_id = NULL) {
        $this->db->from($this->table);
        if($login_user_id)
        {
            $this->db->where('user_id',$login_user_id);   
        }
        return $this->db->count_all_results();
    }

    function get_study_material_by_id($study_material_id, $login_user_id = NULL)
    {
    	$this->db->where('id',$study_material_id);
        if($login_user_id)
        {
            $this->db->where('user_id',$login_user_id); 
        }
        return $this->db->get('study_material')->row();
    }


    function get_study_material_section_by_study_material_id($study_material_id)
    {
        $this->db->select('study_section.*,
            (select SUM(duration) from study_material_content where study_material_content.section_id = study_section.id ) as total_duration'
        );

        return $this->db->where('study_material_id',$study_material_id)->order_by('order','asc')->get('study_section')->result();
    }

    function get_user_completed_s_m_contant($study_material_id,$user_id)
    {
        return $this->db->where('s_m_id',$study_material_id)->where('user_id',$user_id)->order_by('id','asc')->get('study_material_user_history')->result_array();
    }
    function get_user_completed_s_m_section_contant($study_material_id,$section_id,$user_id)
    {
        return $this->db->where('s_m_id',$study_material_id)
        ->where('user_id',$user_id)
        ->where('s_m_section_id',$section_id)
        ->order_by('id','asc')->get('study_material_user_history')->result_array();
    }

    function get_study_material_and_file_by_id($study_material_id)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
        return $this->db->select('study_material.*,
            (select count(id) from study_material_content where study_material_id = study_material.id)as total_file,
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = "pdf") as total_pdf, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND (type = "video" OR type = "vimeo-embed-code" OR type = "youtube-embed-code")) as total_video, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = "audio") as total_audio, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = "doc") as total_doc, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = "image") as total_images, 
            (select count(id) from study_material_content where study_material_content.study_material_id = study_material.id AND type = "content") as total_other, 

            (SELECT id FROM study_material_like where study_material_id = study_material.id AND user_id = '.$user_id.') as like_id,
            (select count(id) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status =  1 AND quiz_reviews.rel_type = "material") as rating,
            (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = study_material.id AND quiz_reviews.status = 1 AND quiz_reviews.rel_type = "material") as total_rating,
            (select count(id) from study_material_view where study_material_view.study_material_id = study_material.id ) as total_view,
            (select count(id) from study_material_like where study_material_like.study_material_id = study_material.id ) as total_like

            ')
        ->where('study_material.status',1)
        ->where('id',$study_material_id)
        ->get('study_material')->row();
    }

    function update_study_material($study_material_id, $data) 
	{
	    $this->db->set($data)->where('id', $study_material_id)->update('study_material');
	    return $this->db->affected_rows();
	}

	function study_material_name_like_this($id, $title) 
	{
	    $this->db->like('title', $title);
	    if ($id) 
	    {
	        $this->db->where('id !=', $id);
	        $this->db->where('id <', $id);
	    }
	    return $this->db->count_all_results('study_material');
	}

	function delete_study_material($study_material_id,$login_user_id = NULL)
	{
		$this->db->where('id', $study_material_id);
        if($login_user_id)
        {
           $this->db->where('user_id',$login_user_id); 
        }
        $this->db->delete('study_material');
    	$return = $this->db->affected_rows();
        if($return) 
        {
            $material_id_array = array($study_material_id);
            $this->delete_study_related_data($material_id_array);
        }
        return $return;
	}

    function delete_study_related_data($material_id_array)
    {
        if($material_id_array)
        {
            $this->db->where_in('study_material_id', $material_id_array)->delete('package_study_material'); 

            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_content'); 
            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_like'); 
            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_view'); 

        }

        return true;

    }










	function insert_study_material_content($data)
	{
		$this->db->insert('study_material_content',$data);
    	return $this->db->insert_id();		
	}

	var $table_content = 'study_material_content';
    var $column_order_content = array(null, 'title', 'type', NULL);
    var $column_search_content = array('title', 'type');
    var $order_content = array('study_material_content.id' => 'DESC');

    private function _get_datatables_query_content() {
        $this->db->from($this->table_content);
        $i = 0;
        foreach ($this->column_search_content as $item) {
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
                if (count($this->column_search_content) - 1 == $i) {
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
        // here order processing
        if (isset($_POST['order'])) {
            $this->db->order_by($this->column_order_content[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->order_content)) {
            $order = $this->order_content;
            $this->db->order_by(key($order), $order[key($order) ]);
        }
    }

    function get_study_material_content($study_material_id) {
        $this->_get_datatables_query_content();  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('study_material_content.*')
        ->where('study_material_id',$study_material_id)
        ->order_by('study_material_content.id', 'desc')
        ->get();
        return $query->result();
    }
    
    function count_filtered_content($study_material_id) {
        $this->_get_datatables_query_content();
        $query = $this->db->where('study_material_id',$study_material_id)->get();
        return $query->num_rows();
    }
    
    public function count_all_content($study_material_id) {
        $this->db->from($this->table_content)->where('study_material_id', $study_material_id);
        return $this->db->count_all_results();
    }

    function get_material_content_by_id($study_material_content_id)
    {
    	return $this->db->select('study_material_content.*')
    		   			->where('id',$study_material_content_id)
    		   			->get('study_material_content')->row();	 
    }

    function update_study_material_content($study_material_content_id,$data)
    {
    	$this->db->set($data)->where('id', $study_material_content_id)->update('study_material_content');
    	return $this->db->affected_rows();
    }

    function get_file_by_study_and_content_id($study_material_content_id)
    {
    	return $this->db->where('id',$study_material_content_id)->get('study_material_content')->row();
    }

    function delete_material_content($study_material_id,$study_material_content_id)
    {
    	$this->db->where('id', $study_material_content_id)->where('study_material_id',$study_material_id)->delete('study_material_content');
	    return $this->db->affected_rows();
    }

    function get_study_material_content_by_ids($study_material_id,$study_material_contant_id)
    {
        if($study_material_contant_id)
        {
            $this->db->where('id',$study_material_contant_id);
        }
        $this->db->where('study_material_id',$study_material_id);
        return $this->db->order_by('material_order','asc')->get('study_material_content')->row();
    }

    function get_next_study_material_content_by_ids($data,$section_id)    
    {
        $material_order = $data->material_order ? $data->material_order : 0;
        if($data->section_id == $section_id)
        {
            $this->db->where('material_order >',$material_order);
        }
        $this->db->where('study_material_id',$data->study_material_id);
        $this->db->where('section_id',$section_id);
        return $this->db->order_by('material_order','asc')->get('study_material_content')->row();
    }

    function get_study_material_content_by_id($study_material_id)
    {
        return $this->db->select('study_material_content.*')
                        ->where('study_material_content.study_material_id',$study_material_id)
                        ->order_by('material_order','asc')
                        ->get('study_material_content')
                        ->result();
    }

    function get_study_view($study_material_id,$ip_address,$date)
    {
        return $this->db->select('study_material_view.*')->where('study_material_id',$study_material_id)->where('ip_address',$ip_address)->where('DATE(added)',$date)->get('study_material_view')->row();
    }

    function save_study_view_data($data)
    {
        $this->db->insert('study_material_view',$data);

        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }

    function study_section_name_like_this($id, $title,$study_material_id) {
        $this->db->like('title', $title);          
        $this->db->where('study_material_id', $study_material_id);
        if ($id) 
        {
            $this->db->where('id !=', $id);
            $this->db->where('id <', $id);
        }
        return $this->db->count_all_results('study_section');
    }

    function get_all_quiz()
    {
        return $this->db->order_by('title','asc')->get('quizes')->result_array();
    }

    function insert_study_quiz($data)
    {
        $this->db->insert('study_quiz',$data);

        $insert_id = $this->db->insert_id();
        return  $insert_id;
    }

    function get_study_quiz($study_material_id)
    {
        return $this->db->where('study_material_id',$study_material_id)->get('study_quiz')->result();
    }

    function delete_study_quiz($study_material_id)
    {
        $this->db->where_in('study_material_id',$study_material_id)->delete('study_quiz');
    }

    function get_associate_quiz_by_material_id($study_material_id)
    {
        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
         return $this->db->select('quizes.*,(SELECT count(id) FROM quiz_count where quiz_id = quizes.id) as total_view,
                (SELECT id FROM quiz_like where quiz_id = quizes.id AND user_id = "'.$user_id.'" limit 1)as like_id,
                (SELECT count(id) FROM quiz_like where quiz_id = quizes.id) as total_like, 
                (select first_name from users where users.id = quizes.user_id  limit 1) as first_name , 
                (select last_name from users where users.id = quizes.user_id  limit 1) as last_name,
                (select count(id) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = "'.'quiz'.'") as rating,
                (select SUM(rating) from quiz_reviews where quiz_reviews.rel_id = quizes.id AND quiz_reviews.status =1 AND quiz_reviews.rel_type = "'.'quiz'.'") as total_rating
                ')
                ->join('quizes','quizes.id = study_quiz.quiz_id','left')
                ->where('study_material_id',$study_material_id)
                //->where('quizes.number_questions <= (select count(id) from questions where questions.quiz_id = quizes.id)')
                ->where('is_quiz_active',1)
                ->where(time().' BETWEEN start_date_time AND end_date_time')
                ->get('study_quiz')->result();
                  
    }
}