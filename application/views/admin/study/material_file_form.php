<?php defined('BASEPATH') OR exit('No direct script access allowed'); ?>

<div class="row"> 
	<div class="col-12 col-md-12 col-lg-12">
		<div class="card">
			<?php
				if(isset($study_material_id)) {
    				$data['tab_study_material_id'] = $study_material_id;
    				$data['contant_type'] = $contant_type;
    		 		$this->load->view('admin/study/common_tab_list',$data);
    		 	}
    			?>
    		<div class="card-body">
				
				<?php echo form_open_multipart('', array('role'=>'form','novalidate'=>'novalidate')); ?>
					
					<div class="row mt-3">
						
						<div class="col-4">
				            <div class="form-group <?php echo form_error('section_id') ? ' has-error' : ''; ?>">
				              <?php echo  form_label(lang('section'), 'section_id'); ?> 
				              <span class="required">*</span>
				              <?php 
				                $populateData = $this->input->post('section_id') ? $this->input->post('section_id') : (isset($study_material_content_data->section_id) ? $study_material_content_data->section_id :  '' );
				              ?>
				              <select name="section_id" class="form-control section_id" id="section_id">
				              	<?php
				              		foreach ($study_material_section_data as $key => $section_obj) 
				              		{
				              			$selected = $populateData == $section_obj->id ? "selected" : "";
				              			echo "<option $selected value='$section_obj->id'>$section_obj->title</option>";
				              		}
				              	?>
				              </select>
				              <?php 

				              ?>
				              
				              <span class="small form-error"> <?php echo strip_tags(form_error('section_id')); ?> </span>
				            </div>
				        </div>


						<div class="col-5">
				            <div class="form-group <?php echo form_error('title') ? ' has-error' : ''; ?>">
				              <?php echo  form_label(lang('admin_title'), 'title'); ?> 
				              <span class="required">*</span>
				              <?php 
				                $populateData = $this->input->post('title') ? $this->input->post('title') : (isset($study_material_content_data->title) ? $study_material_content_data->title :  '' );
				              ?>
				              <input type="text" name="title" id="title" class="form-control" value="<?php echo xss_clean($populateData);?>">
				              <span class="small form-error"> <?php echo strip_tags(form_error('title')); ?> </span>
				            </div>
				        </div>

						<div class="col-3">
				            <div class="form-group <?php echo form_error('duration') ? ' has-error' : ''; ?>">
				              <?php echo  form_label(lang('content_duration_in_minutes'), 'duration'); ?> 
				              <span class="required">*</span>
				              <?php 
				                $populateData = $this->input->post('duration') ? $this->input->post('duration') : (isset($study_material_content_data->duration) ? $study_material_content_data->duration :  '' );
				              ?>
				              <input type="text" name="duration" id="duration" class="form-control" value="<?php echo xss_clean($populateData);?>">
				              <span class="small form-error"> <?php echo strip_tags(form_error('duration')); ?> </span>
				            </div>
				        </div>
			           
			          <?php if($this->settings->file_uploader == 'default uploader') { ?>  
			           <div class="col-12">
			           		<div class="form-group  <?php echo (form_error('file_upload') OR form_error('embed_code_contant')) ? ' has-error' : ''; ?>">
			           		
			               <?php 
			               if($contant_type == 'image')
			               	{
				           		if(isset($study_material_content_data->value) && !empty($study_material_content_data->value))
				               	{ ?>
				               		<a href="<?php echo base_url('assets/uploads/study_material/'.$study_material_content_data->value);?>" target="_blank"><?php echo $study_material_content_data->value;?></a>	
				               		<br/>
				               		<?php 
				               	} 

				               	echo form_label(lang('admin_upload_image'), 'file_upload'); ?>
				               	<span class="text-danger"> * <span class="text-warning">  <?php echo lang("upload_max_file_size") ." ". ini_get('upload_max_filesize')."B";?></span></span>
				               	<br/>
				               	<input type="file" name="file" id="categoryimage" class="form-control" accept="image/*">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('file_upload')); ?> </span>
				               	<div class="clearfix"></div>

				           	   	<?php $allowed_formate = 'gif, jpg, png, bmp, jpeg'; ?>
				           	   	<label class="mt-2">Allowed File Type: <?php echo $allowed_formate; ?></label>
				           	   <?php  
			           		}
			           		elseif ($contant_type == "video") 
			           		{
				           		if(isset($study_material_content_data->value) && !empty($study_material_content_data->value))
				               	{ ?>
				               		<a href="<?php echo base_url('assets/uploads/study_material/'.$study_material_content_data->value);?>" target="_blank"><?php echo $study_material_content_data->value;?></a>	
				               		<br/>
				               		<?php 
				               	} 

				               	echo form_label(lang('admin_upload_image'), 'file_upload'); ?>
				               	<span class="text-danger"> * <span class="text-warning">  <?php echo lang("upload_max_file_size") ." ". ini_get('upload_max_filesize')."B";?></span></span>
				               	<br/>
				               	<input type="file" name="file" id="categoryimage" class="form-control" accept="video/*">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('file_upload')); ?> </span>
				               	<div class="clearfix"></div>

				           	   	<?php $allowed_formate = 'flv, wma, avi, wmv, mp4, wav, mov, avchd, webm, mkv'; ?>
				           	   	<label class="mt-2">Allowed File Type: <?php echo $allowed_formate; ?></label>
				           	   <?php 

			           		}			            
			           		elseif ($contant_type == "audio") 
			           		{
				           		if(isset($study_material_content_data->value) && !empty($study_material_content_data->value))
				               	{ ?>
				               		<a href="<?php echo base_url('assets/uploads/study_material/'.$study_material_content_data->value);?>" target="_blank"><?php echo $study_material_content_data->value;?></a>	
				               		<br/>
				               		<?php 
				               	} 

				               	echo form_label(lang('admin_upload_image'), 'file_upload'); ?>
				               	<span class="text-danger"> * <span class="text-warning">  <?php echo lang("upload_max_file_size") ." ". ini_get('upload_max_filesize')."B";?></span></span>
				               	<br/>
				               	<input type="file" name="file" id="categoryimage" class="form-control" accept="audio/*">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('file_upload')); ?> </span>
				               	<div class="clearfix"></div>
				           	   
				           	   	<?php $allowed_formate = 'ogg, aac, mp3, mpeg, amr'; ?>
				           	   	<label class="mt-2">Allowed File Type: <?php echo $allowed_formate; ?></label>
				           	   <?php 

			           		}			            
			           		elseif ($contant_type == "pdf") 
			           		{
				           		if(isset($study_material_content_data->value) && !empty($study_material_content_data->value))
				               	{ ?>
				               		<a href="<?php echo base_url('assets/uploads/study_material/'.$study_material_content_data->value);?>" target="_blank"><?php echo $study_material_content_data->value;?></a>	
				               		<br/>
				               		<?php 
				               	} 

				               	echo form_label(lang('admin_upload_image'), 'file_upload'); ?>
				               	<span class="text-danger"> * <span class="text-warning">  <?php echo lang("upload_max_file_size") ." ". ini_get('upload_max_filesize')."B";?></span></span>
				               	<br/>
				               	<input type="file" name="file" id="categoryimage" class="form-control" accept="application/pdf">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('file_upload')); ?> </span>
				               	<div class="clearfix"></div>
				           	   				           	   
				           	   	<?php $allowed_formate = 'pdf'; ?>
				           	   	<label class="mt-2">Allowed File Type: <?php echo $allowed_formate; ?></label>
				           	   <?php 

			           		}			            
			           		elseif ($contant_type == "doc") 
			           		{
				           		if(isset($study_material_content_data->value) && !empty($study_material_content_data->value))
				               	{ ?>
				               		<a href="<?php echo base_url('assets/uploads/study_material/'.$study_material_content_data->value);?>" target="_blank"><?php echo $study_material_content_data->value;?></a>	
				               		<br/>
				               		<?php 
				               	} 

				               	echo form_label(lang('admin_upload_image'), 'file_upload'); ?>
				               	<span class="text-danger"> * <span class="text-warning">  <?php echo lang("upload_max_file_size") ." ". ini_get('upload_max_filesize')."B";?></span></span>
				               	<br/>
				               	<input type="file" name="file" id="categoryimage" class="form-control" accept=".xlsx,.xls,.doc, .docx,.ppt, .pptx, .odt, .rtf, .xps, .csv, .ods ">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('file_upload')); ?> </span>
				               	<div class="clearfix"></div>				           	   
				           	   
				           	   	<?php $allowed_formate = 'doc, docx, odt, rtf, xps, csv, ods, xls, xlsx, ppt, pptx'; ?>
				           	   	<label class="mt-2">Allowed File Type: <?php echo $allowed_formate; ?></label>
				           	   <?php 

			           		}
			           		elseif ($contant_type == "youtube-embed-code") 
			           		{
								$populateData = (isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "";
				               	echo form_label(lang('youtube_embed_code'), 'embed_code_contant'); ?>
				               	<span class="text-danger"> *</span>
				               	<br/>
				               	<input type="text" name="embed_code_contant" id="embed_code_contant" class="form-control embed_code_contant" required="true" value="<?php echo xss_clean($populateData);?>">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('embed_code_contant')); ?> </span>
				           	   	<div class="clearfix"></div>
				           	   <?php 
			           		}
			           		elseif ($contant_type == "vimeo-embed-code") 
			           		{
			           			$populateData = (isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "";
				               	echo form_label(lang('vimeo_embed_code'), 'embed_code_contant'); ?>
				               	<span class="text-danger"> *</span>
				               	<br/>
				               	<input type="text" name="embed_code_contant" id="embed_code_contant" class="form-control embed_code_contant" required="true" value="<?php echo xss_clean($populateData);?>">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('embed_code_contant')); ?> </span>
				           	   	<div class="clearfix"></div>
				           	   <?php 
			           		}
			           		elseif ($contant_type == "content") 
			           		{
			           			$populateData = (isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "";
				               	echo form_label(lang('contaet_or_embed_code'), 'embed_code_contant'); ?>
				               	<span class="text-danger"> *</span>
				               	<br/>
				               	<textarea required="true" name="embed_code_contant" id="embed_code_contant" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('embed_code_contant')); ?> </span>
				           	   	<div class="clearfix"></div>
				           	   <?php 
			           		}


			           		?>

			           		</div>
			           </div>
						<?php } ?>	   

			           
			           <div class="col-12">
			           		<div class="form-group <?php echo (form_error('file_upload') OR form_error('embed_code_contant') OR form_error('media_path')  OR form_error('media_path_image')  OR form_error('media_path_doc')  OR form_error('media_path_pdf')  OR form_error('media_path_audio')  OR form_error('media_path_video')) ? ' has-error' : ''; ?>">
			           		
			               <?php 

			               if(($contant_type == 'image' OR $contant_type == "video"  OR $contant_type == "audio"  OR $contant_type == "pdf"  OR $contant_type == "doc" ) && $this->settings->file_uploader == 'media manager')
			               	{
			               		$populateData_val = "";

			               		$allowed_formate = 'gif, jpg, png, bmp, jpeg';
			               		if($contant_type == "video")
			               		{
			               			 $allowed_formate = 'flv, wma, avi, wmv, mp4, wav, mov, avchd, webm, mkv';
			               		}
			               		else if ($contant_type == "audio") 
			               		{
			               			$allowed_formate = 'ogg, aac, mp3, mpeg, amr'; 
			               		}
			               		else if ($contant_type == "pdf") 
			               		{
			               			$allowed_formate = 'pdf';
			               		}
			               		else if ($contant_type == "doc") 
			               		{
			               			$allowed_formate = 'doc, docx, odt, rtf, xps, csv, ods, xls, xlsx, ppt, pptx';
			               		}

			               		echo form_label(lang('upload_file'), 'media_path');
			               		$populateData_val = $this->input->post('media_path') ? $this->input->post('media_path') : ((isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "" );
			               		$is_media_file = $this->input->post('media_path') ? 1 : ((isset($study_material_content_data->is_media_file) && $study_material_content_data->is_media_file == 1) ? 1 : 0 );
				           		if($populateData_val)
				               	{ 
				               		if($is_media_file)
				               		{
				               			?>
				               			: <span class="mr-2">
				               			<a href="<?php echo base_url("media/").$populateData_val;?>" target="_blank"><?php echo $populateData_val;?></a></span>
				               			<?php
				               		}
				               		else
				               		{
				               			?>
				               				: <span class="mr-2"><a href="<?php echo base_url('assets/uploads/study_material/'.$populateData_val);?>" target="_blank"><?php echo $populateData_val;?></a></span>
				               			<?php
				               		}
				               		?>
				               		
				               		<?php 
				               	} 

				                ?>
				               	<span class=" ml-2 small form-error text-danger"> <?php echo (form_error('media_path_image')) ? lang('only_images_allowed') : ""; ?> </span>  
				               	<span class=" ml-2 small form-error text-danger"> <?php echo (form_error('media_path_video')) ? lang('only_video_allowed') : ""; ?> </span>  
				               	<span class=" ml-2 small form-error text-danger"> <?php echo (form_error('media_path_audio')) ? lang('only_audio_allowed') : ""; ?> </span>  
				               	<span class=" ml-2 small form-error text-danger"> <?php echo (form_error('media_path_pdf')) ? lang('only_pdf_allowed') : ""; ?> </span>  
				               	<span class=" ml-2 small form-error text-danger"> <?php echo (form_error('media_path_doc')) ? lang('only_doc_allowed') : ""; ?> </span>  
				               	<?php if($this->settings->file_uploader == 'media manager') { ?>	
			                    	<input type="text" name="media_path" id="media_path" class="form-control ifile_img_filed" value="<?php echo $populateData_val; ?>" readonly="true" />

			                    	<a href = "javascript:void(0)" id ="elfinder_button"><?php echo lang("upload_file"); ?></a>
								<?php } ?>
									
			                        <?php 
			                        if($contant_type == 'image')
			                        { ?>
			                        	<div id="selectedImages">
			                        	<?php
				                        if($populateData_val && empty(form_error('media_path_image')) && $study_material_content_data->is_media_file == 1)
				                        {
				                           ?>

				                            <img class="img-thumbnail ifile_img_thumb" src="<?php echo base_url("media/").$populateData_val; ?>">
				                           <?php
				                        }
				                        else
				                        {
				                        	if($populateData_val && $study_material_content_data->is_media_file != 1)
							                {
							              		?> 
							                  <img  class="img-thumbnail ifile_img_thumb" id="popup" src="<?php echo base_url('assets/images/studymaterial/').$populateData_val; ?>">
							              		<?php
							                }
				                        }
				                        ?>
				                        </div>
				                        <?php
				                    }
			                        ?>

		                    	<span class="ml-2 text-warning"><?php echo lang('allowed_file_type'); ?> : <?php echo $allowed_formate; ?></span>

		                     	<span class="small form-error text-danger ml-2"> <?php echo strip_tags(form_error('media_path')); ?> </span> 
			                    
					           

				               
				           	<?php  
			           		}
			           		elseif ($contant_type == "youtube-embed-code") 
			           		{
								$populateData = (isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "";
				               	echo form_label(lang('youtube_embed_code'), 'embed_code_contant'); ?>
				               	<span class="text-danger"> *</span>
				               	<br/>
				               	<input type="text" name="embed_code_contant" id="embed_code_contant" class="form-control embed_code_contant" required="true" value="<?php echo xss_clean($populateData);?>">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('embed_code_contant')); ?> </span>
				           	   	<div class="clearfix"></div>
				           	   <?php 
			           		}
			           		elseif ($contant_type == "vimeo-embed-code") 
			           		{
			           			$populateData = (isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "";
				               	echo form_label(lang('vimeo_embed_code'), 'embed_code_contant'); ?>
				               	<span class="text-danger"> *</span>
				               	<br/>
				               	<input type="text" name="embed_code_contant" id="embed_code_contant" class="form-control embed_code_contant" required="true" value="<?php echo xss_clean($populateData);?>">
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('embed_code_contant')); ?> </span>
				           	   	<div class="clearfix"></div>
				           	   <?php 
			           		}
			           		elseif ($contant_type == "content") 
			           		{
			           			$populateData = (isset($study_material_content_data->value) && $study_material_content_data->value) ? $study_material_content_data->value : "";
				               	echo form_label(lang('contaet_or_embed_code'), 'embed_code_contant'); ?>
				               	<span class="text-danger"> *</span>
				               	<br/>
				               	<textarea required="true" name="embed_code_contant" id="embed_code_contant" class="form-control editor" rows="5" ><?php echo xss_clean($populateData);?></textarea>
				               	<span class="small form-error w-100"> <?php echo strip_tags(form_error('embed_code_contant')); ?> </span>
				           	   	<div class="clearfix"></div>
				           	   <?php 
			           		}
			           		?>

			           		</div>
			           </div>
			           
			           <div class="clearfix"></div>


				        <div class="col-12">
				            <?php $saveUpdate = (isset($study_material_id) && !empty($study_material_id) && isset($study_material_content_id) && !empty($study_material_content_id) ? lang('core_button_update') : lang('core_button_save')); ?>
				            <input type="submit"  value="<?php echo $saveUpdate;?>" class="btn btn-primary px-5">
				            <a href="<?php echo base_url('admin/study/material-file/'.$study_material_id);?>" class="btn btn-dark px-5"><?php echo lang('core_button_cancel'); ?></a>
				        </div>

					</div>

				<?php echo form_close();?>

			</div>

    	</div>
    </div>
</div>

<script type="text/javascript">
    var connector = "<?php echo $connector; ?>";
    var ApiFullVersion = "<?php echo elFinder::getApiFullVersion();?>";
    // var file = "<?php echo $media_url;?>";
</script>