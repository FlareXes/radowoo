<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid pt-5 body_background">
	<div class="container">
    	<div class="row">
    		<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
	            <div class="card card-primary">
	               <div class="card-header">
	                  <h4><?php echo lang('login_by_otp');?></h4>
	               </div>
	               <div class="card-body">
	               		<?php echo form_open('', array('class'=>'form-signin formlogin')); ?>
	               			
	               			<div class="form-group">
	               				<?php echo form_label(lang('mobileno')); ?>
	               				<span class="required">*</span> 
			                    <?php echo form_input(array('name'=>'mobile_no', 'id'=>'mobile_no', 'class'=>'form-control', 'placeholder'=>lang('user_mobileno'))); ?>
			                    <span class="text-warning"><?php echo lang('enter_only_ten_digit_mobile_number');?></span><br/>
			                    <span class="small text-danger"> <?php echo strip_tags(form_error('mobile_no')); ?> </span>
	               			</div>
	               			<div class="form-group">
		                     	<?php echo form_submit(array('name'=>'submit', 'class'=>'btn btn-primary btn-lg btn-block'), lang('send_otp')); ?>
		                  	</div>
	               		<?php echo form_close();?>
	               </div>
	    		</div>
  			</div>
		</div>
	</div>
</div>