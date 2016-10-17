<?php
/**
 * Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright 2015 - 2016, Inimist Technologies (http://inimist.com)
 * @license MIT License (http://www.opensource.org/licenses/mit-license.php)
 */
App::uses('SpreadsheetAppModel', 'Spreadsheet.Model');
/**
 * Log Model
 *
 * @property Users $Users
 * @property LogAttribute $LogAttribute
 */
class Log extends SpreadsheetAppModel {

  public $actsAs = array('Containable', 'SoftDelete');
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'log_name';

  public $column_types = array(
    'varchar'=>'Inline Text',
    'longtext'=>'Multiline Text',
    'date'=>'Date',
    'integer'=>'Integer',
    'float'=>'Float',
		'dropdown'=>'Dropdown List'
  );

  private $extra_columns = array(
    array(
      'id'=>'attachment',
      'column_name'=>'Document (attachment)',
      'column_type'=>'varchar',
      'is_active' => 1,
      'skip_table' => false,
      'inputtype'=>'file',
    ),
    array(
      'id'=>'entrytype_id', 
      'column_name'=>'Related To', 
      'column_type'=>'varchar', 
      'inputtype'=>'select', 
      'options'=>array(
        ''=>'--None--', 'Task'=>'Task', 'Control'=>'Control'
      ),
      'is_active' => 1,
      'skip_table' => true,
    ),
    array(
      'id'=>'entrytype_foreign_key',
      'column_name'=>'Relation ID',
      'column_type'=>'integer',
      'inputtype'=>'number',
      'is_active' => 1,
      'skip_table' => true,
    ),
    array(
      'id'=>'set_reminder',
      'column_name'=>'Reminder (Y/N)',
      'column_type'=>'integer', 
      'inputtype'=>'checkbox',
      'is_active' => 1,
      'skip_table' => false,
    ),
    array(
      'id'=>'user_id',
      'column_name'=>'User', 
      'column_type'=>'integar',
      'type'=>'select', 
      'options'=>'User', 
      'empty'=>'--Select--',
      'is_active' => 1,
      'skip_table' => false,
    ),
    array(
      'id'=>'reminder_date',
      'column_name'=>'Reminder Date', 
      'column_type'=>'date',
      'is_active' => 1,
      'skip_table' => false,
    ),
    array(
      'id'=>'status_id',
      'column_name'=>'Status', 
      'column_type'=>'integar',
      'type'=>'select', 
      'options'=>'Status', 
      'empty'=>false,
      'is_active' => 1,
      'skip_table' => false,
    )
  );

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'log_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Log name must not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'users_id' => array(
			'numeric' => array(
				'rule' => array('numeric'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
    'file_to_import' => array(
      'extension' => array(
          'rule'=>array('extension',array('xls', 'xlsx', 'csv', 'xlsm', 'msg')),
          'message'=>'Please select a valid file to upload (xls, xlsx, csv only)',
          'required' => false,
          'allowEmpty' => true,
          'on' => 'create'
      )
		)
	);

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'User' => array(
			'className' => 'User',
			'foreignKey' => 'user_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		)
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'LogColumn' => array(
			'className' => 'Spreadsheet.LogColumn',
			'foreignKey' => 'log_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
    'LogEntry' => array(
			'className' => 'Spreadsheet.LogEntry',
			'foreignKey' => 'log_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		),
	);

  public $raw_entry_values = true;

/**
  Build ready to spread-sheet array out of raw db records
  * @param $log (array), the actual data array find() would return.
  * @param $headerrow (bool), whether to keep the header row (recall excel).
  * @param $raw (bool), whether to pass the related model ids(task_id, user_id etc to create titles/links).
*/
  function prepareLog(&$log, $headerrow = true, $raw = true) {
    $this->log = $log;
		//debug($log);
   //merge extra columns
   $log['LogColumn'] = array_merge($log['LogColumn'], $this->extra_columns());

   //merge entries
   if(!isset($this->useRequestData))  {
    $log['entries'] = $this->buildEntries( $log, $headerrow, $raw);
   }
   return $log;
  }

  function buildEntries(&$log, $headerrow = true, $raw)  {
   $entries = array();

   if(isset($log['LogColumn'])) {

     //pr($log['LogColumn']);

     //pr($this->get_extra_columns());exit;

     if($headerrow) $entries[] = $log['LogColumn'];
			
			//debug($entries);
			//exit;
					//pr($log['LogEntry']);
					$row_ids = array();
					foreach($log['LogEntry'] as $entry) {
						$row_ids[$entry['row_id']] = $entry['row_id'];
					}
					//pr($row_ids);

       if(isset($log['LogEntry'])) {

				 //debug($log['LogEntry']);
				 //exit;
         foreach($log['LogColumn'] as $column) {

					//pr($column);
					//exit;
				foreach($row_ids as $row_id)	{
						
					

            $entries[$row_id][$column['id']] = $this->get_column_entry($row_id, $column, $log['LogEntry']);

						//pr($entries[$row_id][$column['id']]);
            /*if(!isset($entries[$entry['row_id']][$column['id']])) {
              $entries[$entry['row_id']][$column['id']] = array('id'=>null, 'log_id'=>$column['log_id'], 'log_column_id'=>$column['id'], 'row_id'=>$entry['row_id'], 'log_entry'=>null, 'skip_table'=>false, 'rich_text'=>false);
            }*/
					
          }
				 }
       }
     }
    //pr($entries);
     //exit;
    return $entries;
  }

	function get_column_entry($row_id, $column, $log_entries)	{
	//pr($log_entries); exit;
		$column_entry = '';
		foreach( $log_entries as $entry ){
				//	pr($entry);
			// pr($column['id']);
			//pr($row_id);
			
		if($entry['row_id'] == $row_id  && $entry['log_column_id'] == $column['id']){
				
			$column_entry =  $entry;
				}
			
			}

	if($column_entry == ''){
				$column_entry = array('id'=>null, 'log_id'=>$column['log_id'], 'log_column_id'=>$column['id'], 'row_id'=>$row_id, 'log_entry'=>null, 'skip_table'=>false, 'rich_text'=>false); }
	
		//	pr($column_entry);
		return $column_entry;
	

	}

  function updateEntryCount($id, $operation='+', $change=1) {
    $this->updateAll(
        array('Log.log_entry_count' => 'Log.log_entry_count '. $operation . ' ' . $change),
        array('Log.id' => $id)
    );
  }

  function set_extra_columns() {
    foreach($this->extra_columns as $i => $custom_column)  {
      if($this->id) $this->extra_columns[$i]['log_id'] = $this->id;
      if(isset($custom_column['options'])) {
        if(is_string($custom_column['options'])) {//we assume that this is a model
          unset($this->extra_columns[$i]['options']);
          //debug($custom_column['options']);
          //$this->extra_columns[$i]['options'] = ClassRegistry::init($custom_column['options'])->find('list');
          //debug($this->extra_columns[$i]['options']);
        }
      }
    }
  }

  function is_extra_column($column_name)  {
    foreach($this->extra_columns as $column) {
      if($column['column_name'] == trim($column_name))  return true;
    }
    return false;
  }

  function getExtraColumnByName($column_name)  {
    foreach($this->extra_columns as $column) {
      if($column['column_name'] == trim($column_name))  return $column;
    }
  }

  function extra_columns() {
    $this->set_extra_columns();
    return $this->get_extra_columns();
  }

  function get_extra_columns() {
    return $this->extra_columns;
  }

  function rearrangeExtraFields(&$data, $model = 'LogEntry') {
    if(!$data) return;
    //debug($data);
    $new_data = array();
    //pr($data[$model]['attachment']);
    foreach($this->get_extra_columns() as $column)  {
      if(isset($data[$model][$column['id']])) {
        $new_data[$model][$column['id']] = $data[$model][$column['id']];
        unset($data[$model][$column['id']]);
      }
    }
    //debug(array_merge($data, $new_data));
    return $data = array_merge_recursive($data, $new_data);
  }

  function refineEntry(&$entry)  {
    if($entry['log_column_id']=='set_reminder') {
      if(trim($entry['log_entry'])=='1') $entry['log_entry'] = 'Yes';
      else $entry['log_entry'] = 'No';
    }
    if( $entry['log_column_id'] == 'entrytype_id') {
      $model = ClassRegistry::init($entry['log_entry']);
      $model->recursive = -1;
      $this->set_foreign_key();
      if($this->get_foreign_key())  {
        $row = $model->find('first', array('conditions'=>array('id'=>$this->get_foreign_key()), 'fields'=>array($model->primaryKey, $model->displayField)));
        if($row) $entry['log_entry'] = $entry['log_entry'] . ': <a href="' . Router::url(array('controller'=>Inflector::pluralize(Inflector::underscore($model->alias)), 'action'=>'view', $row[$model->alias][$model->primaryKey]), true) . '">' . $row[$model->alias][$model->displayField] . '</a>';
      }
    }
    if( $entry['log_column_id'] == 'user_id') {
      $model = ClassRegistry::init('User');
      $model->recursive = -1;
      $row = $model->find('first', array('conditions'=>array('id'=>$entry['log_entry']), 'fields'=>array($model->primaryKey, $model->displayField)));
      if($row) $entry['log_entry'] = '<a href="' . Router::url(array('controller'=>Inflector::pluralize(Inflector::underscore($model->alias)), 'action'=>'view', $row[$model->alias][$model->primaryKey]), true) . '">' . $row[$model->alias][$model->displayField] . '</a>';
    }
    return $entry;
  }
  function set_foreign_key()  {
    if(!isset($this->row_id)) return;
    if(!$this->row_id) return;
    foreach($this->log['LogEntry'] as $entry) {
      if($entry['row_id'] = $this->row_id && $entry['log_column_id']=='entrytype_foreign_key') {
        $this->set_foreign_key = $entry['log_entry'];break;
      }
    }
  }

  function get_foreign_key()  {
    //debug($this->log['entries'][$this->row_id]);
    return @$this->set_foreign_key;
  }

  function find_log_column($log_id, $column_name)  {
    $log_column = $this->LogColumn->find('first', array('conditions'=>array('LogColumn.log_id'=>$log_id, 'LogColumn.column_name'=>$column_name), 'fields'=>array('LogColumn.id', 'LogColumn.column_name', 'LogColumn.column_type', 'LogColumn.log_columns_custom_list_id')));

    if(!$log_column)  { //lets try to find in extra columns
      foreach($this->get_extra_columns() as $column)  {
        if($column['column_name'] == $column_name) {
          return $column;
        }
      }
    } else  {
				//	pr($log_column['LogColumn']);
      return $log_column['LogColumn'];
    }
  }

	function beforePurge()	{
		
	}

	function afterPurge( $id )	{
		$this->LogColumn->query('delete from `log_columns` where `log_id`=\'' . $id . '\'');
		$this->LogEntry->query('delete from `log_entries` where `log_id`=\'' . $id . '\'');
	}

	function sheetDetectColumnType($sheet, $col, $row, $column_name='')	{
		$column_type = $this->LogColumn->_defaultColumnType;
		if(PHPExcel_Shared_Date::isDateTime($sheet->getCell($col . $row))) {
			$column_type = 'date';
		}	
		else if($column_name && strstr($column_name, 'mm/dd/yyyy'))
		{
			$column_type = 'date';
		}
		return $column_type;
	}


/**
 * stats - to find current stats
 * @param string $date
 * @return array
 */
 public function stats($date=null){
	if(!$date) $date = date('Y-m-d');
	$date = date('Y-m-d', strtotime($date));
	$result = array();
	 $result = array('total'=> $this->getTotalLogCount($date),
											//'overdue' =>$this->getOverdueLogEntryCount($date),
											//'completed' => $this->getCompletedLogEntryCount($date),
											'deleted' => $this->getDeletedLogCount($date)
											); 
	return $result;
 }

 function getTotalLogCount($date){
		if(!$date) $date = date('Y-m-d');
		return $this->find('count', array('conditions'=>array('Log.created <='=>$date), 'recursive'=> -1));
 }

 function getDeletedLogCount($date){
		if(!$date) $date = date('Y-m-d');
		return $this->find('count', array('conditions'=>array('Log.deleted'=>1, 'Log.deleted_date <='=>$date), 'recursive'=> -1));
 }


}
