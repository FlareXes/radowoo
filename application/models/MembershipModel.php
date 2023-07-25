<?php defined('BASEPATH') OR exit('No direct script access allowed');
class MembershipModel extends CI_Model {
 
    function insert_membership($data) 
    {
        $this->db->insert('membership', $data);
        return $this->db->insert_id();
    }

    var $table = 'membership';
    var $column_order = array(null, 'title', 'duration', 'amount', NULL);
    var $column_search = array('title', 'duration','amount');
    var $order = array('membership.id' => 'DESC');

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

    function get_membership() {
        $this->_get_datatables_query();  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('membership.*')->get();
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

    function get_membership_by_id($membership_id)
    {
        return $this->db->where('id',$membership_id)->get('membership')->row();
    }

    function update_membership($membership_id,$data)
    {
        $this->db->set($data)->where('id', $membership_id)->update('membership');
        return $this->db->affected_rows();
    }

    function delete_membership($membership_id)
    {
        $this->db->where('id', $membership_id)->delete('membership');
        return $this->db->affected_rows();
    }

    function get_membership_data()
    {
        
        return $this->db->select('membership.*, (select category_title from category where category.id = membership.category_id) as category_name')->get('membership')->result();
    }

    function get_user_membership($user_id)
    {
        $this->db->select('user_membership_payment.*, (select category_title from category where category.id = user_membership_payment.category_id) as category_name');
        $this->db->where('category_id',0);
        $this->db->where('user_id',$user_id);
        $this->db->where('validity >=',date("Y-m-d"));
        $this->db->order_by('validity', 'desc');
        return $this->db->get('user_membership_payment')->row();
    }
}