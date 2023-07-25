<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="container-fluid pt-5 body_background">
   <div class="container">
      <div class="row">
          <div class="col-12 col-sm-10 offset-sm-1 col-md-8 offset-md-2 col-lg-8 offset-lg-2 col-xl-8 offset-xl-2">
            <div class="card card-primary">
               <div class="card-header">
                  <h4><?php echo lang('contact_title'); ?></h4>
               </div>
               <div class="card-body">
                  <?php echo form_open('', array('role'=>'form')); ?>
                  <div class="row">
                     <?php  // message title  ?>
                     <div class="form-group col-sm-12<?php echo form_error('title') ? ' has-error' : ''; ?>">
                        <?php echo form_label(lang('title'), 'title', array('class'=>'control-label')); ?>
                        <span class="required">*</span>
                        <?php echo form_input(array('name'=>'title', 'value'=>set_value('title'), 'class'=>'form-control')); ?>
                        <span class="small text-danger"> <?php echo strip_tags(form_error('title')); ?> </span>
                     </div>
                     <?php  // name ?>
                     <div class="form-group col-sm-6<?php echo form_error('name') ? ' has-error' : ''; ?>">
                        <?php echo form_label(lang('name'), 'name', array('class'=>'control-label')); ?>
                        <span class="required">*</span>
                        <?php echo form_input(array('name'=>'name', 'value'=>set_value('name'), 'class'=>'form-control')); ?>
                        <span class="small text-danger"> <?php echo strip_tags(form_error('name')); ?> </span>
                     </div>
                     <?php  // email ?>
                     <div class="form-group col-sm-6<?php echo form_error('email') ? ' has-error' : ''; ?>">
                        <?php echo form_label(lang('email'), 'email', array('class'=>'control-label')); ?>
                        <span class="required">*</span>
                        <?php echo form_input(array('name'=>'email', 'value'=>set_value('email'), 'class'=>'form-control')); ?>
                        <span class="small text-danger"> <?php echo strip_tags(form_error('email')); ?> </span>
                     </div>
                  </div>
                  <div class="row">
                     <?php  // messsage body ?>
                     <div class="form-group col-sm-12<?php echo form_error('message') ? ' has-error' : ''; ?>">
                        <?php echo form_label(lang('message'), 'message', array('class'=>'control-label')); ?>
                        <span class="required">*</span>
                        <?php echo form_textarea(array('name'=>'message', 'value'=>set_value('message'), 'class'=>'form-control')); ?>
                        <span class="small text-danger"> <?php echo strip_tags(form_error('message')); ?> </span>
                     </div>
                  </div>
                  
                     
                  <!-- ************************************************************* -->

                  <div class="row">
                    <?php

                    if($customfields)
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
                  </div>

                  <!-- ************************************************************* -->





                  <div class="row my-3">
                     
                        <div class="col-12 text-center">
                           
                           <?php 
                           if (!empty($this->settings->recaptcha_secret_key) && !empty($this->settings->recaptcha_site_key) && $this->settings->enable_captch_code_login == "YES") 
                           { ?>
                              <div class="g-recaptcha m-auto <?php echo form_error('g-recaptcha-response') ? ' has-error captch_error' : ''; ?>" data-sitekey="<?php echo $this->settings->recaptcha_site_key; ?>">
                                 
                              </div> 

                               <span class="small form-error"> <?php echo strip_tags(form_error('g-recaptcha-response')); ?> </span> 

                              <?php 
                           } ?>

                        </div>
                  </div>

                  <?php // buttons ?>
                  <div class="row">
                     <div class="form-group col-sm-12">
                        <button type="submit" name="submit" class="btn btn-block btn-primary">Send</button>
                     </div>
                  </div>
                  <?php echo form_close(); ?>
               </div>
            </div>
          </div>
      </div>
   </div>
</div>