<div class="log-entries index">
	<h2><?php echo __('Manage Entries'); ?></h2>

	<p><?php echo __('Manage Entries'); ?> for log "<strong><?php echo $log['Log']['log_name']; ?></strong>"</p>

	<div class="alert alert-info alert-dismissable">
			<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>

			<p>You can <span class="alert-link">append to existing log</span> or <span class="alert-link">overwrite entire log</span> by importing a new spreadsheet.</p>

			<p>Append/Overwrite log spreadsheet by importing a <em class="alert-link">csv, xls or xlsx</em> spreadsheet file. Click <span class="alert-link"><?php echo $this->Html->link(__('Import Log'), array('controller'=>'logs', 'action' => 'import', $log['Log']['id']), array('class'=>"")); ?></span> and it will bring you on to a page with a file upload control. You can select option to overwrite or append entries to existing log.</p>
	</div>
		
   <?php
			
	 echo $this->Element('Entries/entries_table'); ?>

</div>



<script>
jQuery(function($)	{
  
});
</script>

