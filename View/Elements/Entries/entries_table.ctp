<style>
table {
    table-layout: fixed;
    width: 0; /* otherwise Chrome will not increase table size */
}

.log-entries-table{
   border-collapse: inherit;
}
.log-entries-table>thead>tr>th, .log-entries-table>tbody>tr>td{
	border-left:none !important;
	border-top:none !important;
}
.log-entries-table>thead>tr>th:first, .log-entries-table>tbody>tr>td:first{
	border-left:1px !important;
}

/*
thead {
    -ms-transform: translate(0px,0px); 
   	-webkit-transform: translate(0px,0px); 
    transform: translate(0px,0px);
}
*/
tr.last td {
    border-bottom: 1px solid black;
}

th {

    position: relative;
}

th.last {
    border-right: none;
}

th, td {
    padding: 2px 5px;
    text-align: left;
    overflow: hidden;
}

/*
resizable columns
*/
.scrollContainer {
    overflow: auto;
    width: 100%;
    max-height: 550px;
}

.resizeHelper,.ui-resizable-e {
    cursor: e-resize;
    width: 10px;
    height: 100%;
    top: 0;
    right: -8px;
    position: absolute;
    z-index: 100;
    font-size: 100%;
}

.default-column{
background: #42B4E6 !important;
}

/* handle for FF */
@-moz-document url-prefix() {
    .resizeHelper,.ui-resizable-e {
        position: relative;
        float: right;
    }
}



.sticky-header {
           /* -webkit-transform: translate(0,100px) !important;
            -moz-transform: translate(0,100px) !important;
            -ms-transform: translate(0,100px) !important;
            -o-transform: translate(0,100px) !important;
           transform: translate(0,100px) !important; */
        }

</style>
<?php
		//pr($log);
    $columns_skipped_table = array();

		$defaultColumns = array('attachment', 'set_reminder', 'user_id', 'reminder_date', 'status_id');

    if(count($log['LogColumn'])>0): ?>
		<?php 
		$border_option = array('No Border'=>'No Border', 'All Borders'=>'All Borders');
		echo $this->Form->input('border_option', array('type'=>'select', 'options'=>$border_option)); ?>
    <?php  echo $this->Element('Logs/links', array('pull'=>'pull-right')); ?>
	<div id="wrapTable" class="scrollContainer">
    <table cellpadding="0" cellspacing="0" class="table table-striped widget resizable log-entries-table" > <!-- id="tblNeedsScrolling" -->
      <thead class="sticky-header"> <!-- style="position: fixed; display:block; width:100%;" -->
      <tr class="colHeaders">
				<th class="ui-resizable fix-on-top" style="width:70px"><span class="columnLabel">Action</span>
                    <div class="resizeHelper ui-resizable-handle ui-resizable-e">&nbsp;</div></th>
      <?php
        foreach($log['LogColumn'] as $column)  {
					
          if(!$column['skip_table']){
						$class = in_array($column['id'], $defaultColumns) ? ' default-column': '';
          echo '<th class="ui-resizable fix-on-top '.$class.'" style="width:100px"><span class="columnLabel">' . $column['column_name'] .'</span>
                    <div class="resizeHelper ui-resizable-handle ui-resizable-e">&nbsp;</div></th>';
					}
          else
          $columns_skipped_table[] = $column['column_name'];
        }
      ?>
    </tr>
    </thead>
    <tbody>
    <tr>
    <?php
	
			//	pr($log['entries']);
        foreach($log['entries'] as $row_id => $columns)  {
          if($row_id==0) continue;
					$columnsHTML = ''; $actionsHTML = '';
					//pr($columns);
          foreach($columns as $entry)  {
						//	pr($entry);
					  if(!$this->Util->skip_table($entry['log_column_id'], $log['LogColumn']))  {
              $options = array();
              $columnsHTML .= '<td>';
             // if($entry['log_column_id'] =='' ) {
              $columnsHTML .= $this->Util->display_entry($entry);
						//	}
							
              $columnsHTML .= '</td>';
            }
          }
					//debug($entry['row_id']);
          $actionsHTML .= '
          <td class="actions">';
           $actionsHTML .= $this->Html->link(__(''), array('controller'=>'entries', 'action' => 'view', $entry['row_id'], '?'=>['log_id'=>$log['Log']['id']]),array('class'=>'glyphicon glyphicon-th-list','title'=>'View'));
					 $actionsHTML .= $this->Html->link(__(''), array('controller'=>'entries', 'action' => 'edit', $entry['row_id'], '?'=>['log_id'=>$log['Log']['id']]),array('class'=>'glyphicon glyphicon-edit','title'=>'Edit'));
           $actionsHTML .= $this->Form->postLink(__(''), array('controller'=>'entries', 'action' => 'delete', $entry['row_id'], '?'=>['log_id'=>$log['Log']['id']]), array('class'=>'glyphicon glyphicon-remove','title'=>'Delete'), __('Are you sure you want to delete entry for Log # %s?', $log['Log']['id'], $entry['row_id']));
					 $actionsHTML .= '</td>';

					echo $actionsHTML . $columnsHTML . '</tr><tr>';
        }
				
      ?>
    </tr>
	</tbody>
	</table>
</div>


<?php

 if($columns_skipped_table)  {
      echo 'Column(s): <strong>' . implode(', ', $columns_skipped_table) . '</strong> were not shown</p>';
    }
    


 ?>

  <?php // echo $this->Element('Logs/links'); ?>

  <?php endif; ?>

	<script>
	/**
 * enables resizable data table columns.
 * Script by Ingo Hofmann
 */
jQuery(function($) {

    /**
     * Widget makes columns of a table resizable.
     */
    $.widget("ih.resizableColumns", {

        /**
         * initializing columns
         */
        _create: function() {
            this._initResizable();
        },

        /**
         * init jQuery UI sortable
         */
        _initResizable: function() {

            var colElement, colWidth, originalSize;
            var table = this.element;

            this.element.find("th").resizable({
                // use existing DIV rather than creating new nodes
                handles: {
                    "e": " .resizeHelper"
                },
   
                // default min width in case there is no label
                minWidth: 10,
                
                // set min-width to label size
                create: function(event, ui) {
                    var minWidth = $(this).find(".columnLabel").width();
                    if (minWidth) {
                        
                        // FF cannot handle absolute resizable helper
                        /*if ($.browser.mozilla) {
                            minWidth += $(this).find(".ui-resizable-e").width();
                        }*/
                        minWidth += $(this).find(".ui-resizable-e").width();
                        
                        $(this).resizable("option", "minWidth", minWidth);
                    }
                },

                // set correct COL element and original size
                start: function(event, ui) {
                    var colIndex = ui.helper.index() + 1;
                    colElement = table.find("tr > th:nth-child(" + colIndex + ")");
                    colWidth = parseInt(colElement.get(0).style.width, 10); // faster than width
                    originalSize = ui.size.width;
                },

                // set COL width
                resize: function(event, ui) {
                    var resizeDelta = ui.size.width - originalSize;

                    var newColWidth = colWidth + resizeDelta;
                    colElement.width(newColWidth);

                    // height must be set in order to prevent IE9 to set wrong height
                    $(this).css("height", "auto");
                }
            });
        }

    });

    // init resizable
    $(".resizable").resizableColumns();

	$('#wrapTable').scroll(function(){
		var thead = this.querySelector("thead");
		var offset = $(this).offset();
		console.log(offset.top);
		var scroll = this.scrollTop;
   var translate = "translate(0,"+this.scrollTop+"px)";
	 console.log(translate);
   //thead.style.transform = translate;
		$(this).find('thead').find('th').css('transform', translate);
	 //$(this).find('thead').css('top', scroll + 'px');

		console.log($(this).find('thead').css('transform'));
	 //$(this).find('thead').css('-ms-background', 'red');
	 //thead.style.webkitTransform = translate;
	 //thead.style.msTransform = translate;
		});

	$('#border_option').val('All Borders');
	$('.log-entries-table').find('td').css('border', '1px solid');

	$('#border_option').on('change', function(){
		var borderOpt = $(this).val();
		switch(borderOpt){
			case 'All Borders':
				$('.log-entries-table').find('td').css('border', '1px solid');
				$('.log-entries-table').find('th').css('border', '1px solid');
			break;
			case 'No Border':
				$('.log-entries-table').find('td').css('border', 'none');
			break;
			default:
		}
	});

});
	</script>

