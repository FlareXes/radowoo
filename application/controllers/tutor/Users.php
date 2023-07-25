<?php defined('BASEPATH') OR exit('No direct script access allowed');
class Users extends Tutor_Controller 
{
    /**
     * @var string
     */
    private $_redirect_url;
    /**
     * Constructor
     */
    function __construct() {
        parent::__construct();
        $this->load->model('UsersModel');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('tutor/users'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "last_name");
        define('DEFAULT_DIR', "asc");
        // use the url in session (if available) to return to the previous filter/sorted/paginated list
        if ($this->session->userdata(REFERRER)) {
            $this->_redirect_url = $this->session->userdata(REFERRER);
        } else {
            $this->_redirect_url = THIS_URL;
        }
        $this->add_js_theme('users_i18n.js', false);
    }
    /**************************************************************************************
     * PUBLIC FUNCTIONS
     **************************************************************************************/
    /**
     * User list page
     */
    function index() {

        if($this->settings->tutor_can_see_user_list != 'YES')
        {
            $this->session->set_flashdata('error', lang('you_dont_have_access'));
            return redirect(base_url('tutor/'));
        }
        // get parameters
        $limit = $this->input->get('limit') ? $this->input->get('limit', TRUE) : DEFAULT_LIMIT;
        $offset = $this->input->get('offset') ? $this->input->get('offset', TRUE) : DEFAULT_OFFSET;
        $sort = $this->input->get('sort') ? $this->input->get('sort', TRUE) : DEFAULT_SORT;
        $dir = $this->input->get('dir') ? $this->input->get('dir', TRUE) : DEFAULT_DIR;
        // get filters
        $filters = array();
        if ($this->input->get('username')) {
            $filters['username'] = $this->input->get('username', TRUE);
        }
        if ($this->input->get('first_name')) {
            $filters['first_name'] = $this->input->get('first_name', TRUE);
        }
        if ($this->input->get('last_name')) {
            $filters['last_name'] = $this->input->get('last_name', TRUE);
        }
        // build filter string
        $filter = "";
        foreach ($filters as $key => $value) {
            $filter.= "&{$key}={$value}";
        }
        // save the current url to session for returning
        $this->session->set_userdata(REFERRER, THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
        // are filters being submitted?
        if ($this->input->post()) {
            if ($this->input->post('clear')) {
                // reset button clicked
                redirect(THIS_URL);
            } else {
                // apply the filter(s)
                $filter = "";
                if ($this->input->post('username')) {
                    $filter.= "&username=" . $this->input->post('username', TRUE);
                }
                if ($this->input->post('first_name')) {
                    $filter.= "&first_name=" . $this->input->post('first_name', TRUE);
                }
                if ($this->input->post('last_name')) {
                    $filter.= "&last_name=" . $this->input->post('last_name', TRUE);
                }
                // redirect using new filter(s)
                redirect(THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}&offset={$offset}{$filter}");
            }
        }
        // get list
        $user_ids = array();

        $quiz_ids = $this->get_quiz_id_by_user_id($this->user['id']);
        $quiz_user_ids = $this->get_quiz_partipcent_user_ids($quiz_ids);

        $study_material_ids = $this->get_study_material_ids_by_user_id($this->user['id']);
        $study_material_user_ids = $this->get_study_material_user_histry_user_ids($study_material_ids);
        $user_ids = array_merge($quiz_user_ids,$study_material_user_ids);

        $users = $this->UsersModel->get_all_tutor_students($limit, $offset, $filters, $sort, $dir,$user_ids);

        // build pagination
        $this->pagination->initialize(array('base_url' => THIS_URL . "?sort={$sort}&dir={$dir}&limit={$limit}{$filter}", 'total_rows' => $users['total'], 'per_page' => $limit));
        // setup page header data
        $this->set_title(lang('users_list'));
        $data = $this->includes;
        // set content data
        $content_data = array('this_url' => THIS_URL, 'users' => $users['results'], 'total' => $users['total'], 'filters' => $filters, 'filter' => $filter, 'pagination' => $this->pagination->create_links(), 'limit' => $limit, 'offset' => $offset, 'sort' => $sort, 'dir' => $dir);
        // load views
        $data['content'] = $this->load->view('tutor/users/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }



    function get_quiz_id_by_user_id($id) 
    {
        $quiz_array = $this->db->where('user_id', $id)->get('quizes')->result_array();
        $quiz_ids = array();
        if($quiz_array)
        {
            $quiz_ids = array_column($quiz_array,"id");
        }
        return $quiz_ids;
    }

    function get_study_material_ids_by_user_id($id) 
    {
        $study_material_array = $this->db->where('user_id', $id)->get('study_material')->result_array();
        $study_material_ids = array();
        if($study_material_array)
        {
            $study_material_ids = array_column($study_material_array,"id");
        }
        return $study_material_ids;
    }

    function get_quiz_partipcent_user_ids($quiz_ids) 
    {
        $record_array = array();
        if($quiz_ids)
        {
            $record_array = $this->db->where_in('quiz_id', $quiz_ids)->get('participants')->result_array();
        }

        $user_ids = array();

        if($record_array)
        {
            foreach ($record_array as $value_Arr) 
            {
                $user_ids[$value_Arr['user_id']] = $value_Arr['user_id'];
            }
            // $user_ids = array_column($record_array,"user_id");
        }
        return $user_ids;
    }

    function get_study_material_user_histry_user_ids($study_material_ids) 
    {
        $record_array = array();
        if($study_material_ids)
        {
            $record_array = $this->db->where_in('study_material_id', $study_material_ids)->get('study_material_user_histry')->result_array();
        }

        $user_ids = array();

        if($record_array)
        {
            foreach ($record_array as $value_Arr) 
            {
                $user_ids[$value_Arr['user_id']] = $value_Arr['user_id'];
            }
            // $user_ids = array_column($record_array,"user_id");
        }
        return $user_ids;
    }

    function get_study_material_by_user($id)
    {
        $data_array = $this->db->where('user_id', $id)->get('study_material')->result_array();
        $material_id_array = array();
        if($data_array)
        {
            $material_id_array = array_column($data_array,"id");
        }
        return $material_id_array;
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

    function delete_study_related_data($material_id_array)
    {
        if($material_id_array)
        {
            $this->db->where_in('study_material_id', $material_id_array)->delete('package_study_material'); 

            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_content'); 
            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_like'); 
            $this->db->where_in('study_material_id', $material_id_array)->delete('study_material_view'); 

        }
        return TRUE;
    }



    function user_quiz_history($user_id)
    {
        $this->set_title(lang('users_quiz_history_list'));
        $data = $this->includes;
        // set content data
        $content_data = array('this_url' => THIS_URL,'user_id' => $user_id,);
        // load views
        $data['content'] = $this->load->view('tutor/users/user_quiz_list', $content_data, TRUE);
        $this->load->view($this->template, $data);        
    }

    function user_quiz_history_list($user_id = false)
    {
        $data = array();
        $list = $this->UsersModel->get_quiz_history($user_id); 

        $no = $_POST['start'];
        foreach ($list as $quiz_history) {
            $no++;
            $row = array();
            $row[] = xss_clean($quiz_history->quiz_title);
            $row[] = json_decode($quiz_history->questions);
            $total_attemp = $quiz_history->total_attemp - $quiz_history->correct;
            $total_attemp = $total_attemp ? $total_attemp : 0;
            $row[] = $total_attemp; 
            $row[] = $quiz_history->correct;
            $wrong = $total_attemp - $quiz_history->correct; 
            $row[] = $wrong;                
            $date_of_quiz = date( "d M Y", strtotime($quiz_history->started));
            $row[] = $date_of_quiz;                
            $button = '<a href="' . base_url("tutor/users/summary/". $quiz_history->id) . '" data-toggle="tooltip"  title="'.lang("admin_edit_record").'" class="btn btn-primary btn-action mr-1" title="'.lang("quiz_summary").'"><i class="fa fa-eye"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'], 
            "recordsTotal" => $this->UsersModel->count_all($user_id), 
            "recordsFiltered" => $this->UsersModel->count_filtered($user_id), 
            "data" => $data
        );

        //output to json format
        echo json_encode($output);        
    }

    function summary($participant_id = NULL)
    {

        $participant_data = $this->UsersModel->get_participant_by_id($participant_id);

        if(empty($participant_data))
        {
            return redirect(base_url('tutor/quiz'));
        }

        $this->load->model('TestModel');
        $user_question_data = $this->TestModel->get_user_question_by_participant_id($participant_id);

        
        if(empty($user_question_data))
        {
            $this->session->set_flashdata('error',lang('Test Not complet'));
            return redirect(base_url('tutor/users/user_quiz_history/'.$participant_data['user_id']));
        }

        $quiz_id = $participant_data['quiz_id'];
        $quiz_data = $this->UsersModel->get_quiz_by_id($quiz_id);

        if($quiz_data->user_id != $this->user['id'])
        {
            $this->session->set_flashdata('error', lang('you_dont_access_to_see_this_result'));
            return redirect(base_url('tutor/report/'.$quiz_data->id));
        }
        
        $correct = $participant_data['correct'] ? $participant_data['correct'] : 0;
        $total_question = $participant_data['questions'] ? $participant_data['questions'] : 0;
        $total_attemp = $participant_data['total_attemp'] ? $participant_data['total_attemp'] : 0;

        $this->set_title(lang('test_result').": ".$quiz_data->title);
        $this->add_external_js(base_url("/{$this->settings->themes_folder}/quizzy/js/Chart.min.js"));   
        $this->add_external_js(base_url("/{$this->settings->themes_folder}/quizzy/js/autogrow.js"));
        $data = $this->includes;
        $content_data = array('Page_message' => lang('test_result'), 'page_title' => lang('test_result'),'correct' => $correct,'total_question' => $total_question, 'total_attemp' => $total_attemp, 'quiz_data' => $quiz_data, 'participant_data' => $participant_data, 'user_question_data' => $user_question_data );
        $data['content'] = $this->load->view('result', $content_data, TRUE);
        $this->load->view($this->template, $data);      
    }
}
