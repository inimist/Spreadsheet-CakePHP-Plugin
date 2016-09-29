<div class="logs form">
<?php echo $this->Form->create('Log', array(
  'novalidate',
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
)); ?>
	<fieldset>
		<legend><?php echo __('Add Log'); ?></legend>
	<?php
		echo $this->Form->input('log_name'); ?>
		<div class="alert alert-info alert-dismissable">
				<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
				You can create a log spreadsheet by importing a <em class="alert-link">csv, xls or xlsx</em> spreadsheet file. Click <span class="alert-link">Create From File</span> checkbox option(below) to open a file upload control. Chose a file from your computer to upload. In case of <span class="alert-link">multiple tabs</span> in the spreadsheet only the <span class="alert-link">first tab will be imported</span> to create log.<br /><br />

				You can <span class="alert-link">overwrite</span> entire log by importing a new spreadsheet later on.
		</div>
		<?php
    echo $this->Form->input('create_from_file', array('div' => array(
        'class' => 'form-group dtdd',
    ), 'type'=>'checkbox'));

    echo '<div class="rem-options" style="display:none;">';

    echo $this->Form->input('file_to_import', array('type'=>'file', 'label'=>__('Select file to create a Log from'), 'class'=>'btn btn-default'));

    echo '</div>';

		echo $this->Form->input('user_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>


<script>

jQuery(function($)	{
	
  $('#LogCreateFromFile').on('click ifClicked', function()	{
	 
    $('div.rem-options').toggle();
  })
  if($('#LogCreateFromFile').is(':checked'))	{
    $('div.rem-options').show();
  }
});

</script>