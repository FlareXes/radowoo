<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<div class="container">

	<div class="row">	

        <div class="col-md-4"> </div>
        <div class="col-md-4"> 
        	<div class="card mt-5">
        		<div class="card-header bg-success text-white"><?php echo $item_data->title; ?> (<?php echo $this->settings->paid_currency." ". round($itemdisplayprice); ?>)</div>
        		<div class="card-body bg-light">
        			
                    <?php if (validation_errors()): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Oops!</strong>
                            <?php echo validation_errors() ;?> 
                        </div>  
                    <?php endif ?>

                    <div id="payment-errors"></div>  
                    <?php echo form_open_multipart(base_url("instamojo/checkout/$purchases_type/$quiz_id"), array('role'=>'form','id'=>'paymentFrm')); ?>
                    	<div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo set_value('name'); ?>" required>
                        </div>

                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="email@you.com" value="<?php echo set_value('email'); ?>" required />
                        </div>
                        <div class="form-group ">
                          <button type="submit" id="payBtn" class="btn btn-success btn-block">Pay Now</button>
                          <button class="btn btn-dark btn-block" type="reset">Reset</button>
                        </div>
                    <?php form_close();?>    
        		</div>
        	</div>
        </div>
        <div class="col-md-4"> </div>
        <div class="clearfix"></div>
    </div>
</div>