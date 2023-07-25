<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row"> 
	<div class="col-12 col-md-12 col-lg-12">
		<div class="card">
			<?php
				if(isset($study_material_id) && !empty($study_material_id)) 
				{
    				$data['tab_study_material_id'] = $study_material_id;
    				$data['study_section_id'] = $study_section_id;
    		 		$this->load->view('tutor/study/common_tab_list',$data);
    		 	}
    		?>
			<div class="card-body">
				<?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
					<div class="row mt-3">
						

						<div class="col-12">
							<div class="form-group <?php echo form_error('study_material_id') ? ' has-error' : ''; ?>">
				              <?php echo  form_label(lang('study_material'), 'study_material_id'); ?>
				              <span class="required">*</span>
				              <?php 
				                $populateData = $this->input->post('study_material_id') ? $this->input->post('study_material_id') : (isset($study_section_data->study_material_id) ? $study_section_data->study_material_id :  $study_material_id );                     
				                echo form_dropdown('study_material_id', $study_material_data, $populateData, 'id="study_material_id" class="form-control select_dropdown"'); 
				              ?> 
				              <span class="small form-error"> <?php echo strip_tags(form_error('study_material_id')); ?> </span>  
				            </div>
						</div>


						<div class="col-12">
				            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
				              <?php echo  form_label(lang('admin_title'), 'title'); ?> 
				              <span class="required">*</span>
				              <?php 
				                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($study_section_data->title) ? $study_section_data->title :  '' );
				              ?>
				              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
				              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
				            </div>
				        </div>

				        


						<div class="clearfix"></div>





			          <div class="col-12">
			            <?php $saveUpdate = isset($study_section_id) && !empty($study_section_id) ? lang('core_button_update') : lang('core_button_save'); ?>
			            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
			            <a href="<?php echo base_url('tutor/study');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
			          </div>
			          <div class="clearfix"></div>

					</div>
				<?php echo form_close();?>
			</div>
		</div>
	</div>
</div>