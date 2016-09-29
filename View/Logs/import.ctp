<div class="logs index">

<div class="col-xs-14 col-md-10">
<?php echo $this->Form->create('Log', array(
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
));

/*$this->Html->script('/TinyMCE/js/tiny_mce4/tinymce.min.js', array(
    'inline' => false
));*/
?>
	<fieldset>
		<legend>Import Log to "<?php echo $log['Log']['log_name']; ?>"..</legend>

		<div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

			<p>You can <span class="alert-link">append to existing log</span> or <span class="alert-link">overwrite entire log</span> by importing a new spreadsheet.</p>

			<p>Append/Overwrite log spreadsheet by importing a <em class="alert-link">csv, xls or xlsx</em> spreadsheet file. Chose a file from your computer by clicking Browse button next to <strong>Select a file to import</strong>(below). Select from <strong>Import Option</strong>, whether to <strong>Append</strong> or <strong>Overwrite</strong></p>

			<p>In case of <span class="alert-link">multiple tabs</span> in spreadsheet, only the <span class="alert-link">first tab will be imported</span> to overwrite or append entries to existing log.</p>
	</div>
    <?php
    echo $this->Form->input('file_to_import', array('type'=>'file', 'label'=>__('Select a file to import'), 'class'=>'btn btn-default'));

    $options = array(
      'type'=>'select',
      'options'=>array(
        'Append'=>'Append',
        'Overwrite'=>'Overwrite'
      ),
      'div' => 'form-group',

    );

    echo $this->Form->input('import_option',  $options); 

    ?>
	  </fieldset>
<?php echo $this->Form->end(__('Import')); ?>
</div>

  <div class="clearfix"></div>
  
</div>



<script>
jQuery(function($)	{
  $('#LogImportOption').on('change', function() {
     if($(this).val()=='Overwrite')   {
        alert('It will remove all existing records for this log and import from file. This action is irreversible!!');
     }
  })
});
</script>
