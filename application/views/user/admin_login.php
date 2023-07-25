<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">

  <link rel="icon" type="image/x-icon" sizes="32x32" href="<?php echo base_url('/assets/images/logo/'); ?><?php echo get_admin_setting('site_favicon'); ?>" />
  <link href="https://fonts.googleapis.com/css?family=Quicksand:300,500,700|Work+Sans:400,700" rel="stylesheet">
         
  
  <?php if (isset($css_files) && is_array($css_files)) : ?>
  <?php foreach ($css_files as $css) : ?>
    <?php if ( ! is_null($css)) : ?>
      <?php $separator = (strstr($css, '?')) ? '&' : '?'; ?>
      <link rel="stylesheet" href="<?php echo xss_clean($css); ?><?php echo xss_clean($separator); ?>v=<?php echo xss_clean($this->settings->site_version); ?>"><?php echo "\n"; ?>
    <?php endif; ?>
  <?php endforeach; ?>
  <?php endif; ?>

  <?php
  $flash_error_msg =  str_replace("'","`",$this->session->flashdata('error'));
  $flash_success_msg =  str_replace("'","`",$this->session->flashdata('message'));
  ?>

  <script> 
    var BASE_URL = '<?php echo base_url(); ?>'; 
    var csrf_Name = '<?php echo $this->security->get_csrf_token_name() ?>'; 
    var csrf_Hash = '<?php echo $this->security->get_csrf_hash(); ?>'; 

    var flash_message = '<?php echo $flash_success_msg; ?>';
    var flash_error = '<?php echo $flash_error_msg; ?>';
    var error_report = '<?php echo xss_clean($this->error); ?>';
  </script>
</head>

<body class="h-100 my-auto pt-5">

  <input type="hidden" id="BASE_URL_OF_SITE" value="<?php echo base_url(); ?>">

      <div class="container-fluid pt-5 body_background  h-100">
         <div class="container  h-100">
            <div class="row align-items-center h-100">
               <div class="mx-auto col-12 col-sm-8 offset-sm-2 col-md-6 offset-md-3 col-lg-6 offset-lg-3 col-xl-4 offset-xl-4">
                  <div class="card card-primary">
                     <div class="card-header">
                        <h4>Admin Login</h4>
                     </div>
                     <div class="card-body">
                        <?php echo form_open('', array('class'=>'form-signin formlogin')); ?>
                        
                          <div class="form-group">
                             <?php echo form_label(lang('user_username_email'), 'username_email'); ?> 
                             <?php echo form_input(array('name'=>'username', 'id'=>'username', 'class'=>'form-control', 'placeholder'=>lang('user_username_email'), 'maxlength'=>256)); ?>
                             <span class="small text-danger"> <?php echo strip_tags(form_error('username_email')); ?> </span>
                          </div>

                          <div class="form-group">
                             <?php echo form_password(array('name'=>'password', 'id'=>'password', 'class'=>'form-control', 'placeholder'=>lang('password'), 'maxlength'=>72, 'autocomplete'=>'off')); ?>
                             <span class="small text-danger"> <?php echo strip_tags(form_error('password')); ?> </span>
                          </div>

                            <div class="row my-3">
                              <div class="col-12">
                                 <?php 
                                 if ($this->settings->recaptcha_secret_key && $this->settings->recaptcha_site_key && $this->settings->enable_captch_code_login == "YES") 
                                 { ?>
                                    <div class="g-recaptcha w-100 <?php echo form_error('g-recaptcha-response') ? ' has-error captch_error' : ''; ?>" data-sitekey="<?php echo $this->settings->recaptcha_site_key; ?>">
                                       
                                    </div> 

                                     <span class="small form-error"> <?php echo strip_tags(form_error('g-recaptcha-response')); ?> </span> 

                                    <?php 
                                 } ?>

                              </div>
                            </div>

                            <div class="form-group">
                               <?php echo form_submit(array('name'=>'submit', 'class'=>'btn btn-primary btn-lg btn-block'), lang('core_button_login')); ?>
                            </div>

                        <?php echo form_close(); ?> 
                     </div>
                  </div>
               </div>
            </div>
         </div>
      </div>



      <?php // Javascript files ?>
      <?php if (isset($js_files) && is_array($js_files)) : ?>
        <?php foreach ($js_files as $js) : ?>
        <?php if ( ! is_null($js)) : ?>
          <?php $separator = (strstr($js, '?')) ? '&' : '?'; ?>
          <?php echo "\n"; ?><script type="text/javascript" src="<?php echo xss_clean($js); ?><?php echo xss_clean($separator); ?>v = <?php echo xss_clean($this->settings->site_version); ?>"></script><?php echo "\n"; ?>
        <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <?php if (isset($js_files_i18n) && is_array($js_files_i18n)) : ?>
        <?php foreach ($js_files_i18n as $js) : ?>
          <?php if ( ! is_null($js)) : ?>
            <?php echo "\n"; ?><script type="text/javascript"><?php echo "\n" . $js . "\n"; ?></script><?php echo "\n"; ?>
          <?php endif; ?>
        <?php endforeach; ?>
      <?php endif; ?>

      <script type="text/javascript">  
          <?php if(!empty(get_admin_setting('footer_javascript'))) { echo html_entity_decode(get_admin_setting('footer_javascript')); } ?>
      </script>

</body>
</html>



