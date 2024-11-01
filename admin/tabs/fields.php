<?php 
global $wpticketultra, $wptucomplement, $wptu_custom_fields;
if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

$fields = array();
$fields = get_option('wptu_profile_fields');
ksort($fields);


$last_ele = end($fields);
$new_position = $last_ele['position']+1;

$meta_custom_value = "";
$qtip_classes = 'qtip-light ';
?>

<div class="wptu-ultra-sect" >
<h3>
	<?php _e('Custom Fields Customizer','wp-ticket-ultra'); ?>
</h3>
<p>
	<?php _e("This section allow you to set different fields to each of your Products and Departments. For example, for the sales department you can ask for sale number and for the support department you can ask for a serial number, website etc etc.",'wp-ticket-ultra'); ?>
</p>


<a href="#wptu-add-field-btn" class="button button-secondary"  id="wptu-add-field-btn"><i
	class="wptu-icon-plus"></i>&nbsp;&nbsp;<?php _e('Click here to add new field','wp-ticket-ultra'); ?>
</a>


</div>

<div class="wptu-ultra-sect" >



<label for="bup__custom_form"><?php _e('Department:','wp-ticket-ultra'); ?> </label>
<?php echo $this->get_sites_drop_down_admin();?>
               

</div>

<div class="wptu-ultra-sect wptu-ultra-rounded" id="wptu-add-new-custom-field-frm" >

<table class="form-table uultra-add-form">

	

	<tr valign="top">
		<th scope="row"><label for="uultra_type"><?php _e('Type','wp-ticket-ultra'); ?> </label>
		</th>
		<td><select name="uultra_type" id="uultra_type">
				<option value="usermeta">
					<?php _e('Field','wp-ticket-ultra'); ?>
				</option>
				<option value="separator">
					<?php _e('Separator','wp-ticket-ultra'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('You can create a separator or a field','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_field"><?php _e('Editor / Input Type','wp-ticket-ultra'); ?>
		</label></th>
		<td><select name="uultra_field" id="uultra_field">
				<?php  foreach($wpticketultra->allowed_inputs as $input=>$label) { ?>
				<option value="<?php echo $input; ?>">
					<?php echo $label; ?>
				</option>
				<?php } ?>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('When user edit profile, this field can be an input (text, textarea, image upload, etc.)','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>

	<tr valign="top" >
		<th scope="row"><label for="uultra_meta_custom"><?php _e('New Custom Meta Key','wp-ticket-ultra'); ?>
		</label></th>
		<td><input name="uultra_meta_custom" type="text" id="uultra_meta_custom"
			value="<?php echo $meta_custom_value; ?>" class="regular-text" /> <i
			class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Enter a custom meta key for this profile field if do not want to use a predefined meta field above. It is recommended to only use alphanumeric characters and underscores, for example my_custom_meta is a proper meta key.','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>
    
   
	<tr valign="top">
		<th scope="row"><label for="uultra_name"><?php _e('Label','wp-ticket-ultra'); ?> </label>
		</th>
		<td><input name="uultra_name" type="text" id="uultra_name"
			value=""
			class="regular-text" /> <i
			class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Enter the label / name of this field as you want it to appear in front-end (Profile edit/view)','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_tooltip"><?php _e('Tooltip Text','wp-ticket-ultra'); ?>
		</label></th>
		<td><input name="uultra_tooltip" type="text" id="uultra_tooltip"
			value=""
			class="regular-text" /> <i
			class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('A tooltip text can be useful for social buttons on profile header.','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>
    
    
     <tr valign="top">
                <th scope="row"><label for="uultra_help_text"><?php _e('Help Text','wp-ticket-ultra'); ?>
                </label></th>
                <td>
                    <textarea class="uultra-help-text" id="uultra_help_text" name="uultra_help_text" title="<?php _e('A help text can be useful for provide information about the field.','wp-ticket-ultra'); ?>" ></textarea>
                    <i class="uultra-icon-question-sign uultra-tooltip2"
                                title="<?php _e('Show this help text under the profile field.','wp-ticket-ultra'); ?>"></i>
                </td>
            </tr>

	
  

	<tr valign="top">
		<th scope="row"><label for="uultra_can_edit"><?php _e('User can edit','wp-ticket-ultra'); ?>
		</label></th>
		<td><select name="uultra_can_edit" id="uultra_can_edit">
				<option value="1">
					<?php _e('Yes','wp-ticket-ultra'); ?>
				</option>
				<option value="0">
					<?php _e('No','wp-ticket-ultra'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Users can edit this profile field or not.','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>

	
	


	<tr valign="top">
		<th scope="row"><label for="uultra_private"><?php _e('This field is required','wp-ticket-ultra'); ?>
		</label></th>
		<td><select name="uultra_required" id="uultra_required">
				<option value="0">
					<?php _e('No','wp-ticket-ultra'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','wp-ticket-ultra'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Selecting yes will force user to provide a value for this field at registration and edit profile. Registration or profile edits will not be accepted if this field is left empty.','wp-ticket-ultra'); ?>"></i>
		</td>
	</tr>

	<tr valign="top">
		<th scope="row"><label for="uultra_show_in_register"><?php _e('Show on Registration form','wp-ticket-ultra'); ?>
		</label></th>
		<td><select name="uultra_show_in_register" id="uultra_show_in_register">
				<option value="0">
					<?php _e('No','wp-ticket-ultra'); ?>
				</option>
				<option value="1">
					<?php _e('Yes','wp-ticket-ultra'); ?>
				</option>
		</select> <i class="uultra-icon-question-sign uultra-tooltip2"
			title="<?php _e('Show this field on the registration form? If you choose no, this field will be shown on edit profile only and not on the registration form. Most users prefer fewer fields when registering, so use this option with care.','wp-ticket-ultra'); ?>"></i>
		</td>
        
        
	</tr>
    
    
     
    
            
   

	
	<tr valign="top">
		<th scope="row"></th>
		<td>
          <div class="wptu-ultra-success wptu-notification" id="wptu-sucess-add-field"><?php _e('Success ','wp-ticket-ultra'); ?></div>
        <input type="submit" name="bup-add" 	value="<?php _e('Submit New Field','wp-ticket-ultra'); ?>"
			class="button button-primary" id="wptu-btn-add-field-submit" /> 
            <input type="button"class="button button-secondary " id="wptu-close-add-field-btn"	value="<?php _e('Cancel','wp-ticket-ultra'); ?>" />
		</td>
	</tr>

</table>


</div>


<!-- show customizer -->
<ul class="wptu-ultra-sect wptu-ultra-rounded" id="uu-fields-sortable" >
		
  </ul>
  
           <script type="text/javascript">  
		
		      var custom_fields_del_confirmation ="<?php _e('Are you totally sure that you want to delete this field?','wp-ticket-ultra'); ?>";
			  
			  var custom_fields_reset_confirmation ="<?php _e('Are you totally sure that you want to restore the default fields?','wp-ticket-ultra'); ?>";
			   
			  var custom_fields_duplicate_form_confirmation ="<?php _e('Please input a name','wp-ticket-ultra'); ?>";
		 
		      wptu_reload_custom_fields_set();
		 </script>
         
         <div id="bup-spinner" class="wptu-spinner" style="display:">
            <span> <img src="<?php echo wptu_url?>admin/images/loaderB16.gif" width="16" height="16" /></span>&nbsp; <?php echo __('Please wait ...','wp-ticket-ultra')?>
	</div>
         
        