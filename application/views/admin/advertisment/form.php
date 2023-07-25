<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($article_id) && $article_id ? '_update' : '' ?>
<div class="row page">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body">
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?> 
        <div class="row">

          <div class="col-6">
            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('title'), 'title'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($advertisment_data->title) ? $advertisment_data->title :  '' );
              ?>

              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
            </div>
          </div>

          <div class="col-3">
            <div class="form-group <?php echo form_error('ad_order') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('order'), 'ad_order'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('ad_order') ? $this->input->post('ad_order') : (isset($advertisment_data->ad_order) ? $advertisment_data->ad_order :  '' );
              ?>

              <input type="text" name="ad_order" id="ad_order" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('ad_order')); ?> </span>
            </div>
          </div>


          <div class="col-3">
            <div class="form-group <?php echo form_error('is_goole_adsense') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('is_goole_adsense'), 'is_goole_adsense'); ?> 
              <span class="required">*</span>
              <?php 
                $is_goole_adsense = $this->input->post('is_goole_adsense') ? $this->input->post('is_goole_adsense') : (isset($advertisment_data->is_goole_adsense) ? $advertisment_data->is_goole_adsense :  0);
                $checked_it = $is_goole_adsense == 1 ? 'checked' : "";
              ?>
              <div class="form-control">
                <label class="custom-switch">
                <input type="checkbox" <?php echo $checked_it; ?> name="is_goole_adsense" value='1'  class="custom-switch-input togle_switch is_goole_adsense">
                <span class="custom-switch-indicator indication"></span>
                </label>
              </div>
            </div>
          </div>



          <div class="clearfix"></div>


          <div class="col-6">
            <div class="form-group">
              <?php 
                echo  form_label(lang('position'));
                $populateData = $this->input->post('position') ? $this->input->post('position') : (isset($advertisment_data->position) ? $advertisment_data->position :  '' );;
              ?>
              <select name="position" class="form-control">
                  <?php 
                  foreach($position_array as $key => $value) 
                  { 
                    $select_val = ($populateData == $key) ? 'selected' : ''; ?>
                      <option <?php echo $select_val;?> value="<?php echo $key;?>"><?php echo $value;?></option>
                    <?php 
                  } ?>  
              </select>
              <span class="small form-error"> <?php echo strip_tags(form_error('position')); ?> </span>
            </div>
          </div>


          <div class="col-6">
            <div class="form-group">
              <?php 
                echo  form_label(lang('status'));
                $status_array = array('Disable','Enable');
                $populateData = $this->input->post('status') ? $this->input->post('status') : (isset($advertisment_data->status) ? $advertisment_data->status : 1);
              ?>
              <select name="status" class="form-control">
                  <?php 
                  foreach($status_array as $key => $value) 
                  { 
                    $select_val = ($populateData == $key) ? 'selected' : ''; ?>
                      <option <?php echo $select_val;?> value="<?php echo $key;?>"><?php echo $value;?></option>
                    <?php 
                  } ?>  
              </select>
              <span class="small form-error"> <?php echo strip_tags(form_error('status')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>
          
          <div class="col-12  url_or_image_div <?php echo $is_goole_adsense == 1 ? " d-none" : ''; ?>">
            <div class="row">
           
              <div class="col-9">
                <div class="form-group <?php echo form_error('url') ? ' has-error' : ''; ?>">
                  <?php echo  form_label(lang('url'), 'url'); ?> 
                  <span class="required">*</span>
                  <?php 
                    $populateData = $this->input->post('url') ? $this->input->post('url') : (isset($advertisment_data->url) ? $advertisment_data->url :  '' );
                  ?>

                  <input type="text" name="url" id="url" class="form-control" value="<?php echo xss_clean($populateData);?>">
                  <span class="small form-error"> <?php echo strip_tags(form_error('url')); ?> </span>
                </div>
              </div>

              <div class="col-3">
                <div class="form-group <?php echo form_error('image_upload_error') ? ' has-error' : ''; ?>">
                  <?php echo  form_label(lang('image'), 'image'); ?>

                  <span class="required">*</span>
                  <?php  $populateData = isset($advertisment_data->image) && $advertisment_data->image ? $advertisment_data->image :  '';  ?> 

                  <input type="file" name="image" id="image" class="form-control" value="<?php echo xss_clean($populateData);?>">
                  <span class="small text-danger form-error"> <?php echo strip_tags(form_error('image_upload_error')); ?> </span> 
                  <?php 
                    if($populateData)
                    { ?> 
                      <div class="">
                        <img class="image_preview popup" src="<?php echo base_url('assets/images/advertisment/').$populateData; ?>">
                      </div> <?php
                    } ?>
                </div>
              </div>

            </div>
          </div>
              
          
          <div class="col-12 google_ad_code_div <?php echo $is_goole_adsense == 1 ? '' : " d-none"; ?>">
            <div class="row">
              <div class="col-12">

                <div class="form-group <?php echo form_error('google_ad_code') ? ' has-error' : ''; ?>">
                  <?php echo  form_label(lang('google_ad_code'), 'google_ad_code'); ?> 
                  <span class="required">*</span>
                  
                   <?php 
                    $populateData = $this->input->post('google_ad_code') ? $this->input->post('google_ad_code') : (isset($advertisment_data->google_ad_code) ? $advertisment_data->google_ad_code :  '' );
                  ?>
                  <textarea rows="4" name="google_ad_code" id="google_ad_code" class="form-control google_ad_code h-100"><?php echo htmlspecialchars_decode($populateData);?></textarea>
                  <span class="small form-error"> <?php echo strip_tags(form_error('google_ad_code')); ?> </span>

                </div>
              </div> 
            </div>
          </div>

          <div class="clearfix"></div>


          <hr />

          <div class="col-12">
            <?php $saveUpdate = isset($advertisment_id) ? lang('core_button_update') : lang('core_button_save'); ?>
            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
            <a href="<?php echo base_url('admin/advertisment');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
          </div>
          <div class="clearfix"></div>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>

