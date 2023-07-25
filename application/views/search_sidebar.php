<div class="card">
	<div class="card-body">
		<?php echo form_open($serch_url, array('role'=>'form','class' => "w-100 serch_headr_form",'method'=>"GET"));?>
			<input type="hidden" name="query" value="<?php echo $find_like; ?>">
			<div class="row">
				<div class="col-md-12">
					<div class="form-group">
						<select name="category" class="form-control">
							<option value=""><?php echo lang('filter_by_category'); ?></option>
							
							<?php
							if($all_category_data)
							{
								foreach ($all_category_data as $cat_arr) 
								{
									$f_selected_cat = $filter_category == $cat_arr->category_slug ? "selected" : "";
									echo "<option $f_selected_cat value='$cat_arr->category_slug'> $cat_arr->category_title</option>";
								}
							}


							?>

						</select>
					</div>
					<div class="form-group">
						<select name="purchage_type" class="form-control select2">
							<option value=""><?php echo lang('filter_by_price'); ?></option>
							<option <?php echo  $filter_purchage_type == 'paid' ? "selected" : ""; ?>  value="paid">Paid</option>
							<option <?php echo  $filter_purchage_type == 'premium' ? "selected" : ""; ?> value="premium">Premium</option>
							<option <?php echo  $filter_purchage_type == 'free' ? "selected" : ""; ?> value="free">Free</option>
						</select>
					</div>

					<div class="form-group">
						<select name="rating" class="form-control">
							<option value=""><?php echo lang('filter_by_rating'); ?></option>
							<option <?php echo  $filter_rating == 5 ? "selected" : ""; ?> value="5"><?php echo lang("4 to 5 star"); ?></option>
							<option <?php echo  $filter_rating == 4 ? "selected" : ""; ?> value="4"><?php echo lang("3 to 4 star"); ?></option>
							<option <?php echo  $filter_rating == 3 ? "selected" : ""; ?> value="3"><?php echo lang("2 to 3 star"); ?></option>
							<option <?php echo  $filter_rating == 2 ? "selected" : ""; ?> value="2"><?php echo lang("1 to 2 star"); ?></option>
							<option <?php echo  $filter_rating == 1 ? "selected" : ""; ?> value="1"><?php echo lang("0 to 1 star"); ?></option>
						</select>
					</div>

					<div class="form-group">
						<select name="duration" class="form-control">
							<option value=""><?php echo lang('filter_by_duration'); ?></option>
							<option <?php echo  $filter_duration == '5min' ? "selected" : ""; ?> value="5min"><?php echo ("1 to 5 minute"); ?></option>
							<option <?php echo  $filter_duration == '10min' ? "selected" : ""; ?> value="10min"><?php echo ("6 to 10 minute"); ?></option>
							<option <?php echo  $filter_duration == '20min' ? "selected" : ""; ?> value="20min"><?php echo ("11 to 20 minute"); ?></option>
							<option <?php echo  $filter_duration == '30min' ? "selected" : ""; ?> value="30min"><?php echo ("21 to 30 minute"); ?></option>
							<option <?php echo  $filter_duration == '1hour' ? "selected" : ""; ?> value="1hour"><?php echo ("31 Minute to 1 Hour"); ?></option>
							<option <?php echo  $filter_duration == '6hour' ? "selected" : ""; ?> value="6hour"><?php echo ("1 Hour to 6 Hour"); ?></option>
							<option <?php echo  $filter_duration == '1day' ? "selected" : ""; ?> value="1day"><?php echo ("6 Hour to 1 Day"); ?></option>
							<option <?php echo  $filter_duration == 'unlimited' ? "selected" : ""; ?> value="unlimited"><?php echo ("More Then One Day"); ?></option>
						</select>
					</div>

					<div class="form-group">
						<button class="btn btn-block btn-info" type="submit">Apply Filter</button>
						<a class="btn btn-block btn-secondary" href="?" >Clear </a>
					</div>

				</div>
			</div>
		<?php echo form_close();?>

	</div>
</div>