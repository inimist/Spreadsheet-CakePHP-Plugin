<div class="log-columns form">
<?php /*echo $this->Form->create('LogEntry', array(
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
));
$this->Html->script('/TinyMCE/js/tiny_mce4/tinymce.min.js', array(
    'inline' => false
));*/
?>
	<fieldset>
		<legend><?php echo __('View Log Entry'); ?></legend>
    <p>View Entry of "<?php echo $log['Log']['log_name']; ?>"..</p>
    <?php

    global $script;

    $this->Utility->log = $log;
    $this->Utility->row_id = $row_id;

    if(count($log['LogColumn'])>0): ?>

  <table cellpadding="0" cellspacing="0" class="table table-striped">
    <?php
      foreach($log['LogColumn'] as $column)  {

        if($column['id']=='entrytype_foreign_key') continue; //we dont need to show id of the action

        $this->Utility->log_column_id = $column['id'];

        echo '<tr><th width="30%">' . $column['column_name'] .'</th>';

        echo '<td width="70%">' . $this->Util->display_entry($log['entries'][$row_id][$column['id']]);

        echo '</td></tr>
        ';
      }
    
    echo '<tr><td width="30%">' . $this->Html->link(__('« Go Back'), 'javascript:history.go(-1)') .'</th>';

        echo '<td width="70%">' . $this->Html->link(__('Edit Log Entry'), array('action' => 'edit', $row_id, '?'=>['log_id'=>$log['Log']['id']])) . '</td></tr>';
   ?>
  </table>
  <?php endif; ?>

	</fieldset>
</div>



<script>
jQuery(function($)	{
  <?php echo $script; ?>
});
</script>