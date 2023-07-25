<?php defined('BASEPATH') OR exit('No direct script access allowed');
class CouponModel extends CI_Model {
	var $table = 'coupon';
    // set column field database for datatable orderable
    var $column_order = array(null, 'coupon_code', 'coupon_for','coupon_discount_type','expiry_date','status',null);
    // set column field database for datatable searchable
    var $column_search = array('coupon_code', 'coupon_for','coupon_discount_type','expiry_date','status');
    // default order
    var $order = array('id' => 'DESC');

	public function __construct() {
        parent::__construct();
        $this->load->database();
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

    function get_coupon() {
        $this->_get_datatables_query();
        if ($_POST['length'] != - 1) $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
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

    function get_coupon_for($table_name,$search_text)
    {   
        if($table_name == 'users')
        {
            $this->db->select('id, CONCAT(first_name, " ", last_name) AS title');
            $this->db->where("first_name LIKE '%$search_text%' OR last_name LIKE '%$search_text%'");
        }
        else
        {
            $this->db->select('id,title');
            $this->db->where("title LIKE '%$search_text%'");
        }
       return $this->db->get($table_name)->result();
    }

    function get_all_cateogry()
    {
        return $this->db->select('id,category_title')->get('category')->result();
    }

    function insert_coupon($data)
    {
        $this->db->insert('coupon',$data);
        return $this->db->insert_id();
    }

    function get_coupon_by_id($id)
    {
        return $this->db->select('coupon.*,(select count(id) from payments where coupon_id = '.$id.') as total_used_coupon')->where('id',$id)->get('coupon')->row();
    }

    function update_coupon($data,$id)
    {
        $this->db->where('id',$id)->update('coupon',$data);
        return $this->db->affected_rows();
    }

    function delete_coupon($id)
    {
        $this->db->where('id',$id)->delete('coupon');
        return $this->db->affected_rows();
    }

    function insert_coupon_related_data($data)
    {
        $this->db->insert('coupon_relation_data',$data);
        return $this->db->insert_id();
    }

    function check_coupon_data($coupon_id)
    {
       return $this->db->where('coupon_id',$coupon_id)->get('coupon_relation_data')->result();
    }

    function delete_related($coupon_id)
    {
        $this->db->where_in('coupon_id',$coupon_id)->delete('coupon_relation_data');
    }

    function update_coupon_related($coupon_id,$data)
    {
        $this->db->where('id',$coupon_id)->update('coupon',$data);
        return $this->db->affected_rows();
    }

    function get_coupon_related_data($coupon_id,$coupon_for)
    {
      
        if($coupon_for == 'users')
        {
            $this->db->select('id, CONCAT(first_name, " ", last_name) AS title');
        }
        else
        {
            $this->db->select('id,title');
        }   
            
        $this->db->join($coupon_for,$coupon_for.'.id = coupon_relation_data.relation_data_id','left');
       return $this->db->where('coupon_id',$coupon_id)->get('coupon_relation_data')->result();
    }

    var $track_table = 'payments';
    // set column field database for datatable orderable
    var $track_column_order = array(null, 'username', 'item_name', 'discount_value', null);
    // set column field database for datatable searchable
    var $track_column_search = array('username', 'item_name', 'item_price');
    // default order
    var $track_order = array('payments.id' => 'DESC');

    private function _get_track_coupon_datatables_query() {

        $this->db->from($this->track_table);
        // $this->db->join('quizes', 'quizes.id = payments.quiz_id', 'left');
        $this->db->join('users', 'users.id = payments.user_id', 'left');
        $i = 0;
        foreach ($this->track_column_search as $item) {

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
                if (count($this->track_column_search) - 1 == $i) {
                    // close bracket
                    $this->db->group_end();
                }
            }
            $i++;
        }
        // here order processing
        if (isset($_POST['order'])) {
            $this->db->order_by($this->track_column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } else if (isset($this->track_order)) {
            $order = $this->track_order;
            $this->db->order_by(key($order), $order[key($order) ]);
        }
    }

    function get_coupon_user_wise_data($coupon_id) {
        $this->_get_track_coupon_datatables_query(); 
        $user_id = isset($this->user['id']) ? $this->user['id'] : NULL; 
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('discount_value,item_name,item_price,first_name,last_name,coupon_id,(select COUNT(id) from payments where coupon_id = "'.$coupon_id.'") as no_of_use')
        ->where('coupon_id',$coupon_id)
        ->order_by('payments.id', 'desc')
        ->get();
        
        return $query->result();
        
    }
    
    function count_filtered_track($coupon_id) {

        $this->_get_track_coupon_datatables_query();
        $query = $this->db->where('coupon_id',$coupon_id)->get();
        
        return $query->num_rows();
    }
    
    public function count_all_track($coupon_id) {
        $this->db->from($this->track_table)->where('coupon_id',$coupon_id);
        return $this->db->count_all_results(); 
    }
}