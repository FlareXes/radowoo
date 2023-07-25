<?php
defined('BASEPATH') OR exit('No direct script access allowed');

// require_once "../vendor/stripe/strip-php/init.php";
class Stripe extends  Public_Controller{ //CI_Controller 

    function __construct() 
    {
        parent::__construct();
        
        $this->load->model('Payment_model');
        $this->stripe_key  =  array(
			  "secret_key"      => $this->settings->stripe_secret_key,
			  "publishable_key" => $this->settings->stripe_key
			);

        $user_id = isset($this->user['id']) ? $this->user['id'] : 0;
		if($user_id <= 0)
		{
			$this->session->set_flashdata('error', 'Plz Login First');
			return redirect(base_url("login"));
		}

		try 
		{ 
			\Stripe\Stripe::setApiKey($this->stripe_key['secret_key']);
		}
		catch(Exception $e) 
		{
		    $this->session->set_flashdata('error', $e->getMessage());
			return redirect(base_url());
		}

    }

	public function index($purchases_type,$quiz_id)
	{
		if(empty($this->input->post('address')) || empty($this->input->post('address')) || empty($this->input->post('city')) || empty($this->input->post('state')) || empty($this->input->post('country')) || empty($this->input->post('postal_code')))
		{
			$this->session->set_flashdata('error', lang('please_fill_all_billing_address!'));
			return redirect(base_url('quiz-pay/payment-mode/'.$purchases_type.'/'.$quiz_id));
		}
		
		$user_id = isset($this->user['id']) ? $this->user['id'] : NULL;

        if(empty($user_id))
        {
            $this->session->set_flashdata('error', lang('please_login_first!'));
            return redirect(base_url('login')); 
        }
        $user_name = $this->user['first_name'].' '.$this->user['last_name'];



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

		$item_price = $item_data->price;
		if($this->session->coupon_data)
		{
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $user_id && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
		}
		

		$name = $this->user['first_name'] . " " .$this->user['last_name'];
		$email = $this->input->post('email');
		$email = $this->input->post('email') ? $this->input->post('email') : $this->user['email'];
		$itemPrice = $item_price*100;
		
		
		try 
		{ 
			// Verify your integration in this guide by including this parameter
			$intent = \Stripe\PaymentIntent::create([
			  	'amount' => $itemPrice,
			  	'currency' => $this->settings->paid_currency,
			 	'metadata' => ['integration_check' => 'accept_a_payment'],
			  	'description' => 'Quiz Payment',
			  	'shipping' => ['name' => $name,'address' => ['line1' => $this->input->post('address'),'postal_code' => $this->input->post('postal_code'),'city' => $this->input->post('city'),'state' => $this->input->post('state'),'country' => $this->input->post('country'), ],],
			]);
			
			$customer = \Stripe\Customer::create([
				'name' => $name,
				'description' => $item_data->title.' (Stripe Payment)',
				'address' => ['line1' => $this->input->post('address'),'postal_code' => $this->input->post('postal_code'),'city' => $this->input->post('city'),'state' => $this->input->post('state'),'country' => $this->input->post('country'),],
				]);
			
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
		
		
				
        $this->add_js_theme('stripepayment.js');

        $this->set_title(sprintf(lang('Stripe Payment'), $this->settings->site_name));

        $content_data = array('Page_message' => lang('Stripe Payment'), 'Stripe Payment' => lang('Stripe Payment'),'stripe_key' => $this->stripe_key,'quiz_id' => $quiz_id, 'item_data' => $item_data,'purchases_type'=> $purchases_type,'client_secret'=>$intent->client_secret,'txn_id' =>$intent->id,'user_name' => $user_name,'item_price' => $item_price,);

        $data = $this->includes;
        $data['stripe_key'] = $this->stripe_key;

        $data['content'] = $this->load->view('product_form', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
	}






	public function check($purchases_type,$quiz_id,$txn_id) 
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

		// $intent = \Stripe\PaymentIntent::retrieve($txn_id);
		// $charges = $intent->charges->data;
		$stripe_response = NULL;
		try
		{
			$stripe = new \Stripe\StripeClient($this->stripe_key['secret_key']);
			$stripe_response = $stripe->paymentIntents->retrieve($txn_id,[]);		
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



		//check whether stripe token is not empty
		//check whether the charge is successful

		$stripe_response_id = (isset($stripe_response->id) && $stripe_response->id) ? $stripe_response->id : NULL;
		$stripe_response_status = (isset($stripe_response->status) && $stripe_response->status) ? $stripe_response->status : NULL;
		$stripe_response_amount = (isset($stripe_response->amount) && $stripe_response->amount) ? $stripe_response->amount : NULL;

		


		//item information
		$itemName = $item_data->title;
		$itemNumber = $item_data->id;
		$itemPrice = $item_data->price*100;
		$currency = $this->settings->paid_currency;
		

		$get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
	    }
		
		$date = date("Y-m-d H:i:s");
		$name = $this->user['first_name'] . " " .$this->user['last_name'];
		$email = $this->user['email'];
		$item_price = $item_data->price;
		if($this->session->coupon_data)
		{
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $this->user['id'] && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
		
		}



		if(empty($stripe_response_id) && empty($stripe_response_amount) && $stripe_response_status != "succeeded")
		{
				$dataDB = array
				(
					'user_id' 				=> $user_id,
					'quiz_id' 				=> $quiz_id,
					'name' 					=> $name,
					'email' 				=> $email, 
					'item_name' 			=> $itemName, 
					'item_price' 			=> $item_price, 
					'item_price_currency' 	=> $currency, 
					'txn_id' 				=> $txn_id,
					'payment_status' 		=> 'fail',
					'created' 				=> $date,
					'modified' 				=> $date,
					'payment_gateway' 		=> 'stripe',
					'invoice_no' 			=> $invoice_no,
					'purchases_type' 		=> $purchases_type,
					'coupon_id'				=> $this->session->coupon_data['coupon_id'] ? $this->session->coupon_data['coupon_id'] : NULL,
					'coupon_code'			=> $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL,
					'discount_value'		=> $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL,
				);

			$this->Payment_model->insert_stripe_detail($dataDB);
			$this->session->set_flashdata('error', lang('Invalid Payment Details Plz Try Again Later'));
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
					

		//insert tansaction data into the database
		$dataDB = array
		(
			'user_id' 				=> $user_id,
			'quiz_id' 				=> $quiz_id,
			'name' 					=> $name,
			'email' 				=> $email, 
			'item_name' 			=> $itemName, 
			'item_price' 			=> $item_price, 
			'item_price_currency' 	=> $currency, 
			'txn_id' 				=> $txn_id,
			'payment_status' 		=> 'succeeded',
			'created' 				=> $date,
			'modified' 				=> $date,
			'payment_gateway' 		=> 'stripe',
			'invoice_no' 			=> $invoice_no,
			'purchases_type' 		=> $purchases_type,
			'coupon_id'				=> $this->session->coupon_data['coupon_id'] ? $this->session->coupon_data['coupon_id'] : NULL,
			'coupon_code'			=> $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL,
			'discount_value'		=> $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL,
		);

		$inserted_id = $this->Payment_model->insert_stripe_detail($dataDB);

		if($inserted_id)
		{
			if(isset($inserted_id) && $purchases_type == 'membership')
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
			$email_msg  = "stripe Payment Success Please add mail templet for complete mail";
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

			$payment_id = $inserted_id;
			return redirect(base_url("stripe/quiz-pay/payment-success/$purchases_type/$quiz_id/$payment_id"));
		}
		else
		{
			$this->session->set_flashdata('error', 'Transaction Success Error During Storing Details..!');
			return redirect(base_url("quiz/payment-fail/$purchases_type/$quiz_id"));
		}	
			
	}

	public function payment_success($purchases_type, $quiz_id, $payment_id)
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
		
		$payment_data =  $this->Payment_model->get_payment_data_by_id($purchases_type,$payment_id);
		if(empty($payment_data))
		{
			$this->session->set_flashdata('error', 'Payment Not Recived For  '.$purchases_type.' Please Check Your Network Connection And Try AGain Later');
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

        
        $content_data = array('Page_message' => lang('Stripe Payment'), 'page_title' => "Stripe Payment", 'payment_id' => $payment_id, 'item_data' => $item_data, 'payment_data' => $payment_data, 'quiz_id' => $quiz_id,'purchases_type'=>$purchases_type,);
         
        $this->set_title(sprintf(lang('home'), $this->settings->site_name));
        $data = $this->includes;
        $data['page_title'] = "Stripe Payment";
        $data['page_header'] = "Stripe Payment";
        $data['content'] = $this->load->view('payment_success', $content_data, TRUE);
        $this->load->view($this->template, $data);		
	}



	public function payment_error($purchases_type, $quiz_id)
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

        $this->set_title(sprintf(lang('Payment Error'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('Payment Error'), 'page_title' => lang('Payment Error'),'purchases_type' => $purchases_type,);
        $data = $this->includes;
        $data['content'] = $this->load->view('payment_error', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
	}

	public function help()
	{
        $this->set_title(sprintf(lang('Help'), $this->settings->site_name));
        $content_data = array('Page_message' => lang('Help'), 'page_title' => lang('Help'));
        $data = $this->includes;
        $data['content'] = $this->load->view('help', $content_data, TRUE);
        $this->load->view($this->template, $data);
	}
}