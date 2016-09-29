<div class="log-columns form">
	<h2><?php echo __('Manage Custom Lists'); 
	
	//pr($custom_lists);

	//die();
	?></h2>
	
	<?php echo $this->Html->link(__('Add New List'), array('action' => 'add'), array('class'=>"btn-info btn-med pull-right")); ?>

 <div class="clearfix"></div>

	<table cellpadding="0" cellspacing="0" class="table table-striped table-fixed-header">
	<thead>
	<tr>
			<!--<th>#<!-- <?php echo $this->Paginator->sort('id'); ?> </th> -->
			<th class="actions"><?php echo __('Actions'); ?></th>
			<th><?php echo $this->Paginator->sort('List Name','name'); ?></th>
					
	</tr>
	</thead>
	<tbody>
	<?php foreach ($custom_lists as $custom_list): @$i++ ?>
	<tr class="<?php // echo $this->Utility->statuscss('LogColumn', $log_column); ?>">
		<!-- <td><?php echo h($i); ?>&nbsp;</td>  -->
			<td class="actions">
			<?php echo $this->Html->link(__('View'), array('action' => 'view', $custom_list['LogColumnsCustomList']['id']),array('class'=>'glyphicon glyphicon-th-list','title'=>'View')); ?>
			
			<?php if($custom_list['LogColumnsCustomList']['user_id'] == $this->Session->read('Auth.User.id') || $this->Utility->isAdmin()){ 
				echo $this->Html->link(__(''), array('action' => 'edit', $custom_list['LogColumnsCustomList']['id']),array('class' => 'p_right glyphicon glyphicon-edit','title'=>'Edit')); ?>

			<?php  
			//echo $this->Utility->deleteButton($custom_list, 'LogColumnsCustomList');
			  echo $this->Form->postLink(
                    __('Delete'),
                    array('action' => 'delete', $custom_list['LogColumnsCustomList']['id']), array('class'=>'color-fff glyphicon glyphicon-remove','title'=>'Delete'),  __('Are you sure you want to delete # %s?', $custom_list['LogColumnsCustomList']['id'])
                        );
			
			//echo $this->Html->link(__('Delete'), array('action' => 'delete', $custom_list['LogColumnsCustomList']['id']));
			// echo $this->Utility->deleteButton($custom_list, 'LogColumnsCustomList');	
			}
			?>
		</td>
		<td><?php echo $this->Html->link($custom_list['LogColumnsCustomList']['name'], array('action' => 'view', $custom_list['LogColumnsCustomList']['id']), array('title'=>$custom_list['LogColumnsCustomList']['name']));?>&nbsp;</td>

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

