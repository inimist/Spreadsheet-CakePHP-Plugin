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
 * LogEntry Model
 *
 */
class LogEntry extends SpreadsheetAppModel {

  //public $actsAs = array('SoftDelete');
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'row_id';

  //public $actsAs = array('Containable');
  var $actsAs = array(
    'FileUpload.FileUpload' => array(
      'fileModel' => 'LogEntry',
      'fileVar' => 'attachment',
      'uploadDir' => 'files',
      'forceWebroot' => true,
      'allowedTypes' => array(
        'jpg' => array('image/jpeg', 'image/pjpeg'),
        'jpeg' => array('image/jpeg', 'image/pjpeg'), 
        'gif' => array('image/gif'),
        'png' => array('image/png','image/x-png'),
        'pdf',
        'txt' => 'text/plain',
        'doc', 'docx', 'xls', 'xlsx', 'xlsm', 'msg'
      ),
      'required' => false,
      'maxFileSize' => '1000000',
      'unique' => true,
      'fileNameFunction' => 'sha1'
    ),
		'SoftDelete'
  );


/**
 * Validation rules
 *
 * @var array
 */
	/*public $validate = array(
		'log_entry' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				//'message' => 'Your custom message here',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		)
	);*/

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Log' => array(
			'className' => 'Log',
			'foreignKey' => 'log_id',
			'conditions' => '',
			'fields' => array('id','log_name'),
			'order' => ''
		),
	/*	'Status' => array(
			'className' => 'Status',
			'foreignKey' => '',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		) */
	);

/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'Upload' => array(
			'className' => 'Upload',
			'foreignKey' => 'foreign_key',
			'dependent' => false,
			'conditions' => array('Upload.model' => 'LogEntry'),
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'counterQuery' => ''
		)
	);



  //finding next available row number for a Log
  function getMaxRowNumber($log_id) {
    $options = array(
      'conditions' => array(
        'LogEntry.log_id' => $log_id
      ),
      'fields' => array(
        'MAX(row_id)'
      )
    );
    $row = $this->find('first' , $options);
    return (int)$row[0]['MAX(row_id)'];
  }
  //finding next available row number for a Log
  function getNextRowNumber($log_id) {
    $next_row_number = $this->getMaxRowNumber($log_id) + 1;
    return $next_row_number;
  }
	
	/**
 * calendar - Basically for calendar month view
 *@params $date(date)
 *@params $type_id(int)
 *@params $type_id(int)
 *@params $department_id(int)
 * @return array
 */
	function calendar($date, $type_id, $calendar_view_id, $user_id = null, $department_id = null) {
				
				//set start end Date according to Calendar view
				$this->_setCalendarStartEndDate($date, $calendar_view_id);
				$start = date('m/d/Y', strtotime($this->calandar_startdate));
				$end = date('m/d/Y', strtotime($this->calandar_enddate));

			$conditions = array(
					'LogEntry.log_column_id' => 'reminder_date',
					'LogEntry.log_entry >=' => $start,
					'LogEntry.log_entry <=' => $end,
					'Log.deleted' => 0
			);
		
			//debug($start);
			//debug($end);
			$this->Behaviors->load('Containable');
			$options = array('conditions' => $conditions); //, 'ControlCompletion'=>array('Upload')
			$this->recursive = 0;
			$entries = $this->find('all', $options);
			//debug($entries); 
			$logEntries = array(); 
			foreach($entries as $entry){

			//pr($entry);
			//exit;
			$varRow = $this->find('all', array('conditions'=>array('LogEntry.row_id'=>$entry['LogEntry']['row_id'], 'LogEntry.log_id'=>$entry['LogEntry']['log_id'], 'Log.deleted'=>0)));
				$row = array('Entry'=>null);
				foreach($varRow as $rowentry){
						$row['Entry'][$rowentry['LogEntry']['log_column_id']] = $rowentry['LogEntry']['log_entry'];
					}
				$row['Log'] = $entry['Log'];
				$row['Entry']['row_id'] = $entry['LogEntry']['row_id'];
				switch($type_id){
					case 1: //for logged in user only
					if($row['Entry']['user_id'] == $user_id)
					$logEntries[] = $row;
					break;
					case 2:
					$logEntries[] = $row;
					break;
					case 3:
					$logEntries[] = $row;
					break;
				}
			}
			$events = array();
			foreach($logEntries as $logEntry)	{
				//pr($logEntry);
					$events[$logEntry['Entry']['reminder_date']][] = $logEntry;
				}
				//pr($events); exit;
			return $events;
		}

/**
 * getOverdueLogEntries - fetch Overdue logEntries Reminders
 *@params $date(date)
 *@params $type_id(int)
 *@params $type_id(int)
 *@params $department_id(int)
 * @return array
 */
	function getOverdueLogEntries($type_id, $user_id, $department_id, $date = null){
		if(!$date)
		{ 
			$date = date('Y-m-d'); 
		}
		else
		{ 
			$date = date('Y-m-d', strtotime($date));
		}
		//debug($date);
		$entries = array();

		$this->Behaviors->load('Containable');

		$options = array(
			'conditions' => array('LogEntry.log_column_id' => 'reminder_date', "STR_TO_DATE(`LogEntry.log_entry`, '%m/%d/%Y') <=" =>$date, 'LogEntry.log_entry !='=>'', 'Log.deleted'=>0),
		); //, 'ControlCompletion'=>array('Upload')

		$this->recursive = 0;
		//debug($options);
		$entries = $this->find('all', $options);

		//debug($entries); exit;
		$logEntries = array(); 
		$User = ClassRegistry::init('User');
		foreach($entries as $entry){ //fetch valid log entries
			$rowOptions = array('conditions'=>array('LogEntry.row_id'=>$entry['LogEntry']['row_id'], 'LogEntry.log_id'=>$entry['LogEntry']['log_id']));
			$varRow = $this->find('all', $rowOptions);
			$row = array('Entry'=>null);
				//fetch all columns entries for single row
				foreach($varRow as $rowentry){
						$row['Entry'][$rowentry['LogEntry']['log_column_id']] = $rowentry['LogEntry']['log_entry'];
					}
					//debug($row);
				if(isset($row['Entry']['status_id']) && $row['Entry']['status_id'] != STATUS_COMPLETE && $row['Entry']['set_reminder']){
					//$row['Status'] = $this->Status->find('first', array('conditions'=>array('Status.id'=>$row['Entry']['status_id'])));
					$row['Log'] = $entry['Log'];
					if(isset($row['Entry']['user_id']) && $row['Entry']['user_id'] ) $row['User'] = $User->find('first', array('conditions'=>array('User.id'=>$row['Entry']['user_id']), 'fields'=>array('id','first_name','last_name','email_address','full_name')))['User'];
					$row['Entry']['row_id'] = $entry['LogEntry']['row_id'];
					switch($type_id){
						case 1: //for logged in user only
						if($row['Entry']['user_id'] == $user_id)
						$logEntries[] = $row;
						break;
						case 2:
						$logEntries[] = $row;
						break;
						case 3:
						$logEntries[] = $row;
						break;
					}
				}
			}
		//pr($logEntries); exit;
		return $logEntries;
	}

	public function parseSheetColumnValue($sheet, $col, $row, $val)	{
		return $this->parseDateTime($sheet, $col, $row, $val);
	}

	public function parseDateTime($sheet, $col, $row, $val)	{
		if(is_float($val)) {
			//debug($val);
			$cell = $sheet->getCell($col . $row);
			if(PHPExcel_Shared_Date::isDateTime($cell)) {
				$val = date('m/d/Y', PHPExcel_Shared_Date::ExcelToPHP($cell->getValue())); 
			}
		}
		return $val;
	}
}
