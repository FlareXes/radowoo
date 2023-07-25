<?php defined('BASEPATH') OR exit('No direct script access allowed');
class CategoryModel extends CI_Model {
    var $table = 'category';
    // set column field database for datatable orderable
    var $column_order = array(null, 'category_title', NULL,null, null, 'category_status', null);
    // set column field database for datatable searchable
    var $column_search = array('category_title', 'category_description', 'category_status', 'category_added');
    // default order
    var $order = array('id' => 'DESC');
    public function __construct() {
        parent::__construct();
        $this->load->database();
    }
    function insert($insert) {
        $this->db->insert('category', $insert);
    }
    function allcategory($id = NULL) {
        $query = $this->db->select('*');
        if ($id) {
            $query->where('id !=', $id);
        }
        $result = $query->order_by('category.order','asc')->get('category')->result_array();
        return $result;
    }
    private function _get_datatables_query() {
        $this->db->from($this->table);
        $i = 0;
        // loop column
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
    function get_category() {
        $this->_get_datatables_query();
        if ($_POST['length'] != - 1) $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->order_by('category.order','asc')->get();
        return $query->result();
    }
    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    public function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }
    function updatestatus($id, $status) {
        $this->db->set('category_status', $status)->where('id', $id)->update('category');
    }
    function getfetch($id) {
        return $this->db->where('id', $id)->get('category')->row_array();
    }
    function getImage($id) {
        return $this->db->where('id', $id)->get('category')->row('category_image');
    }
    function update($data, $id) {
        $this->db->where('id', $id)->update('category', $data);
    }
    function deleteimage($id) {
        return $this->db->where('id', $id)->get('category')->row('category_image');
    }
    

    function delete($id) 
    {
        $this->db->where('id', $id)->delete('category');
        $quiz_id_array = $this->get_quiz_id_by_category_id($id);

        if($quiz_id_array)
        {
            $this->db->where_in('id', $quiz_id_array)->delete('quizes');
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
    }

    function get_quiz_id_by_category_id($id) 
    {
        $quiz_array = $this->db->where('category_id', $id)->where('is_quiz_active',1)->get('quizes')->result_array();
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


    function customfield_id($id) {
        $result = $this->db->select('custom_field_id')->where('category_id', $id)->get('category_custom_field')->result_array();
        return $result;
    }
    function getLabel($customField_id) {
        $result = $this->db->select('id,custom_label')->where_not_in('id', $customField_id)->get('custom_field')->result_array();
        return $result;
    }
    function fetchcategory($id) {
        $result = $this->db->select('category_id,custom_field_id')->where('category_id', $id)->get('category_custom_field')->result_array();
        return $result;
    }
    function fetchcategory_name($id) {
        $result = $this->db->select('custom_label,id as custom_field_id')->where_in('id', $id)->get('custom_field')->result_array();
        return $result;
    }
    function delete_label($id) {
        $result = $this->db->where_in('category_id', $id)->delete('category_custom_field');
    }
    function category_custom($data) {
        $this->db->insert('category_custom_field', $data);
    }
    function category_name_like_this($id, $title) {
        $this->db->like('category_title', $title);
        if ($id) {
            $this->db->where('id !=', $id);
            $this->db->where('id <', $id);
        }
        return $this->db->count_all_results('category');
    }
    function get_category_custom_fields($category_id)
    {
        $result = $this->db
        ->select("id, custom_label, CCF.category_custom_order")
        ->join("category_custom_field CCF", "CCF.custom_field_id = CF.id AND CCF.category_id = $category_id", "LEFT")
        ->order_by("CCF.category_custom_order",  "DESC")
        ->get("custom_field CF")
        ->result_array();
        return $result;
    }



    public function get_categories(){

        $this->db->select('*');
        $this->db->from('category');
        $this->db->where('parent_category', 0);

        $parent = $this->db->order_by('category.order','asc')->get();
        
        $categories = $parent->result();
        $i=0;
        foreach($categories as $p_cat){

            $categories[$i]->sub = $this->sub_categories($p_cat->id);
            $i++;
        }
        return $categories;
    }

    public function sub_categories($id){

        $this->db->select('*');
        $this->db->from('category');
        $this->db->where('parent_category', $id);

        $child = $this->db->order_by('category.order','asc')->get();
        $categories = $child->result();
        $i=0;
        foreach($categories as $p_cat){

            $categories[$i]->sub = $this->sub_categories($p_cat->id);
            $i++;
        }
        return $categories;       
    }





}
