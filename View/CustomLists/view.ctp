	<legend><?php //debug($customList); 
	echo $customList['LogColumnsCustomList']['name']; ?></legend>

									<strong>Entries <?php //echo $customList['LogColumnsCustomList']['name'];  ?></strong> 
									<?php if($customList['LogColumnsCustomList']['user_id'] == $this->Session->read('Auth.User.id')){
										echo $this->Html->link(__('Edit Custom List'), array('action' => 'edit', $customList['LogColumnsCustomList']['id']), array('class'=>"btn-info btn-med pull-right")); 
									} ?>
									<table cellpadding="0" cellspacing="0" class="table table-striped">
									<thead>
									<tr>
									<th><?php echo h('Entry Name'); ?></th>
									</tr>
								</thead>
								<tbody>
								<?php foreach ($customList['LogColumnsCustomListEntry'] as $listEntry): @$i++ ?>
											<tr>
											<td><?php echo h($listEntry['entry_name']); ?>&nbsp;</td>

									</tr>
<?php endforeach; ?>
	</tbody>
	</table>

