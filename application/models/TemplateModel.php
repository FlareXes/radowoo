<?php defined('BASEPATH') OR exit('No direct script access allowed');
class TemplateModel extends CI_Model {
	
	var $table = 'email_template';
    var $column_order = array(null, 'subject',NULL);
    var $column_search = array('subject');
    var $order = array('email_template.id' => 'DESC');	

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

    function get_email_template() {
        $this->_get_datatables_query();  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('email_template.*')->get();
        //p($this->db->last_query($query));
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

    function get_email_template_by_id($email_template_id)
    {
    	return $this->db->where('id',$email_template_id)->get('email_template')->row();
    }

    function update_email_template($email_template_id,$data)
    {
    	$this->db->set($data)->where('id', $email_template_id)->update('email_template');
    	return $this->db->affected_rows();
    }
}