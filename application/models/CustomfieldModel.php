<?php defined('BASEPATH') OR exit('No direct script access allowed');
class CustomfieldModel extends CI_Model 
{
    var $table = 'custom_fields';
    var $column_order = array(null, 'title', 'slug', NULL );
    var $column_search = array('title', 'slug');
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

    function insert_custom_fields($data) 
    {
        $this->db->insert('custom_fields', $data);
        return $this->db->insert_id();
    }

    function custom_fields_name_like_this($title) 
    {
        $this->db->like('title', $title);
        return $this->db->count_all_results('custom_fields');
    }

    function page_slug_like_this($slug,$id) 
    {
        $this->db->like('slug', $slug);
        if($id)
        {
            $this->db->where('id !=', $id);
        }
        $count = $this->db->count_all_results('custom_fields');
        return $count > 0 ? "-$count" : '';
    }
    
    function get_fields() {
        $this->_get_datatables_query();
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('custom_fields.*')
        ->get();
        return $query->result();
    }

    function get_custom_fields_by_id($c_f_id)
    {
        return $this->db->where('id',$c_f_id)->get('custom_fields')->row();
    }

    function get_custom_fields_by_slug($page_slug)
    {
        
        return $this->db->where('slug',$page_slug)->get('custom_fields')->row();
    }

    function update_custom_fields($custom_fields_id, $data) 
    {
        $this->db->set($data)->where('id', $custom_fields_id)->update('custom_fields');
        return $this->db->affected_rows();
    }

    function delete_customfield($c_f_id) 
    {
        $this->db->where('id', $c_f_id)->delete('custom_fields');
        $return = $this->db->affected_rows();
        if($return)
        {
            $this->db->where('rel_id', $c_f_id)->delete('custom_field_values'); 
        }
        return $return;
    }

}
