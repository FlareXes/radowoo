<?php defined('BASEPATH') OR exit('No direct script access allowed');
class GradingModel extends CI_Model 
{

    var $table = 'quiz_grading';
    var $column_order = array(null, 'title' , NULL);
    var $column_search = array('title');
    var $order = array('quiz_grading.id' => 'DESC');

    public function __construct() {
        parent::__construct();
        $this->load->database();
    }

    private function _get_datatables_query() 
    {
        $this->db->from($this->table);

        $i = 0;
        foreach ($this->column_search as $item) 
        {
            if ($_POST['search']['value']) 
            {
                if ($i === 0) 
                {
                    $this->db->group_start();
                    $this->db->like($item, $_POST['search']['value']);
                } 
                else 
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
                if (count($this->column_search) - 1 == $i) 
                {
                    $this->db->group_end();
                }
            }
            $i++;
        }
       
        if (isset($_POST['order'])) 
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if (isset($this->order)) 
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order) ]);
        }
    }

    function get_grading() 
    {
        $this->_get_datatables_query();  
        if ($_POST['length'] != - 1) 
        {
            $this->db->limit($_POST['length'], $_POST['start']);
        }
        $query = $this->db->order_by('quiz_grading.id', 'desc')->get();
        return $query->result(); 
    }
    
    function count_filtered() 
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
    
    public function count_all() 
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }

    function insert_grading($data) 
    {
        $this->db->insert('quiz_grading', $data);
        return $this->db->insert_id();
    }

    function get_grading_by_id($grading_id)
    {
        return $this->db->where('id',$grading_id)->get('quiz_grading')->row();
    }

    function update_grading_by_id($grading_id, $data) 
    {
        $this->db->set($data)->where('id', $grading_id)
        ->update('quiz_grading');
        return $this->db->affected_rows();
    }


    function delete_grading_by_id($grading_id) 
    {
        $this->db->where('id', $grading_id)->delete('quiz_grading');
        return $this->db->affected_rows();
    }

    

}