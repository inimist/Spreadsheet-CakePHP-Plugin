<style>
fieldset {
  margin-bottom: 10px;

}
#addEntry .form-control{
width:80% !important;
display: inline-block !important;
}

</style>

<div class="log-columns form">

<?php 
//pr($this->Session->read('Auth.User.department_id'));
$listPrivacy = array('me'=>'Only me', 'department'=>'My Department');
echo $this->Form->create('LogColumnsCustomList', array(
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
));
?>
<fieldset>
		<legend><?php echo __('Add New List'); ?></legend>
						
									<?php echo $this->Form->hidden('user_id', array('label'=>false));  ?>
									<?php echo $this->Form->input('name', array('label'=>'List Name')); ?>
									<strong>Add Entries For List</strong>
									<div id="addEntry">
											<?php echo $this->Form->input('LogColumnsCustomListEntry.0.entry_name', array('label'=>false)); ?>
									</div>
											<?php //echo $this->Form->button('Add Entry', array('type'=>'button', 'id'=>'addNewEntry'));
											echo $this->Html->link(__('Add New Entry'), array('#'), array('id' => 'addNewEntry','title'=>'Add New Entry')); 
											?>
							<br><br>
							<?php 
								if(isset($is_admin)){
										echo $this->Form->input('is_public', array('label'=>'Public List'));
											}
								else{
									echo $this->Form->input('list_privacy', array('type'=>'select', 'options'=>$listPrivacy, 'label'=>'Who Can View', 'empty'=>false, 'id'=>'listPrivacy'));
									echo $this->Form->hidden('department_id', array('label'=>false));
										}
								?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>															
	



<script>

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
