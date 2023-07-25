<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>
<div class="container">
	<div class="row">	
        <div class="col-md-4"> </div>
        <div class="col-md-4">  
            <div class="card mt-5">
                <div class="card-header bg-success text-white"><?php echo $item_data->title; ?> (<?php echo $this->settings->paid_currency ." ". round($item_price); ?>)</div>
                <?php if($item_price > 0) { ?>
                  <div class="card-body bg-light">
                      <?php 
                      if(!empty($this->settings->paypal_key) && !empty($this->settings->paypal_secret_key) && !empty($this->settings->paid_currency))
                      { ?>

                          <div class="mb-3">
                              <?php echo form_open(base_url("paypal/payment/quiz-pay/$purchases_type/$quiz_id"), array('role'=>'form')); ?>
                                  <input type="hidden" name="plan_name" value="<?php echo $item_data->title; ?>" /> 
                                  <input type="hidden" name="plan_description" value="<?php echo strip_tags($item_data->description); ?>" />
                                  <input type="submit" name="subscribe" value="<?php echo lang('paypal_payment_getway') ?>" class="btn-subscribe btn btn-primary btn-block" />
                              <?php echo form_close(); ?>
                          </div>

                          <?php 
                      } ?>

                      <?php     
                      if(!empty($this->settings->stripe_key) && !empty($this->settings->stripe_secret_key) && !empty($this->settings->paid_currency))
                      { ?> 
                          <span class="btn btn-block btn-warning mb-3" data-toggle="modal" data-target="#mystripeModal" title="<?php echo lang('stripe_payment_getway'); ?>"><?php echo lang('stripe_payment_getway'); ?></span>       
                          <!-- <a href="<?php echo base_url("stripe/pay-now/$purchases_type/$quiz_id"); ?>" class="btn   btn-block btn-warning mb-3"><?php echo lang('stripe_payment_getway'); ?></a>   --> 
                          <?php 
                      }   ?>

                      <?php
                      if($this->settings->instamojo_apikey && $this->settings->instamojo_token && $this->settings->paid_currency && $this->settings->paid_currency == "INR")
                      
                      { ?>
                          <a href="<?php echo base_url("instamojo/$purchases_type/$quiz_id"); ?>" class="btn btn-block btn-dark mb-3"><?php echo lang('instamojo_payment_getway'); ?></a>
                          <?php 
                      } ?> 

                      <?php
                      if(!empty($this->settings->razorpay_key) && !empty($this->settings->razorpay_secret_key) && !empty($this->settings->paid_currency))
                      { ?>
                          <a href="<?php echo base_url("razorpay/checkout/$purchases_type/$quiz_id"); ?>" class="btn btn-block btn-danger mb-3"><?php echo lang('razorpay_payment_getway'); ?></a>  
                          <?php 
                      } ?>


                      <?php

                      if(!empty(strip_tags($this->settings->bank_transfer)) && !empty($this->settings->paid_currency)) 
                      { ?>
                          <span class="btn btn-block btn-info mb-3" data-toggle="modal" data-target="#myModal" title="<?php echo lang('pay_by_bank_transfer'); ?>"><?php echo lang('pay_by_bank_transfer'); ?></span>
                          <?php 
                      } ?>
                      <?php echo form_open_multipart(base_url('coupon-apply/coupon/'.$purchases_type.'/'.$item_data->id), array('role'=>'form','novalidate'=>'novalidate')); ?>
                        <input type="text" name="coupon_code" value="" placeholder="Enter coupon code here" class="form-control coupon" required>
                        <input type="submit" class="btn btn-dark my-3 d-block w-100 " style="margin:0px auto;" value="<?php echo lang('apply_coupon'); ?>">
                      <?php echo form_close(); ?>  

                  </div>
                <?php } else { ?>  
                  <div class="card-body bg-light">
                    <div class="mb-3">
                      <?php echo form_open(base_url("payment/enroll-pay/$purchases_type/$quiz_id"), array('role'=>'form')); ?>
                          <input type="hidden" name="plan_name" value="<?php echo $item_data->title; ?>" /> 
                          <input type="hidden" name="plan_description" value="<?php echo strip_tags($item_data->description); ?>" />
                          <input type="submit" name="enrollnow" value="<?php echo lang('enroll_now') ?>" class="btn-subscribe btn btn-warning btn-block" />
                      <?php echo form_close(); ?>
                    </div>
                  </div>
                <?php } ?>  
            </div>      
        </div>

        <div class="col-md-4"> </div>
        <div class="clearfix"></div>
    </div>
</div> 





<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Bank Transfer Detail</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

       <?php //echo form_open('',array('role'=>'form')); ?>  

            <div class="modal-body">
                <?php echo $this->settings->bank_transfer;

                ?>   
                  <div class="form-group">
                    <label for="transaction-no" class="col-form-label">Reference No / Transaction No.:</label>
                    
                    <?php $token_value = (isset($payment_pending_status->token_no) && !empty($payment_pending_status->token_no) ? $payment_pending_status->token_no : "");?>
                    <input type="text" class="form-control" name="transaction_no" id="transaction-no" value="<?php echo $token_value;?>" placeholder="Enter your transaction/reference number">
                    <span class="bank text-danger"></span>
                    <input type="hidden" class="quiz_id" value="<?php echo $quiz_id;?>">
                    <input type="hidden" class="purchases_type" value="<?php echo $purchases_type;?>">
                    <input type="hidden" class="item-price" value="<?php echo $item_price;?>">
                  </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <?php $save_update = (isset($token_value) && !empty($token_value) ? 'update-data' : 'save-data');?>
                <input type="submit" name="save" class="btn btn-primary <?php echo $save_update;?>">
            </div>

      <?php //echo form_close();?>    

    </div>
  </div>
</div>


<div class="modal fade" id="mystripeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Billing Address</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <?php echo form_open(base_url('stripe/pay-now/'.$purchases_type.'/'.$quiz_id),array('role'=>'form')); ?>  
        <div class="modal-body">
          <div class="form-group">
            <label for="transaction-no" class="col-form-label"><?php echo lang('address'); ?>*</label>        
            <input type="text" name="address" class="form-control" placeholder="enter your address" value="<?php echo set_value('address'); ?>" required />
          </div>

          <div class="form-group">
            <label for="transaction-no" class="col-form-label"><?php echo lang('city'); ?>*</label>        
            <input type="text" name="city" class="form-control" placeholder="enter your city" value="<?php echo set_value('city'); ?>" required />
          </div>

          <div class="form-group">
            <label for="transaction-no" class="col-form-label"><?php echo lang('state'); ?>*</label>        
            <input type="text" name="state" class="form-control" placeholder="enter your state" value="<?php echo set_value('state'); ?>" required />
          </div>

          <div class="form-group">
            <label for="transaction-no" class="col-form-label"><?php echo lang('country'); ?>*</label>          
            <input type="text" name="country" class="form-control" placeholder="enter your country" value="<?php echo set_value('country'); ?>" required />
          </div>

          <div class="form-group">
            <label for="transaction-no" class="col-form-label"><?php echo lang('postal_code'); ?>*</label>          
            <input type="number" name="postal_code" class="form-control" placeholder="enter your postal code" value="<?php echo set_value('postal_code'); ?>" required />
          </div>        
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            <?php $save_update = (isset($token_value) && !empty($token_value) ? 'update-data' : 'save-data');?>
            <input type="submit" name="save" class="btn btn-primary <?php echo lang('save');?>">
        </div>
      <?php echo form_close(); ?>   
    </div>
  </div>          
</div>   