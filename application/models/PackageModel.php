<?php defined('BASEPATH') OR exit('No direct script access allowed');
class PackageModel extends CI_Model {

	var $table = 'packages';
    var $column_order = array(null, 'title', 'price', NULL);
    var $column_search = array('title','price');
    var $order = array('packages.id' => 'DESC');

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

    function get_package_quiz() {
        $this->_get_datatables_query();  
        if ($_POST['length'] != - 1) 
            $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->select('packages.*')
        ->order_by('packages.id', 'desc')
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


	function get_all_quiz() {
        return $this->db->where('deleted',0)->where('is_quiz_active',1)->order_by('title','asc')->get('quizes')->result();
    }

    function insert_package($data) 
    {

        $this->db->insert('packages', $data);
        return $this->db->insert_id();

    }

    function get_package_by_id($package_id)
    {
    	return $this->db->select('packages.*')->where('id',$package_id)->get('packages')->row();	
    }

    function update_package($data,$package_id) 
	{
    	$this->db->set($data)->where('id', $package_id)->update('packages');
    	return $this->db->affected_rows();
	}

	function get_image_by_package_id($package_id)
	{
		return $this->db->select('image')->where('id',$package_id)->get('packages')->row();
	}

	function delete_package($package_id) 
	{
		// delete pakage relation from package_quizes table
        $this->db->where_in('package_id', $package_id)->delete('package_quizes');
        // delete package from packages table
	    $this->db->where('id', $package_id)->delete('packages');
	    return $this->db->affected_rows();
	}

	function get_all_category() {
        return $this->db->where('category_is_delete',0)->where('category_status',1)->order_by('category_title','asc')->get('category')->result();
    }

    function get_quiz_by_category_id($category_id,$package_id)
    {
    	$package = $this->db->where('package_id',$package_id)->get('package_quizes')->result_array();
    	$quiz_ids = array_column($package,"quiz_id");

    	$this->db->select('id,title');
    	if ($quiz_ids) 
    	{
    		$this->db->where_not_in('quizes.id',$quiz_ids);
    	}
    	return	$this->db->where('category_id',$category_id)->get('quizes')->result();
    		
    }

    function get_maximum_order_no($package_id)
    {
    	return $this->db->select_max('quiz_order')->where('package_id',$package_id)->get('package_quizes')->result();
    }

    function add_quiz_package_order($data)
    {
	    return $this->db->insert('package_quizes',$data);
    }

    function get_quiz_by_package_id($package_id)
    {
    	return $this->db->select('id,title,number_questions,price,duration_min,quiz_order')
    		   ->join('quizes','quizes.id = package_quizes.quiz_id','left')
    		   ->where('package_id',$package_id)
    		   ->order_by('quiz_order',"asc")
    		   ->get('package_quizes')->result();

    }

    function delete_quiz_package($package_id,$package_quiz_id)
    {
 		$this->db->where('package_id', $package_id)->where('quiz_id',$package_quiz_id)->delete('package_quizes');
	    return $this->db->affected_rows();   	
    }

    function delete_package_related_quiz($package_id)
    {
    	$this->db->where_in('package_id',$package_id)->delete('package_quizes');
    	return $this->db->affected_rows();		
    }

    function save_package_quiz_and_order($data)
    {
    	$insert = $this->db->insert_batch('package_quizes', $data);
	    if($insert > 0){
	       $status = TRUE;
	    }
		return $status;
    }

    function get_study_material_by_category_id($category_id,$package_id)
    {
    	$package = $this->db->where('package_id',$package_id)->get('package_study_material')->result_array();
    	$study_material_ids = array_column($package,"study_material_id");

    	$this->db->select('id,title');
    	if($study_material_ids)
    	{
    		$this->db->where_not_in('study_material.id',$study_material_ids);
    	}
    	return	$this->db->where('category_id',$category_id)->get('study_material')->result();
    }

    function get_maximum_order_no_in_study_material($package_id)
    {
    	return $this->db->select_max('study_material_order')->where('package_id',$package_id)->get('package_study_material')->result();
    }

    function add_study_material_package_order($data)
    {
 		return $this->db->insert('package_study_material',$data);   	
    }

    function get_study_by_package_id($package_id)
    {
 		return $this->db->select('study_material.id,study_material.title,price,study_material_order,(select count(id) from study_material_content where study_material_content.study_material_id = package_study_material.study_material_id)as no_of_files')
    		   ->join('study_material','study_material.id = package_study_material.study_material_id','left')
    		   
    		   ->where('package_id',$package_id)
    		   ->order_by('study_material_order',"asc")
    		   ->get('package_study_material')->result();   	
    }

    function delete_study_package($package_id,$package_study_id)
    {
 		$this->db->where('package_id', $package_id)->where('study_material_id',$package_study_id)->delete('package_study_material');
	    return $this->db->affected_rows();   	
    }

    function delete_package_related_study($package_id)
    {
    	$this->db->where_in('package_id',$package_id)->delete('package_study_material');
    	return $this->db->affected_rows();		
    }

    function save_package_study_and_order($data)
    {
 		$insert = $this->db->insert_batch('package_study_material', $data);
	    if($insert > 0){
	       $status = TRUE;
	    }
		return $status;   	
    }
}