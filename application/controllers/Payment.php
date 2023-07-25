<?php
class Payment extends Public_Controller { // CI_Controller 
	 
	public function __construct()
	{
		parent::__construct();
		$this->load->library("paypal");
		$this->load->library('form_validation');
        $this->load->model('Payment_model');
		$this->load->helper("url");
		$user_id = isset($this->user['id']) ? $this->user['id'] : 0;
		if($user_id < 0)
		{
			$this->session->set_flashdata('error', 'Plz Login First');
			return redirect(base_url("login"));
		}
	}

	// from here user can choose Any available Payment Getway
	public function payment_mode($purchases_type,$quiz_id) 
	{	
		$user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		
		if(empty($user_id))
		{
			$this->session->set_flashdata('error', lang('please_login_first'));
			return redirect(base_url('login'));
		}

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

		$payment_pending_status = $this->Payment_model->get_paymetn_status($quiz_id,$user_id,$purchases_type);
		
		if($this->session->coupon_data)
		{
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;

			if($this->session->coupon_data['user_id'] == $user_id && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
		}
	
		
        $this->set_title(sprintf(lang('Payment Mode'), $this->settings->site_name));

        $this->add_external_js(base_url("/assets/themes/admin/js/payment_custome_script.js"));
		
        $content_data = array('Page_message' => lang('Payment Mode'), 'page_title' => lang('Payment Mode'),'quiz_id' => $quiz_id, 'item_data' => $item_data,'payment_pending_status'=>$payment_pending_status,'purchases_type' => $purchases_type,'item_price'=>$item_price,);

        $data = $this->includes;

        $data['content'] = $this->load->view('payment_method', $content_data, TRUE);
        
        $this->load->view($this->template, $data);
	}

	//when user request for payment we will start from here 
	public function create_payment($purchases_type,$quiz_id)
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
		$item_price = $item_data->price;

		if($this->session->coupon_data)
		{
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $user_id && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
		
			
		}

		$payment_method = "paypal";
		$return_url     = base_url("paypal/quiz-pay/pay-successfuly/$purchases_type/$quiz_id");
		$cancel_url     = base_url("paypal/quiz-pay/payment-fail/$purchases_type/$quiz_id");
		$total          = $item_price;
		$description    = $item_data->title;
		$intent         = "SALE";

		
		$this->paypal->set_api_context();
		$status_payment = $this->paypal->create_payment($payment_method, $return_url, $cancel_url, 
        $total, $description, $intent);

       if($status_payment['status'] == FALSE)
        {
        	$this->session->set_flashdata('error',lang('Invalid Api Keys..Error During Authentication ! '.$status_payment['message'])); 
			
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

        return TRUE;
	}
	
	//After payment cancel we will be redirected here
	public function cancel($purchases_type,$quiz_id)
	{
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

		$payment_token = isset($_GET['token']) && $_GET['token'] ? $_GET['token'] : NULL;
		
		if(empty($payment_token))
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("quiz-pay/payment-mode/$purchases_type/$quiz_id"));
		}

	    $payment_exist = $this->Payment_model->check_payment_by_token($_GET['token']);
	    if($payment_exist)
	    {
	    	$this->session->set_flashdata('error', 'Payment Has Already Done With This Transaction No !');
	    	$exist_payment_id = $payment_exist->id;
	    	return redirect(base_url("paypal/quiz-pay/payment-status/$purchases_type/$quiz_id/$exist_payment_id"));
	    }

	    $get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
		}

		$item_price = $item_data->price;
		$coupon_id = NULL;
		if($this->session->coupon_data)
		{
			$coupon_id = $this->session->coupon_data['coupon_id'] ? $this->session->coupon_data['coupon_id'] : NULL;
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $this->user['id'] && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
		}
		
	    $payment_data = array();
	    $payment_data['user_id'] = $this->user['id'];
	    $payment_data['payer_id'] = 0;
	    $payment_data['quiz_id'] = $quiz_id;
	    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
	    $payment_data['email'] = $this->user['email'];
	    $payment_data['item_price'] = $item_price;
	    $payment_data['item_name'] = $item_data->title;
	    $payment_data['txn_id'] = $_GET['token'];
	    $payment_data['item_price_currency'] = $this->settings->paid_currency;
	    $payment_data['token_no'] = $_GET['token'];
	    $payment_data['payment_status'] = 'fail';
	    $payment_data['created'] = date("Y-m-d H:i:s");
	    $payment_data['purchases_type'] = $purchases_type;
	    $payment_data['invoice_no'] = $invoice_no;
	    $payment_data['modified'] = date("Y-m-d H:i:s");
		$payment_data['payment_gateway'] = 'paypal';
		$payment_data['coupon_id'] = $coupon_id;
		$payment_data['coupon_code'] = $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL;
		$payment_data['discount_value'] = $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL;

	    $payment_id = $this->Payment_model->insert_payment($payment_data);
	    if($payment_id)
	    {
			$this->session->set_flashdata('error', 'Payment Has canceled... !');
	    }
	    else
	    {
	    	$this->session->set_flashdata('error', 'Payment Has canceled Or Record Insert Error ... !');
	    }
	    return redirect(base_url("paypal/quiz-pay/payment-status/$purchases_type/$quiz_id/$payment_id"));
	}

	//After successfully create an agreement we will be redirected to this function
	public function success_payment($purchases_type,$quiz_id)
	{
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


		$quiz_last_paymetn_status = $this->Payment_model->get_quiz_last_paymetn_status($purchases_type,$quiz_id);
		if(isset($quiz_last_paymetn_status) && $purchases_type == 'quiz')
		{
			$this->session->set_flashdata('error', 'You Have Already Pay For This Quiz... !');
			return redirect(base_url("instruction/$purchases_type/$quiz_id"));
		}
		elseif(isset($quiz_last_paymetn_status) && $purchases_type == 'material')
		{
			$this->session->set_flashdata('error', 'You Have Already Pay For This Study Material... !');
			return redirect(base_url("study-material-detail/$purchases_type/$quiz_id"));
		}


		$payment_txn_id = (isset($_GET['paymentId']) && $_GET['paymentId']) ? $_GET['paymentId'] : NULL; 
		$payment_payer_id = (isset($_GET['PayerID']) && $_GET['PayerID']) ? $_GET['PayerID'] : NULL; 
		if(empty($payment_txn_id ) && $payment_payer_id)
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("quiz-pay/payment-mode/$purchases_type/$quiz_id"));
		}


		$paypal_data = $this->paypal->execute_payment($payment_txn_id, $payment_payer_id);
		if($paypal_data['status'] == FALSE)
		{
			$this->session->set_flashdata('error', 'Sorry Paypal Payment Has Been Fail. '.$paypal_data['message']);
			return redirect(base_url("quiz-pay/payment-mode/$purchases_type/$quiz_id"));
		}

	    $this->paypal->execute_payment($payment_txn_id, $payment_payer_id);
	    $payment_exist = $this->Payment_model->check_payment_by_txn_id($payment_txn_id);
	    if($payment_exist)
	    {
	    	$this->session->set_flashdata('error', 'Payment Has Already Done With This Transaction No !');
	    	$exist_payment_id = $payment_exist->id;
	    	return redirect(base_url("paypal/quiz-pay/payment-status/$purchases_type/$quiz_id/$exist_payment_id"));
	    }

	    $get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
		}
		
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

		
		else
		{
			
		}

	    $payment_data = array();
	    $payment_data['user_id'] = $this->user['id'];
	    $payment_data['payer_id'] = $payment_payer_id;
	    $payment_data['quiz_id'] = $quiz_id;
	    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
	    $payment_data['email'] = $this->user['email'];
	    $payment_data['item_name'] = $item_data->title;
	    $payment_data['item_price'] = $item_price;
	    $payment_data['item_price_currency'] = $this->settings->paid_currency;
	    $payment_data['txn_id'] = $payment_txn_id;
	    $payment_data['payment_status'] = 'succeeded';
	    $payment_data['created'] = date("Y-m-d H:i:s");
	    $payment_data['modified'] = date("Y-m-d H:i:s");
	    $payment_data['payment_gateway'] = 'paypal';
	    $payment_data['invoice_no'] = $invoice_no;
		$payment_data['purchases_type'] = $purchases_type;
		$payment_data['coupon_id'] = $coupon_id;
		$payment_data['coupon_code'] = $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL;
		$payment_data['discount_value'] = $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL;

	    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);

	    if($payment_id)
		{

			if($purchases_type == 'membership')
			{
				$user_membership_data = array();
				$check_user_wise_membership = $this->Payment_model->get_user_wise_membership($this->user['id'],$item_data->id);


				$user_membership_id = NULL;
				$user_pay_for_membership_id = $item_data->id;
				$membership_category_id = $item_data->category_id;
				
				if($membership_category_id == 0)
				{
					if($check_user_wise_membership && $check_user_wise_membership->validity >= date('Y-m-d'))
					{
						$user_membership_data['user_id'] = $this->user['id'];	
						$user_membership_data['membership_id'] = $item_data->id;	
						$user_membership_data['payment_id'] = $payment_id;
						$user_membership_data['category_id'] = 0;

						$validity_date = $check_user_wise_membership->validity;
						$current_date = date('Y-m-d');
						
					    $date1 = date("Y-m-d",strtotime($validity_date));
						$date2 = date("Y-m-d",strtotime($current_date));
						$diff = abs(strtotime($date1) - strtotime($date2));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						$validity = date('Y-m-d', strtotime($current_date. "+".$item_data->duration."days"."+".$days."days"));
						$user_membership_data['validity'] = $validity;		
						$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);	
					}
					else
					{
						$user_membership_data['user_id'] = $this->user['id'];	
						$user_membership_data['membership_id'] = $item_data->id;	
						$user_membership_data['payment_id'] = $payment_id;
						$user_membership_data['category_id'] = 0;
						$validity = date('Y-m-d', strtotime("+".$item_data->duration."days"));
						$user_membership_data['validity'] = $validity;		
						$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
					}
				}
				else
				{
					$user_category_membership = $this->Payment_model->get_user_category_membership($this->user['id'],$membership_category_id);

					if($user_category_membership && $user_category_membership->validity >= date('Y-m-d'))
					{
						$user_membership_data['user_id'] = $this->user['id'];	
						$user_membership_data['membership_id'] = $item_data->id;	
						$user_membership_data['payment_id'] = $payment_id;
						$user_membership_data['category_id'] = $membership_category_id;

						$validity_date = $user_category_membership->validity;
						$current_date = date('Y-m-d');
						
					    $date1 = date("Y-m-d",strtotime($validity_date));
						$date2 = date("Y-m-d",strtotime($current_date));
						$diff = abs(strtotime($date1) - strtotime($date2));

						$years = floor($diff / (365*60*60*24));
						$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
						$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

						$validity = date('Y-m-d', strtotime($current_date. "+".$item_data->duration."days"."+".$days."days"));
						$user_membership_data['validity'] = $validity;		
						$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);	
					}
					else
					{
						$user_membership_data['user_id'] = $this->user['id'];	
						$user_membership_data['membership_id'] = $item_data->id;	
						$user_membership_data['payment_id'] = $payment_id;
						$user_membership_data['category_id'] = $membership_category_id;
						$validity = date('Y-m-d', strtotime("+".$item_data->duration."days"));
						$user_membership_data['validity'] = $validity;		
						$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
					}


				}
			}

		
			$logged_in_user_data = $this->session->userdata('logged_in');
			$email_template = get_email_template('payment_success');
			$full_name = $logged_in_user_data['first_name']." ".$logged_in_user_data['last_name'];
			$email_msg = "";
			$mail_subject = "";
			if($email_template)
			{
				$email_msg = str_replace('{customer_full_name}',$full_name,$email_template->description);
                $email_msg = str_replace('{purchaged_item_name}',$purchases_type,$email_msg);
                $email_msg = str_replace('{item_name}',$item_data->title,$email_msg);
				$email_msg = str_replace("{your_site_name}",$this->settings->site_name,$email_msg);
                $email_msg = str_replace("{site_name_with_url}",'<a href="'.base_url().'">'.$this->settings->site_name.'</a>',$email_msg);
                $mail_subject = ucfirst($purchases_type)." ".$email_template->subject;
	        }
	        else
	        {
	        	$mail_subject = "Payment Recived For ".ucfirst($purchases_type);
	        	$email_msg = "Hello ".$full_name." We Have Recived Payment For ".ucfirst($purchases_type)." On ".$this->settings->site_name;
	        }

			$mail_to = $logged_in_user_data['email'];
            $recipet_name = $logged_in_user_data['first_name'];
            $this->load->library('SendMail');
            $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
        	$mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
        	if($mail_status)
            {
            	$this->session->set_flashdata('message', lang("Congratulation for purchased item"));
            }
            else
            {
            	$this->session->set_flashdata('error', lang('Payment Paid successfully ... Mail Send Error..!'));
            }
            return redirect(base_url("paypal/quiz-pay/payment-status/$purchases_type/$quiz_id/$payment_id"));
    	}
    	else
    	{
    		$this->session->set_flashdata('error', lang('Error During Inserting Payment Details ... !'));
    		return redirect(base_url());
    	}	

	}

	//After Payment we will be redirected to this function for common view success or fail
	public function paypal_payment_view($purchases_type, $quiz_id, $payment_id)
	{

		$payment_data = $this->Payment_model->get_paypal_payment_by_id($purchases_type,$payment_id);
		if(empty($payment_data))
		{
			$this->session->set_flashdata('error', lang('Invalid Uri Arguments... !'));
			return redirect(base_url());
		}

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
			$this->session->set_flashdata('error', lang('Invalid Uri Arguments... !'));
			return redirect(base_url());
		}

		
        $this->set_title(sprintf(lang('Payment Success '), $this->settings->site_name));
        $content_data = array('Page_message' => lang('Payment Success '), 'page_title' => lang('Payment Success'),'quiz_id' => $quiz_id, 'payment_id' => $payment_id, 'item_data' => $item_data, 'payment_data' => $payment_data,'purchases_type'=>$purchases_type,);

        $data = $this->includes;

        if($payment_data->payment_status == 'succeeded')
        {
        	$data['content'] = $this->load->view('paypal_success', $content_data, TRUE);
        }
        else
        {
        	$data['content'] = $this->load->view('paypal_error', $content_data, TRUE);
        }
        
        $this->load->view($this->template, $data);
	}

	//for free membership payment goes from here 
	private function free_membership_payment($purchases_type,$quiz_id,$membership_data)
	{
		if(empty($membership_data) && $purchases_type != 'membership')
		{
			$this->session->set_flashdata('error', 'Someting Went Wrong !');
			return redirect(base_url("membership"));
		}


	    $get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
	    }

	    $payment_data = array();
	    $payment_data['user_id'] = $this->user['id'];
	    $payment_data['payer_id'] = NULL;
	    $payment_data['quiz_id'] = $quiz_id;
	    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
	    $payment_data['email'] = $this->user['email'];
	    $payment_data['item_name'] = $membership_data->title;
	    $payment_data['item_price'] = $membership_data->price;
	    $payment_data['item_price_currency'] = $this->settings->paid_currency;
	    $payment_data['txn_id'] = NULL;
	    $payment_data['payment_status'] = 'succeeded';
	    $payment_data['created'] = date("Y-m-d H:i:s");
	    $payment_data['modified'] = date("Y-m-d H:i:s");
	    $payment_data['payment_gateway'] = 'none because free membership';
	    $payment_data['invoice_no'] = $invoice_no;
	    $payment_data['purchases_type'] = $purchases_type;
		
	    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);

	   	if(empty($payment_id))
		{
			$this->session->set_flashdata('error', 'Error During Inserting Payment Details ... !');
    		return redirect(base_url('membership'));
		}

		$user_membership_data = array();
		$user_membership_data['user_id'] = $this->user['id'];	
		$user_membership_data['membership_id'] = $membership_data->id;	
		$user_membership_data['payment_id'] = $payment_id;
		$user_membership_data['category_id'] = $membership_data->category_id;
		$validity = date('Y-m-d');
		$user_membership_data['validity'] = $validity;		
		$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);

		if(empty($user_membership_id))
		{
			$this->session->set_flashdata('error', 'Payment Details Inserted ...Error During Inserting User Membership  Details ... !');
    		return redirect(base_url('membership'));
		}



		$logged_in_user_data = $this->session->userdata('logged_in');
		$email_template = get_email_template('payment_success');
		$full_name = $logged_in_user_data['first_name']." ".$logged_in_user_data['last_name'];
		$email_msg = "";
		$mail_subject = "";

		if($email_template)
		{
			$email_msg = str_replace('{firstname}',$logged_in_user_data['first_name'],$email_template->description);
            $email_msg = str_replace('{lastname}',$logged_in_user_data['last_name'],$email_msg);
            $email_msg = str_replace('{email}',$logged_in_user_data['email'],$email_msg);
            $email_msg = str_replace('{item_type}',$purchases_type,$email_msg);
            $email_msg = str_replace('{item_name}',$membership_data->title,$email_msg);

            $mail_subject = ucfirst($purchases_type)." ".$email_template->subject;
        }
        else
        {
        	$mail_subject = "Payment Recived For ".ucfirst($purchases_type);
        	$email_msg = "Hello ".$full_name." We Have Recived Payment For ".ucfirst($purchases_type)." On ".$this->settings->site_name;
        }

		$mail_to = $logged_in_user_data['email'];
        $recipet_name = $logged_in_user_data['first_name'];
        $this->load->library('SendMail');
        $mail_html = $this->load->view('email',array('html' => $email_msg,'subject' => $mail_subject),true);
    	$mail_status = $this->sendmail->sendTo($mail_to, $mail_subject, $recipet_name, $mail_html);
    	if($mail_status)
        {
        	$this->session->set_flashdata('message', "Congratulation for purchased item");
        }
        else
        {
        	$this->session->set_flashdata('error', 'Payment Paid successfully ... Mail Send Error..!');
        }
        return redirect(base_url("paypal/quiz-pay/payment-status/$purchases_type/$quiz_id/$payment_id"));
    }

	public function save_bank_transfer()
	{
		$response['msg'] = '';
		$response['status'] = 'error';

	    $get_max_invoice = $this->Payment_model->find_max_invoice_no();
	    $invoice_no = get_admin_setting('invoice_start_number')+1;
	    if($get_max_invoice)
	    {
	    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
	    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
	    }
		

		$user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		if(empty($user_id))
		{
			$response['msg'] =  lang('please_login_first');
			echo json_encode($response);
			exit;
		}

		$transaction_no = (isset($_POST['transaction_no']) && $_POST['transaction_no']) ? $_POST['transaction_no'] : NULL ;
		$quiz_id = (isset($_POST['quiz_id']) && $_POST['quiz_id']) ? $_POST['quiz_id'] : NULL ;
		$purchases_type = (isset($_POST['purchases_type']) && $_POST['purchases_type']) ? $_POST['purchases_type'] : NULL ;
		$purchases_type = trim($purchases_type);

		if(empty($transaction_no ) OR empty($quiz_id) OR empty($purchases_type))
		{
			$response['msg'] =  lang('required_information_is_missing');
			echo json_encode($response);
			exit;
		}


		if($purchases_type !="quiz" && $purchases_type !='membership')
		{
			$response['msg'] =  lang('payment_for_invalid_item');
			echo json_encode($response);
			
			exit;
		}

		$item_data = FALSE;

		if($purchases_type =="quiz")
		{
			$item_data = $this->db->where('id',$quiz_id)->get('quizes')->row();
		}

		if($purchases_type == 'membership')
		{
			$item_data = $this->db->select('title, amount as price')->where('id',$quiz_id)->get('membership')->row();

		}

		if(empty($item_data))
		{
			$response['msg'] =  lang('someting_went_wrong');
			echo json_encode($response);
			exit;
		}

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
		
		

		$transaction_data = array();
		$transaction_data['user_id'] = $user_id;
		$transaction_data['quiz_id'] = $quiz_id;
		$transaction_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
		$transaction_data['email'] = $this->user['email'];
		$transaction_data['item_name'] = $item_data->title;
		$transaction_data['item_price'] = $item_price;
		$transaction_data['item_price_currency'] = $this->settings->paid_currency;
		$transaction_data['payment_status'] = 'pending';
		$transaction_data['created'] = date("Y-m-d H:i:s");
		$transaction_data['modified'] = date("Y-m-d H:i:s");
		$transaction_data['payment_gateway'] = 'bank-transfer';
		$transaction_data['token_no'] = $transaction_no;
		$transaction_data['invoice_no'] = $invoice_no;
		$transaction_data['purchases_type'] = $purchases_type;
		$payment_data['coupon_id'] = $coupon_id;
		$payment_data['coupon_code'] = $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL;
		$payment_data['discount_value'] = $this->session->coupon_data['discount_price'] ? $this->session->coupon_data['discount_price'] : NULL;
		
		$inserted_id = $this->Payment_model->insertTransaction($transaction_data);

		if($inserted_id)
		{
			$this->session->set_flashdata('message', lang('bank_transfer_added_successfully'));
			$response['msg'] = 	lang('bank_transfer_added_successfully');
			$response['status'] = 'success';
		}
		else
		{
			$response['msg'] = 	lang('bank_transfer_insert_error');
		}

		echo json_encode($response);
		exit;
	}

	function update_bank_transfer()
	{
		
		$response['msg'] = '';
		$response['status'] = 'error';
		
		$user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		if(empty($user_id))
		{
			$response['msg'] =  lang('please_login_first');
			echo json_encode($response);
			exit;
		}

		$transaction_no = (isset($_POST['transaction_no']) && $_POST['transaction_no']) ? $_POST['transaction_no'] : NULL ;
		$quiz_id = (isset($_POST['quiz_id']) && $_POST['quiz_id']) ? $_POST['quiz_id'] : NULL ;
		$purchases_type = (isset($_POST['purchases_type']) && $_POST['purchases_type']) ? $_POST['purchases_type'] : NULL ;

		if(empty($transaction_no ) OR empty($quiz_id) OR empty($purchases_type))
		{
			$response['msg'] =  lang('required_information_is_missing');
			echo json_encode($response);
			exit;
		}


		if($purchases_type !="quiz" && $purchases_type !='membership')
		{
			$response['msg'] =  lang('payment_for_invalid_item');
			echo json_encode($response);
			exit;
		}

		$item_data = FALSE;

		if($purchases_type == "quiz")
		{
			$item_data = $this->db->where('id',$quiz_id)->get('quizes')->row();
		}

		if($purchases_type == 'membership')
		{
			$item_data = $this->db->select('title, amount as price')->where('id',$quiz_id)->get('membership')->row();
		}


		if(empty($item_data))
		{
			$response['msg'] =  lang('someting_went_wrong');
			echo json_encode($response);
			exit;
		}

		
		$transaction_data = array();
		$transaction_data['token_no'] = $transaction_no;
		$transaction_data['modified'] = date("Y-m-d H:i:s");
		$update_id = $this->Payment_model->update_bank_transfer_token($quiz_id,$purchases_type,$user_id,$transaction_data);
		if($update_id)
		{
			$this->session->set_flashdata('message', lang('bank_transfer_updated_successfully'));
			$response['msg'] = lang('bank_transfer_updated_successfully');
			$response['status'] = 'success';
		}
		else
		{
			$response['msg'] =  lang('record_update_error');
		}
		echo json_encode($response);
		exit;
	}

	function coupon($purchases_type,$item_id)
	{
		$this->session->unset_userdata('coupon_data');
		$user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		$this->form_validation->set_rules('coupon_code', lang('coupon_code'), 'required|trim');
		if ($this->form_validation->run() == false) 
        {
			$validation_error  = $this->form_validation->error_array('coupon_code');
			$this->session->set_flashdata('error', lang($validation_error['coupon_code']));
        } 
        else 
        {
			$coupon_code = $this->input->post('coupon_code');
			$get_coupon_data = $this->Payment_model->get_coupon_by_coupon_code($coupon_code);

			if($purchases_type == 'quiz')
			{
				$item_data =  $this->Payment_model->get_paid_quiz_by_id($item_id);
			}
			elseif($purchases_type == 'material')
			{
				$item_data = $this->Payment_model->get_paid_material_by_id($item_id);
			}
			elseif($purchases_type == 'membership')
			{
				$item_data = $this->Payment_model->get_paid_membership_by_id($item_id);
			}

			if($get_coupon_data)
			{
				$coupon_data = array();
				if($get_coupon_data->coupon_for == 'users')
				{
					$get_coupon_for_user = $this->Payment_model->get_user_coupon($get_coupon_data->id,$user_id);
					if($get_coupon_for_user)
					{
						if($get_coupon_data->no_of_times_can_be_used > 0)
						{
							$check_how_many_time_coupon_use = $this->Payment_model->get_no_of_time_coupon($get_coupon_data->id,$user_id);
							
							if($check_how_many_time_coupon_use == $get_coupon_data->no_of_times_can_be_used)
							{
								$this->session->set_flashdata('error', lang('you_have_already_use_coupon'));
								redirect(base_url('quiz-pay/payment-mode/'.$purchases_type.'/'.$item_id));
							}

							if($get_coupon_data->coupon_discount_type == 'Percent')
							{
								$item_price = ($item_data->price * $get_coupon_data->discount_value) / 100;
								$discount_price = round($item_data->price - $item_price); 
							}
							else
							{
								$item_price = $item_data->price - $get_coupon_data->discount_value;
								$discount_price = round($item_price);
							}
						}
					}
					else
					{
						$this->session->set_flashdata('error', lang('this_coupon_not_exixt_for_you'));
						redirect(base_url('quiz-pay/payment-mode/'.$purchases_type.'/'.$item_id));
					}
					
					$coupon_data['purchases_type'] = 'users';
				}
				else
				{
					if($get_coupon_data->coupon_for == 'all')
					{
						$check_coupon_other = $this->Payment_model->get_coupon_for_other($get_coupon_data->id,$get_coupon_data->coupon_for,$item_id);

					}
					else
					{
						$check_coupon_other = $this->Payment_model->get_coupon_for_other($get_coupon_data->id,$purchases_type,$item_id);
					}
					
					if($check_coupon_other)
					{
						$coupon_expiry_date = date('Y-m-d',strtotime($check_coupon_other->expiry_date));
						
						if($coupon_expiry_date >= date("Y-m-d"))
						{
							if($check_coupon_other->no_of_times_can_be_used > 0)
							{

								$check_how_many_time_coupon_use = $this->Payment_model->get_no_of_time_coupon($check_coupon_other->id,$user_id);
								
								if($check_how_many_time_coupon_use == $check_coupon_other->no_of_times_can_be_used)
								{
									$this->session->set_flashdata('error', lang('you_have_already_use_coupon'));
									redirect(base_url('quiz-pay/payment-mode/'.$purchases_type.'/'.$item_id));
								}
								
							}
							
							if($check_coupon_other->coupon_discount_type == 'Percent')
							{

								$item_price = ($item_data->price * $check_coupon_other->discount_value) / 100;
								$discount_price = round($item_data->price - $item_price); 
							}
							else
							{
								$item_price = $item_data->price - $check_coupon_other->discount_value;
								$discount_price = round($item_price);
							}
							
						}
						else
						{
							$this->session->set_flashdata('error', lang('this_coupon_is_expire'));
						}
						$coupon_data['purchases_type'] = $purchases_type;
					}
					else
					{
						$this->session->set_flashdata('error', lang('this_coupon_not_exist_for_this_item'));
						redirect(base_url('quiz-pay/payment-mode/'.$purchases_type.'/'.$item_id));
					}
				}

				$coupon_data['discount_price'] = $discount_price;
				$coupon_data['coupon_id'] = $get_coupon_data->id;
				$coupon_data['coupon_code'] = $coupon_code;
				$coupon_data['item_id'] = $item_data->id;
				$coupon_data['user_id'] = $user_id;
				
				$this->session->set_userdata('coupon_data', $coupon_data);
				$this->session->set_flashdata('message', lang('coupon_apply_successfully'));
			}
			else
			{
				$this->session->set_flashdata('error', lang('coupon_code_not_exist'));
			}
		}
		redirect(base_url('quiz-pay/payment-mode/'.$purchases_type.'/'.$item_id));
	}

	function enroll_now($purchases_type,$item_id)
	{
		$user_id = isset($this->user['id']) ? $this->user['id'] : NULL;
		
		if($this->session->coupon_data['discount_price'] > 0)
		{
			$this->session->set_flashdata('error', lang('you_can_not_access_this'));
			return redirect(base_url());
		}

		if($this->session->coupon_data)
		{
			if($purchases_type == 'quiz')
			{
				$item_data =  $this->Payment_model->get_paid_quiz_by_id($item_id);
			}
			elseif($purchases_type == 'material')
			{
				$item_data = $this->Payment_model->get_paid_material_by_id($item_id);
			}
			elseif($purchases_type == 'membership')
			{
				$item_data = $this->Payment_model->get_paid_membership_by_id($item_id);
			}

			$coupon_id = $this->session->coupon_data['coupon_id'] ? $this->session->coupon_data['coupon_id'] : NULL;
			$coupon_purchases_type = ($this->session->coupon_data['purchases_type'] == 'users') ? $this->session->coupon_data['purchases_type'] : $purchases_type;
			if($this->session->coupon_data['user_id'] == $user_id && $this->session->coupon_data['item_id'] == $item_data->id && $this->session->coupon_data['purchases_type'] == $coupon_purchases_type)
			{
				$item_price = $this->session->coupon_data['discount_price'];
			}
			
			$get_max_invoice = $this->Payment_model->find_max_invoice_no();
		    $invoice_no = get_admin_setting('invoice_start_number')+1;
		    if($get_max_invoice)
		    {
		    	$max_nvoice_no = $get_max_invoice[0]->invoice_no+1;
		    	$invoice_no = $max_nvoice_no < $invoice_no ? $invoice_no : $max_nvoice_no;		    	
			}

			$payment_data = array();
		    $payment_data['user_id'] = $this->user['id'];
		    $payment_data['payer_id'] = NULL;
		    $payment_data['quiz_id'] = $item_id;
		    $payment_data['name'] = $this->user['first_name'].' '.$this->user['last_name'];
		    $payment_data['email'] = $this->user['email'];
		    $payment_data['item_name'] = $item_data->title;
		    $payment_data['item_price'] = $item_price;
		    $payment_data['item_price_currency'] = $this->settings->paid_currency;
		    $payment_data['txn_id'] = NULL;
		    $payment_data['payment_status'] = 'succeeded';
		    $payment_data['created'] = date("Y-m-d H:i:s");
		    $payment_data['modified'] = date("Y-m-d H:i:s");
		    $payment_data['payment_gateway'] = 'coupon applied with 100% discount';
		    $payment_data['invoice_no'] = $invoice_no;
			$payment_data['purchases_type'] = $purchases_type;
			$payment_data['coupon_id'] = $coupon_id;
			$payment_data['coupon_code'] = $this->session->coupon_data['coupon_code'] ? $this->session->coupon_data['coupon_code'] : NULL;
			$payment_data['discount_value'] = $this->session->coupon_data['discount_price'];

		    $payment_id = $this->Payment_model->insert_paypal_detail($payment_data);

		    if($payment_id)
			{
				if($purchases_type == 'membership')
				{
					$user_membership_data = array();
					$check_user_wise_membership = $this->Payment_model->get_user_wise_membership($this->user['id'],$item_data->id);
					$user_membership_id = NULL;
					$user_pay_for_membership_id = $item_data->id;
					$membership_category_id = $item_data->category_id;
					
					if($membership_category_id == 0)
					{
						if($check_user_wise_membership && $check_user_wise_membership->validity >= date('Y-m-d'))
						{
							$user_membership_data['user_id'] = $this->user['id'];	
							$user_membership_data['membership_id'] = $item_data->id;	
							$user_membership_data['payment_id'] = $payment_id;
							$user_membership_data['category_id'] = 0;

							$validity_date = $check_user_wise_membership->validity;
							$current_date = date('Y-m-d');
							
						    $date1 = date("Y-m-d",strtotime($validity_date));
							$date2 = date("Y-m-d",strtotime($current_date));
							$diff = abs(strtotime($date1) - strtotime($date2));

							$years = floor($diff / (365*60*60*24));
							$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
							$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

							$validity = date('Y-m-d', strtotime($current_date. "+".$item_data->duration."days"."+".$days."days"));
							$user_membership_data['validity'] = $validity;		
							$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);	
						}
						else
						{
							$user_membership_data['user_id'] = $this->user['id'];	
							$user_membership_data['membership_id'] = $item_data->id;	
							$user_membership_data['payment_id'] = $payment_id;
							$user_membership_data['category_id'] = 0;
							$validity = date('Y-m-d', strtotime("+".$item_data->duration."days"));
							$user_membership_data['validity'] = $validity;		
							$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
						}
					}
					else
					{
						$user_category_membership = $this->Payment_model->get_user_category_membership($this->user['id'],$membership_category_id);

						if($user_category_membership && $user_category_membership->validity >= date('Y-m-d'))
						{
							$user_membership_data['user_id'] = $this->user['id'];	
							$user_membership_data['membership_id'] = $item_data->id;	
							$user_membership_data['payment_id'] = $payment_id;
							$user_membership_data['category_id'] = $membership_category_id;

							$validity_date = $user_category_membership->validity;
							$current_date = date('Y-m-d');
							
						    $date1 = date("Y-m-d",strtotime($validity_date));
							$date2 = date("Y-m-d",strtotime($current_date));
							$diff = abs(strtotime($date1) - strtotime($date2));

							$years = floor($diff / (365*60*60*24));
							$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
							$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));

							$validity = date('Y-m-d', strtotime($current_date. "+".$item_data->duration."days"."+".$days."days"));
							$user_membership_data['validity'] = $validity;		
							$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);	
						}
						else
						{
							$user_membership_data['user_id'] = $this->user['id'];	
							$user_membership_data['membership_id'] = $item_data->id;	
							$user_membership_data['payment_id'] = $payment_id;
							$user_membership_data['category_id'] = $membership_category_id;
							$validity = date('Y-m-d', strtotime("+".$item_data->duration."days"));
							$user_membership_data['validity'] = $validity;		
							$user_membership_id = $this->Payment_model->insert_user_membership_payment_detail($user_membership_data);
						}


					}
				}
				$this->session->unset_userdata('coupon_data');
	            $this->session->set_flashdata('message', lang("Congratulation for purchased item"));
	            
	            return redirect(base_url("paypal/quiz-pay/payment-status/$purchases_type/$item_id/$payment_id"));
			}
			else
	    	{
	    		$this->session->set_flashdata('error', lang('Error During Inserting Payment Details ... !'));
	    		return redirect(base_url());
	    	}
			

			
		}
		else
		{
			$this->session->set_flashdata('error', lang('you_can_not_access_this'));
			return redirect(base_url());
		}
	}

}