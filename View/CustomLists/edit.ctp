<?php if( $this->request->is('ajax') )	{ ?>
<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="editCustomListLabel">Edit Custom List</h4>
</div>
<?php } ?>
<style>
fieldset {
  margin-bottom: 10px;
}
.modal-footer {
  text-align: left;
}

</style>

<div class="log-columns form">

<?php  if($this->request->data['LogColumnsCustomList']['user_id']!= $this->Session->read('Auth.User.id') && !$this->Utility->isAdmin()){
	?>	<div class="modal-body"> <!---Modal Body Start --->
<h3>Sorry!! You cann't edit this list</h3>
</div>
<?php if( $this->request->is('ajax') )	{ ?>
<div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
</div>
<?php 
	}	
}
else{
	$listPrivacy = array('me'=>'Only me', 'department'=>'My Department');
 echo $this->Form->create('LogColumnsCustomList', array(
  'type' => 'file',
	'inputDefaults' => array(
		//'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	),
	'id'=>'editList'
	//'onsubmit'=>'return false;'
		));
echo $this->Form->hidden('id'); 
echo $this->Form->hidden('user_id'); 
echo $this->Form->hidden('request_type');
echo $this->Form->hidden('remove_Entries'); 

?>

<fieldset>
				
				<?php if( !$this->request->is('ajax') )	{ ?>
					<legend><?php echo __('Edit List'); ?></legend>
				<?php } ?>
				
				<div class="modal-body"> <!---Modal Body Start --->
								
								<?php	echo $this->Form->input('name', array('label'=>'List Name')); ?>
									<legend>Entries</legend>
									<div id="addEntry">
										<?php 
										
										
										if($this->request->data['LogColumnsCustomListEntry']) {

										foreach ($this->request->data['LogColumnsCustomListEntry'] as $entry_name): @$i++ ?>
										<?php 
											echo '<div class="form-group">';
											echo $this->Form->hidden('LogColumnsCustomListEntry.'.($i-1).'.id');
											echo $this->Form->input('LogColumnsCustomListEntry.'.($i-1).'.entry_name', array('label'=>false)); 
											echo '<a href="#" class="remove_entry">Remove</a></div>'; ?>

										<?php endforeach; 
										}
										else{
											$i = 0;
											echo "<strong>No Entry Found in " . $this->request->data['LogColumnsCustomList']['name'] . " List, Add New Entry </strong><br>";
										}
												?>
									</div>
											<?php //echo $this->Form->button('Add Entry', array('type'=>'button', 'id'=>'addNewEntry'));
												echo $this->Html->link(__('Add New Entry'), array('#'), array('id' => 'addNewEntry','title'=>'Add New Entry')); ?>
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
				</div><!---Modal Body End --->

	</fieldset>
			<div class="modal-footer">
			<?php echo $this->Form->end(__('Submit') ); ?>
			</div>
<?php 
	}	//End if Else
		?>
</div>															


<script>
var removeEntries =[];
$(document).ready(function() {
		
		var x = <?php echo $i; ?>; //initlal text box count
		$('#addNewEntry').click(function(e){ //on add input button click
			e.preventDefault();
			x++; //text box increment
			$('#addEntry').append('<div class="form-group"><input class="form-control" type="text" name="data[LogColumnsCustomListEntry]['+ x + '][entry_name]"/><a href="#" class="remove_field">Remove</a></div>'); //add input box
			});

		$('#addEntry').on("click",".remove_field", function(e){ //user click on remove text
			 e.preventDefault(); $(this).parent('div').remove(); x--;
			});
			
		$('#addEntry').on("click",".remove_entry", function(e){ //user click on remove text
			 e.preventDefault();
			 var removeEntryId = $(this).siblings('input').val(); 
				removeEntries.push(removeEntryId);
				$(this).parents('.form-group').remove();
				var removables = removeEntries.join(',');
				$('#LogColumnsCustomListRemoveEntries').val(removables);
				//alert(removables);
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
			
			//Saving Form with Ajax	
		<?php if( $this->request->is('ajax'))	{ ?>
		
		$('#editList').attr('onsubmit', 'return false;');

		$('#editList').on('submit', function()	{

			


		var action = $(this).attr('action');

		$.ajax({
			data: $(this).serialize(),
			url: action,
			type:'POST',
			success:function(data) {
				$("#editCustomList").modal('hide');
			},
			error:function(a,b,c)	{
				console.log(a);
				console.log(b);
				console.log(c);
			}
		});
	})
				<?php } ?>
});
</script>
