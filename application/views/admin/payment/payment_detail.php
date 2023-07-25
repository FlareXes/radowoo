<div class="row">
    <?php //p($payment_data); ?>
    <div class="col-md-12 col-xl-12 col-sm-12 mt-3">
            <?php 
                if($payment_data->payment_status == 'succeeded')
                {
                    $status = 'btn btn-success';
                }
                elseif($payment_data->payment_status == 'fail')
                {
                    $status = 'btn btn-danger';
                }
                elseif($payment_data->payment_status == 'pending')
                {
                    $status = 'btn btn-warning';
                }
                else
                {
                    $status = '';   
                }
            ?>
            Payment Status: <div class="<?php echo $status;?>"><?php echo $payment_data->payment_status;?></div>
    </div>
    <hr class="w-100">

    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Payment Method:</label> <span class=""><?php echo $payment_data->payment_gateway;?></span>
        </div> 
    </div>
    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Payment For :</label> <span class=""><?php echo ucfirst($payment_data->purchases_type);?></span>
        </div> 
    </div>

    <hr class="w-100">
    
    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Item Name:</label> <span class=""><?php echo $payment_data->item_name;?></span>
        </div>
    </div>
    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Item Price:</label> <span class=""><?php echo $payment_data->item_price;?></span>
        </div> 
    </div>
    <hr class="w-100">
    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Payment Date:</label> <span class=""><?php echo get_date_or_time_formate($payment_data->created);?></span> 
        </div> 
    </div>
    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Email:</label> <span class=""><?php echo $payment_data->email;?></span>
        </div>
    </div>
    <hr class="w-100">
    <?php if(isset($payment_data->txn_id) && !empty($payment_data->txn_id)){ ?>
        <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
            <div>
                <label>Txn Id:</label> <span class=""><?php echo $payment_data->txn_id;?></span>
            </div> 
        </div>
    <?php } ?>
    <?php if(isset($payment_data->token_no) && !empty($payment_data->token_no)){ ?>
        <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
            <div>
                <label>Token No:</label> <span class=""><?php echo $payment_data->token_no;?></span>
            </div> 
        </div>
    <?php } ?>
    <div class="col-md-6 col-xl-6 col-sm-12 mt-3">
        <div>
            <label>Name:</label> <span class=""><?php echo $payment_data->first_name.' '.$payment_data->last_name;?></span>
        </div>
    </div>
</div>    