<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($article_id) && $article_id ? '_update' : '' ?> 
<div class="row page">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body"> 
        <?php echo form_open_multipart('', array('role'=>'form')); ?> 
        <div class="row">

          <div class="col-5">
            <div class="form-group <?php echo form_error('field_label') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_field_label'), 'field_label'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('field_label') ? $this->input->post('field_label') : (isset($c_f_data->field_label) ? $c_f_data->field_label :  '' );
              ?>

              <input required type="text" name="field_label" id="field_label" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('field_label')); ?> </span>
            </div>
          </div>



           <div class="col-5">
              <div class="form-group">
                  <?php echo form_label(lang('admin_field_form'), 'form'); ?> <span class="required">*</span>
                  <?php $populateData = $this->input->post('form') ? $this->input->post('form') : (isset($c_f_data->form) ? $c_f_data->form :  ''); ?>
                  <select class="select_dropdown form-control" name="form">
                    <option value=""><?php echo lang('admin_select_one'); ?></option>
                    <option <?php echo $populateData =='registration' ? 'selected' : ''; ?> value="registration"><?php echo 'Registration'; ?></option>
                    <option <?php echo $populateData =='contact' ? 'selected' : ''; ?> value="contact"><?php echo 'Contact'; ?></option>
                  </select>
              </div>
           </div>


          <div class="col-2">
            <div class="form-group">
              <div class="control-label"><?php echo form_label(lang('admin_is_required'), 'is_required'); ?></div>
              <label class="custom-switch  form-control">
                <?php $checked = $this->input->post('is_required') ? 'checked' : (isset($c_f_data->is_required) && $c_f_data->is_required == 1 ? 'checked' : '' );?>
                <input type="checkbox"  name="is_required" value="1" id="cf_is_required_togle" class="custom-switch-input" <?php echo xss_clean($checked);?>>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"></span>
              </label>
            </div>
          </div>

          <div class="clearfix"></div>



          <div class="col-5">
            <div class="form-group <?php echo form_error('field_help_text') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_field_help_text'), 'field_help_text'); ?> 
              <?php 
                $populateData = $this->input->post('field_help_text') ? $this->input->post('field_help_text') : (isset($c_f_data->field_help_text) ? $c_f_data->field_help_text :  '' );
              ?>

              <input type="text" name="field_help_text" id="field_help_text" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('field_help_text')); ?> </span>
            </div>
          </div>


          <div class="col-5">
            <div class="form-group <?php echo form_error('field_placeholder') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_field_placeholder'), 'field_placeholder'); ?> 
              <?php 
                $populateData = $this->input->post('field_placeholder') ? $this->input->post('field_placeholder') : (isset($c_f_data->field_placeholder) ? $c_f_data->field_placeholder :  '' );
              ?>

              <input type="text" name="field_placeholder" id="field_placeholder" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('field_placeholder')); ?> </span>
            </div>
          </div>


          <div class="col-2">
            <div class="form-group">
              <div class="control-label"><?php echo form_label(lang('admin_status'), 'status'); ?></div>
              <label class="custom-switch  form-control">
                <?php $checked = $this->input->post('status') ? 'checked' : (isset($c_f_data->status) && $c_f_data->status == 1 ? 'checked' : '' );?>
                <input type="checkbox"  name="status" value="1" id="cf_status_togle" class="custom-switch-input" <?php echo xss_clean($checked);?>>
                <span class="custom-switch-indicator"></span>
                <span class="custom-switch-description"></span>
              </label>
            </div>
          </div>

          <div class="clearfix"></div>



           <div class="col-5">
              <div class="form-group">
                  <?php echo form_label(lang('admin_field_width'), 'width'); ?> <span class="required">*</span>
                  <?php $populateData = $this->input->post('width') ? $this->input->post('width') : (isset($c_f_data->width) ? $c_f_data->width :  ''); ?>
                  <select class="select_dropdown form-control" name="width">
                    <option value=""><?php echo lang('admin_select_one'); ?></option>
                    <option <?php echo $populateData =='12' ? 'selected' : ''; ?> value="12"><?php echo 'Full'; ?></option>
                    <option <?php echo $populateData =='6' ? 'selected' : ''; ?> value="6"><?php echo 'Half'; ?></option>
                    <option <?php echo $populateData =='4' ? 'selected' : ''; ?> value="4"><?php echo '1/3 Rd'; ?></option>
                    <option <?php echo $populateData =='3' ? 'selected' : ''; ?> value="3"><?php echo '1/4 Th'; ?></option>
                  </select>
              </div>
           </div>

           <div class="col-5">
              <div class="form-group">
                  <?php echo form_label(lang('admin_field_type'), 'field_type'); ?> <span class="required">*</span>
                  <?php $populateData = $this->input->post('field_type') ? $this->input->post('field_type') : (isset($c_f_data->field_type) ? $c_f_data->field_type :  ''); 
                  $display_none = ($populateData == 'select' OR $populateData == 'checkbox' ) ? : "style='display:none;'";
                  ?>
                  <select class="select_dropdown form-control" name="field_type" id="field_type">
                    <option value=""><?php echo lang('admin_select_one'); ?></option>
                    <option <?php echo $populateData =='input' ? 'selected' : ''; ?> value="input"><?php echo 'Input'; ?></option>
                    <option <?php echo $populateData =='email' ? 'selected' : ''; ?> value="email"><?php echo 'Email'; ?></option>
                    <option <?php echo $populateData =='phone' ? 'selected' : ''; ?> value="phone"><?php echo 'Phone'; ?></option>
                    <option <?php echo $populateData =='date' ? 'selected' : ''; ?> value="date"><?php echo 'Date'; ?></option>
                    <option <?php echo $populateData =='textarea' ? 'selected' : ''; ?> value="textarea"><?php echo 'Textarea'; ?></option>
                    <option <?php echo $populateData =='select' ? 'selected' : ''; ?> value="select"><?php echo 'Select'; ?></option>
                    <option <?php echo $populateData =='checkbox' ? 'selected' : ''; ?> value="checkbox"><?php echo 'Checkbox'; ?></option>
                    <option <?php echo $populateData =='file' ? 'selected' : ''; ?> value="file"><?php echo 'File'; ?></option>
                  </select>
              </div>
           </div>


          <div class="col-2">
            <div class="form-group <?php echo form_error('admin_field_order') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_field_order'), 'field_order'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('field_order') ? $this->input->post('field_order') : (isset($c_f_data->field_order) ? $c_f_data->field_order :  '' );
              ?>

              <input type="number" min="5" name="field_order" id="field_order" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('field_order')); ?> </span>
            </div>
          </div>

          <div class="clearfix"></div>

          <?php   
          $populateData = NULL;
          if(isset($option_array) && $option_array && is_array($option_array))
          { 
            $option_val = isset($option_array[0]) ? $option_array[0] : NULL;
            $populateData = $option_val;
            unset($option_array[0]);
          } ?>


          <div class="col-12 field_options_section" <?php echo $display_none; ?>>
            <div class="row field_options_block">
              <div class="col-10">

                <div class="form-group <?php echo form_error('field_options[]') ? ' has-error' : ''; ?>">
                  <?php echo  form_label(lang('admin_field_options'), 'field_options[]'); ?> 
                  <span class="required">*</span>
                  <input  type="text" name="field_options[]" id="field_options" class="form-control" value="<?php echo xss_clean($populateData);?>">
                  <span class="small form-error"> <?php echo strip_tags(form_error('field_options[]')); ?> </span>
                </div>

              </div>
              <div class="col-2 mt-1">
                <a href="javascript:void(0)" class="btn btn-warning btn-block mt-4 add_more_option" id="add_more_option"><?php echo lang('add_more'); ?></a>
              </div>
            </div>

            <?php
                if(isset($option_array) && $option_array && is_array($option_array))
                {
                  foreach ($option_array as $option_val) 
                  { ?>
                    <div class="row copied_section">
                      <div class="col-10">
                        <div class="form-group ">
                          <input type="text" name="field_options[]" id="field_options" class="form-control" value="<?php echo xss_clean($option_val);?>">
                        </div>
                      </div>
                      <div class="col-2">
                        <a href="javascript:void(0)" class="btn btn-danger btn-block remove_block_btn" id="add_more_option"><?php echo lang('remove_option'); ?></a>
                      </div>    
                    </div>           
                    <?php
                  }
                }?>

          </div>


          <div class="clearfix"></div>











<!-- ************************************************************************************************************* -->

    

          <?php
          if($customfields && 1==2)
          {
            foreach ($customfields as $customfield) 
            {
              $value = isset($customfield->value) ? $customfield->value : NULL;
              if($customfield->field_type=='input')
              {
                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$value,'input'); ?>
                        
                      </div>
                  </div>
                  <?php
              }
              else if($customfield->field_type=='email')
              {

                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$value,'email'); ?>
                        
                      </div>
                  </div>
                  <?php
              }
              else if($customfield->field_type=='phone')
              {

                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$value,'tel'); ?>
                        
                      </div>
                  </div>
                  <?php
              }
              else if($customfield->field_type=='date')
              {

                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$value,'text','datepicker'); ?>
                        
                      </div>
                  </div>
                  <?php
              }
              else if($customfield->field_type=='textarea')
              {

                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_textarea($customfield->field_name, $customfield->field_label,$value); ?>
                        
                      </div>
                  </div>
                  <?php

              }
              else if($customfield->field_type=='select')
              {
                $field_options = $customfield->field_options ? json_decode($customfield->field_options) : array();
                $selected = $value;
                $classes = '';
                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_select($customfield->field_name, $customfield->field_label,$field_options,$selected,$classes); ?>
                        
                      </div>
                  </div>
                  <?php
              }
              else if($customfield->field_type=='checkbox')
              {

                $field_options = $customfield->field_options ? json_decode($customfield->field_options) : array();
                $selected = $value ? json_decode($value) : [];
                $classes = '';
                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name.'[]') ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$field_options,$selected,$classes); ?>
                        <span class="small text-danger form-error"> <?php echo strip_tags(form_error($customfield->field_name.'[]')); ?> </span>
                      </div>
                  </div>
                  <?php
              }
              else if($customfield->field_type=='file')
              {


                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$value,'file'); ?>
                        <span class="small cf_image"><a target="_blank" href="<?php echo base_url('/assets/images/custom_fields/').$value ?>"><?php echo $value; ?></a></span>
                      </div>

                  </div>
                  <?php
              }
              else
              {

                  ?>
                  <div class="col-md-<?php echo $customfield->width; ?>">
                      <div class="form-group <?php echo form_error($customfield->field_name) ? ' has-error' : ''; ?>">
                        <?php echo  render_input($customfield->field_name, $customfield->field_label,$value,'input'); ?>
                        <span class="small text-danger form-error"> <?php echo strip_tags(form_error($customfield->field_name.'[]')); ?> </span>
                      </div>
                  </div>
                  <?php

              }

            }
          }
          ?>
        <style type="text/css">.form-group.has-error label { color: red; }</style>



<!-- ************************************************************************************************************* -->
          <div class="clearfix"></div>
           <hr />

          <div class="col-12">
            <?php $saveUpdate = isset($c_f_id) ? lang('core_button_update') : lang('core_button_save'); ?>
            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
            <a href="<?php echo base_url('admin/custom_fields');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
          </div>
          <div class="clearfix"></div>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>


<div class="col-12 copy_this_section d-none">
  <div class="row copied_section">
    <div class="col-10">
      <div class="form-group">
        <input type="text" name="field_options[]" id="field_options" class="form-control">
      </div>
    </div>
    <div class="col-2">
        <a href="javascript:void(0)" class="btn btn-danger btn-block remove_block_btn" id="add_more_option"><?php echo lang('remove_option'); ?></a>
    </div>
  </div>
</div>