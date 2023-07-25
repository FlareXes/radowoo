<div class="container">
	<div class="row">
	    <div class="col-12 text-center"> <h2 class="heading"><?php echo lang('admin_membership'); ?></h2> 
	    	<hr>
	    </div>

	    <?php 
	    if($user_membership_history && $user_id > 0)
	    { 
	    	?>

		    <div class="col-12 mb-5">
		    	<h4 class=" bg-warning text-center w-100 text-white m-0 p-5">Hello <span class="text-danger"> <?php echo $this->user['first_name']; ?> <?php echo $this->user['last_name']; ?> </span> Your Memberships is Valid Upto <span class="text-danger"><?php echo date("D d - m - Y",strtotime($user_membership_history->validity)) ?> </span> </h4>
		    </div>

	    	<?php
	    }
	    ?>

	    <div class="clearfix"></div>
	    <?php if($membership_data) 
	    {  

	    	   foreach($membership_data as $membership_key => $membership_value)
	    	   {
	    	   	if($membership_value->amount > 0)
	    	   	{
	    	   		$membership_url = base_url("quiz-pay/payment-mode/membership/$membership_value->id");	
	    	   	}
	    	   	
	    ?>
		    <div class="col-md-4 col-sm-6 mb-5">
	        	<div class="pricingTable blue">
	            	<div class="pricingTable-header">
	            		<h3 class="title"><?php echo $membership_value->title?> </h3>
	            		
	            			<span class="text-uppercase small font-weight-bold mb-2"><?php echo $membership_value->category_name ? "( ".$membership_value->category_name." )" : " &nbsp;" ;?></span>
	            			

	            	</div>
	            	<div class="price-value">
	                    <span class="currency"><?php echo get_admin_setting('paid_currency');?></span>
	                    <span class="amount"><?php echo $membership_value->amount?></span>
	                </div>
	                <ul class="pricing-content">
	                    <li><?php echo $membership_value->duration .' Days'?></li>
	                    <li><?php echo $membership_value->description?></li>
	                </ul>
	                <?php 
	                if(isset($membership_value->amount) && $membership_value->amount > 0) 
	                { ?>
		                <div class="pricingTable-signup">
		                    <a href="<?php echo $membership_url;?>"><?php echo lang('buy_now');?></a>
		                </div>
		            <?php 
		        	}
		        	else
		        	{
		        		?>
		                <div class="pricingTable-signup">
		                    <a href="javascript:void(0)" class="disabled" style="pointer-events: none; cursor: default;" disabled><?php echo lang('buy_now');?></a>
		                </div>

		        		<?php
		        	} ?>    
	            </div>
	        </div>
	    <?php }  } ?>    
	</div>
</div>