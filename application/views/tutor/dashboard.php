<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>
<div class="row">

   <div class="col-lg-3 col-md-6 col-sm-6 col-12"> 
      <div class="card card-statistic-1">
         <div class="card-icon bg-primary"><i class="far fa-user"></i></div>
         <div class="card-wrap">
            <div class="card-header">
               <h4>
                  <a  href="<?php echo base_url('tutor/users') ?>"><?php echo lang('dashboard_user'); ?> (<?php echo xss_clean($users); ?>) </a>
               </h4>
            </div>
         </div>
      </div>
   </div>

   <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
         <div class="card-icon bg-primary">
            <i class="fas fa-newspaper"></i>
         </div>
         <div class="card-wrap">
            <div class="card-header">
               <h4>
                  <a  href="<?php echo base_url('tutor/quiz') ?>"><?php echo lang('dashboard_quiz'); ?> (<?php echo xss_clean($quiz); ?>) </a>
               </h4>
            </div>
         </div>
      </div>
   </div>

   <div class="col-lg-3 col-md-6 col-sm-6 col-12">
      <div class="card card-statistic-1">
         <div class="card-icon bg-primary">
            <i class="fas fa-newspaper"></i>
         </div>
         <div class="card-header">
            <h4>
               <a  href="<?php echo base_url('tutor/blog/post') ?>"><?php echo lang('dashboard_blog_post'); ?>  (<?php echo xss_clean($blog_post); ?>) </a>
            </h4>
         </div>
      </div>
   </div>

</div>