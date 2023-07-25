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
					                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($level_data['title']) ? $level_data['title'] :  '' );
					              ?>
					              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
					              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
      						</div>
      					</div>
      					<div class="col-4">
			              <div class="form-group <?php echo form_error('min_points') ? ' has-error' : ''; ?>">
			                 <?php echo  form_label(lang('min_points')); ?>
			                 <?php 
			                 	$populateData = $this->input->post('min_points') ? $this->input->post('min_points') : (isset($level_data['min_points']) ? $level_data['min_points'] :  '' );
			                 ?>
			                 <input type="number" name="min_points" id="min_points" class="form-control" value="<?php echo $populateData;?>">
			                 <span class="small form-error"> <?php echo strip_tags(form_error('min_points')); ?> </span>
			              </div>
			           </div>
			           <div class="col-4">
			              <div class="form-group <?php echo form_error('level_order') ? ' has-error' : ''; ?>">
			                 <?php echo  form_label(lang('material_order')); ?>
			                 <?php 
			                 	$populateData = $this->input->post('level_order') ? $this->input->post('level_order') : (isset($level_data['level_order']) ? $level_data['level_order'] :  '' );
			                 ?>
			                 <input type="number" name="level_order" id="level_order" class="form-control" value="<?php echo $populateData;?>">
			                 <span class="small form-error"> <?php echo strip_tags(form_error('level_order')); ?> </span>
			              </div>
			           </div>
			           <div class="col-4">
			           		<?php echo  form_label(lang('admin_upload_image'), 'image'); ?>
			           		<input type="File" name="image" id="image" class="form-control">
              				<span class="small form-error"> <?php echo strip_tags(form_error('image')); ?> </span>
			              	<?php 
			                	if(isset($level_data['badge']))
			                	{
			              	?>
			                	<img src='<?php echo base_url("assets/images/level_image/".$level_data['badge']); ?>' style="width: 45px; height: 45px; position: absolute; right: 0px; top: 25px;">

			              	<?php
			                	}
			              	?>
			           </div>
			           <div class="col-12">
			            <div class="form-group <?php echo form_error('description') ? ' has-error' : ''; ?>">
			              <?php echo  form_label(lang('description')); ?>
			              <?php
			                $populateData = $this->input->post('description') ? $this->input->post('description') : (isset($level_data['description']) ? $level_data['description'] :  '' );
			              ?>
			              <textarea name="description" id="p_desc" class="form-control editorr" rows="5" ><?php echo xss_clean($populateData);?></textarea>
			              <span class="small form-error"> <?php echo strip_tags(form_error('description')); ?> </span>
			            </div>
			           </div>
			           <div class="col-12">
			            <?php $saveUpdate = isset($level_id) ? lang('core_button_update') : lang('core_button_save'); ?>
			            <input type="submit"  value="<?php echo $saveUpdate;?>" class="btn btn-primary px-5">
			            <a href="<?php echo base_url('admin/level');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
			          </div>
      				</div>
      			<?php echo form_close();?>
      		</div>
      	</div>
    </div>
</div>
