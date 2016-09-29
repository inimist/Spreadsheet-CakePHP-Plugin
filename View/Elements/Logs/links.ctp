<?php if(!isset($pull)) $pull = ''; ?>

<div class="row-fluid logentries-links <?php echo $pull; ?>">
  <?php echo $this->Html->link(__('Add Entry'), array('controller'=>'entries', 'action' => 'add', $log['Log']['id']), array('class'=>"")); ?>

  <?php echo $this->Html->link(__('Export Log'), array('controller'=>'logs', 'action' => 'export', $log['Log']['id']), array('class'=>"")); ?>

  <?php echo $this->Html->link(__('Import Log'), array('controller'=>'logs', 'action' => 'import', $log['Log']['id']), array('class'=>"")); ?>
</div>