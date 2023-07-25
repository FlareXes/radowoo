<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($package_id) && $package_id ? '_update' : '' ?>

<div class="row"> 
	<div class="col-12 col-md-12 col-lg-12">
		<div class="card">
			<?php
				if(isset($package_id)) {
    				$data['tab_package_id'] = $package_id;
    		 		$this->load->view('admin/package/common_tab_list',$data);
    		 	}
    		?>
			<div class="card-body">
				<?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
				<div class="row mt-3">
					<div class="col-6">
						<div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
							<?php echo  form_label(lang('admin_title'), 'title'); ?> 
							<span class="required">*</span>
							<?php 
							?>
							<?php 
				                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($package_data->title) ? $package_data->title :  '' );
				              ?>
							<input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
							<span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
						</div>
					</div>

					<div class="col-6">
		              <div class="form-group <?php echo form_error('price') ? ' has-error' : ''; ?>">
		                 <?php echo  form_label(lang('quiz_price'), 'price'); ?>
		                 <?php 
				                $populateData = $this->input->post('price') ? $this->input->post('price') : (isset($package_data->price) ? $package_data->price :  '' );
				              ?>
		                 <input type="number" name="price" id="price" class="form-control" value="<?php echo $populateData;?>">
		                 <span class="small form-error"> <?php echo strip_tags(form_error('price')); ?> </span>
		              </div>
		           </div>

		           <div class="clearfix"></div>
		           <div class="col-6">
		           	<div class="form-group">
		               <?php echo form_label(lang('admin_upload_image'), 'categoryimage'); ?>
		               <input type="file" name="image" id="categoryimage" class="form-control">
		               <?php 
		               if(!empty($package_data->id) && isset($package_data->id))
		               {
		                  $populateData = (!empty($package_data->image) && isset($package_data->image) ? base_url('assets\images\package\\'.$package_data->image) : (empty($package_data->image) ? base_url('assets/images/category_image/default_category.jpg') : ''));
		                  ?>
		                  <img src="<?php echo xss_clean($populateData);?>" class="img_thumb mt-2 popup">
		               <?php } ?>
		            </div>
		           </div>

		           <div class="col-6">
		            <div class="form-group togle_button">
		              <?php echo  form_label(lang('display_on_leaderboard'), 'leader_board'); ?>
		              <label class="custom-switch form-control">
		              	<?php 
				            $populateData = $this->input->post('leader_board') == 1 ? 'checked' : (isset($package_data->leader_board) && $package_data->leader_board == 1 ? 'checked' :  '' );
				          ?>
		                <input type="checkbox" name="leader_board" value="1" <?php echo xss_clean($populateData); ?> class="custom-switch-input leader_board"  data-size="sm">
		                <span class="custom-switch-indicator"></span>
		              </label>
		            </div>
		          </div>	

				  <div class="clearfix"></div>
				  <div class="col-12">
					<div class="form-group <?php echo form_error('description') ? ' has-error' : ''; ?>">
						<?php echo  form_label(lang('description'), 'description'); ?>
						<?php 
				            $populateData = $this->input->post('description') ? $this->input->post('description') : (isset($package_data->description) ? $package_data->description :  '' );
				          ?>
						<textarea name="description" id="p_description" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
						<span class="small form-error"> <?php echo strip_tags(form_error('description')); ?> </span>
					</div>
				  </div>

				  <div class="clearfix"></div>
				  <div class="col-12"> 
		            <h2 class="text-center"><?php echo lang('seo_heading');?></h2>
		          </div>

		          <div class="col-12">
		            <div class="form-group <?php echo form_error('meta_title') ? ' has-error' : ''; ?>">
		              <?php echo  form_label(lang('meta_title'), 'metatitle'); ?> 
		              <?php 
		                $populateData = $this->input->post('metatitle') ? $this->input->post('metatitle') : (isset($package_data->meta_title) ? $package_data->meta_title :  '' );
		              ?>
		              <input type="text" name="metatitle" id="metatitle" class="form-control" value="<?php echo xss_clean($populateData);?>">
		              <span class="small form-error"> <?php echo strip_tags(form_error('metatitle')); ?> </span>
		            </div>
		          </div>

		          <div class="clearfix"></div>
		          <hr />
		          
		          <div class="col-12">
		            <div class="form-group <?php echo form_error('meta_kewords') ? ' has-error' : ''; ?>">
		              <?php echo  form_label(lang('meta_kewords'), 'metakeywords'); ?>
		              <?php
		                $populateData = $this->input->post('metakeywords') ? $this->input->post('metakeywords') : (isset($package_data->meta_keywords) ? $package_data->meta_keywords :  '' );
		              ?>
		              <input type="text" name="metakeywords" id="metakeywords" class="form-control" value="<?php echo $populateData;?>" data-role="tagsinput">
		            </div>
		          </div>    

		          <div class="col-12">
		            <div class="form-group <?php echo form_error('meta_description') ? ' has-error' : ''; ?>">
		              <?php echo  form_label(lang('meta_description'), 'metadescription'); ?>
		              <?php
		                $populateData = $this->input->post('metadescription') ? $this->input->post('metadescription') : (isset($package_data->meta_description) ? $package_data->meta_description :  '' );
		              ?>
		              <textarea name="metadescription" id="metadescription" class="form-control " rows="5" ><?php echo xss_clean($populateData);?></textarea>
		              <span class="small form-error"> <?php echo strip_tags(form_error('metadescription')); ?> </span>
		            </div>
		          </div>

		          <div class="clearfix"></div>
		          <div class="col-12">
		            <?php $saveUpdate = isset($package_id) ? lang('core_button_update') : lang('core_button_save'); ?>
		            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
		            <a href="<?php echo base_url('admin/package');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
		          </div>
		          <div class="clearfix"></div>
				</div>
				<?php echo form_close(); ?>
			</div>
		</div>	
	</div>
</div>
