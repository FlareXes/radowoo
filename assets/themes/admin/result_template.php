<?php defined('BASEPATH') OR exit('No direct script access allowed');
/**
* Admin Template
*/  
?>
<!DOCTYPE html>
   <?php 
   $is_rtl = '';
   $rtl_dir = '';
   $margin_auto = 'mr-auto'; 
   $order_two = NULL; 
   if ($this->session->is_rtl) 
   {
      
      ?>
      <html lang="en" dir="rtl">
      <?php  
      $is_rtl = 'rtl_language';
      $rtl_dir = 'rtl';
      $margin_auto = 'ml-auto';
      $order_two = 'order-2';
   }
   else
   { 
      ?>
      <html lang="en">
      <?php 
   } 
   ?>
   <head>
      <meta charset="utf-8">
      <meta http-equiv="X-UA-Compatible" content="IE=edge">
      <meta name="viewport" content="width=device-width, initial-scale=1">
      <!-- CSRF Token -->
      <meta name="csrf-token" content="">
      <link rel="icon" type="image/x-icon" sizes="32x32" href="<?php echo base_url('/assets/images/logo/'); ?><?php echo get_admin_setting('site_favicon'); ?>" />
      <title><?php echo xss_clean($page_title); ?> - <?php echo xss_clean($this->settings->site_name); ?></title>

      <?php 
      if ($this->session->is_rtl) 
      {

         ?>
         <link rel="stylesheet" type="text/css" href="<?php echo base_url("assets/themes/admin/css/");?>rtl.bootstrap.min.css">
         <?php  
      }
      ?>

         <?php // CSS files ?>
         <?php if (isset($css_files) && is_array($css_files)) : ?>
         <?php foreach ($css_files as $css) : ?>
            <?php if ( ! is_null($css)) : ?>
               <?php $separator = (strstr($css, '?')) ? '&' : '?'; ?>
               <link rel="stylesheet" href="<?php echo xss_clean($css); ?><?php echo xss_clean($separator); ?>v=<?php echo xss_clean($this->settings->site_version); ?>">
               <?php echo "\n"; ?>
            <?php endif; ?>
         <?php endforeach; ?>
      <?php endif; ?>


      <?php 
         $disable_right_click = get_admin_setting('disable_right_click');
         $disable_print_screen = get_admin_setting('disable_print_screen');
         $disable_cut_copy_paste = get_admin_setting('disable_copy_paste_click');
         $hader_logo_height = get_admin_setting('header_logo_height');
         $hader_logo_height = $hader_logo_height > 1 ? $hader_logo_height : 65;
         $login_user_id = isset($this->user['id']) ? $this->user['id'] : 0;

      ?>


      <script> 
         var BASE_URL = '<?php echo base_url(); ?>'; 
         var csrf_Name = '<?php echo $this->security->get_csrf_token_name() ?>'; 
         var csrf_Hash = '<?php echo $this->security->get_csrf_hash(); ?>'; 
         var rtl_dir = "<?php echo xss_clean($rtl_dir); ?>";
         var are_you_sure = "<?php echo lang('are_you_sure'); ?>";
         var permanently_deleted = "<?php echo lang('it_will_permanently_deleted'); ?>";
         var yes_delere_it = "<?php echo lang('yes_delere_it'); ?>";
         var table_search = "<?php echo lang('table_search'); ?>";
         var table_show = "<?php echo lang('table_show'); ?>";
         var table_entries = "<?php echo lang('table_entries'); ?>";
         var table_showing = "<?php echo lang('table_showing'); ?>";
         var table_to = "<?php echo lang('table_to'); ?>";
         var table_of = "<?php echo lang('table_of'); ?>";
         var java_error_msg = "<?php echo lang('java_error_msg'); ?>";
         var table_previous = "<?php echo lang('table_previous'); ?>";
         var table_next = "<?php echo lang('table_next'); ?>";
         var update_company_also = "<?php echo lang('you_need_to_update_also'); ?>";
         var yes_add_more_field = "<?php echo lang('yes_add_more_field'); ?>";
         var yes_remove_it = "<?php echo lang('yes_delere_it'); ?>";
         var remove_from_company = "<?php echo lang('it_will_remove_from_quiz_also'); ?>";

         var flash_message = '<?php echo $this->session->flashdata('message'); ?>';
         var flash_error = '<?php echo $this->session->flashdata('error'); ?>';

         var error_report = '<?php echo xss_clean($this->error); ?>';


         var login_user_id = <?php echo xss_clean($login_user_id); ?>;
          login_user_id = parseInt(login_user_id); 

          var disable_right_click = '<?php echo xss_clean($disable_right_click); ?>';
          var disable_print_screen = '<?php echo xss_clean($disable_print_screen); ?>';
          var disable_cut_copy_paste = '<?php echo xss_clean($disable_cut_copy_paste); ?>';

      </script>

   </head>
   <body class="<?php echo xss_clean($is_rtl); ?>">
      <div id="app">
         <div class="main-wrapper main-wrapper-1">
            
            <?php // Main body ?>

            <div class="main-content px-0">
               <section class="section">
                  <?php // Page title ?>
                  <div class="section-header d-none">
                     <h1><?php echo xss_clean($page_header); ?></h1>
                  </div>
                  <div class="section-body"></div>

                  <?php // Main content ?>
                  <?php echo ($content); ?>
               </section>
            </div>

            

            <?php // Javascript files ?>
            <?php if (isset($js_files) && is_array($js_files)) : ?>
            <?php foreach ($js_files as $js) : ?>
               <?php if ( ! is_null($js)) : ?>
                  <?php $separator = (strstr($js, '?')) ? '&' : '?'; ?>
                  <?php echo "\n"; ?><script type="text/javascript" src="<?php echo xss_clean($js); ?><?php echo xss_clean($separator); ?>v=<?php echo xss_clean($this->settings->site_version); ?>"></script><?php echo "\n"; ?>
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

         </div>
      </div>
      <?php 
      if (isset($model_box))
      {
         echo xss_clean($model_box);
      } ?>
      <div class="modal fade" id="imagemodal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
         <div class="modal-dialog model_1000">
            <div class="modal-content">
               <div class="modal-header">
                  <h4 class="modal-title" id="myModalLabel"><?php echo lang('model_image_preview') ?></h4>
                  <button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only"><?php echo lang('close') ?></span></button>
               </div>
               <div class="modal-body text-center">
                  <img src="" id="imagepreview" >
               </div>
               <div class="modal-footer">
                  <button type="button" class="btn btn-default" data-dismiss="modal"><?php echo lang('close') ?></button>
               </div>
            </div>
         </div>
      </div>


      <!-- Button trigger modal -->
      <div class="modal fade" tabindex="-1" role="dialog" id="model_box_content">
         <div class="modal-dialog " role="document">
            <div class="modal-content">
               <div class="modal-header">
                  <h5 class="modal-title"><?php echo lang('model_box') ?></h5>
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                     <span aria-hidden="true">&times;</span>
                  </button>
               </div>
               <div class="modal-body">
                  <p><?php echo lang('no_data') ?></p>
               </div>
               <div class="modal-footer bg-whitesmoke br">
                  <button type="button" class="btn btn-secondary" data-dismiss="modal"><?php echo lang('close') ?></button>
                  <button type="button" class="btn btn-primary"><?php echo lang('welcome') ?></button>
               </div>
            </div>
         </div>
      </div>
      </div>
      <!-- Button trigger modal -->


      <script type="text/javascript">
         if(flash_message == 'undefined'){ var flash_message = ''; }
         if(flash_error == 'undefined'){ var flash_error = ''; }

         if(error_report == 'undefined'){ var error_report = ''; }

         if(flash_message )
         {
            new Noty({
               type: 'success',
               layout: 'topRight',
               text: flash_message,
               timeout : 5000,
               progressBar : true,
               theme    : 'metroui ',
               closeWith: ['click', 'button'],

            }).show();
         }

         if(flash_error)
         {
            new Noty({
               type: 'error',
               layout: 'topRight',
               text: flash_error,
               timeout : 5000,
               progressBar : true,
               theme    : 'mint',
               closeWith: ['click', 'button'],

            }).show();
         }       

         if(error_report)
         {
            new Noty({
               type: 'error',
               layout: 'topRight',
               text: error_report,
               timeout : 5000,
               progressBar : true,
               theme    : 'mint',
               closeWith: ['click', 'button'],

            }).show();
         }
      </script>

   </body>
</html>