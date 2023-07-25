<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Payment_Controller extends Admin_Controller {
    function __construct() {
        parent::__construct();
        $this->add_css_theme('all.css');
        $this->add_js_theme('jquery.multi-select.min.js');
        $this->add_js_theme('payment_custome_script.js');
        $this->add_js_theme('plugin/taginput/bootstrap-tagsinput.min.js');
        $this->add_css_theme('plugin/taginput/bootstrap-tagsinput.css');
        // load the language files 
        // load the category model
        $this->load->model('Payment_model');
        //load the form validation
        $this->load->library('form_validation');
        // set constants
        define('REFERRER', "referrer");
        define('THIS_URL', base_url('admin/payment'));
        define('DEFAULT_LIMIT', 10);
        define('DEFAULT_OFFSET', 0);
        define('DEFAULT_SORT', "id");
        define('DEFAULT_DIR', "asc");

        if(is_loged_in_user_is_subadmin() == TRUE)
        {
            $this->session->set_flashdata('error', lang('you_dont_have_permission_to_access_this_page'));
            return redirect(base_url('admin/dashboard'));
        }
        
    }

    function index() {

        $this->add_css_theme('sweetalert.css')->add_js_theme('sweetalert-dev.js')->add_js_theme('bootstrap-notify.min.js')->set_title(lang('payment_list'));

        $data = $this->includes;
        $content_data = array();
        // load views
        $data['content'] = $this->load->view('admin/payment/list', $content_data, TRUE);
        $this->load->view($this->template, $data);
    }

    function payment_list()
    {
        $list = $this->Payment_model->get_payment();
        
        $data = array();
        $no = $_POST['start'];
        foreach ($list as $payment) {
            $no++;
            $row = array();
            $row[] = $no;
            $row[] = ucfirst($payment->first_name .' '.$payment->last_name);
            $row[] = ucfirst($payment->username);
            $row[] = ($payment->email);
            $row[] = ucfirst($payment->purchases_type);
            $row[] = ucfirst($payment->item_name);
            $row[] = ucfirst($payment->item_price_currency.' '.$payment->item_price);
            $row[] = ucfirst($payment->payment_gateway);

            $success = $fail = $pending ="";
            if($payment->payment_status == 'succeeded')
            {
                $success = 'selected';
            }
            elseif($payment->payment_status == 'fail')
            {
                $fail = 'selected';
            }
            elseif($payment->payment_status == 'pending')
            {
                $pending = 'selected';
            }
            
            $payment_status = '<select class="form-control w-75 float-left pay-change-'.$payment->id.'"  name="pay_status">
                                    <option '.$fail.' value="fail">Fail</option>
                                    <option '.$pending.' value="pending">Pending</option>
                                    <option '.$success.' value="succeeded">Success</option>
                                </select>
                                <i class="fas fa-check btn btn-info  pay-status" data-purchases_type="'.$payment->purchases_type.'" data-payment_id="'.$payment->id.'" data-payment_gateway="'.$payment->payment_gateway.'"></i>';
            $row[] = $payment_status;
            $row[] = '<button id="myBtn-'.$payment->id.'" data-toggle="modal" data-target="#myModal" title="View Detail" class="btn btn-warning myBtn btn-action mr-1" data-payment_id="'.$payment->id.'">View Detail</button>
                <a href="'.base_url('admin/payment/invoice/'.$payment->id).'" target="_blank" class="btn btn-info text-white">'.lang('invoice').'</a>';
            $data[] = $row;
            
        }
        $output = array("draw" => $_POST['draw'], "recordsTotal" => $this->Payment_model->count_all(), "recordsFiltered" => $this->Payment_model->count_filtered(), "data" => $data);

        //output to json format
        echo json_encode($output);
    }

    function update_status()
    {
        $id = $_POST['payment_id'];
        $status = $_POST['status_value'];
        $purchases_type = $_POST['purchases_type'];
        $payment_gateway = $_POST['payment_gateway'];

        $payment_data = $this->Payment_model->get_payment_detail_by_id($id);
        if(empty($payment_data))
        {
            $this->session->set_flashdata('error', lang('something_went_wrong'));
            return redirect(base_url("admin/payment"));
        }


        $this->Payment_model->updatestatus($id, $status);

        if($status == 'succeeded' && $payment_gateway == 'bank-transfer' && $purchases_type == 'membership')
        {
            $user_pay_for_membership_id = $payment_data->relation_id;

            $item_data = $this->db->where('id',$user_pay_for_membership_id)->get('membership')->row();

            if($item_data)
            {

                $membership_category_id = $item_data->category_id;
                $get_membership_data = $this->Payment_model->get_membership_by_payment_id($id,'bank-transfer','membership');

                if($membership_category_id)
                {
                    $user_category_membership = $this->Payment_model->get_user_category_membership($get_membership_data->user_id,$membership_category_id);

                    if($user_category_membership && $user_category_membership->validity > date('Y-m-d'))
                    {
                        $user_membership_data['user_id'] = isset($get_membership_data->user_id) ? $get_membership_data->user_id : NULL;  
                        $user_membership_data['membership_id'] = $get_membership_data->membership_id;    
                        $user_membership_data['payment_id'] = $id;
                        $user_membership_data['category_id'] = $membership_category_id;
                        $validity = strtotime($user_category_membership->validity);
                        $validity = strtotime("+".$get_membership_data->duration."days", $validity);
                        $validity = date('Y-m-d', $validity);
                        $user_membership_data['validity'] = $validity;      
                        $user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
                    }
                    else
                    {
                        $user_membership_data['category_id'] = $membership_category_id;
                        $user_membership_data['user_id'] = isset($get_membership_data->user_id) ? $get_membership_data->user_id : NULL;
                        $user_membership_data['membership_id'] = $get_membership_data->membership_id;    
                        $user_membership_data['payment_id'] = $id;
                        $validity = date('Y-m-d', strtotime("+".$get_membership_data->duration."days"));  
                        $user_membership_data['validity'] = $validity;       
                        $user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
                    }
                }
                else
                {
                    
                    $user_membership_data = array();
                    $check_user_wise_membership = $this->Payment_model->get_user_wise_membership($get_membership_data->user_id,date('Y-m-d'));
                    if($check_user_wise_membership && $check_user_wise_membership->validity > date('Y-m-d'))
                    {
                        $user_membership_data['category_id'] = 0;
                        $user_membership_data['user_id'] = isset($get_membership_data->user_id) ? $get_membership_data->user_id : NULL;  
                        $user_membership_data['membership_id'] = $get_membership_data->membership_id;    
                        $user_membership_data['payment_id'] = $id;
                        $validity = strtotime($check_user_wise_membership->validity);
                        $validity = strtotime("+".$get_membership_data->duration."days", $validity);
                        $validity = date('Y-m-d', $validity);
                        $user_membership_data['validity'] = $validity;      
                        $user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
                    }
                    else
                    {
                        $user_membership_data['category_id'] = 0;
                        $user_membership_data['user_id'] = isset($get_membership_data->user_id) ? $get_membership_data->user_id : NULL;
                        $user_membership_data['membership_id'] = $get_membership_data->membership_id;    
                        $user_membership_data['payment_id'] = $id;
                        $validity = date('Y-m-d', strtotime("+".$get_membership_data->duration."days"));  
                        $user_membership_data['validity'] = $validity;       
                        $user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
                    }
                }

            }
            
        }

        if($purchases_type == 'membership')
        {


            $this->load->model('UsersModel');
            $user_data = $this->UsersModel->get_user_by_id($payment_data->user_id);
            $user_data = json_decode(json_encode($user_data), true);
            $logged_in_user_data = $user_data;
            $full_name = $logged_in_user_data['first_name']." ".$logged_in_user_data['last_name'];

            $email_template = get_email_template('item-purchased'); 
            $email_msg  = "stripe Payment Success Please add mail templet for complete mail";
            $mail_subject = "Stripe Payment ";
            if($email_template)
            {
                $email_msg = str_replace('{customer_full_name}',$full_name,$email_template->description);
                $email_msg = str_replace('{purchaged_item_name}',$purchases_type,$email_msg);
                $email_msg = str_replace('{item_name}',$purchases_type,$email_msg);
                $email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);
                $email_msg = str_replace("{site_name_with_url}",'<a href="'.base_url().'">'.$this->settings->site_name.'</a>',$email_msg);                
                $mail_subject = ucfirst($purchases_type)." ".$email_template->subject;
            }

            $mail_to = $logged_in_user_data['email'];
            $recipet_name = $logged_in_user_data['first_name'];
            $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
            
            $this->load->library('SendMail');
            
            if($this->settings->email_user_activation == 'YES')
            {
                $mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
                //$mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $email_msg);
            }
        }

        $success = array('status' => $status, 'messages' => lang('admin_record_updated_successfully'));
        echo json_encode($success);
    }



    function payment_detail()
    {
        $id = $_POST['payment_id'];
        $payment_data = $this->Payment_model->get_payment_detail_by_id($id);

        if(empty($payment_data))
        {
            $this->session->set_flashdata('error', lang(' Payment Not Found ... !')); 
            redirect(base_url('admin/payment'));
        }
        $relation_id = $payment_data->relation_id;
        $purchases_type = $payment_data->purchases_type;

        $item_title = NULL;
        $item_price = NULL;
        $description = NULL;
    
        $item_data = array();
        if($purchases_type == 'quiz')
        {
            $item_data =  $this->Payment_model->get_paid_quiz_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;
        }
        elseif($purchases_type == 'material')
        {
            $item_data = $this->Payment_model->get_paid_material_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }
        elseif($purchases_type == 'membership')
        {
            $item_data = $this->Payment_model->get_paid_membership_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }

        if(empty($item_data))
        {
            $this->session->set_flashdata('error', ucfirst($purchases_type).' Details Not Exist... !');
            return redirect(base_url("admin/payment"));
        }   


        $content_data['title'] = lang('invoice');
        $content_data['payment_data'] = $payment_data;
        $content_data['purchases_type'] = $purchases_type;
        $content_data['relation_id'] = $relation_id;
        $content_data['item_title'] = $item_title;
        $content_data['item_price'] = $item_price;
        $content_data['description'] = $description;
        $content_data['item_data'] = $item_data;

        
        $data = $this->includes;
        // load views
        $modal_data = $this->load->view('admin/payment/payment_detail', $content_data, TRUE);
        echo json_encode($modal_data);
    }

    function invoice($payment_id = NULL)
    {   

        $payment_data = $this->Payment_model->get_payment_detail_by_id($payment_id);

        if(empty($payment_data))
        {
            $this->session->set_flashdata('error', lang(' Payment Not Found ... !')); 
            redirect(base_url('profile'));
        }
        
        $payment_id = $payment_data->id;
        $relation_id = $payment_data->relation_id;
        $purchases_type = $payment_data->purchases_type;

        $item_title = NULL;
        $item_price = NULL;
        $description = NULL;
    
        $item_data = array();
        if($purchases_type == 'quiz')
        {
            $item_data =  $this->Payment_model->get_paid_quiz_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;
        }
        elseif($purchases_type == 'material')
        {
            $item_data = $this->Payment_model->get_paid_material_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }
        elseif($purchases_type == 'membership')
        {
            $item_data = $this->Payment_model->get_paid_membership_by_id($relation_id);
            $item_title = $item_data->title;
            $item_price = $item_data->price;
            $description = $item_data->description;

        }

        if(empty($item_data))
        {
            $this->session->set_flashdata('error', ucfirst($purchases_type).' Details Not Exist... !');
            return redirect(base_url("profile"));
        }  


        
        $content_data['title'] = lang('invoice');
        $content_data['payment_data'] = $payment_data;
        $content_data['purchases_type'] = $purchases_type;
        $content_data['relation_id'] = $relation_id;
        $content_data['item_title'] = $item_title;
        $content_data['item_price'] = $item_price;
        $content_data['description'] = $description;
        $content_data['item_data'] = $item_data;

        
        $data = $this->includes;

        // load views
        $modal_data = $this->load->view('admin/payment/invoice', $content_data);
    }

}