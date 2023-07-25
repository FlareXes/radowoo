<?php if($blog_list_data) {
   foreach($blog_list_data as $like_post_key => $like_post_value)
   {      
?>
	<div class="col-md-4 col-xl-4 col-sm-6 blog-page blog_boxes">




      <div class="grid pb-5 mb-5">



        <div class="blog_post_icons">
           <a href="/javascript:void(0)" class="icon float-left text-white-im post-view"><i class="fe fe-eye mr-1"></i> 
             <span class="value"><?php echo xss_clean($like_post_value->view_total_post);?></span>
            </a>
            <a href="javascript:void(0)" class="icon like-post inline-block ml-3 float-right text-white-im post-like">
                 
                 <?php $like_or_not = (isset($like_post_value->is_like) && $like_post_value->is_like) ? 'text-success' : 'text-muted';?>
                 
                 <i class="fav_icon  fas fa-heart <?php echo xss_clean($like_or_not);?> " data-post_id="<?php echo $like_post_value->id; ?>"></i> 

                 <?php $total_like = (isset($like_post_value->total_like) && $like_post_value->total_like > 0 )? $like_post_value->total_like : "";?>
                 <span class="value like-quiz-count text-white"><?php echo xss_clean($total_like);?></span>
             </a>
        </div>
         
         <figure class="effect-ming">
            
            <?php 
               $blog_post_image = (isset($like_post_value->post_image) && !empty($like_post_value->post_image) ? base_url('assets/images/blog_image/post_image/'.$like_post_value->post_image) : base_url('assets/images/blog_image/default.jpg'));
            ?>
            
            <img src="<?php echo xss_clean($blog_post_image);?>" alt="img09"/>
            
            <figcaption>
               <?php 
                  $title = strlen($like_post_value->post_title) > 10 ? substr($like_post_value->post_title,0,7)."..." : $like_post_value->post_title;
               ?>
              <h2><?php echo xss_clean($title);  ?></h2>
              <a href="<?php echo  base_url('blog/').$like_post_value->post_slug ?>"><?php echo lang('view_more') ?></a>
            </figcaption>

         </figure>
         
         <div class="pt-3 infobox">
            <div class="quiz-single-title translate_quiz_title">
                <a href="<?php echo  base_url('blog/').$like_post_value->post_slug ?>" class="text-dark"><?php echo xss_clean($like_post_value->post_title);?></a>
            </div>
            <?php 

              $user_full_name = $like_post_value->first_name ." ". $like_post_value->last_name;
              $user_full_name = trim($user_full_name) ? $user_full_name : "Site Administrator";

             ?>
            <div class="text-black-im user-list-name">
                <span> <i class="fas fa-user mr-2"></i> <?php echo xss_clean($user_full_name);?></span> 
            </div>

            <a href="<?php echo  base_url('blogs/').$like_post_value->blog_category_slug ?>" class="btn btn-sm w-100 btn-link" > <?php echo xss_clean($like_post_value->title);?></a>
            <div class="clearfix"></div>
          </div>
         


      </div>
   </div>
<?php } } 
	else {
?>
	<div class="col-12 text-center text-danger"> <?php echo lang('no_data_found'); ?></div>
<?php } ?>