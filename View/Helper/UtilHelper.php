<?php
App::uses('Sanitize', 'Utility');
class UtilHelper extends AppHelper {

/**
 * uses HTML helper
 */
	
	public $helpers = array('Html', 'Form', 'Time', 'TinyMCE.TinyMCE', 'Session');

  public $users, $controls, $tasks, $log; //whichever required here

  function build_log_input($column, $entry = array(), $options = array())  {

    global $script;

		$default = array('label'=>false);

		$options = array_merge($default, $options);

    if(isset($column['options'])) $options = array_merge($options, $column['options']);
    if(isset($column['empty'])) $options['empty'] = $column['empty'];

    if($column['column_type']=='integer')  {
      $options['type'] = 'number';
    }

		if($column['column_type']=='dropdown')  {
      $options['type'] = 'select';
			$options['options'] = $column['list_entries'];
    }

    if($column['column_type']=='float')  {
      $options['type'] = 'float';
    }

    //$column['id'] = isset($column['id']) ?  $column['id'] : $column['column_name']; //Id means field Name

    $model = strpos($column['id'] ,'.') ? '' : 'LogEntry';

    $elementID = $model . Inflector::camelize(str_replace('.', '_', $column['id']));

    if($column['column_type']=='longtext')  {
      $options['type'] = 'textarea';

      if($column['rich_text'] && is_object($this->TinyMCE))  {
        $this->TinyMCE->editor(array(
        'theme' => 'modern', 
        'skin'=>'lightgray', 
        'mode' => "exact", 
        'elements' => $elementID,
        'plugins' => 'advlist autolink lists link image charmap print preview anchor searchreplace visualblocks code fullscreen insertdatetime media table contextmenu paste'
        ));
      }
    }
    //If there is an entry for this reco
    if($entry) {
      if(isset($entry['log_entry'])) {
        $options['value'] =   $entry['log_entry'];
        //a fix for checkboxes
        if(isset($column['inputtype']) && $column['inputtype']=='checkbox')  {
          if($options['value']=="0") {
            $options['value'] = '1';
          } else  {
            $options['checked'] = 'checked';
          }
        }
      }
    }

    if($column['column_type']=='date')  {
      $script .= "$('#" . $elementID . "').datetimepicker({pickTime:false});
      ";
    }

		if(isset($column['inputtype'])) {
			$options['type'] = $column['inputtype'];
			
		}

		if(isset($column['options'])){

			$options['options'] = $column['options'];
		}

		if( isset($options['type']) && $options['type']=='file' )  {
      $options['class'] = 'btn btn-default';
		}

    return $this->Form->input($column['id'], $options);
  }

  function display_entry($entry) {
	
    $html = '';
    if($entry['log_column_id']=='set_reminder') {
      if(trim($entry['log_entry'])=='1') $html = 'Yes';
      else $html = 'No';
    } //TODO for attachments
    /*else if($entry['log_column_id']=='attachment') 
    {
      if(isset($entry['Upload']) && count($entry['Upload'])>0) {
          $html .= '<table id="attachements" class="table table-striped pad10 info" style="width:50%;">
				    <tr><th colspan="4">Attached Files:</th></tr>';
            foreach($entry['Upload'] as $attachment):
              $html .= '<tr>';
                $html .= '<td>' . $attachment['filename'] . '( ' . $attachment['type'] . ' file) ' . '</td>';
                $html .= '<td>' . $this->Html->link(__('View'),	array('action' => 'viewupload', $attachment['id'])) . '</td>';
                $html .= '<td>' . $this->Html->link(__('Download'),	array('action' => 'getdownload', $attachment['id'])) . '</td>';
                $html .= '<td>' . $this->Html->link(__('Remove'),	array('action' => 'delupload', $attachment['id'], '?'=>array('redirect'=>$this->Html->url(array('controller'=>'logs', 'action'=>'edit_entry', $entry['log_id'], $entry['row_id']), true))), array('confirm'=>__('Are you sure you remove this attachement? This cannot be undone!!'))) . '</td>';
              $html .= '</tr>';
            endforeach;
            $html .= '</table>';
        }
    } */
		if($entry['log_column_id']=='status_id' && $entry['log_entry']) {
				$statuses = array('1'=>'Not Started', '2'=>'In Progress', '3'=>'Completed', '4'=>'Overdue');
				$html = $statuses[$entry['log_entry']];
				}
    else if( $entry['log_column_id'] == 'entrytype_id' && trim($entry['log_entry'])!="") 
    {
			//debug($entry['log_entry']);
      $model = ClassRegistry::init($entry['log_entry']);
      $model->recursive = -1;
      $this->set_foreign_key();
      if($this->get_foreign_key())  {
        $row = $model->find('first', array('conditions'=>array('id'=>$this->get_foreign_key()), 'fields'=>array($model->primaryKey, $model->displayField)));
        if($row) return $html .= $entry['log_entry'] . ': ' . $this->Html->link($row[$model->alias][$model->displayField], array('controller'=>Inflector::pluralize(Inflector::underscore($model->alias)), 'action'=>'view', 'plugin'=>false, $row[$model->alias][$model->primaryKey]));
      }
    }
    else if( $entry['log_column_id'] == 'user_id' && trim($entry['log_entry'])!="") 
    {
      $model = ClassRegistry::init('User');
      $model->recursive = -1;
      $row = $model->find('first', array('conditions'=>array('id'=>$entry['log_entry']), 'fields'=>array($model->primaryKey, $model->displayField)));
			if($row) $html .= $row[$model->alias][$model->displayField];
     /* if($row) $html .= $this->Html->link($row[$model->alias][$model->displayField], array('controller'=>Inflector::pluralize(Inflector::underscore($model->alias)), 'action'=>'view', 'plugin'=>false, $row[$model->alias][$model->primaryKey]));  */
    } 
    else  
    {
      $html = $entry['log_entry'];
    }
    return $html;
  }

  function skip_table($column_id, $columns) {
    foreach($columns as $column)  {
      if($column_id == $column['id'] && ($column['skip_table']))  {return true;}
    }
    return false;
  }

  function set_foreign_key()  {
    if(!isset($this->row_id)) return;
    if(!$this->row_id) return;
    foreach($this->log['entries'][$this->row_id] as $entry) {
      if($entry['log_column_id']=='entrytype_foreign_key') {
        $this->set_foreign_key = $entry['log_entry'];break;
      }
    }
  }

    function get_foreign_key()  {
    //debug($this->log['entries'][$this->row_id]);
    return @$this->set_foreign_key;
  }
}