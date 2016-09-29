<div class="logs index">
	<h2><?php echo __('Logs- ' . $log['Log']['log_name']); ?></h2>


<div class="widget col-xs-16 col-md-12 well">
	<div class="widget-header">
		<h2><strong><?php echo __('Logs- ' . $log['Log']['log_name']); ?></strong> Details</h2>
		<div class="additional-btn">
			<a href="#" class="hidden reload"><i class="glyphicon glyphicon-repeat"></i></a>
			<a href="#" class="widget-toggle"><i class="glyphicon glyphicon-chevron-down"></i></a>
			<a href="#" class="widget-close"><i class="glyphicon glyphicon-remove"></i></a>
		</div>
	</div>
	<div class="widget-content padding" style="display: none;" >
			<div class="row-fluid">
				<div class="col-xs-7 col-md-5">
					<strong>Log Name: <?php echo h($log['Log']['log_name']); ?></strong> 
					(<?php echo $this->Html->link(__('Edit Log'), array('action' => 'edit', $log['Log']['id'])); ?>)
				</div>
				<div class="col-xs-7 col-md-5">
					<strong>User <?php echo h($log['User']['full_name']); ?></strong> 
					(<?php echo $this->Html->link(__('Edit Log'), array('action' => 'edit', $log['Log']['id'])); ?>)
				</div>
			</div>


			<div class="row-fluid">
				<div class="col-xs-7 col-md-5">
					<strong>Columns: <?php echo h($log['Log']['log_column_count']); ?></strong> 
					<?php echo $this->Html->link(__('Manage Columns'), array('controller'=>'columns', 'action' => 'index', '?'=>array('log_id'=>$log['Log']['id'])), array('class'=>"")); ?>
				</div>
				<div class="col-xs-7 col-md-5">
					<strong>Entries: <?php echo h($log['Log']['log_entry_count']); ?></strong> 
					<?php echo $this->Html->link(__('Manage Entries'), array('controller'=>'entries', 'action' => 'index', '?'=>array('log_id'=> $log['Log']['id'])), array('class'=>"")); ?>  <?php echo $this->Html->link(__('Add Entry'), array('controller'=>'entries', 'action' => 'add', '?'=>array('log_id'=> $log['Log']['id'])), array('class'=>"")); ?>
				</div>
			</div>
		</div>
</div>

  <div class="clearfix"></div>

  <h2><?php echo __('Log Entries'); ?></h2>

   <?php echo $this->Element('Entries/entries_table'); ?>

</div>


