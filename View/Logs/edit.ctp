<div class="logs form">
<?php echo $this->Form->create('Log', array(
  'type' => 'file',
	'inputDefaults' => array(
		'div' => 'form-group',
		'wrapInput' => false,
		'class' => 'form-control'
	)
)); ?>
	<fieldset>
		<legend><?php echo __('Edit Log'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('log_name');
    echo $this->Form->input('log_column_count', array('label'=>__('Number of Columns'), 'readonly', 'div'=>array('style'=>'margin-bottom:0;')));
    echo $this->Html->link(__('Manage Columns'), array('controller'=>'columns','action'=>'index', '?'=>['log_id'=>$this->request->data['Log']['id']]), array('style'=>'margin-bottom:15px;display:inline-block;'));
		echo $this->Form->input('user_id');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>


