<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<?php $update = isset($article_id) && $article_id ? '_update' : '' ?> 
<div class="row page">
  <div class="col-12 col-md-12 col-lg-12">
    <div class="card">
      <div class="card-body"> 
        <?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?> 
        <div class="row">

          <div class="col-9">
            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('admin_title'), 'title'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($section_data->title) ? $section_data->title :  '' );
              ?>

              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
            </div>
          </div>


          <div class="col-3">
            <div class="form-group <?php echo form_error('order') ? ' has-error' : ''; ?>">
              <?php echo  form_label(lang('Sort Order'), 'order'); ?> 
              <span class="required">*</span>
              <?php 
                $populateData = $this->input->post('order') ? $this->input->post('order') : (isset($section_data->order) ? $section_data->order :  '' );
              ?>

              <input type="text" name="order" id="order" class="form-control" value="<?php echo xss_clean($populateData);?>">
              <span class="small form-error"> <?php echo strip_tags(form_error('order')); ?> </span>
            </div>
          </div>



          <div class="clearfix"></div>
          <hr />

          <div class="col-12">
            <?php $saveUpdate = isset($section_id) ? lang('core_button_update') : lang('core_button_save'); ?>

            <a href="<?php echo base_url('admin/section');?>" class="btn btn-dark px-5 float-right"><?php echo lang('core_button_cancel'); ?></a>

            <input type="submit"  value="<?php echo ucfirst($saveUpdate);?>" class="btn btn-primary mr-4  px-5 float-right">
            
          </div>
          <div class="clearfix"></div>
        </div>
        <?php echo form_close();?>
      </div>
    </div>
  </div>
</div>
