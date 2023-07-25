<?php defined('BASEPATH') OR exit('No direct script access allowed');
class UsertutorModal extends CI_Model 
{
    var $table = 'users';
    var $column_order = array(null, 'first_name','email','username', 'status',NULL);
    var $column_search = array('first_name','last_name','email','username');
    var $order = array('id' => 'DESC');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query() 
    {
        $this->db->from($this->table);
        // $this->db->where('role','tutor');
        // $this->db->where('status !=','1')
        $this->db->or_where('user_request_for_tutor',1);
        $i = 0;
        foreach ($this->column_search as $item) 
        {
            // if datatable send POST for search
            if ($_POST['search']['value']) 
            {
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

    function get_user_tutor() 
    {
        $this->_get_datatables_query();
        if ($_POST['length'] != - 1)
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        } 
        $query = $this->db->select($this->table.'.*')->get();
        return $query->result();
    }

    function count_filtered() 
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    function count_all() 
    {
        $this->db->from($this->table);
        // $this->db->where('role','tutor');
        // $this->db->where('status !=','1');
        $this->db->or_where('user_request_for_tutor',1);
        return $this->db->count_all_results();
    }


}
