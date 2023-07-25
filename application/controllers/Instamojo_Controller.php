<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Instamojo_Controller extends  Public_Controller{ //CI_Controller 

	public function __construct() 
	{
		    	
        parent::__construct();
        
        $this->load->model('Payment_model');
        $this->load->library('Instamojo');

        $user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		if(empty($user_id))
		{
			$this->session->set_flashdata('error', 'Plz Login First');
			return redirect(base_url("login"));
		}
	}

	public function index($purchases_type,$quiz_id)
	{
		//6d6a918f1e582a5e3e2761dafa61716d  Instamojo apikey
		//10c32cf1c546b95eb6dace8910b48801  Instamojo token
		$user_id = isset($this->user['id']) ? $this->user['id'] : 0;

		$item_data = array();
		if($purchases_type == 'quiz')
		{
			$item_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		}
		elseif($purchases_type == 'material')
		{
			$item_data = $this->Payment_model->get_paid_material_by_id($quiz_id);
		}
		elseif($purchases_type == 'membership')
		{
			$item_data = $this->Payment_model->get_paid_membership_by_id($quiz_id);
		}

		if(empty($item_data))
		{
			$this->session->set_flashdata('error', 'Invalid Uri Arguments... !');
			return redirect(base_url());
		}

		if($purchases_type == 'membership')
		{
			$membership_data = $item_data;
			//check if membership is free
			if($membership_data->price < 1)
			{
				$is_user_has_membership = $this->Payment_model->get_free_membership($quiz_id,$user_id);
				if(isset($is_user_has_membership) && $is_user_has_membership)
				{
					$this->session->set_flashdata('error', lang('already_purchase_this_membership'));
					return redirect(base_url("membership"));		
				}
				$this->free_membership_payment($purchases_type,$quiz_id,$membership_data);
			}
		}
		
		$quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($purchases_type,$quiz_id);
		if($quiz_last_paymetn_status)
		{
			if($purchases_type == 'quiz')
			{
				$this->session->set_flashdata('error', "You Have Already Pay For This Quiz... !");
				return redirect(base_url("instruction/$purchases_type/$quiz_id"));
			}
			if($purchases_type == 'material')
			{
				$this->session->set_flashdata('error', "You Have Already Pay For This Study Material ... !");
				return redirect(base_url("study-material-detail/$purchases_type/$quiz_id"));
			}
		}

		$name = $this->user['first_name'] . " " .$this->user['last_name'];
		$email = $this->input->post('email');
		$email = $this->input->post('email') ? $this->input->post('email') : $this->user['email'];



		$item_price = $item_data->price;
		$coupon_id = null;
		if($this->session->coupon_data)
		{
			$coupon_id = $this->session->coupon_data['coupon_id'] ? $this->session->coupon_data['coupon_id'] : NULL;
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $this->user['id'] && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
		}
		

		$itemPrice = $item_price*100;
		$itemdisplayprice = $item_price;

		$page_title = lang('Instamojo Payment')." ".$this->settings->site_name;
		$this->set_title(lang('Instamojo Payment'), $this->settings->site_name);

		$content_data = array(
								'itemPrice' => $itemPrice,
								'coupon_id' => $coupon_id,
								'item_data' => $item_data,
								'Page_message' => $page_title, 
								'page_title' => $page_title,
								'purchases_type'=>$purchases_type,
								'quiz_id'=>$quiz_id,
								'itemdisplayprice' => $itemdisplayprice,
							);

        $data = $this->includes;
		$data['content'] = $this->load->view('instamojo', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
	}

	public function checkout($purchases_type,$quiz_id)
    {
		$user_id = isset($this->user['id']) ? $this->user['id'] : 0;

		$item_data = array();
		if($purchases_type == 'quiz')
		{
			$item_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		}
		elseif($purchases_type == 'material')
		{
			$item_data = $this->Payment_model->get_paid_material_by_id($quiz_id);
		}
		elseif($purchases_type == 'membership')
		{
			$item_data = $this->Payment_model->get_paid_membership_by_id($quiz_id);
		}

		if(empty($item_data))
		{
			$this->session->set_flashdata('error', 'Invalid Uri Arguments... !');
			return redirect(base_url());
		}

		if($purchases_type == 'membership')
		{
			$membership_data = $item_data;
			//check if membership is free
			if($membership_data->price < 1)
			{
				$is_user_has_membership = $this->Payment_model->get_free_membership($quiz_id,$user_id);
				if(isset($is_user_has_membership) && $is_user_has_membership)
				{
					$this->session->set_flashdata('error', lang('already_purchase_this_membership'));
					return redirect(base_url("membership"));		
				}
				$this->free_membership_payment($purchases_type,$quiz_id,$membership_data);
			}
		}
		
		$quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($purchases_type,$quiz_id);
		if($quiz_last_paymetn_status)
		{
			if($purchases_type == 'quiz')
			{
				$this->session->set_flashdata('error', "You Have Already Pay For This Quiz... !");
				return redirect(base_url("instruction/$purchases_type/$quiz_id"));
			}
			if($purchases_type == 'material')
			{
				$this->session->set_flashdata('error', "You Have Already Pay For This Study Material ... !");
				return redirect(base_url("study-material-detail/$purchases_type/$quiz_id"));
			}
		}

		$name = $this->user['first_name'] . " " .$this->user['last_name'];
		$email = $this->input->post('email');
		$email = $this->input->post('email') ? $this->input->post('email') : $this->user['email'];

		$itemPrice = $item_data->price;
		$coupon_id = null;
		if($this->session->coupon_data)
		{
			$coupon_id = $this->session->coupon_data['coupon_id'] ? $this->session->coupon_data['coupon_id'] : NULL;
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $this->user['id'] && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$itemPrice = $this->session->coupon_data['discount_price'];
			}
		}
		


		$get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
	    }


        $pay = $this->instamojo->pay_request(
									            $amount = $itemPrice,
									            $purpose = "Payment",
									            $buyer_name = $this->user['first_name'].' '.$this->user['last_name'],
									            $email = $this->user['email'],
									            $phone = "",
									            $send_email = 'FALSE',
									            $send_sms = 'FALSE',
									            $repeated = 'FALSE'
									        );


        if(isset($pay['id']) && $pay['id'])
        {
	        $payment_data = array();
		    $payment_data['user_id'] = $this->user['id'];
		    $payment_data['txn_id'] = $pay['id'];
		    $payment_data['quiz_id'] = $quiz_id;
		    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
		    $payment_data['email'] = $this->user['email'];
		    $payment_data['item_name'] = $item_data->title;
		    $payment_data['item_price'] = $itemPrice;
		    $payment_data['item_price_currency'] = 'INR';
		    $payment_data['payment_status'] = $pay['status'];
		    $payment_data['created'] = date("Y-m-d H:i:s");
		    $payment_data['modified'] = date("Y-m-d H:i:s");
		    $payment_data['payment_gateway'] = 'instamojo';
		    $payment_data['invoice_no'] = $invoice_no;
		    $payment_data['purchases_type'] = $purchases_type;
		    $payment_data['coupon_id'] = (isset($coupon_id) && $coupon_id) ? $coupon_id : NULL;
		    $payment_data['coupon_code'] = $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL;
			$payment_data['discount_value'] = $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL;

		    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);
		}
		else
		{
			$this->session->set_flashdata('error', $pay);

			if($purchases_type == 'membership')
			{
				return redirect(base_url("membership"));
			}
        	if($purchases_type == 'quiz')
			{
				return redirect(base_url("quiz-detail/quiz/$quiz_id"));
			}
			if($purchases_type == 'material')
			{
				return redirect(base_url("study-material-detail/$purchases_type/$quiz_id"));
			}
			return redirect(base_url());
		}

		$redirect_url = $pay['longurl'];

        redirect($redirect_url, 'refresh');
    }

    public function payment_status()
    {
    	$get_data = $this->input->get();
    	$payment_request_id = isset($get_data['payment_request_id']) ? $get_data['payment_request_id'] : NULL;
    	$last_payment_data = $this->Payment_model->get_last_paymetn_status($payment_request_id);
    	
    	if(empty($get_data) OR empty($payment_request_id) OR empty($last_payment_data))
    	{
    		$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url('quiz-pay/payment-mode/'.$last_payment_data->purchases_type.'/'.$last_payment_data->quiz_id));
    	}
    	$payment_id = $last_payment_data->id;
    	$purchases_type = $last_payment_data->purchases_type;
    	$quiz_id = $last_payment_data->quiz_id;

		$user_id = isset($this->user['id']) ? $this->user['id'] : 0;

		$item_data = array();
		if($purchases_type == 'quiz')
		{
			$item_data =  $this->Payment_model->get_paid_quiz_by_id($quiz_id);
		}
		elseif($purchases_type == 'material')
		{
			$item_data = $this->Payment_model->get_paid_material_by_id($quiz_id);
		}
		elseif($purchases_type == 'membership')
		{
			$item_data = $this->Payment_model->get_paid_membership_by_id($quiz_id);
		}

		if(empty($item_data))
		{
			$this->session->set_flashdata('error', 'Invalid Uri Arguments... !');
			return redirect(base_url());
		}


		if($purchases_type == 'membership')
		{
			$membership_data = $item_data;
			//check if membership is free
			if($membership_data->price < 1)
			{
				$is_user_has_membership = $this->Payment_model->get_free_membership($quiz_id,$user_id);
				if(isset($is_user_has_membership) && $is_user_has_membership)
				{
					$this->session->set_flashdata('error', lang('already_purchase_this_membership'));
					return redirect(base_url("membership"));		
				}
				$this->free_membership_payment($purchases_type,$quiz_id,$membership_data);
			}
		}
		
		$quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($purchases_type,$quiz_id);
		if($quiz_last_paymetn_status)
		{
			if($purchases_type == 'quiz')
			{
				$this->session->set_flashdata('error', "You Have Already Pay For This Quiz... !");
				return redirect(base_url("instruction/$purchases_type/$quiz_id"));
			}
			if($purchases_type == 'material')
			{
				$this->session->set_flashdata('error', "You Have Already Pay For This Study Material ... !");
				return redirect(base_url("study-material-detail/$purchases_type/$quiz_id"));
			}
		}


        $status = $this->instamojo->status($payment_request_id);
        $instamozo_txn_id = isset($status['id']) ? $status['id'] : NULL;
        $instamozo_pay_status = (isset($status['status']) && strtolower($status['status']) == 'completed') ? TRUE : FALSE;

        if(empty($instamozo_txn_id) OR $instamozo_pay_status == FALSE)
        {
        	$this->session->set_flashdata('error', "Payment Verification Failed ... !");

        	if($purchases_type == 'membership')
			{
				return redirect(base_url("membership"));
			}
        	if($purchases_type == 'quiz')
			{
				return redirect(base_url("quiz-detail/quiz/$quiz_id"));
			}
			if($purchases_type == 'material')
			{
				return redirect(base_url("study-material-detail/$purchases_type/$quiz_id"));
			}
			return redirect(base_url());

        }	

    	$payment_update['payment_status'] = 'succeeded';
    	$payment_update['modified'] = date("Y-m-d H:i:s");

    	$this->Payment_model->update_payment_detail($payment_update,$instamozo_txn_id);
    	$get_payment_data_id = $this->db->select('id')->where('txn_id',$instamozo_txn_id)->get('payments')->row();
    	if($purchases_type == "membership")
    	{

			$user_membership_data = array();
				

				$user_membership_id = NULL;
				$user_pay_for_membership_id = $item_data->id;
				$membership_category_id = $item_data->category_id;

				$validity = date('Y-m-d', strtotime("+".$item_data->duration."days"));	
				if($membership_category_id)	
				{
					$user_category_membership = $this->Payment_model->get_user_category_membership($this->user['id'],$membership_category_id);

					if($user_category_membership && $user_category_membership->validity >= date('Y-m-d'))
					{
						$validity_date = $user_category_membership->validity;
						$current_date = date('Y-m-d');
						
					    $date1 = date("Y-m-d",strtotime($validity_date));
						$date2 = date("Y-m-d",strtotime($current_date));
						$diff = abs(strtotime($date1) - strtotime($date2));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						$validity = date('Y-m-d', strtotime($current_date. "+".$item_data->duration."days"."+".$days."days"));
					}
				}
				else
				{
					$check_user_wise_membership = $this->Payment_model->get_user_wise_membership($this->user['id'],$item_data->id);
					
					if($check_user_wise_membership && $check_user_wise_membership->validity >= date('Y-m-d'))
					{
						$validity_date = $check_user_wise_membership->validity;
						$current_date = date('Y-m-d');
						
					    $date1 = date("Y-m-d",strtotime($validity_date));
						$date2 = date("Y-m-d",strtotime($current_date));
						$diff = abs(strtotime($date1) - strtotime($date2));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						$validity = date('Y-m-d', strtotime($current_date. "+".$item_data->duration."days"."+".$days."days"));
					}
				}
				$user_membership_data['category_id'] = $membership_category_id;	
				$user_membership_data['user_id'] = $this->user['id'];	
				$user_membership_data['membership_id'] = $item_data->id;	
				$user_membership_data['payment_id'] = $get_payment_data_id->id;
				$user_membership_data['purchased'] = date("Y-m-d H:i:s");
				$user_membership_data['validity'] = $validity;
				$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
			
    	}


    	$logged_in_user_data = $this->session->userdata('logged_in');
		$full_name = $logged_in_user_data['first_name']." ".$logged_in_user_data['last_name'];

		$email_template = get_email_template('item-purchased');	
		$email_msg  = "Instamojo Payment Success Please add mail templet for complete mail";
		$mail_subject = "Instamojo Payment ";
		if($email_template)
		{
            $email_msg = str_replace('{customer_full_name}',$full_name,$email_template->description);
            $email_msg = str_replace('{purchaged_item_name}',$purchases_type,$email_msg);
            $email_msg = str_replace('{item_name}',$item_data->title,$email_msg);
			$email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);
            $email_msg = str_replace("{site_name_with_url}",'<a href="'.base_url().'">'.$this->settings->site_name.'</a>',$email_msg);                
            $mail_subject = ucfirst($purchases_type)." ".$email_template->subject;
		}

        $mail_to = $logged_in_user_data['email'];
        $recipet_name = $logged_in_user_data['first_name'];

        $this->load->library('SendMail');
        if($this->settings->email_user_activation == 'YES')
        {
        	$mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
        	$mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
        	if($mail_status)
            {
            	$this->session->set_flashdata('message', "Congratulation for purchased item");
            }
            else
            {
            	$this->session->set_flashdata('error', 'Sorry Mail Send Error');
            }
        }
        else
        {
        	$this->session->set_flashdata('message', "Payment Success... But Mail send  Error !");
        }
		
		return redirect(base_url("stripe/quiz-pay/payment-success/$purchases_type/$quiz_id/$payment_id"));
    }

}