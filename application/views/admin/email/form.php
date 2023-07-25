<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="row">  
  	<div class="col-12 col-md-12 col-lg-12">
    	<div class="card">
      		<div class="card-body">
      			<?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
      				<div class="row mt-3">
      					<div class="col-12">
      						<div class="form-group <?php echo form_error('subject') ? ' has-error' : ''; ?>">
      							<?php echo  form_label(lang('admin_subject'), 'subject'); ?> 
					              <span class="required">*</span>
					              <?php 
					                $populateData = $this->input->post('subject') ? $this->input->post('subject') : (isset($email_template_data['subject']) ? $email_template_data['subject'] :  '' );
					              ?>
					              <input type="text" name="subject" id="subject" class="form-control" value="<?php echo xss_clean($populateData);?>">
					              <span class="small form-error"> <?php echo strip_tags(form_error('subject')); ?> </span>
      						</div>
      					</div>

      					<div class="col-12">
				            <div class="form-group <?php echo form_error('description') ? ' has-error' : ''; ?>">
				              <?php echo  form_label(lang('description'), 'description'); ?>
				              <?php
				                $populateData = $this->input->post('description') ? $this->input->post('description') : (isset($email_template_data['description']) ? $email_template_data['description'] :  '' );
				              ?>
				              <textarea name="description" id="p_desc" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
				              <span class="small form-error"> <?php echo strip_tags(form_error('description')); ?> </span>
				            </div>
				        </div>




      					<div class="col-12">
				            <div class="form-group">
				              <label class="form-label">Available Tokens</label>
				              <label class="form-control h-auto"><?php echo $email_template_data['email_tokens']; ?></label>
				            </div>
				        </div>



				        <div class="col-12">
				            <?php $saveUpdate = isset($email_template_id) ? lang('core_button_update') : lang('core_button_save'); ?>
				            <input type="submit"  value="<?php echo $saveUpdate;?>" class="btn btn-primary px-5">
				            <a href="<?php echo base_url('admin/template');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
				        </div>
      				</div>
      			<?php echo form_close();?>	
      		</div>
      	</div>
    </div>
</div>
