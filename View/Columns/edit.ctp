<style>

.custom-dropdown-list .form-control{
width:30% !important;
}
#editCol_addEntry .form-control{
display:inline;
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

//debug($custom_lists);

	?>
	<fieldset>
		<legend><?php echo __('Edit Log Column'); ?></legend>
    <p>Edit column of "<?php echo $log['Log']['log_name']; ?>"..</p>
	<?php
    echo $this->Form->input('id');
		echo $this->Form->hidden('log_id');
		echo $this->Form->input('column_name');
    echo $this->Form->input('column_type', array('type'=>'select', 'options'=>$column_types, 'label'=>__('Column Type'))); ?>

	<!---  Code for Custom List start  -->
		<div id="dropDownList" class="custom-dropdown-list">

						<div id="existingList">
									
									<?php  echo $this->Form->input('log_columns_custom_list_id', array('type'=>'select', 'options'=>$custom_lists, 'label'=>__('Choose From Given Lists'), 'empty' => '-- Select --'));  ?>

									<?php 									
									echo $this->Html->link(__('Edit salected List'), array('controller'=>'CustomLists', 'action'=>'edit', $this->request->data['LogColumn']['log_columns_custom_list_id']), array("data-toggle"=>"modal", 'id'=>'link-edit-cl', "data-target"=>"#editCustomList", 'onclick'=>'return false')); 
									?>
						</div>
							<?php echo $this->Html->link(__('Add New List'), array('#'), array('id' => 'editCol_addNewList','title'=>'Add New List')); ?>   <?php echo $this->Html->link(__('Choose From Exiting'), array('#'), array('id' => 'editCol_existingList','title'=>'Choose From Exiting')); ?>
								<div id="editCol_newList">
								<div id="editCol_newList"> 
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
											<div id="editCol_addEntry">
													<?php echo $this->Form->input('LogColumnsCustomListEntry.0.entry_name', array('label'=>false)); ?>
											</div>
											<?php		echo $this->Html->link(__('Add New Entry'), array('#'), array('id' => 'editCol_addNewEntry','title'=>'Add New Entry')); 
											?>

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

<div class="modal fade" id="editCustomList" tabindex="-1" role="dialog" aria-labelledby="editCustomListLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
    </div>
  </div>
</div>

<script>
$(document).ready(function(){

	$("#editCustomList").on("show.bs.modal", function(e) {
			var link = $(e.relatedTarget);
			$(this).find(".modal-content").load(link.attr("href"));
	});



$("#dropDownList").hide();
$("#editCol_existingList").hide();
if($('#LogColumnColumnType').val()=='dropdown')	{
					$("#dropDownList").show();
					$("#editCol_newList").hide();
					}


$('#LogColumnColumnType').on('change', function(){
				if($(this).val()=='dropdown')	{
					$("#dropDownList").show();
					$("#editCol_newList").hide();

				}

				else{
				$("#dropDownList").hide();

				}

		})
		
		
$('#editCol_addNewList').click(function(e){ //on add New List button click
						 e.preventDefault();
					$("#editCol_newList").show();
					$("#existingList").hide();
					$("#editCol_existingList").show();
					var listName =  $('#LogColumnColumnName').val();
						$('#LogColumnsCustomListName').val(listName);
				});
			$('#editCol_existingList').click(function(e){ //on add New List button click
						 e.preventDefault();
					$("#existingList").show();
					$("#editCol_newList").hide();
					$(this).hide();
					});

					if($('#LogColumnsCustomListDepartmentId').val()!=''){
							$('#listPrivacy').val()== 'department';
									}
						$('#listPrivacy').on('change', function(){
						if($(this).val()== 'department')
						{
							$('#LogColumnsCustomListDepartmentId').val("<?php echo $this->Session->read('Auth.User.department_id');  ?>");
						}
						else{
							$('#LogColumnsCustomListDepartmentId').val('');
						}
						
					});
											 var x = 0; //initlal text box count
													$('#editCol_addNewEntry').click(function(e){ //on add input button click
												 e.preventDefault();
													x++; //text box increment
													$('#editCol_addEntry').append('<div class="form-group"><input class="form-control" type="text" name="data[LogColumnsCustomListEntry]['+ x + '][entry_name]"/><a href="#" class="editCol_remove_field">Remove</a></div>'); //add input box
													});
							
													$('#editCol_addEntry').on("click",".editCol_remove_field", function(e){ //user click on remove text
														 e.preventDefault(); $(this).parent('div').remove(); x--;
													});


});
		


jQuery(function($)	{


	 $('#LogColumnLogColumnsCustomListId').on('change', function()	{
			 $('#link-edit-cl').attr("href", "<?php echo $this->Html->url(array('controller'=>'customlists', 'action'=>'edit')); ?>/"+ $(this).val());
				console.log($('#link-edit-cl').attr("href"));
		});

})

						
</script>

