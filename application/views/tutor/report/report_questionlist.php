<?php defined('BASEPATH') OR exit('No direct script access allowed');
   if($this->session->flashdata('success')) {
    echo '<div class="alert alert-success alert-dismissible">
            <button type="button" class="close" aria-label="close" data-dismiss="alert">&times;</button>
            <strong>Success!</strong> '.$this->session->flashdata("success").'
        </div>';
   }
?>
<div class="panel panel-default"> 
   <div class="clearfix"></div>
   <hr>
   <table id="reportquestiontable" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <thead>
         <tr>
            <th><?php echo lang('admin_table_no'); ?></th>
            <th><?php echo lang('user_name'); ?></th>
            <th><?php echo lang('selected_option'); ?></th>
            <th><?php echo lang('remark'); ?></th>
            <th><?php echo lang('datetime'); ?></th>
            <th><?php echo lang('goto_question'); ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>
</div>