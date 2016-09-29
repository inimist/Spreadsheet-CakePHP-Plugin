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
App::uses('SpreadsheetAppController', 'Spreadsheet.Controller');
App::uses('Sanitize', 'Utility');
/**
 * Logs Controller
 *
 * @package spreadsheet
 * @subpackage spreadsheet.controllers
 *
 * @property Spreadsheet $Log
 * @property PrgComponent $Prg
 * @property SessionComponent  $Paginator
 * @property PaginatorComponent $RequestHandler
 * @property PhpExcelComponent  $PhpExcel
 * @property FileUploadComponent  $FileUpload
 */
class EntriesController extends SpreadsheetAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Spreadsheet.PhpExcel', 'FileUpload.FileUpload');

  public $helpers = array('TinyMCE.TinyMCE', 'Spreadsheet.Util');

	public $uses = array('Spreadsheet.LogEntry');
	
	public $status = array('1'=>'Not Started', '2'=>'In Progress', '3'=>'Completed', '4'=>'Overdue');
	
  function beforeFilter()	{
    /* defaults to:
    'jpg' => array('image/jpeg', 'image/pjpeg'),
    'jpeg' => array('image/jpeg', 'image/pjpeg'),
    'gif' => array('image/gif'),
    'png' => array('image/png','image/x-png'),*/
   
    $this->FileUpload->allowedTypes(array(
      'jpg' => array('image/jpeg','image/pjpeg'),
      'gif',
      'png' => array('image/png','image/x-png'),
      'pdf' => array('application/pdf'),
      'txt' => 'text/plain',
      'doc', 'docx', 'xlsx', 'xls', 'xlsm', 'msg'
    ));
    $this->FileUpload->uploadDir('files');
    $this->FileUpload->fileModel('LogEntry');
    $this->FileUpload->fileVar('attachment');
    $this->FileUpload->fileNameFunction('sha1');
		//$this->FileUpload->modelFieldCheck( true );

    parent::beforeFilter();
  }
  //----------------Managing Log Columns Ends ----------------------//

  //----------------Managing Log Entries Starts ----------------------//

  public function index($log_id = null) {
		if( !$this->Log->id && $log_id )	{
			$this->Log->id = $log_id;
		}
		if($this->Log->id)	{
			$options = array('conditions' => array('Log.' . $this->Log->primaryKey => $this->Log->id), 'recursive'=>1);
		}	else	{
			throw new NotFoundException(__('Missing Course ID!'));
		}

		$log = $this->Log->find('first', $options);
    $log = $this->Log->prepareLog($log);

		//debug($log);
		//exit;

    //pr($log);

    //pr($log['LogColumn']);
    //pr($log['entries']);

    /*$this->paginate = array(
      'LogEntry' => array(
        'limit' => 20, 
        'recursive' => 0, 
        'model' => 'LogEntry', 
        'order' => array('LogEntry.id' => 'ASC')
      )
    );*/

    //$log_entries = $this->Paginator->paginate('LogEntry', array('LogEntry.log_id'=>$log_id));
    //pr($log_entries);

    //$options = array('conditions' => array('LogColumn.log_id' => $log_id));
		//$log_columns = $this->Log->LogColumn->find('all', $options);

    //pr($log_columns);
    //$extra_columns = $this->Log->extra_columns();

    //pr($log['LogColumn']);

    $this->set(compact('log'));
	}

/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id, string $row_id
 * @return void
 */

  public function view($row_id) {

		/* if( !$this->Log->id && $log_id )	{
			$this->Log->id = $log_id;
		}
		*/
		
    if (!$this->Log->exists()) {
      throw new NotFoundException(__('Invalid log'));
    }

		$this->Log->recursive = 1;

    $options = array(
      'conditions' => array('Log.id' =>  $this->Log->id),
      'contain' => array(
        'LogColumn',
        'LogEntry' => array(
          'conditions' => array(
            'LogEntry.row_id' => $row_id
          ),
          'Upload'
        )
      )
    );

    $log = $this->Log->find('first', $options);
    $log = $this->Log->prepareLog($log, false, false);

    $column_types = $this->Log->column_types;
    $this->set(compact('log', 'column_types', 'row_id'));
  }

/**
 * add_entry method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

  public function add($log_id = null) {
		
		if( !$this->Log->id && $log_id )	{
			$this->Log->id = $log_id;
		}
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		
		//debug($this->Log->id);
		if( !$log_id ) $log_id = $this->Log->id;

		//$this->Log->recursive = 1;
		$saved = false;

		if ($this->request->is('post')) {

      $this->Log->rearrangeExtraFields($this->request->data); //passed by reference

      $__upload_id = null;

      if($this->FileUpload->hasUpload()) {
        if($this->FileUpload->success)  {
          $this->request->data['LogEntry']['attachment'] =  $this->FileUpload->getRawFilename();
          $__upload_id =  $this->FileUpload->uploadId;
        } else {
          if($this->FileUpload->hasErrors()) {
            //pr($this->FileUpload->showErrors());
            $this->Session->setFlash($this->FileUpload->showErrors(), 'default', array('class'=>'btn-danger'));
            $this->Log->useRequestData = true;
            $this->__setLogByLogAndRowID($log_id);
            return $this->render();
          }
        }
      } else  {
        $this->request->data['LogEntry']['attachment'] =  '';
      }

      $data = array();
      $row_id = $this->LogEntry->getNextRowNumber($log_id);
      foreach($this->request->data['LogEntry'] as $column_id=>$value) {
        $data[] = array(
          'log_id'=>$log_id,
          'log_column_id' => $column_id,
          'log_entry' => $value,
          'row_id' => $row_id
        );
      }

			$this->LogEntry->create();
			if ($this->LogEntry->saveMany($data)) {
        //$this->__modelClass = 'LogEntry';
        $this->log("##authuser## added an entry ##action-view/{$row_id}?log_id={$log_id}## to log ##Log:{$log_id}##", 'system');
				$this->Session->setFlash(__('The log entry has been saved.'),'default', array());
        $this->Log->updateEntryCount($log_id, '+');
				$saved = true;
			} else {
				$this->Session->setFlash(__('The log entry could not be saved. Please, try again.'), 'default', array('class'=>'btn-danger'));
			}

      if($__upload_id) {
        $id = $this->LogEntry->field('id', array('log_column_id' => 'attachment', 'row_id'=>$row_id));
				//debug(array('log_column_id' => 'attachment', 'row_id'=>$row_id));
				//debug( $id );
        $this->LogEntry->Upload->saveField('foreign_key',  $id);
      }

			if( $saved )	{
				return $this->redirect(array('action' => 'index', '?'=>['log_id'=>$log_id]));
			}
		}
		//debug($this->Log->id);
    $options = array('conditions' => array('Log.' . $this->Log->primaryKey => $this->Log->id), 'recursive'=>1);
		$log = $this->Log->find('first', $options);

		//debug($log );  
		    
    $this->Log->prepareLog($log);
		
		//unset data which is not required for this action.
		if(isset($log['entries'])) unset($log['entries']);
			unset($log['LogEntry']);
		
    $column_types = $this->Log->column_types;

    //$options = array('conditions' => array('LogColumn.log_id' => $log_id));
		//$log_columns = $this->Log->LogColumn->find('all', $options);
   
	//Fetching and pushing Custom List entries/values with $log_columns
	$this->loadModel('Spreadsheet.LogColumnsCustomList');

	 foreach($log['LogColumn'] as $i => $column) {
		if($column['column_type'] == 'dropdown') {
			$entries = array();
					$options = array('conditions' => array('LogColumnsCustomListEntry.log_columns_custom_list_id' => $column['log_columns_custom_list_id'] ));
					$lists_entries = $this->LogColumnsCustomList->LogColumnsCustomListEntry->find('all', $options); 
			 			
			foreach($lists_entries as $lists_entry){
									$entries[$lists_entry['LogColumnsCustomListEntry']['entry_name']]	= 	$lists_entry['LogColumnsCustomListEntry']['entry_name'];
								}
					
			$log['LogColumn'][$i]['list_entries'] =	$entries;
				
			}
	 }
	//pr($log['LogColumn']);
    $extra_columns = $this->Log->extra_columns();

    $users = $this->Log->User->find('list');
		$statuses = $this->status;

    $this->set(compact('log', 'column_types', 'extra_columns', 'users', 'statuses'));
	}

/**
 * edit entry method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */

  public function edit($row_id) {

		/* if( !$this->Log->id && $log_id )	{
			$this->Log->id = $log_id;
		}
		*/
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		$log_id = $this->Log->id ;
		if( !$log_id ) $log_id = $this->Log->id;

		$this->Log->recursive = 1;

		if ($this->request->is('post')) {

			//debug($this->FileUpload);

      $this->Log->rearrangeExtraFields($this->request->data);

      $db = ConnectionManager::getDataSource('default');

      $__upload_id = null;

      if($this->FileUpload->hasUpload()) {
				
        if($this->FileUpload->success)  {
          $this->request->data['LogEntry']['attachment'] =  $this->FileUpload->getRawFilename();
          $__upload_id =  $this->FileUpload->uploadId;
        } else {
          if($this->FileUpload->hasErrors()) {
            //pr($this->FileUpload->showErrors());
            $this->Session->setFlash($this->FileUpload->showErrors(), 'default', array('class'=>'btn-danger'));
            $this->Log->useRequestData = true;
            $this->__setLogByLogAndRowID($log_id, $row_id);
            return $this->render();
          }
        }
      } else  {
        $this->request->data['LogEntry']['attachment'] =  '';
      }
      
      //pr($this->FileUpload);
      $data = array();$updatedAll = false;
      //$row_id = $this->LogEntry->getNextRowNumber($log_id);
      foreach($this->request->data['LogEntry'] as $column_id=>$value) {

        //$upload_id = ($column_id == 'attachment') ? $__upload_id : null;

        $conditions = array(
          'log_id'=>$log_id,
          'log_column_id' => $column_id,
          'row_id' => $row_id
        );

        //pr($conditions);pr($value);

        $data = array(
          'log_entry' => $db->value($value, 'string')
        );

        //pr($column_id);

        //pr($upload_id);

        $find = $this->LogEntry->find('first', array('conditions'=>$conditions));

        if($find) {          
          if ($this->LogEntry->updateAll($data, $conditions))  {
            $updatedAll = true;
          }
        } else  {
          $data = array_merge(array('log_entry'=>$value), $conditions); //conditions becomes data, what? right!
          $this->LogEntry->create();
          $this->LogEntry->save($data);
          $updatedAll = true;
        }

        if($__upload_id) {
          $id = $this->LogEntry->field('id', array('log_column_id' => 'attachment', 'row_id'=>$row_id, 'log_id'=>$log_id));
          $this->LogEntry->Upload->saveField('foreign_key',  $id);
        }
      }

			if ($updatedAll) {
				if(!$this->LogEntry->id) 
						$this->LogEntry->id = $this->LogEntry->field('id', array('log_column_id' => 'status_id', 'row_id'=>$row_id, 'log_id'=>$log_id));
        //$this->__modelClass = 'LogEntry';
        $this->log("##authuser## updated log entry ##action-view/{$row_id}?log_id={$log_id}## in log ##Log:{$log_id}##", 'system');
				$this->Session->setFlash(__('The log entry has been updated.'),'default', array());
				return $this->redirect(array('action' => 'index', '?'=>['log_id'=>$log_id]));
			} else {
				$this->Session->setFlash(__('The log entry could not be updated. Please, try again.'), 'default', array('class'=>'btn-danger'));
			}
		}
    $this->__setLogByLogAndRowID($log_id, $row_id);
	}

  function __setLogByLogAndRowID($log_id, $row_id = null)  {    
   
   $options = array(
      'conditions' => array('Log.id' =>  $log_id),
      'contain' => array(
        'LogColumn'
      )
    );

    if($row_id) {
      $options['contain'] = array_merge(
        $options['contain'], 
        array(
          'LogEntry' => array(
            'conditions' => array(
              'LogEntry.row_id' => $row_id
            ),
            'Upload'
          )
        )
      );
    }

    //pr($options);
    //exit;

    //$this->LogEntry->contain("LogEntry.log_column_id = 'attachment'");

    $this->Log->recursive = 2;
		$log = $this->Log->find('first', $options);

    //pr($log);
    $log = $this->Log->prepareLog($log, false);
    //pr($log['LogColumn']);
    $column_types = $this->Log->column_types;
    //$extra_columns = $this->Log->extra_columns();

    //pr($extra_columns);

    $users = $this->Log->User->find('list');
		$statuses = $this->status;
    //pr($users);
		$this->loadModel('Spreadsheet.LogColumnsCustomList');
		$i = 0;
		foreach($log['LogColumn'] as $column) {
		if($column['column_type'] == 'dropdown') {
			$entries = array();
					$options = array('conditions' => array('LogColumnsCustomListEntry.log_columns_custom_list_id' => $column['log_columns_custom_list_id'] ));
					$lists_entries = $this->LogColumnsCustomList->LogColumnsCustomListEntry->find('all', $options); 
						
			foreach($lists_entries as $lists_entry){
					$entries[$lists_entry['LogColumnsCustomListEntry']['entry_name']]	= 	$lists_entry['LogColumnsCustomListEntry']['entry_name'];
				}
	
				$log['LogColumn'][$i]['list_entries'] =	$entries;
			}
			$i++;
		}


    $this->set(compact('log', 'column_types', 'row_id', 'users', 'statuses'));
  }


  public function delete($row_id = null) {

    $log_id = $this->Log->id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		$entry = $this->LogEntry->find('first', array('conditions'=>array('LogEntry.log_id'=>$log_id, 'LogEntry.row_id'=>$row_id)));
		if (!$entry) {
			throw new NotFoundException(__('Invalid log entry'));
		}

		$this->request->allowMethod('post', 'delete_entry');

		if ($this->LogEntry->deleteAll(array('LogEntry.log_id' =>$log_id, 'LogEntry.row_id'=>$row_id))) {
      $this->Log->updateEntryCount($log_id, '-');
      if(isset($entry['Upload']) && $entry['Upload'] > 0) {
        foreach($entry['Upload'] as $upload) {
          $this->__delupload($upload['id']);
        }
      }
      //$this->delupload();
      $this->log("##authuser## deleted an entry #".$entry['LogEntry']['row_id']." from log ##Log:{$log_id}##", 'system');
			$this->Session->setFlash(__('The log entry has been deleted.'),'default', array());
		} else {
			$this->Session->setFlash(__('The log entry could not be deleted. Please, try again.'), 'default', array('class'=>'btn-danger'));
		}
		return $this->redirect(array('action' => 'index', '?'=>['log_id'=>$log_id]));
	}

	public function export_overdue(){
		if(!isset($this->request->data['Export'])) return;

		if($this->request->is(array('put', 'post'))){
			$type_id = $this->request->data['LogEntry']['report_by_type_id'];
			$user_id = $this->request->data['LogEntry']['report_by_user_id'];
			$department_id = $this->request->data['LogEntry']['report_by_department_id'];
			
			// create new empty worksheet and set default font
		$this->PhpExcel->createWorksheet() ->setDefaultFont('Calibri', 12);
		$results = $this->LogEntry->getOverdueLogEntries($type_id, $user_id, $department_id);
						//debug($results); exit;
			$this->loadModel('Status');
			$status = $this->Status->find('list');
				$table = array(
											array('label' => __('Log Name'), 'filter' => true), //0
											array('label' => __('Row Entry #')),
											array('label' => __('Reminder Date'), 'filter' => true),
											array('label' => __('User')),
											array('label' => __('Status'), 'filter' => true),
					);
					$this->PhpExcel->addTableHeader($table, array('name' => 'Report', 'bold' => true));
					foreach ($results as $result) {
							$tmpdata = array(
								$result['Log']['log_name'],
								$result['Entry']['row_id'],
								$result['Entry']['reminder_date'],
								$result['User']['full_name'],
								$status[$result['Entry']['status_id']]
							);
								$this->PhpExcel->addTableRow($tmpdata);
						}
				$this->PhpExcel->addTableFooter()->output('LogEntry Overdue Reports'.date("Fj-Y").'.xlsx');
			}
	}

}