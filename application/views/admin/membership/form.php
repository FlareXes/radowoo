<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">  
  	<div class="col-12 col-md-12 col-lg-12">
    	<div class="card">
      		<div class="card-body">
      			<?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
      				<div class="row mt-3">
      					<div class="col-12">
      						<div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
      							<?php echo  form_label(lang('admin_title'), 'title'); ?> 
					              <span class="required">*</span>
					              <?php 
					                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($membership_data['title']) ? $membership_data['title'] :  '' );
					              ?>
					              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
					              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
      						</div>	
      					</div>
	      					
			          <div class="col-6">
			            <div class="form-group <?php echo form_error('category_id') ? ' has-error' : ''; ?>">
			              <?php echo  form_label(lang('select_category_for_membership'), 'category_id'); ?>

			              <span class="required">*</span>
			              <?php 
			                $populateData = $this->input->post('category_id') ? $this->input->post('category_id') : (isset($membership_data['category_id']) ? $membership_data['category_id'] :  '' );                     
			                echo form_dropdown('category_id', $category_data, $populateData, 'id="category_id" class="form-control select_dropdown"'); 
			              ?> 
			              <span class="small form-error"> <?php echo strip_tags(form_error('category_id')); ?> </span>  
			            </div>
			          </div>      					

      					<div class="col-3">
			              <div class="form-group <?php echo form_error('amount') ? ' has-error' : ''; ?>">
			                 <?php echo  form_label(lang('amount')); ?>
			                 <?php 
			                 	$populateData = $this->input->post('amount') ? $this->input->post('amount') : (isset($membership_data['amount']) ? $membership_data['amount'] :  '' );
			                 ?>
			                 <input type="number" name="amount" id="amount" class="form-control" value="<?php echo $populateData;?>">
			                 <span class="small form-error"> <?php echo strip_tags(form_error('amount')); ?> </span>
			              </div>
			           </div>

			           <div class="col-3">
			              <div class="form-group <?php echo form_error('duration') ? ' has-error' : ''; ?>">
			                 <?php echo  form_label(lang('admin_test_duration')); ?>
			                 <?php 
			                 	$populateData = $this->input->post('duration') ? $this->input->post('duration') : (isset($membership_data['duration']) ? $membership_data['duration'] :  '' );
			                 ?>
			                 <input type="number" name="duration" id="duration" class="form-control" value="<?php echo $populateData;?>">
			                 <span class="text-warning"><?php echo lang('no_of_days');?></span>
			                 <span class="small form-error"> <?php echo strip_tags(form_error('duration')); ?> </span>
			              </div>
			           </div>

			           <div class="col-12">
			            <div class="form-group <?php echo form_error('description') ? ' has-error' : ''; ?>">
			              <?php echo  form_label(lang('description')); ?>
			              <?php
			                $populateData = $this->input->post('description') ? $this->input->post('description') : (isset($membership_data['description']) ? $membership_data['description'] :  '' );
			              ?>
			              <textarea name="description" id="p_desc" class="form-control editorr" rows="5" ><?php echo xss_clean($populateData);?></textarea>
			              <span class="small form-error"> <?php echo strip_tags(form_error('description')); ?> </span>
			            </div>
			           </div>

			           <div class="col-12">
			            <?php $saveUpdate = isset($membership_id) ? lang('core_button_update') : lang('core_button_save'); ?>
			            <input type="submit"  value="<?php echo $saveUpdate;?>" class="btn btn-primary px-5">
			            <a href="<?php echo base_url('admin/membership');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
			          </div>

      				</div>
      			<?php echo form_close();?>
      		</div>
  		</div>
  	</div>
</div>