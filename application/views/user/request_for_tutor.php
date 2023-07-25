<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
  <div class="container-fluid pt-5 body_background">
      <div class="container">
        <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="card card-primary">
               <div class="card-header">
                  <h4><?php echo lang('request_for_tutor') ?></h4>
               </div>
                <div class="card-body " id="tutor_request_form">

                    <?php 
                    if($user_data->user_request_for_tutor == 0 OR empty($user_data->user_request_for_tutor))
                    {


                        echo form_open_multipart('', array('role'=>'form')); ?>
                      
                            <?php // user_qualification_experience ?>
                            <div class="row">
                                 <div class="form-group col-12<?php echo form_error('user_qualification_experience') ? ' has-error' : ''; ?>">
                                    <?php echo form_label(lang('your_qualification_experience'), 'user_qualification_experience', array('class'=>'control-label')); ?>
                                    <span class="required"> * </span>
                                   
                                        <textarea required rows="8" name="user_qualification_experience" class="form-control user_qualification_experience" value="<?php echo $this->input->post('user_qualification_experience'); ?>"></textarea>

                                    <span class="small text-danger"> <?php echo strip_tags(form_error('user_qualification_experience')); ?> </span>
                                 </div>
                            </div>

                            <?php // buttons  ?>
                            <div class="row ">
                               <div class="form-group col-12 mt-3"> 
                                    <button type="submit" name="submit" class="btn btn-block btn-success">
                                        <span class="glyphicon glyphicon-save"></span> 
                                        <?php echo lang('request_now'); ?>
                                    </button>
                                   
                                </div>
                            </div>
                        <?php echo form_close(); 
                    }
                    else if($user_data->user_request_for_tutor == 1)
                    {
                        ?>
                         <div class="row">
                            <div class="col-12">
                                <div class="alert alert-success">
                                    <?php echo lang('your_request_for_tutor_is_already_submited'); ?>
                                </div>
                            </div>
                         </div>

                        <?php
                        
                    }
                    else if($user_data->user_request_for_tutor == 2)
                    {
                        ?>
                         <div class="row">
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <?php echo lang('your_request_for_tutor_has_been_rejected'); ?>
                                </div>
                            </div>
                         </div>

                        <?php
                        
                    }
                    else
                    {
                         ?>
                         <div class="row">
                            <div class="col-12">
                                <div class="alert alert-warning">
                                    <?php echo lang('something_went_wrong'); ?>
                                </div>
                            </div>
                         </div>

                        <?php
                    } ?>


               </div>
              </div>     
            </div>
          </div>
        </div>

      </div>
  </div>

   <?php
      if ($this->session->userdata('logged_in')) : 
         if(uri_string() == 'user/register')
         {
            return redirect(base_url('profile'));
         }
      endif;
   ?>

