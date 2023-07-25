<?php if($custom_field) { 
    foreach($custom_field as $c_key => $c_value) {  
?>
    <div class="row">
        <div class="col-md-6">
            <label><?php echo $c_value['field_label']; ?>:</label>
        </div>
        <div class="col-md-6">    
            <div><?php echo $c_value['value']; ?></div>
        </div>
    </div>
<?php  } } else { ?>
    <?php echo lang('no_additional_data'); ?>
<?php } ?>    