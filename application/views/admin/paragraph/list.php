<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
      echo '<div class="alert alert-success alert-dismissible">
      <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
      <strong>Success!</strong> '.$this->session->flashdata("success").'
      </div>';
   }
?>
<div class="panel panel-default">
   <a href="<?php echo base_url("admin/paragraph/add");?>" class="btn btn-primary cat float-right" ><?php echo lang('Add Paragraph'); ?></a>
   <div class="clearfix"></div>
   <hr>
   <table id="table" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
         <tr>
            <th><?php echo lang('No'); ?></th>
            <th><?php echo lang('Title'); ?></th>
            <th><?php echo lang('Sort Order'); ?></th>
            <th><?php echo lang('Action'); ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>
</div>