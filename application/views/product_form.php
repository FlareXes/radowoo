<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>

<script type="text/javascript" src="//js.stripe.com/v3/"></script>    

<div class="container"> 

    <div class="row">   

        <div class="col-md-4"> </div>
        <div class="col-md-4"> 
            
            <div class="card mt-5"> 
                <div class="card-header bg-success text-white"><?php echo $item_data->title; ?> (<?php echo $this->settings->paid_currency." ". $item_price; ?>)</div>
                <div class="card-body bg-light">
                    <?php if (validation_errors()): ?>
                        <div class="alert alert-danger" role="alert">
                            <strong>Oops!</strong>
                            <?php echo validation_errors() ;?> 
                        </div>  
                    <?php endif ?>
                    <div id="payment-errors"></div>  
                    
                    <form id="payment-form">
                        <input type="hidden" name="client_secret" value="<?php echo $client_secret; ?>">
                        <input type="hidden" name="txn-id" value="<?php echo $txn_id; ?>">
                        <input type="hidden" name="name" value="<?php echo $user_name; ?>">
                        <input type="hidden" name="purchases_type" value="<?php echo $purchases_type; ?>">
                        <input type="hidden" name="quiz_id" value="<?php echo $quiz_id; ?>">

                        <div class="form-group">
                            <input type="text" name="name" class="form-control" placeholder="Name" value="<?php echo set_value('name'); ?>" required>
                        </div>  

                        <div class="form-group">
                            <input type="email" name="email" class="form-control" placeholder="email@you.com" value="<?php echo set_value('email'); ?>" required />
                        </div>

                        

                        <div id="card-element">
                            <div class="form-group">
                                <label class="form-label ml-2">Credit Card Numbder<?php echo lang('credit_card_number');?></label>
                            </div>
                            <!-- Elements will create input elements here -->
                        </div>

                          <!-- We'll put the error messages in this element -->
                          <div id="card-errors" class="text-danger" role="alert"></div>
                       
                        <div class="form-group py-3">
                          <button type="submit" id="submit" class="btn btn-success btn-block">Pay Now</button>
                          <button class="btn btn-dark btn-block" type="reset">Reset</button>
                        </div>
                    </form>
                </div>
            </div>
                 
        </div>

        <div class="col-md-4"> </div>
        <div class="clearfix"></div>
    </div>
</div> 