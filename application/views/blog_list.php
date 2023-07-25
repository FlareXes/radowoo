<div class="container">
	<div class="row">
	    <div class="col-12 text-center"> <h2 class="heading"><?php echo ucwords($title); ?></h2> <hr></div>
	    <div class="col-md-9 col-xl-9 col-sm-6">
	    	<div class="row">
	    		<?php 
                   $data['blog_list_data'] = $blog_post_data;
                   $this->load->view('common_blog_list',$data);
                ?>
	    	</div>
	    	<?php echo xss_clean($pagination) ?>
	    </div>
	    <div class="col-md-3 col-xl-3 col-sm-12">
	    	
	    	<div class="list-group">
	    		<?php $active = $category_slug == 'all' OR $category_slug =='ALL' ? ' active' : '' ?>
	    		<a href="<?php echo base_url('blogs');?>" class="list-group-item list-group-item-action list-group-item-secondary <?php echo $active; ?>">
			       	<h4 class="m-0"> <?php echo lang("admin_blog_category_list");?></h4>
			    </a> 

		    	<?php 
		    	if($blog_category)
	    		{
	    			foreach($blog_category as $blog_category_key => $blog_category_value)
	    			{ 
	    				$active = $category_slug == $blog_category_value->slug ? ' active' : '' ?>

	    				<a href="<?php echo base_url('blogs/'.$blog_category_value->slug);?>" class="list-group-item list-group-item-action <?php echo $active; ?>">
			       			<i class="fas fa-angle-right"></i> <?php echo $blog_category_value->title;?>
			    		</a> <?php 
	    			}
	    		} ?>	
	    	</div>



	        <?php 
	        $advertisments_on_position = get_advertisment_by_position('blog_list_page_below_sidebar');
	        if($advertisments_on_position)
	        {
	        	foreach ($advertisments_on_position as $advertisment_on_position) 
          		{

	         	 $ads_img_url = $path = base_url("assets/images/advertisment/$advertisment_on_position->image");
	          		?>
	              
	              <div class="row mt-5">
	                <div class="col-12 text-center">
	                  	<?php
		                if($advertisment_on_position->is_goole_adsense == 0)
		                { ?>
  		                  	<a class="w-100 h-100 d-flex" target="_blank" href="<?php echo $advertisment_on_position->url; ?>">
	                    		<img class="w-100 advertisment_image_on_front" height="200px" src="<?php echo $ads_img_url; ?>">
	                  		</a>
		                  <?php
		                }
		                else
		                {
		                  echo "<div class='w-100 px-2 bg-white'>". htmlspecialchars_decode($advertisment_on_position->google_ad_code)."</div>";
		                }
		                ?>
	                </div>
	              
	              </div>

	              <?php
		        } 
	        } ?>

	    </div>


	</div>
	    
</div>