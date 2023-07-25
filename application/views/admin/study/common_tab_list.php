<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav"> 
    <ul class="navbar-nav">
    	<?php 

          $sm_slug_url = get_study_material_detail_page_url_by_id($tab_study_material_id);

          $study_section_id = isset($study_section_id) ? $study_section_id : NULL;
          $contant_type = isset($contant_type) ? $contant_type : NULL;
          $study_material_active = $material_file = "";
          $import_active = "";
          if(uri_string() == "admin/study/update/".$tab_study_material_id)
          {
            
            $study_material_active = "active";
          }
          if(uri_string() == "admin/study/material-file/".$tab_study_material_id || uri_string() == "admin/study/add-material-file/".$tab_study_material_id."/".$contant_type || $this->uri->segment(3) == "update-material-content" ||  uri_string() == "admin/study/section/".$tab_study_material_id || uri_string() == "admin/study/section-update/$tab_study_material_id/$study_section_id")
          {
            $material_file = "active";
          }

          if(uri_string() == "admin/rating/material/".$tab_study_material_id)
          {
            $rating_active = "active";  
          }

          if(uri_string() == "admin/study-data/import/".$tab_study_material_id OR uri_string() == "admin/study-data/import/")
          {
            $import_active = "active";  
          }

      ?>          
      <li class="nav-item <?php echo $study_material_active;?>">
        <a class="nav-link " href="<?php echo base_url('admin/study/update/'.$tab_study_material_id);?>"><?php echo lang('admin_edit_study_material'); ?></a>
      </li>
      
      <li class="nav-item <?php echo $material_file;?>">
        <a class="nav-link " href="<?php echo base_url('admin/study/material-file/'.$tab_study_material_id);?>"><?php echo lang('section_or_content'); ?></a>
      </li>

      <li class="nav-item <?php echo $rating_active;?>">
        <a class="nav-link" href="<?php echo base_url('admin/rating/material/').$tab_study_material_id;?>"><?php echo lang('admin_rating'); ?></a>
      </li>

      <li class="nav-item <?php echo $import_active;?>">
        <a class="nav-link" href="<?php echo base_url('admin/study-data/import/').$tab_study_material_id;?>"><?php echo lang('import'); ?></a>
      </li>

      <li class="nav-item ">
        <a class="nav-link" href="<?php echo $sm_slug_url;?>"><?php echo lang('preview'); ?></a>
      </li>



    </ul>
  </div>
</nav>