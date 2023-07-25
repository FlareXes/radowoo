<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row"> 
	<div class="col-12 col-md-12 col-lg-12">
		<div class="card">
			<?php
				if(isset($package_id)) {
    				$data['tab_package_id'] = $package_id;
    		 		$this->load->view('admin/package/common_tab_list',$data);
    		 	}
    		?>
    		<div class="card-body">
				<input type="hidden" class="package-id" value="<?php echo $package_id;?>">
				<div class="row mt-3">
					<div class="col-5">
						<div class="form-group">
	              			<?php echo  form_label(lang('quiz_category_name'), 'category_id'); ?>
	              			<?php 
	                			echo form_dropdown('category_id', $category_data, array(), 'id="category_id" class="form-control select_dropdown select-category"'); 
	              			?> 
	            		</div>
					</div>
					<div class="col-5 select-quiz d-none">
						<div class="form-group">
	              			<?php echo  form_label(lang('quiz_name'), 'quiz_id'); ?>
	              			<select name="quiz_id" class="select_dropdown form-control quiz-data"></select>
	            		</div>
					</div>
					<div class="col-2">
						<div class="form-group mt-4 pt-2">
		                  <button class="btn btn-primary  btn-block add-now" type="button">
		                    <?php echo lang('admin_add_now'); ?></button>
		                  </div>
					</div>
				</div>
    			<hr>
    			<?php echo form_open('',array('id'=>'packagequiz','role'=>'form','novalidate'=>'novalidate'));?>
    			<div class="sortable-div">
    			<?php 
    				if(isset($get_package_quiz_array) && $get_package_quiz_array)
    				{
    					$i=1;
    					$package_count = 0;
    					foreach($get_package_quiz_array as $package_key => $package_value)
    					{
    						$package_count++;
    			?>
	    			<div class="row mt-3 row-drag">
	    				<div class="col-12">
	    					<div class="shadow-lg custom-shawow p-3 mb-3 bg-white rounded">
	    						<div class="quiz-detail package-quiz-position" data-quiz_order="<?php echo $package_value->quiz_order;?>">
	    							<span class="sort-icon"><i class="fa fa-sort" aria-hidden="true"></i></span>
	    							<input type="hidden" name="quiz_id[]" value="<?php echo $package_value->id;?>">
	    							<input type="hidden" name="quiz_order[]" class="sortable_number" value="<?php echo $i;?>">
	    							<input type="hidden" name="package_id[]" value="<?php echo $package_id;?>">
	    							<div class="sortable-number"><?php echo $i++ ?></div>
	    							<div class="package-quiz-title">
	    								<span><?php echo lang('quiz_name') .": ";?></span><br>
	    								<?php 
	    									$quiz_title = strlen($package_value->title) > 75 ? substr($package_value->title,0,75)."..." : $package_value->title;
	    								?>
	    								<span><?php echo $quiz_title;?></span>
	    							</div>
	    							<div class="package-quiz-price">
	    								<span><?php echo lang('quiz_price') .": ";?></span><br>
	    								<?php $price = ($package_value->price > 0) ? $this->settings->paid_currency . " ". $package_value->price : "Free";?>
	    								<span><?php echo $price;?></span>
	    							</div>
	    							<div class="package-quiz-question">
	    								<span><?php echo lang('admin_question_per_test') .": ";?></span><br>
	    								<span><?php echo $package_value->number_questions;?></span>
	    							</div>
	    							<div class="package-quiz-duration">
	    								<span><?php echo lang('admin_test_duration') .": ";?></span><br>
	    								<span><?php echo $package_value->duration_min;?></span>
	    							</div>
	    							<div class="package-quiz-delete">
	    								<a href="<?php echo base_url('admin/package/delete_package_quiz/'.$package_id.'/'.$package_value->id);?>" class="btn btn-danger  btn-block package-delete-quiz"><i class="fa fa-times" aria-hidden="true"></i></a>
				               		</div>	
	    						</div>
	    					</div>
	    				</div>
	    			</div>
    			<?php } }?>
    			<?php if(isset($package_count) && $package_count >=2) { ?>
    				<input type="submit"  value="<?php echo lang('package_quiz_order_save');?>" class="btn btn-primary px-5 float-right">
    			<?php } ?>	
    		</div>
    		<?php echo form_close();?>
    		</div>
    	</div>
    </div>
</div>
