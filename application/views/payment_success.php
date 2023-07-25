<?php
defined('BASEPATH') OR exit('No direct script access allowed');
?>


<!-- Begin page content -->
<div class="container">
    <div class="row mt-3">
        <!-- <div class="col-sm-4"></div> -->
        <div class="col-4 mx-auto">
            <div class="card mt-5">
                <img class="card-imgs-top" src="https://cdn.dribbble.com/users/411286/screenshots/2619563/desktop_copy.png" alt="Card image cap"  height="250">
                <div class="card-block" style="padding: 20px;">
                    <h4 class="card-title font-weight-bolder"><?php echo $item_data->title.' ('.$payment_data->item_price.'₹)'; ?><?php  $payment_id; ?></h4>
                    <p class="card-text">Hello<span class="text-success"> <?php echo $payment_data->name; ?></span> We received your payment on your purchase, check your email for more information. And Your Transcation Id Is <span class="text-primary"><?php echo $payment_data->txn_id; ?></span></p>
                    <?php 
                        if(isset($payment_data) && $purchases_type == 'quiz')
                        {
                    ?>
                        <a href="<?php echo base_url("instruction/$purchases_type/$quiz_id"); ?>" class="btn btn-info btn-block float-right">Start Quiz Now</a>
                    <?php         
                        }
                        elseif(isset($payment_data) && $purchases_type == 'material')
                        {
                    ?>
                            <a href="<?php echo base_url("study-material-detail/$purchases_type/$quiz_id"); ?>" class="btn btn-info btn-block float-right"><?php echo lang('go_to_study_detail');?></a>
                    <?php }
                          elseif(isset($payment_data) && $purchases_type == 'membership')
                          {
                            ?>
                            <a href="<?php echo base_url("membership"); ?>" class="btn btn-info btn-block float-right"><?php echo lang("goto_membership_page");?></a>
                            <?php
                          }  
                     ?>   
                </div>
            </div>
        </div>
    </div>
</div> 