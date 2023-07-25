<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Razorpay\Api\Api;

class Razorpay extends  Public_Controller{ //CI_Controller 

	function __construct() 
	{
		    	
        parent::__construct();
        
        $this->load->model('Payment_model');

        $user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		if(empty($user_id))
		{
			$this->session->set_flashdata('error', 'Plz Login First');
			return redirect(base_url("login"));
		}

	}

    public function index($purchases_type,$quiz_id)
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

		$razor_key_id = $this->settings->razorpay_key;
		$razor_secret = $this->settings->razorpay_secret_key;
		$api = new Api($razor_key_id, $razor_secret);

		$orderData = [
		    'receipt'         => time(), 
		    'amount'          => $itemPrice, // 2000 rupees in paise
		    'currency'        => $this->settings->paid_currency,
		    'payment_capture' => 1 // auto capture
		];

		
		try
		{
			$razorpayOrder = $api->order->create($orderData);
		}
		catch(Exception $e) 
		{
		    $this->session->set_flashdata('error', $e->getMessage());

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

		$razorpayOrderId = (isset($razorpayOrder['id']) &&  $razorpayOrder['id']) ?  $razorpayOrder['id'] : NULL;


		if(empty($razorpayOrderId))
		{
			$this->session->set_flashdata('error', 'Invalid Payment Details Plz Try Again Later');
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

		

		$get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
	    }


		// insert into DB and customer_id = $razorpayOrderId 
		if($razorpayOrderId)
	    {
		    $payment_data = array();
		    $payment_data['user_id'] = $this->user['id'];
		    $payment_data['quiz_id'] = $quiz_id;
		    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
		    $payment_data['email'] = $this->user['email'];
		    $payment_data['item_name'] = $item_data->title;
		    $payment_data['item_price'] = $itemPrice;
		    $payment_data['item_price_currency'] = $this->settings->paid_currency;
		    $payment_data['customer_id'] = $razorpayOrderId;
		    $payment_data['payment_status'] = 'pending';
		    $payment_data['created'] = date("Y-m-d H:i:s");
		    $payment_data['modified'] = date("Y-m-d H:i:s");
		    $payment_data['payment_gateway'] = 'razorpay';
		    $payment_data['invoice_no'] = $invoice_no;
		    $payment_data['purchases_type'] =  $purchases_type;
	     	$payment_data['coupon_id'] = $coupon_id;
		    $payment_data['coupon_code'] = $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL;
			$payment_data['discount_value'] = $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL;

		    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);
		    if($payment_id)
		    {		    	
		    	return redirect(base_url("razorpay/quiz-payment/$purchases_type/$quiz_id/$payment_id"));	   
		    }
		    else
		    {
				$this->session->set_flashdata('error', 'Something Went Wrong');
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
		}

	}

	public function quiz_payment($purchases_type, $quiz_id, $payment_id)
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

		$payment_data =  $this->Payment_model->get_payment_info_by_payment_data($purchases_type,$quiz_id,$payment_id);

		
		if(empty($payment_data))
		{
			$this->session->set_flashdata('error', 'Something Went Wrong');
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


		$razor_key_id = $this->settings->razorpay_key;
		$razor_secret = $this->settings->razorpay_secret_key;

		$form_data = [
					    "key"        	=> $razor_key_id,
					    "amount"        => $payment_data->item_price,
					    "name"          => $item_data->title,
					    "description"   => $item_data->title,
					    "image"         => base_url('/assets/images/logo/'.$this->settings->site_logo),
					    "prefill"       => [
											    "name" => $this->user['first_name'].' '.$this->user['last_name'],
											    "email" => $this->user['email'],
											    "contact" => "",
										    ],
					    "notes"         =>  [
											    "address" => "Hello World",
											    "merchant_order_id" => $payment_id,
										    ],
					    "theme"         =>  [
											    "color" => "#7f67bb"
											],
		    			"order_id"      => $payment_data->customer_id,
					];

		$page_title = lang('Rozorpay Payment')." ".$this->settings->site_name;
		$this->set_title(lang('Rozorpay Payment'), $this->settings->site_name);

		$content_data = array('data'=>$form_data, 'payment_id'=>$payment_id,'item_data' => $item_data,'Page_message' => $page_title, 'page_title' => $page_title,'purchases_type'=>$purchases_type,'quiz_id' => $quiz_id,'payment_data' => $payment_data);

        $data = $this->includes;
		$data['content'] = $this->load->view('razor_payment', $content_data, TRUE);
        
        $this->load->view($this->template, $data);

	}

	function verify($purchases_type, $quiz_id, $payment_id)
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

		$payment_data =  $this->Payment_model->get_payment_info_by_payment_data($purchases_type,$quiz_id,$payment_id);

		
		if(empty($payment_data))
		{
			$this->session->set_flashdata('error', 'Something Went Wrong');
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


		$success = true;

		$error = "Payment Failed";

		$razorpay_payment_id = (isset($_POST['razorpay_payment_id']) && $_POST['razorpay_payment_id']) ? $_POST['razorpay_payment_id'] : NULL;
		
		$razorpay_order_id = (isset($_POST['razorpay_order_id']) && $_POST['razorpay_order_id']) ? $_POST['razorpay_order_id'] : NULL;

		
		$razorpay_signature = (isset($_POST['razorpay_signature']) && $_POST['razorpay_signature']) ? $_POST['razorpay_signature'] : NULL;

		if (empty($razorpay_payment_id) OR empty($razorpay_order_id) OR empty($razorpay_signature))
		{
			$this->session->set_flashdata('error', 'Invalid Payment Details Plz Try Again Later');
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


		$keyId = $this->settings->razorpay_key;
		$keySecret = $this->settings->razorpay_secret_key;

	    $api = new Api($keyId, $keySecret);
	    $rozorpay_verify_responnse = FALSE;
	    try
	    {
	        // Please note that the razorpay order ID must
	        // come from a trusted source (session here, but
	        // could be database or something else)
	        $attributes = array(
						            'razorpay_order_id' => $razorpay_order_id,
						            'razorpay_payment_id' => $razorpay_payment_id,
						            'razorpay_signature' => $razorpay_signature,
						        );
	        
	        $rozorpay_verify_responnse =  $api->utility->verifyPaymentSignature($attributes);

	    }
	    catch(Exception $e)
	    {
	        $this->session->set_flashdata('error', $e->getMessage());

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

		// update payment table and set txn_id and payment_status
		$update_razorpay_payment = $this->Payment_model->update_razorpay_payment_detail($payment_id,$razorpay_payment_id);


		if($purchases_type == 'membership')
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
			$user_membership_data['payment_id'] = $inserted_id;
			$user_membership_data['purchased'] = date("Y-m-d H:i:s");
			$user_membership_data['validity'] = $validity;
			$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
		}

		$logged_in_user_data = $this->session->userdata('logged_in');
		$full_name = $logged_in_user_data['first_name']." ".$logged_in_user_data['last_name'];

		$email_template = get_email_template('item-purchased');	
		$email_msg  = "Payment Success Please add mail templet for complete mail";
		$mail_subject = "Stripe Payment ";
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
            	$this->session->set_flashdata('message', lang("Congratulation for purchased item"));
            }
            else
            {
            	$this->session->set_flashdata('error', lang('Sorry Mail Send Error'));
            }
        }
        else
        {
        	$this->session->set_flashdata('message', lang('Payment Success... But Mail send  Error !'));
        }

		return redirect(base_url("stripe/quiz-pay/payment-success/$purchases_type/$quiz_id/$payment_id"));
	}
}