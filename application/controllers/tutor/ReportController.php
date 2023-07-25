<?php defined('BASEPATH') OR exit('No direct script access allowed');
class ReportController extends Tutor_Controller {
 
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css', 'admin');
        $this->add_css_theme('bootstrap4-toggle.min.css', 'admin');
        $this->add_js_theme('bootstrap4-toggle.min.js', FALSE, 'admin');
        $this->add_css_theme('sweetalert.css', 'admin');
        $this->add_js_theme('sweetalert-dev.js', FALSE, 'admin');
        $this->add_js_theme('Chart.min.js', FALSE, 'quizzy');
        $this->add_js_theme('autogrow.js', FALSE, 'quizzy');

        $this->add_js_theme('report.js');

        $this->load->model('ReportModel');
        $this->load->model('QuizModel');

        $this->load->helper('url');
        
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('tutor/quiz'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");
        $this->add_external_css(base_url('assets/themes/quizzy/css/table-main.css'));
    }    

    function index($quiz_id = NULL) 
    {
    	$quiz_data = $this->QuizModel->get_quiz_by_id($quiz_id);
        
        $this->set_title(lang('admin_report').": ".$quiz_data->title);
        $data = $this->includes;
        $content_data = array('quiz_id' => $quiz_id,);
        $data['content'] = $this->load->view('tutor/report/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function quiz_history_list()
    {	
    	$quiz_id = $_POST['quiz_id'];
    	$data = array();
        $list = $this->ReportModel->get_quiz_history($quiz_id); 
        
        $no = $_POST['start'];
        foreach ($list as $quiz_history) {

        	$total_attemp = $quiz_history->total_attemp;
            $total_attemp = $total_attemp ? $total_attemp : 0;
            
            $wrong = $total_attemp - $quiz_history->correct;
            

            $date_of_quiz = date( "d M Y h:i A", strtotime($quiz_history->started));

            $button = '<a href="' . base_url("tutor/report/summary/". $quiz_history->id) . '" data-toggle="tooltip"  title="'.lang("quiz_summary").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-eye"></i></a>
                <a href="' . base_url("tutor/report/delete/" .$quiz_id.'/'. $quiz_history->id) . '" data-toggle="tooltip"  title="'.lang("admin_delete_record").'" class="btn btn-danger btn-action mr-1 common_delete"><i class="fas fa-trash"></i></a>';
            $name = (isset($quiz_history->first_name) && !empty($quiz_history->first_name) ? $quiz_history->first_name ." ".$quiz_history->last_name : (isset($quiz_history->guest_name) && !empty($quiz_history->guest_name) ? $quiz_history->guest_name : "")); 

            $email = $quiz_history->user_email ? $quiz_history->user_email : "Guest";

            $marks_for_correct_answer = $quiz_history->marks_for_correct_answer;
            $marks_for_correct_answer = $marks_for_correct_answer > 0 ? $marks_for_correct_answer : 1;
            $total_marks = $quiz_history->questions * $marks_for_correct_answer;
            $negative_marking_percentage = isset($quiz_history->negative_marking_percentage) ? $quiz_history->negative_marking_percentage : 0;
            $incorrect_marks = round($negative_marking_percentage * $marks_for_correct_answer * $wrong / 100,2);
            $correct = $quiz_history->correct;
            $correct_marks = $correct * $marks_for_correct_answer;
            $marks_achived = $correct_marks - $incorrect_marks;
            $percent_marks = round(($marks_achived / $total_marks)*100,2);

            $no++;
            $row = array();
            $row[] = $no;
            $row[] = xss_clean($name);
            $row[] = xss_clean($email);
            $row[] = xss_clean($quiz_history->questions);
            $row[] = xss_clean($total_attemp); 
            $row[] = xss_clean($quiz_history->correct); 
            $row[] = xss_clean($wrong);
            $row[] = xss_clean($marks_achived .' ('.$percent_marks .'%)' );  
            $row[] = xss_clean($date_of_quiz); 
            $row[] = $button;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'], 
            "recordsTotal" => $this->ReportModel->count_all($quiz_id), 
            "recordsFiltered" => $this->ReportModel->count_filtered($quiz_id), 
            "data" => $data
        );

        //output to json format
        echo json_encode($output);
    }

    function summary($participant_id = NULL)
    {

    	$participant_data = $this->ReportModel->get_participant_by_id($participant_id);

        if(empty($participant_data))
        {
            return redirect(base_url('tutor/quiz'));
        }

        $this->load->model('TestModel');
        $user_question_data = $this->TestModel->get_user_question_by_participant_id($participant_id);

        
        if(empty($user_question_data))
        {
            $this->session->set_flashdata('error', lang('Test Not complete'));
            return redirect(base_url('tutor/users/user_quiz_history/'.$participant_data->user_id));
        }

        $quiz_id = $participant_data['quiz_id'];
        $quiz_data = $this->ReportModel->get_quiz_by_id($quiz_id);
        
        if($quiz_data->user_id != $this->user['id'])
        {
            $this->session->set_flashdata('error', lang('you_dont_access_to_see_this_result'));
            return redirect(base_url('tutor/report/'.$quiz_data->id));
        }
        

        $correct = $participant_data['correct'] ? $participant_data['correct'] : 0;
        $total_question = $participant_data['questions'] ? $participant_data['questions'] : 0;
        $total_attemp = $participant_data['total_attemp'] ? $participant_data['total_attemp'] : 0;

 		$this->set_title(lang('test_result').": ".$quiz_data->title);
        $data = $this->includes;
        $content_data = array('Page_message' => lang('test_result'), 'page_title' => lang('test_result'),'correct' => $correct,'total_question' => $total_question, 'total_attemp' => $total_attemp, 'quiz_data' => $quiz_data, 'participant_data' => $participant_data, 'user_question_data' => $user_question_data );
        $data['content'] = $this->load->view('result', $content_data, TRUE);
        $this->load->view($this->template, $data);   	
    }

    function delete($quiz_id = NULL,$participant_id = NULL)
    {

        action_not_permitted();
        $status = $this->ReportModel->delete_quiz_result($participant_id,$quiz_id); 
        $status = $this->ReportModel->delete_quiz_from_user_question($participant_id);
        if ($status) 
        {
            $this->session->set_flashdata('message', lang('admin_record_delete_successfully'));
        }
        else
        {
            $this->session->set_flashdata('error', lang('admin_error_during_delete_record')); 
        }
        redirect(base_url('tutor/report/'.$quiz_id));
    }

    function report_question_list()
    {
        $data = array();
        $list = $this->ReportModel->get_report_question(); 
        
        $no = $_POST['start'];
        foreach ($list as $reportquestion) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = $reportquestion->first_name .' '.$reportquestion->last_name;
            $row[] = $reportquestion->report_option;
            $row[] = $reportquestion->other_info;
            $row[] = date('d M, Y h:i:s',strtotime($reportquestion->added));
            $button = '<a href="' . base_url("admin/questions/update/". $reportquestion->quiz_id.'/'.$reportquestion->question_id) . '" data-toggle="tooltip" target="_blank" title="'.lang("goto_question").'" class="btn btn-primary btn-action mr-1"><i class="fas fa-link"></i></a>';

            $row[] = $button;
            $data[] = $row;
        }

        $output = array(
            "draw" => $_POST['draw'], 
            "recordsTotal" => $this->ReportModel->report_question_count_all(), 
            "recordsFiltered" => $this->ReportModel->report_question_count_filtered(), 
            "data" => $data
        );

        //output to json format
        echo json_encode($output);
    }
}