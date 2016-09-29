<div class="log-columns form">
<?php echo $this->Form->create('LogEntry', array(
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
		<legend><?php echo __('Update Log Entry'); ?></legend>
    <p>Update Entry to "<?php echo $log['Log']['log_name']; ?>"..</p>

  <table cellpadding="0" cellspacing="0" class="table table-striped">
  <?php
    global $script;
    if(count($log['LogColumn'])>0): ?>
    <?php
      foreach($log['LogColumn'] as $column)  {
        echo '<tr><th width="30%">' . $this->Form->label($column['column_name'], $column['column_name']) .'</th>';
        echo '<td width="70%">' . $this->Util->build_log_input($column, @$log['entries'][$row_id][$column['id']]);

        //if(isset)

        if(isset($log['entries'][$row_id][$column['id']]['Upload']) && count($log['entries'][$row_id][$column['id']]['Upload'])>0) { ?>
          <table id="attachements" class="table table-striped pad10 info" style="width:50%;">
				    <tr><th colspan="2">Attached Files:</th></tr>
          <?php
            foreach($log['entries'][$row_id][$column['id']]['Upload'] as $attachment):
              echo '<tr>';
                echo '<td>' . $attachment['filename'] . '</td>';
                echo '<td>' . $this->Html->link(__('Remove'),	array('action' => 'delupload', $attachment['id'], '?'=>array('redirect'=>$this->Html->url(array('controller'=>'entries', 'action'=>'edit', $log['Log']['id'], $row_id), true))), array('confirm'=>__('Are you sure you remove this attachement? This cannot be undone!!'))) . '</td>'; //, __('Are you sure you remove this attachement? This cannot be undone!!', $attachment['id'])
              echo '</tr>';
            endforeach;

            ?></table>
            <?php
        } 
        
        echo '</td></tr>
        ';
      }
    ?>
    <?php endif; ?>
    <?php /*if(count($extra_columns)>0):
      foreach($extra_columns as $column)  {
        echo '<tr><th width="30%"><label for="LogEntry'. Inflector::camelize($column['id']) .'">' . $column['column_name'] .'</label></th>';
        echo '<td width="70%">' . $this->Utility->build_log_input($column, $log['entries'][$row_id][$column['id']]) . '</td></tr>
        ';
      }
      endif;*/ ?>
  </table>
  

	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>



<script>
jQuery(function($)	{

  <?php 
  //$this->Plugin = 'Spreadsheet';  
  echo $this->element('Logs/script'); ?>
  <?php echo $script; ?>
});
</script>