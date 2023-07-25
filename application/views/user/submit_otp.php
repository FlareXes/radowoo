<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid pt-5 body_background">
	<div class="container">
    	<div class="row">
    		<div class="col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
    			<div class="card card-primary">
    				<div class="card-header">
	                  <h4><?php echo lang('otp_detail');?></h4>
	               	</div>
	               	<div class="card-body">
	               		<?php echo form_open('', array('class'=>'form-signin formlogin'));?>
	               			<?php if(isset($user_data) && empty($user_data['username'])) { ?>
		               			<div class="form-group">
				                    <?php echo form_label(lang('fullname')); ?>
				                    <span class="required">*</span> 
				                    <?php echo form_input(array('name'=>'full_name', 'id'=>'full_name', 'class'=>'form-control', 'placeholder'=>lang('fullname'), 'maxlength'=>256)); ?>
				                    <span class="small text-danger"> <?php echo strip_tags(form_error('full_name')); ?> </span>
				                </div>
				            <?php } else { ?>
				            	  <label>Hi, <br/><?php echo $user_data['first_name'] .' '. $user_data['last_name'];?></label>  
				            <?php } ?>	  
	               			<div class="form-group">
			                    <?php echo form_label(lang('enter_otp')); ?>
			                    <span class="required">*</span> 
			                    <?php echo form_input(array('name'=>'otp', 'id'=>'otp', 'class'=>'form-control', 'placeholder'=>lang('user_otp_field'), 'maxlength'=>256)); ?>
			                    <span class="small text-danger"> <?php echo strip_tags(form_error('otp')); ?> </span>
			                </div>
			                <div class="form-group">
		                     	<?php echo form_submit(array('name'=>'submit', 'class'=>'btn btn-primary btn-lg btn-block'), lang('core_button_login')); ?>
		                  	</div>
	               		<?php echo form_close();?>
	               	</div>
    			</div>	
    		</div>
    	</div>
    </div>
</div>