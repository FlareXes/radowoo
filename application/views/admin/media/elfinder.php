

<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
      echo '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> '.$this->session->flashdata("success").'
      </div>';
   }
?>
<div class="panel panel-default">

        <div id="elfinder"></div>
   
</div>
<script type="text/javascript">
    var connector = "<?php echo $connector; ?>";
    var ApiFullVersion = "<?php echo elFinder::getApiFullVersion();?>";
</script>
