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
            <div class="navbar-bg"></div>
            <nav class="navbar navbar-expand-lg main-navbar">
               <form class="form-inline mr-auto">
                  <ul class="navbar-nav mr-3">
                     <li>
                        <a href="<?php echo base_url(''); ?>" data-toggle="sidebar" class="nav-link nav-link-lg">
                           <i class="fas fa-bars"></i>
                        </a>
                     </li>
                     <li>
                        <a href="<?php echo base_url(''); ?>" data-toggle="search" class="nav-link nav-link-lg d-sm-none">
                           <i class="fas fa-search"></i>
                        </a>
                     </li>
                  </ul>
               </form>
               <ul class="navbar-nav navbar-right">


                  <li>
                     <span class="dropdown">
                        <button id="session-language" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="btn btn-default">
                           <i class="fa fa-language"></i>
                           <span class="caret"></span>
                        </button>
                        <ul id="session-language-dropdown" class="dropdown-menu" role="menu" aria-labelledby="session-language">
                           <?php foreach ($this->languages as $key=>$name) : ?>
                              <li class=" nav-item">
                                 <a class="pl-2 nav-link text-dark"  href="#" rel="<?php echo xss_clean($key); ?>">
                                    <?php if ($key == $this->session->language) : ?>
                                       <i class="fa fa-check selected-session-language"></i>
                                    <?php endif; ?>
                                    <?php echo xss_clean($name); ?>
                                 </a>
                              </li>
                           <?php endforeach; ?>
                        </ul>
                     </span>
                  </li>

                  <?php 
                  if($this->session->logged_in && $this->session->logged_in['is_admin'])
                  {
                     ?>
                     <li class="dropdown">

                        <a href="<?php echo base_url('admin/users/edit/').$this->session->logged_in['id'];?>" data-toggle="dropdown" class="nav-link dropdown-toggle nav-link-lg nav-link-user">
                           <?php $loginImage = ($this->session->logged_in['image'] ? base_url('assets/images/user_image/'.$this->session->logged_in['image']) : base_url('assets/images/user_image/avatar-1.png'))?>
                           <img alt="" src="<?php echo xss_clean($loginImage);?>" class="rounded-circle mr-1">
                           <div class="d-sm-none d-lg-inline-block">Hi, <?php echo substr($this->session->logged_in['username'],0,10)."..";?>
                           </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right">
                           <?php
                           if(is_loged_in_user_is_subadmin() == FALSE)
                           { ?>
                              <a href="<?php echo base_url('admin/users/edit/').$this->session->logged_in['id'];?>" class="dropdown-item has-icon">
                                 <i class="far fa-user"></i><?php echo lang('admin_profile'); ?> 
                              </a>
                              <a href="<?php echo base_url('admin/settings'); ?>" class="dropdown-item has-icon">
                                 <i class="fas fa-cog"></i><?php echo lang('admin_admin_settings'); ?>
                              </a>
                              <div class="dropdown-divider"></div>

                              <?php
                           }
                           ?>
                           
                           <a href="<?php echo base_url('admin/logout'); ?>" class="dropdown-item has-icon"><i class="fas fa-sign-out-alt"></i><?php echo lang('core_button_logout'); ?>
                           </a>
                        </div>
                     </li>
                     <?php 
                  } ?>
               </ul>
            </nav>
            <?php // Fixed navbar ?>
            <div class="main-sidebar sidebar-style-2" tabindex="1" >
               <aside id="sidebar-wrapper">
                  <div class="sidebar-brand">
                     <a href="<?php echo base_url(); ?>"><?php echo xss_clean($this->settings->site_name); ?></a>
                  </div>
                  
                  <div class="sidebar-brand sidebar-brand-sm">
                     <?php 
                     $words = explode(" ", $this->settings->site_name);
                     $acronym = "";
                     foreach ($words as $w) 
                     {
                        $acronym .= $w[0];
                     }
                     ?>
                     <a class="text-uppercase" href="<?php echo base_url(); ?>"><?php echo xss_clean($acronym); ?></a>
                  </div>

                  <ul class="sidebar-menu">

                     <li class="dropdown <?php echo $this->uri->segment(2) == '' || $this->uri->segment(2) == 'index' || $this->uri->segment(2) == 'index_0' ? 'active' : ''; ?>">
                        <a href="<?php echo base_url('admin/dashboard'); ?>" class="nav-link">
                           <i class="fas fa-fire"></i>
                           <span><?php echo lang('admin_dashboard');?></span>
                        </a>
                     </li>


                     <?php
                     if(is_loged_in_user_is_subadmin() == FALSE)
                     {
                      ?>

                        <li class="dropdown<?php echo (strstr(uri_string(), 'admin/category')) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fas fa fa-list-alt" aria-hidden="true"></i>
                              <span><?php echo lang('admin_category'); ?></span>
                           </a>
                           <ul class="dropdown-menu">
                              <li class="<?php echo (uri_string() == 'admin/category') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/category'); ?>" class="nav-link">
                                    <?php echo lang('category_list'); ?>
                                 </a>
                              </li>
                              <li class="<?php echo (uri_string() == 'admin/category/form') ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('admin/category/form'); ?>" class="nav-link"><?php echo lang('add_category'); ?>
                                 </a>
                              </li>
                           </ul>
                        </li>
                        <?php 
                     } ?>

                     <li class="dropdown<?php echo ((strstr(uri_string(), 'admin/quiz')) OR(strstr(uri_string(), 'admin/paragraph')) OR (strstr(uri_string(), 'admin/section')) ) ? ' active' : ''; ?>">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                           <i class="fas fa-newspaper"></i>
                           <span><?php echo lang('dashboard_quiz'); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                           <li class="<?php echo (uri_string() == 'admin/quiz') ? 'active' : ''; ?>">
                              <a href="<?php echo base_url('admin/quiz'); ?>" class="nav-link"><?php echo lang('quiz_list'); ?> 
                              </a>
                           </li>
                           <li class="<?php echo (uri_string() == 'admin/quiz/add') ? 'active' : ''; ?>">
                              <a href="<?php echo base_url('admin/quiz/add'); ?>" class="nav-link"><?php echo lang('admin_add_quiz'); ?> 
                              </a>
                           </li>

                           <li class="<?php echo (strstr(uri_string(), 'admin/quiz-grading')) ? ' active' : ''; ?>">
                              <a href="<?php echo base_url('admin/quiz-grading'); ?>" class="nav-link"><?php echo lang('quiz_grading'); ?> 
                              </a>
                           </li>

                           <li class="<?php echo (strstr(uri_string(), 'admin/paragraph')) ? ' active' : ''; ?>">
                              <a href="<?php echo base_url('admin/paragraph'); ?>" class="nav-link"><?php echo lang('quiz_paragraphs'); ?> 
                              </a>
                           </li>

                           <li class="<?php echo (strstr(uri_string(), 'admin/section')) ? ' active' : ''; ?>">
                              <a href="<?php echo base_url('admin/section'); ?>" class="nav-link"><?php echo lang('sections'); ?> 
                              </a>
                           </li>

                           <li class="<?php echo (strstr(uri_string(), 'admin/report-question')) ? ' active' : ''; ?>">
                              <a href="<?php echo base_url('admin/report-question'); ?>" class="nav-link"><?php echo lang('report_question'); ?> 
                              </a>
                           </li>
                           
                        </ul>
                     </li>

                     <li class="dropdown <?php echo (strstr(uri_string(), 'admin/study')) ? ' active' : ''; ?>">
                        <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                           <i class="fa fa-window-restore" aria-hidden="true"></i>
                           <span><?php echo lang('admin_study_material'); ?></span>
                        </a>
                        <ul class="dropdown-menu">
                           <li class="<?php echo (uri_string() == 'admin/study') ? 'active' : ''; ?>">
                              <a href="<?php echo base_url('admin/study'); ?>" class="nav-link"> <?php echo lang('admin_quiz_study_material_list'); ?> 
                              </a>
                           </li>
                        </ul>
                     </li>

                     <?php
                     if(is_loged_in_user_is_subadmin() == FALSE)
                     {
                      ?>


                        <li class="dropdown <?php echo ((strstr(uri_string(), 'admin/users')) OR (strstr(uri_string(), 'admin/payment')) OR (strstr(uri_string(), 'admin/membership'))) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fas fa-columns"></i>
                              <span><?php echo lang('dashboard_user'); ?></span>
                           </a>
                           <ul class="dropdown-menu">
                              <li class="<?php echo (strstr(uri_string(), 'admin/users')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('/admin/users'); ?>" class="nav-link"><?php echo lang('users'); ?>
                                 </a>
                              </li>

                              <li class="<?php echo (uri_string() == 'admin/users/tutor') ? 'active' : ''; ?>"><a href="<?php echo base_url('/admin/users/tutor'); ?>" class="nav-link"><?php echo lang('tutor_requests'); ?></a>
                              </li>

                              <li class="<?php echo (uri_string() == 'admin/users/backup') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/users/backup'); ?>" class="nav-link"><?php echo lang('backups'); ?> 
                                 </a>
                              </li>

                              <li class="<?php echo (uri_string() == 'admin/payment') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/payment'); ?>" class="nav-link"><?php echo lang('payment_list'); ?> 
                                 </a>
                              </li>

                              <li class="<?php echo (strstr(uri_string(), 'admin/membership')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/membership'); ?>" class="nav-link"><?php echo lang('membership_list'); ?> 
                                 </a>
                              </li>

                           </ul>
                        </li>

                        <li class="dropdown<?php echo ((strstr(uri_string(), 'admin/settings/menu_item') OR uri_string() == 'admin/settings/menu_list') OR (strstr(uri_string(), 'admin/pages')) OR (strstr(uri_string(), 'admin/blog')) OR (strstr(uri_string(), 'admin/custom_fields')) OR (strstr(uri_string(), 'admin/testimonial')) OR (uri_string() == 'admin/footer') OR (uri_string() == 'admin/media') ) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fas fa-palette"></i> <span><?php echo lang('CMS'); ?></span>
                           </a>
                           <ul class="dropdown-menu">
                              <?php if($this->settings->file_uploader == 'media manager') { ?>
                                 <li class="<?php echo (strstr(uri_string(), 'admin/media')) ? 'active' : ''; ?>">
                                    <a href="<?php echo base_url('admin/media'); ?>" class="nav-link"> <?php echo lang('media'); ?> 
                                    </a>
                                 </li>
                              <?php } ?>   
                              <li class="<?php echo (strstr(uri_string(), 'admin/pages')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/pages'); ?>" class="nav-link"> <?php echo lang('pages'); ?> 
                                 </a>
                              </li>

                              <li class="<?php echo ((uri_string() == 'admin/blog') OR (uri_string() == 'admin/blog/blog_category_form'))  ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/blog'); ?>" class="nav-link"> <?php echo lang('blog_category'); ?> 
                                 </a>
                              </li>

                              <li class="<?php echo ((uri_string() == 'admin/blog/post') OR (uri_string() == 'admin/blog/blog_post_form')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/blog/post'); ?>" class="nav-link"> <?php echo lang('blog_post'); ?>
                                 </a>
                              </li>

                              <li class="<?php echo ((uri_string() == 'admin/settings/menu_list') OR (uri_string() == 'admin/settings/menu_item')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('/admin/settings/menu_list'); ?>" class="nav-link"><?php echo lang('front_menu_items'); ?>
                                 </a>
                              </li>

                              <li class="<?php echo (strstr(uri_string(), 'admin/custom_fields')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/custom_fields'); ?>" class="nav-link"> <?php echo lang('custom_fields'); ?> 
                                 </a>
                              </li>

                              <li class="<?php echo (strstr(uri_string(), 'admin/testimonial')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/testimonial'); ?>" class="nav-link"> <?php echo lang('testimonials'); ?>
                                 </a>
                              </li>
                             

                              <li class="<?php echo (uri_string() == 'admin/footer') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('/admin/footer'); ?>" class="nav-link">
                                    <span><?php echo lang('footers_section'); ?></span>
                                 </a>
                              </li>

                           </ul>
                        </li>




                        <li class="dropdown<?php echo (strstr(uri_string(), 'admin/template') OR (uri_string() == 'admin/contact') OR (strstr(uri_string(), 'admin/advertisment')) OR (strstr(uri_string(), 'admin/sponsors')) ) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fas fa-bullhorn"></i>
                              <span><?php echo lang('SEO/Marketing'); ?> </span>
                           </a>

                           <ul class="dropdown-menu">

                              
                              <li class="<?php echo (uri_string() == 'admin/contact') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('/admin/contact'); ?>" class="nav-link">
                                    <span><?php echo lang('contacts'); ?></span>
                                 </a>
                              </li>


                              <li class="<?php echo (strstr(uri_string(), 'admin/template')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('/admin/template'); ?>" class="nav-link">
                                    <span><?php echo lang('admin_email_template'); ?></span>
                                 </a>
                              </li>

                              <li class="<?php echo (strstr(uri_string(), 'admin/advertisment')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('/admin/advertisment'); ?>" class="nav-link">
                                    <span><?php echo lang('advertisment'); ?></span>
                                 </a>
                              </li>


                              <li class="<?php echo (strstr(uri_string(), 'admin/sponsors')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/sponsors'); ?>" class="nav-link"> <?php echo lang('sponsors'); ?> 
                                 </a>
                              </li>

                           </ul>
                        </li>





                        <li class="dropdown<?php echo (strstr(uri_string(), 'admin/institution') OR strstr(uri_string(), 'admin/course')) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fas fa-university"></i>
                              <span><?php echo lang('institutions'); ?> </span>
                           </a>
                           <ul class="dropdown-menu">
                              <li class="<?php echo (strstr(uri_string(), 'admin/institution')) ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/institution'); ?>" class="nav-link"> <?php echo lang('institutions'); ?> 
                                 </a>
                              </li>
                              <li class="<?php echo (strstr(uri_string(), 'admin/course'))  ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/course'); ?>" class="nav-link"> <?php echo lang('courses'); ?> 
                                 </a>
                              </li>
                           </ul>
                        </li>


                        <li class="dropdown<?php echo (strstr(uri_string(), 'admin/language')) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fas fa-language"></i>
                              <span><?php echo lang('nav_language'); ?></span>
                           </a>
                           <ul class="dropdown-menu">
                              <li class="<?php echo (uri_string() == 'admin/language') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/language'); ?>" class="nav-link"><?php echo lang('language_list'); ?>
                                 </a>
                              </li>
                              <li class="<?php echo (uri_string() == 'admin/language/add') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/language/add'); ?>" class="nav-link"><?php echo lang('admin_add_language'); ?>
                                 </a>
                              </li>
                              <li class="<?php echo (uri_string() == 'admin/language/add_token') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/language/add_token'); ?>" class="nav-link"><?php echo lang('add_new_token') ?>
                                 </a>
                              </li>
                           </ul>
                        </li>

                        <li class="dropdown<?php echo (strstr(uri_string(), 'admin/coupon')) ? ' active' : ''; ?>">
                           <a href="#" class="nav-link has-dropdown" data-toggle="dropdown">
                              <i class="fa fa-gift"></i>
                              <span><?php echo lang('coupons'); ?></span>
                           </a>
                           <ul class="dropdown-menu">
                              <li class="<?php echo (uri_string() == 'admin/coupon') ? 'active' : ''; ?>">
                                 <a href="<?php echo base_url('admin/coupon'); ?>" class="nav-link"><?php echo lang('coupon_list'); ?>
                                 </a>
                              </li>
                           </ul>      
                        </li>


                        <li class="<?php echo (uri_string() == 'admin/settings') ? 'active' : ''; ?>">
                           <a href="<?php echo base_url('/admin/settings'); ?>" class="nav-link">
                              <i class="fas fa-cog"></i>
                              <span><?php echo lang('admin_settings'); ?></span>
                           </a>
                        </li>

                        <li class="dropdown <?php echo (strstr(uri_string(), 'admin/db-backup-list')) ? ' active' : ''; ?>">
                           <a href="<?php echo base_url('admin/db-backup-list'); ?>" class="nav-link">
                              <i class="fas fa-database"></i>
                              <span><?php echo lang('db_backup');?></span>
                           </a>
                        </li>


                        <li class="dropdown<?php echo (strstr(uri_string(), 'admin/updates')) ? ' active' : ''; ?>">
                           <a href="<?php echo base_url('admin/updates'); ?>" class="nav-link">
                              <i class="fa fa-sync" aria-hidden="true"></i>
                              <span><?php echo lang('Updates'); ?></span>
                           </a>
                        </li>  

                        <?php
                        }
                        ?> 
                     
                  </ul>
                  
               </aside>
            </div>
            <?php // Main body ?>

            <div class="main-content">
               <section class="section">
                  <?php // Page title ?>
                  <div class="section-header">
                     <h1><?php echo xss_clean($page_header); ?></h1>
                  </div>
                  <div class="section-body"></div>

                  <?php // Main content ?>
                  <?php echo ($content); ?>
               </section>
            </div>

            <?php // Footer ?>
            <footer class="main-footer">
               <div class="footer-left">
                  <p class="text-muted">
                     <?php echo lang('core_text_page_rendered'); ?>
                     | PHP v<?php echo phpversion(); ?>
                     | MySQL v<?php echo mysqli_get_client_version(); ?>
                     | CodeIgniter v<?php echo xss_clean(CI_VERSION); ?>
                     | <?php echo xss_clean($this->settings->site_name); ?> : <?php echo xss_clean($this->settings->site_version); ?>
                  </p>
               </div>
               <div class="footer-right">
               </div>
            </footer>

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