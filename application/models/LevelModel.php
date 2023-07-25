<?php defined('BASEPATH') OR exit('No direct script access allowed');
class LevelModel extends CI_Model {
	 
	function insert_level($data) 
    {
        $this->db->insert('levels', $data);
        return $this->db->insert_id();
    }

    var $table = 'levels';
    var $column_order = array(null, 'title', 'min_points', 'level_order', NULL);
    var $column_search = array('title', 'min_points','level_order');
    var $order = array('levels.id' => 'DESC');

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

    function get_level() {
        $this->_get_datatables_query();  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('levels.*')
        ->order_by('levels.id', 'desc')
        ->get();
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

    function get_level_by_id($level_id)
    {
    	return $this->db->where('id',$level_id)->get('levels')->row();
    }

    function update_level($level_id,$data)
    {
    	$this->db->set($data)->where('id', $level_id)->update('levels');
    	return $this->db->affected_rows();
    }

    function get_image_by_level_id($level_id)
	{
		return $this->db->select('badge')->where('id',$level_id)->get('levels')->row();
	}

	function delete_level($level_id) 
	{
	    $this->db->where('id', $level_id)->delete('levels');
	    return $this->db->affected_rows();
	}
}