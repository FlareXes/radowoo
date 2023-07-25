<?php defined('BASEPATH') OR exit('No direct script access allowed');
class InstitutionModal extends CI_Model 
{
    var $table = 'institutions';
    var $column_order = array(null, 'title', NULL );
    var $column_search = array('title');
    var $order = array('id' => 'DESC');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query() {
        $this->db->from($this->table);
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

    function count_filtered() {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }

    function count_all() {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function insert_institution($data) 
    {
        $this->db->insert('institutions', $data);
        return $this->db->insert_id();
    }

    function insert_institution_courses($data) 
    {
        $this->db->insert('instute_courses', $data);
        return $this->db->insert_id();
    }

    function institution_name_like_this($title) 
    {
        $this->db->like('title', $title);
        return $this->db->count_all_results('institutions');
    }

    function institution_slug_like_this($slug,$id) 
    {
        $this->db->like('slug', $slug);
        if($id)
        {
            $this->db->where('id !=', $id);
        }
        $count = $this->db->count_all_results('institutions');
        return $count > 0 ? "-$count" : '';
    }
    
    function get_institution() {
        $this->_get_datatables_query();
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('institutions.*')
        ->get();
        return $query->result();
    }

    function get_institution_by_id($institution_id)
    {
        return $this->db->where('id',$institution_id)->get('institutions')->row();
    }

    function get_all_courses()
    {
        return $this->db->get('courses')->result();
    }

    function get_all_institutions()
    {
        return $this->db->get('institutions')->result();
    }

    function get_all_institutions_by_ids($institutions_ids)
    {
        return $this->db->where_in('id',$institutions_ids)->get('institutions')->result();
    }

    function get_all_courses_by_ids($courses_ids)
    {
        return $this->db->where_in('id',$courses_ids)->get('courses')->result();
    }

    function get_courses_by_institution_id($institution_id)
    {
        $result_array =  $this->db->where('instute_id',$institution_id)->get('instute_courses')->result_array();
        $ids_array = array();
        if($result_array)
        {
        	$ids_array = array_column($result_array,"course_id");
        }
        return $ids_array;
    }


    function get_institutions_by_courses_id($course_id)
    {
        $result_array =  $this->db->where('course_id',$course_id)->get('instute_courses')->result_array();
        $ids_array = array();
        if($result_array)
        {
            $ids_array = array_column($result_array,"instute_id");
        }
        return $ids_array;
    }


    function delete_courses_by_institution_id($institution_id)
    {
        return $this->db->where('instute_id',$institution_id)->delete('instute_courses');
    }

    function get_institution_by_slug($page_slug)
    {
        
        return $this->db->where('slug',$page_slug)->get('institutions')->row();
    }

    function update_institution($pages_id, $data) 
    {
        $this->db->set($data)->where('id', $pages_id)->update('institutions');
        return $this->db->affected_rows();
    }

    function delete_institution($institution_id) 
    {
        $this->db->where('id', $institution_id)->delete('institutions');
        $return = $this->db->affected_rows();
        $this->db->where('instute_id', $institution_id)->delete('instute_courses');
        return $return;
    }

}
