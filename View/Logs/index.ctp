<div class="logs index">
		<!-- Render form for custom pagination --> 
	<?php $this->Utility->pagination_form('Log'); ?>
	<h2><?php echo __('Logs'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="table table-striped table-fixed-header">
	<thead>
	<tr>
			<!--<th><?php echo $this->Paginator->sort('Select'); ?></th> -->
			<!-- <th><?php echo __('Select'); ?></th> -->
			<th class="actions"><?php echo __('Actions'); ?></th>
			<th><?php echo $this->Paginator->sort('log_name'); ?></th>
			<th><?php echo $this->Paginator->sort('user_id'); ?></th>
      <!-- <th><?php echo $this->Paginator->sort('log_column_count', __('Num Columns')); ?></th>
      <th><?php echo $this->Paginator->sort('log_entry_count', __('Num Entries')); ?></th> -->
			
	</tr>
	</thead>
	<tbody>
	<?php foreach ($logs as $log): ?>
	<tr class="<?php echo $this->Utility->statuscss('Log', $log); ?>">
		<!--<td><?php echo h($log['Log']['id']); ?>&nbsp;</td> -->
		<!-- <td><?php echo $this->Form->checkbox('Log.id.'.$log['Log']['id'], array('type'=>'checkbox', 'hiddenField'=>false, 'class'=>'usercheckbox',		'label'=>false, 'wrap'=>false, 'legent'=>false)); ?>
		</td> -->
		<td class="actions td_action">
			<?php echo $this->Html->link(__(''), array('action' => 'view', $log['Log']['id']),array('class'=>'glyphicon glyphicon-th-list','title'=>'View')); ?>
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $log['Log']['id']),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit')); ?>
			<?php 
			echo $this->Utility->deleteButton($log, 'Log');
			 ?>
		</td>

		<td><?php echo $this->Html->link($log['Log']['log_name'], array('action' => 'view', $log['Log']['id'])); ?>&nbsp;</td>

		<td>
			<?php echo $this->Html->link($log['User']['username'], array('controller' => 'users', 'action' => 'view', $log['User']['id'])); ?>
		</td>
    <!-- <td>
      <strong><?php // echo h($log['Log']['log_column_count']); ?></strong> 
			(<?php echo $this->Html->link(__('Manage Columns'), array('controller'=>'columns', 'action' => 'index', '?'=>array('log_id' => $log['Log']['id']))); ?>)
		</td>
    <td>
      <strong><?php // echo h($log['Log']['log_entry_count']); ?></strong> 
			(<?php echo $this->Html->link(__('Manage Entries'), array('controller'=>'entries', 'action' => 'index', '?'=>array('log_id' => $log['Log']['id']))); ?>)
		</td> -->
		
	</tr>
<?php endforeach; ?>
	</tbody>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page {:page} of {:pages}, showing {:current} records out of {:count} total, starting on record {:start}, ending on {:end}')
	));
	?>	</p>
	<div class="pagination">
	<?php
		echo $this->Paginator->prev('< ' . __('previous'), array(), null, array('class' => 'prev disabled'));
		echo $this->Paginator->numbers(array('separator' => ''));
		echo $this->Paginator->next(__('next') . ' >', array(), null, array('class' => 'next disabled'));
	?>
	</div>
</div>


