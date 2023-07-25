<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row page">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body">
      <div class="card">



        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?> 
        <div class="row">

          <div class="col-12">
            <div class="form-group <?php echo form_error('zip') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('Upload Zip File'), 'zip'); ?>

              <span class="required">*</span>
              <?php 
                $populateData = isset($testimonial_data->zip) && $testimonial_data->zip ? $testimonial_data->zip :  ''; 
              ?> 
              <input type="file" name="zip" id="zip" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('zip')); ?> </span> 
            </div>
          </div>


          <div class="clearfix"></div>

          <hr />

          <div class="col-12">

            <input type="submit" name="submit"  value="<?php echo "Import Data";?>" class="btn btn-primary px-5 float-right">
          </div>
          <div class="clearfix"></div>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>
