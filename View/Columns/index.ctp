<div class="log-columns form">
	<h2><?php echo __('Manage Columns'); ?></h2><?php echo $this->Html->link(__('Add Column'), array('action' => 'add', '?'=>array('log_id'=>$log['Log']['id'])), array('class'=>"btn-info btn-med pull-right")); ?>
	<?php //echo $this->Html->link(__('Add Custom List'), array('controller'=>'customlists', 'action' => 'index'), array('class'=>"btn-info btn-med pull-right")); ?>

 <div class="clearfix"></div>

	<p><?php echo __('Manage Columns'); ?> for log "<strong><?php echo $log['Log']['log_name']; ?></strong>"</p>

	<table cellpadding="0" cellspacing="0" class="table table-striped table-fixed-header">
	<thead>
	<tr>
			<!--<th>#<!-- <?php echo $this->Paginator->sort('id'); ?> </th> -->
			<th class="actions"><?php echo __('Actions'); ?></th>
			<th><?php echo $this->Paginator->sort('column_name'); ?></th>
			<th><?php echo $this->Paginator->sort('column_type'); ?></th>
      <th><?php echo $this->Paginator->sort('autofill'); ?></th>
      <th><?php echo $this->Paginator->sort('is_active'); ?></th>
			
	</tr>
	</thead>
	<tbody>
	<?php foreach ($log_columns as $log_column): @$i++ ?>
	<tr class="<?php echo $this->Utility->statuscss('LogColumn', $log_column); ?>">
		<!-- <td><?php echo h($i); ?>&nbsp;</td>  -->
				<td class="actions">
			<?php echo $this->Html->link(__(''), array('action' => 'edit', $log_column['LogColumn']['id'], '?'=>['log_id'=>$log['Log']['id']]),array('class' => 'p_right glyphicon glyphicon-edit','title'=>'Edit')); ?>
			<?php  
			//debug($log);
			 echo $this->Utility->deleteButton($log_column, 'LogColumn');	?>
		</td>
		<td><?php echo h($log_column['LogColumn']['column_name']); ?>&nbsp;</td>
    <td><?php echo h($column_types[$log_column['LogColumn']['column_type']]); ?>&nbsp;</td>
    <td><?php echo h($log_column['LogColumn']['autofill']); ?>&nbsp;</td>
    <td><?php echo h($log_column['LogColumn']['is_active']); ?>&nbsp;</td>

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

