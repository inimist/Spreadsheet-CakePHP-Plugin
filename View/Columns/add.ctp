<?php
$dropdownlist = array('option1'=>'Add New List', 'option2'=>'Choose From Exiting Lists');
$listPrivacy = array('me'=>'Only me', 'department'=>'My Department');
 ?>
<style>

.custom-dropdown-list .form-control{
width:30% !important;

}

.custom-dropdown-list .form-group {
  margin-bottom: 5px !important;
	}

#addEntry .form-control {
  width: 30% !important;
	}
</style>


<div class="log-columns form">

<?php echo $this->Form->create('LogColumn', array(
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
));
			$listPrivacy = array('me'=>'Only me', 'department'=>'My Department');


?>
	<fieldset>
		<legend><?php echo __('Add Log Column'); ?></legend>
    <p>Adding column to "<?php echo $log['Log']['log_name']; ?>"..</p>
	<?php
		echo $this->Form->hidden('log_id', array('value'=>$log['Log']['id']));
		echo $this->Form->input('column_name');
    echo $this->Form->input('column_type', array('type'=>'select', 'options'=>$column_types, 'label'=>__('Column Type'))); 
	
		?>
   
	<!---  Code for Custom List start  -->
		<div id="dropDownList" class="custom-dropdown-list">
							<div class="form-group">
								<select id="ListChoice" class="form-control"> 
									<option value="option1" >Add New List</option>
									<option value="option2" >Choose from exiting list</option>
								</select>
							</div>
							<div id="newList">
								<?php // echo $this->Form->hidden('log_columns_custom_list_id');  ?>
									<?php echo $this->Form->hidden('LogColumnsCustomList.user_id', array('label'=>false));  ?>
									List Name
									<?php echo $this->Form->input('LogColumnsCustomList.name', array('label'=>false)); ?>
									<?php 
									if(isset($is_admin)){
											echo $this->Form->input('LogColumnsCustomList.is_public', array('label'=>'Public List'));
												}
									else{
											echo $this->Form->input('list_privacy', array('type'=>'select', 'options'=>$listPrivacy, 'label'=>'Who Can View', 'empty'=>false, 'id'=>'listPrivacy'));
											echo $this->Form->hidden('LogColumnsCustomList.department_id', array('label'=>false));
											}
									?>
									Entries
									<div id="addEntry">
													<?php echo $this->Form->input('LogColumnsCustomListEntry.0.entry_name', array('label'=>false)); ?>
											</div>
									<?php // echo $this->Form->button('Add Entry', array('type'=>'button', 'id'=>'addNewEntry')); 
										echo $this->Html->link(__('Add New Entry'), array('#'), array('id' => 'addNewEntry','title'=>'Add New Entry')); ?>
								<br>

						</div>
						<div id="existingList">
									
									<?php  echo $this->Form->input('log_columns_custom_list_id', array('type'=>'select', 'options'=>$custom_lists, 'label'=>false, 'empty' => 'Please select the list'));  ?>
						</div>

		</div>
		<!---  Code for Custom List End -->
		<?php
		echo $this->Form->input('autofill', array('div' => array(
        'class' => 'form-group dtdd',
    )));
    echo $this->Form->input('skip_table', array('div' => array(
        'class' => 'form-group dtdd',
    )));
    echo $this->Form->input('rich_text', array('div' => array(
        'class' => 'form-group dtdd',
    )));
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>

<script>
$(document).ready(function(){

$("#dropDownList").hide();

$('#LogColumnColumnType').on('change', function(){
				if($(this).val()=='dropdown')	{
					$("#dropDownList").show();
					$("#existingList").hide();

						var listName =  $('#LogColumnColumnName').val();
						$('#LogColumnsCustomListName').val(listName);
						
						
					$('#ListChoice').on('change', function(){
						if($(this).val()=='option2'){
						$("#existingList").show();
						$("#newList").hide();
							}
						else{
								$("#newList").show();
								$("#existingList").hide();
						}
				});

				}

				else{
				$("#dropDownList").hide();

				}

				
		})
			

});


	$(document).ready(function() {
											 var x = 0; //initlal text box count
													$('#addNewEntry').click(function(e){ //on add input button click
												 e.preventDefault();
													x++; //text box increment
													$('#addEntry').append('<div class="form-group"><input class="form-control" type="text" name="data[LogColumnsCustomListEntry]['+ x + '][entry_name]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
													});
							
													$('#addEntry').on("click",".remove_field", function(e){ //user click on remove text
														 e.preventDefault(); $(this).parent('div').remove(); x--;
													});
						if($('#LogColumnsCustomListDepartmentId').val()!=''){
							$('#listPrivacy').val()== 'department';
									}
						$('#listPrivacy').on('change', function(){
						if($(this).val()== 'department')
						{
							//var department_ID = "<?php echo $this->Session->read('Auth.User.department_id');  ?>";
							$('#LogColumnsCustomListDepartmentId').val("<?php echo $this->Session->read('Auth.User.department_id');  ?>");
						}
						else{
							$('#LogColumnsCustomListDepartmentId').val('');
						}
						
					});
			});
						
</script>



