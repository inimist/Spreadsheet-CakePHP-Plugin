<div class="log-columns form">
<?php echo $this->Form->create('LogEntry', array(
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
));
?>
	<fieldset>
		<legend><?php echo __('Add Log Entry'); ?></legend>
    <p>Adding Entry to "<?php echo $log['Log']['log_name']; ?>"..</p>

  <table cellpadding="0" cellspacing="0" class="table table-striped">
    <?php
     global $script;

   //pr($log);
			//pr($log_columns);
    if(count($log['LogColumn'])>0):
      foreach($log['LogColumn'] as $column) {
        echo '<tr><th width="30%">' . $this->Form->label($column['column_name'], $column['column_name']) .'</th>';
				//pr($column);
	        echo '<td width="70%">' . $this->Util->build_log_input($column) . '</td></tr>';
      }
    ?>
    <?php endif; ?>

    <?php /*if(count($extra_columns)>0):
      foreach($extra_columns as $column)  {
        //pr($column);
        echo '<tr><th width="30%"><label for="LogEntry'. Inflector::camelize($column['id']) .'">' . $column['column_name'] .'</label></th>';
        echo '<td width="70%">' . $this->Util->build_log_input($column) . '</td></tr>
        ';
      }
      endif;*/ ?>
  </table>

  

	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>



<script>
jQuery(function($)	{

  //console.log($('label[for="LogEntryEnter%sID"]').text());

  <?php echo $this->Element('Logs/script'); ?>
  <?php echo $script; ?>

});
</script>

