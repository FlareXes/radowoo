<nav class="navbar navbar-expand-lg navbar-light bg-light">
  <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
  <div class="collapse navbar-collapse" id="navbarNav"> 
    <ul class="navbar-nav">
    	<?php 

          $quiz_active = $study_material = "";
          $category_url = $category_id ? "/$category_id" : "";
      		if(uri_string() == "admin/backup/export/quiz".$category_url)
      		{
      			$quiz_active = "active";
      		}
      		elseif(uri_string() == "admin/backup/export/study-material".$category_url)
      		{
      			$study_material = "active";	
      		}
      	?>

      <li class="nav-item <?php echo $quiz_active;?>">
        <a class="nav-link " href="<?php echo base_url('admin/backup/export/quiz');?>"><?php echo lang('Quiz'); ?> </a>
        
      </li>

      <li class="nav-item <?php echo $study_material;?>">
        <a class="nav-link" href="<?php echo base_url('admin/backup/export/study-material');?>"><?php echo lang('Study Material'); ?></a>
      </li>

    </ul>
  </div>
</nav>