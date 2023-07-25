<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row updates">

   <div class="col-12 col-md-12 col-lg-12">
      <div class="box-body">
         <?php echo form_open_multipart(base_url('admin/Updates/update_info'), array('role'=>'form','novalidate'=>'novalidate')); ?>
            <div class="row">
               <div class="col-md-9">
                  <div class="form-group">
                     <label>Update Token</label>
                     <?php if($response['is_verified'] == 1) { ?>
                        <span class="badge badge-pill badge-primary"><i class="fa fa-check"></i> <?php echo lang('verified'); ?></span>
                     <?php } else { ?>
                        <span class="badge badge-pill badge-danger"><i class="fa fa-times"></i> <?php echo lang('not_verified'); ?></span>
                     <?php } ?>
                     <?php 

                        $token_name = (isset($token_value->name) && $token_value->name) ? $token_value->name : ''; 
                        $token_value = (isset($token_value->value) && $token_value->value) ? $token_value->value : '';
                     ?>
                     <input type="text" name="<?php echo $token_name; ?>" value="<?php echo $token_value; ?>" class="form-control">
                  </div>
               </div>
               <div class="col-md-3">
                  <div class="form-group">
                     <label>&nbsp;</label>
                     <input type="submit" value="Update" class="form-control btn btn-primary px-5">
                  </div>
               </div>
            </div>
         <?php echo form_close(); ?>   
      </div>
   </div>

  <div class="col-12 col-md-12 col-lg-12">
      <div class="card">
         <div class="card-body">

               <div class="row text-center">
                  
                  <?php 
                  if($purchase_code_is_verified) 
                  {
                     
                        if($next_version_name)
                        { 
                           ?>

                           <div class="col-6">
                              <!-- <h4>Current Version <?php //echo $response->client_version; ?></h4> -->
                              <h4> Current Version <?php echo $this->settings->site_version; ?> </h4>
                           </div>

                           <div class="col-6">
                              <h4>Latest Version  <?php echo $next_version_name; ?></h4>
                           </div>

                           <div class="col-12 my-5">
                              <iframe src="<?php echo $api_url; ?>next_version_update_info.php?version=<?php echo $local_current_version_code; ?>"  class="w-100 border-0" style="height: 400px !important;"> title="<?php echo lang('next_updates_details')?>"></iframe>
                           </div>

                           <div class="col-12 text-center h-100">
                              <?php echo $next_version_description;  ?>
                           </div>


                           <?php
                           if($is_copy_working)
                           { 
                              echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate' ,'class'=>'w-100')); ?> 
                              <div class="col-12 my-5 text-center w-100">
                                 <input type="submit" name="download" value="Upgrade To latest Version" class="btn btn-primary">
                              </div>
                              <?php echo form_close();
                           }
                           else
                           { ?>

                              <div class="col-12 my-5">
                                 <h6 class="text-center text-danger"> Please download the latest version from below link, than extract and upload on project's root directory.</h6>
                                 <div class="text-danger text-center">
                                    *** Imp: Always keep maintenance mode active while uploading the files. 
                                 </div>
                                 
                              </div>
                              <?php 
                              $download = $next_version_all_in_one_zip ? "download" : "#";
                              $next_version_all_in_one_zip = $next_version_all_in_one_zip ? $next_version_all_in_one_zip : "#";
                              ?>

                              <div class="col-12 my-5 text-center w-100">
                                 <a href="<?php echo $next_version_all_in_one_zip; ?>" <?php echo $download; ?>  class="btn btn-primary">Download Update</a>
                                 
                              </div>
                              <?php
                           }
                        }
                        else
                        {
                           echo "<div class='col-12 text-center>'><h4 class='text-success'>".lang('You are On Latest Version !')."</h4></div>";
                           echo "<div class='col-12 text-center>'><a href='".base_url('admin/updates/check_update')."' class='btn btn-info'>".lang('Check for update')."</a></div>";
                           ?>
                           <div class="col-12 my-5">
                               <iframe src="<?php echo $api_url; ?>all_version_update_info.php"  class="w-100 border-0" style="height: 2000px !important;"> title="<?php echo lang('updates_details')?>"></iframe>

                           </div>
                           <?php 
                        }            
                  }
                  else
                  {
                     echo "<div class='col-12 text-center>'><h4 class='text-danger'>".$update_info_message."</h4></div>"; 
                  }
                  ?>
                  <div class="clearfix"></div>

            </div>
         </div>
      </div>
   </div>
</div>


