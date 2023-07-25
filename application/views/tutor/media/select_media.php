<?php defined('BASEPATH') OR exit('No direct script access allowed');?>
<div class="row">
   <div class="col-12 col-md-12 col-lg-12">  
      <div class="card">
         <div class="card-body">
            <?php echo form_open_multipart('', array('role'=>'form')); ?>
            <div class="row">
               

               
               <div class="col-12">
                  <div class="form-group <?php echo form_error('galleryimages') ? ' has-error' : ''; ?>">
                     <?php echo form_label(lang('select_media'), 'galleryimages'); ?>  
                     <?php $populateData = (!empty($this->input->post('galleryimages')) ? $this->input->post('galleryimages') : (!empty($editData['galleryimages']) && isset($editData['galleryimages']) ? $editData['galleryimages'] : '' )); ?>
                     <input type = "text" name = "galleryimages" id="galleryimages" class="form-control ifile_img_filed" value="<?php echo $populateData; ?>" readonly="true" />
                     <a href = "javascript:void(0)" id = "elfinder_button">Add Gallery Images</a>
                     <div id="selectedImages">
                        <?php 
                        if($populateData)
                        {
                           ?>
                            <img class="img-thumbnail ifile_img_thumb" src="<?php echo $url.$populateData; ?>">
                           <?php
                        }
                        ?>
                     </div>
                     <span class="small form-error"> <?php echo strip_tags(form_error('galleryimages')); ?> </span>  
                  </div>
               </div>

               <div class="clearfix"></div>
               <hr />

               <?php 
                  $populateData = isset($editData['id']) && $editData['id'] ? lang('core_button_update') : lang('core_button_save'); 
               ?>
               <div class="col-12 text-right">
                  <input type="submit" name="<?php echo xss_clean($populateData);?>" value="<?php echo ucfirst($populateData);?>" class="btn btn-primary btn-lg mr-2">
                  <a href="<?php echo base_url("admin/blog/post");?>" class="btn btn-dark btn-lg"><?php echo lang('core_button_cancel'); ?></a>
               </div>
            <?php echo form_close(); ?>
         </div>
      </div>
   </div>
</div>

<script type="text/javascript">
    var connector = "<?php echo $connector; ?>";
    var ApiFullVersion = "<?php echo elFinder::getApiFullVersion();?>";
    // var file = "<?php echo $url;?>";
</script>
