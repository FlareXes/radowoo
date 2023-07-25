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
   <table id="coupontracktable" class="display table table-striped table-bordered" cellspacing="0" width="100%">
      <input type="hidden" class="coupon-id" value="<?php echo $coupon_id; ?>">
      <thead>
         <tr>
            <th><?php echo lang('admin_table_no'); ?></th>
            <th><?php echo lang('username'); ?></th>
            <th><?php echo lang('item_name'); ?></th>
            <th><?php echo lang('paid_amount'); ?></th>
         </tr>
      </thead>
      <tbody>
      </tbody>
   </table>
</div>