<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row page">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body"> 
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?> 
        <div class="row">
          <!-- <div class="col-6">
            <div class="form-group <?php echo form_error('category_id') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('category'), 'category_id'); ?>
              <span class="required">*</span>
              <?php
                $populateData = $this->input->post('category_id') ? $this->input->post('category_id') : (isset($coupon_data['category_id']) ? $coupon_data['category_id'] :  '' );
                echo form_dropdown('category_id', $category_list, $populateData, 'id="category_id" class="form-control select_dropdown coupon-for"'); 
              ?> 
              <span class="small form-error"> <?php echo strip_tags(form_error('category_id')); ?> </span>
            </div>  
          </div> -->
          <div class="col-6">
            <div class="form-group <?php echo form_error('coupon_for') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('coupon_for'), 'coupon_for'); ?>

              <span class="required">*</span>
              <?php 
                $coupon_for = array(''=>'Select coupon for','all'=>'All','quizes'=>'Quiz','study_material'=>'Study Material','membership'=>'Membership','users'=>'User');
                $populateData = $this->input->post('coupon_for') ? $this->input->post('coupon_for') : (isset($coupon_data['coupon_for']) ? $coupon_data['coupon_for'] :  '' );
                if(empty($coupon_data['id'])) { 

                  echo form_dropdown('coupon_for',$coupon_for,'',  'id="coupon_for" class="form-control select_dropdown coupon-for"'); 
                }
                else
                {
                  ?>
                  <label class="form-control"><?php echo ucfirst($populateData); ?></label>
              <?php     
                }
              ?> 
              <span class="small form-error"> <?php echo strip_tags(form_error('coupon_for')); ?> </span>  
            </div>
          </div>

          <?php $coupon_show_class = (isset($coupon_related_data) && $coupon_related_data || empty($coupon_data['id']) ? '' : 'd-none') ; ?>
          <div class="col-6 ">
            <div class="item-for <?php echo $coupon_show_class; ?>">
              <div class="form-group <?php echo form_error('reference_data') ? ' has-error' : ''; ?>">
                <?php echo  form_label(lang('reference_data'), 'data'); ?>
                <span class="required">*</span>
                <?php if(empty($coupon_data['id'])) { ?>
                  <select class="select2-ajax form-control item-data" name="reference_data[]" multiple="multiple"></select>
                <?php } else { 
                  if($coupon_related_data) {
                ?>
                    <label class="form-control">
                      <?php 
                         
                          $prefix = '';
                          foreach ($coupon_related_data as $c_d)
                          {
                              $data_list = $prefix . $c_d;
                              $prefix = ', ';
                              echo $data_list;
                          } 
                        
                      ?>  
                    </label>
                <?php } } ?>  
                <span class="small form-error"> <?php echo strip_tags(form_error('reference_data')); ?> </span>
              </div>
            </div>
            
          </div>
        
          <!-- <div class="col-12">
            <div class="form-group <?php echo form_error('item_name') ? ' has-error' : ''; ?> item-for">
              <?php echo  form_label(lang('item_name'), 'item_name'); ?>
              <span class="required">*</span>
              <select class="select2-ajax form-control select_dropdown item-data">

              </select>
            </div>
          </div> -->
          <div class="col-4">
            <div class="form-group <?php echo form_error('coupon_code') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('coupon_code'), 'coupon_code'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('coupon_code') ? $this->input->post('coupon_code') : (isset($coupon_data['coupon_code']) ? $coupon_data['coupon_code'] :  '' );
              ?>

              <input type="text" name="coupon_code" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('coupon_code')); ?> </span>
            </div>
          </div>

          <div class="col-4">
            <div class="form-group <?php echo form_error('discount_type') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('discount_type'), 'discount_type'); ?> 
              <span class="required">*</span> 
              <?php 
                $discount_type = array(''=>'Select discount type','Percent'=>'Percent','Value'=>'Value');
                $populateData = $this->input->post('discount_type') ? $this->input->post('discount_type') : (isset($coupon_data['coupon_discount_type']) ? $coupon_data['coupon_discount_type'] :  '' );
                echo form_dropdown('discount_type', $discount_type, $populateData, 'id="discount_type" class="form-control select_dropdown "'); 
              ?> 
              <span class="small form-error"> <?php echo strip_tags(form_error('discount_type')); ?> </span>
            </div>   
          </div>
          <div class="col-4">
            <div class="form-group <?php echo form_error('no_time_used') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('No of times can be used'), 'no_time_used'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('no_time_used') ? $this->input->post('no_time_used') : (isset($coupon_data['no_of_times_can_be_used']) ? $coupon_data['no_of_times_can_be_used'] :  '' );
              ?>

              <input type="text" name="no_time_used" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('no_time_used')); ?> </span>
            </div>
          </div>

          <div class="col-4">
            <div class="form-group <?php echo form_error('discount_value') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('Discount value'), 'discount_value'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('discount_value') ? $this->input->post('discount_value') : (isset($coupon_data['discount_value']) ? $coupon_data['discount_value'] :  '' );
              ?>

              <input type="text" name="discount_value" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('discount_value')); ?> </span>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group <?php echo form_error('expiry_date') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('Expiry Date'), 'expiry_date'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('expiry_date') ? $this->input->post('expiry_date') : (isset($coupon_data['expiry_date']) ? date("Y-m-d",strtotime($coupon_data['expiry_date'])) :  '' );
                
              ?>

              <input type="date" name="expiry_date" id="title" class="form-control" value="<?php echo $populateData;?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('expiry_date')); ?> </span>
            </div>
          </div>
          <div class="col-4">
            <div class="form-group <?php echo form_error('status') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('Status'), 'status'); ?> 
              <?php 
                $status = array(''=>'Select status','Active'=>'Active','Inactive'=>'Inactive');
                $populateData = $this->input->post('status') ? $this->input->post('status') : (isset($coupon_data['status']) ? $coupon_data['status'] :  '' );
                echo form_dropdown('status', $status, $populateData, 'id="discount_type" class="form-control select_dropdown"'); 
              ?> 
              
            </div>   
          </div>
          <div class="clearfix"></div>

          <div class="col-12">
            <div class="form-group <?php echo form_error('description') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('description'), 'description'); ?>
              <?php
                $populateData = $this->input->post('description') ? $this->input->post('description') : (isset($coupon_data['description']) ? $coupon_data['description'] :  '' );
              ?>
              <textarea name="description" id="p_desc" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
              
            </div>
          </div>


          <div class="clearfix"></div>
          <hr />

          <div class="col-12">
            <?php $saveUpdate = isset($coupon_id) ? lang('core_button_update') : lang('core_button_save'); ?>
            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary px-5">
            <a href="<?php echo base_url('admin/coupon');?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
          </div>
          <div class="clearfix"></div>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>
