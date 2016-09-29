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
class LogsController extends SpreadsheetAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Spreadsheet.PhpExcel', 'FileUpload.FileUpload');

  public $helpers = array('TinyMCE.TinyMCE', 'Spreadsheet.Util');

  private $subactions = array('LogColumn'=>array('manage_columns')); //these actions are the part of this very controller but uses different models as primary model

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
		$this->FileUpload->modelFieldCheck(true);

  /*  foreach($this->subactions as $model=>$actions)  {
      if(in_array($this->action, $actions)) {
        $this->set('subaction', $this->action);
        $this->__modelClass = $model;
        break;
      }
    } */
    parent::beforeFilter();
  }

//----------------Managing Logs Starts ----------------------//


/**
 * index method
 *
 * @return void
 */
	public function index() {
		//set custom pagination limit
		$this->_setPaginationLimit('Log');
		$userid = $this->Auth->user('id');
		//$options = array('conditions'=> array('Log.user_id'=>$userid));
		//if($this->_isAdmin()){
		$options = array();
		//}
		$this->Log->recursive = 0;
		$this->paginate = array_merge($this->paginate, array('Log' => $options));
		$this->set('logs', $this->Paginator->paginate('Log'));
		$this->render('index');
	}

	/**
 * admin_index method
 *
 * @return void
 */
	public function admin_index() {
			return $this->index();
	}


/**
 * view method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function view($id = null) {
		if (!$this->Log->exists($id)) {
			throw new NotFoundException(__('Invalid log'));
		}
		$options = array('conditions' => array('Log.' . $this->Log->primaryKey => $id));
    $log = $this->Log->find('first', $options);
    $this->Log->id = $log['Log']['id'];
    //debug();
    $log = $this->Log->prepareLog($log);
		$this->set('log', $log);
		$this->render('view');
	}

	/**
 * admin_view method
 *
 * @return void
 */
	public function admin_view($id = null) {
			return $this->view($id);
	}

/**
 * add method
 *
 * @return void
 */
	public function add() {
		if ($this->request->is('post')) {
      if(!$this->request->data['Log']['create_from_file']) {
        $this->Log->validator()->remove('file_to_import');
      }

			

			if ($this->Log->save($this->request->data)) {
        $id = $this->Log->id;
        if($this->request->data['Log']['create_from_file']) {
					$fileuploaded = $this->__processSpreadsheetUpload();
          //$id = 5;
					//echo $fileuploaded;
          if($fileuploaded) {
            $objPHPExcel = $this->PhpExcel->createReader($fileuploaded);
						//debug($objPHPExcel);
            //  Get worksheet dimensions
            $sheet = $objPHPExcel->getSheet(0); 
            $highestRow = $sheet->getHighestDataRow(); 
						//Check if First Row has Data or Null

						$emptyRowCount = 0;  //Empty Row Counter Set;
						for ($row = 1; $row <= $highestRow; $row++) {
									$highestHeaderColumn = $sheet->getHighestDataColumn($row);
									$tempRowData = $sheet->rangeToArray('A' . $row . ':' . $highestHeaderColumn . $row,
									NULL,
									TRUE,
									FALSE);
									$tempRowData = array_filter($tempRowData[0]);
										if(!$tempRowData){
											$emptyRowCount++;
												}
									if($emptyRowCount >= 3){ 
										$this->Session->setFlash(__('New log has been created, but data import was not successful.'));
											return $this->redirect(array('action' => 'edit', $id));
										} //Break Loop, if got 3 contineous Empty Rows
									if($tempRowData){
										$firstRow = $row; //Setting First/Base Non-empty Row
									break;
									}
						}
						
						$highestDataColumn = $sheet->getHighestDataColumn();
						//debug($highestHeaderColumn);  debug($highestDataColumn); 
						for($c=1;$c<=3;$c++){
								if($highestHeaderColumn >= $highestDataColumn){
									break;
								}
								else{
									$highestHeaderColumn++;
								}
						}
						$highestColumn = $highestHeaderColumn;
						//debug($highestRow);
						//debug($highestColumn);
						//exit;

            $columns = array();
            $row_id = 1; //say it is first
            $data_to_insert = array(); $number_of_rows_imported = 0;
						$emptyRowCount = 0;  //Empty Row Counter Set;
            //  Loop From First Non-Empty Row through each row of the worksheet in turn
            for ($row = $firstRow; $row <= $highestRow; $row++) {
              //  Read a row of data into an array
							$tempRowData = $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
              NULL,
              TRUE,
              false,
							true);
							
							$tempRowData = array_filter($tempRowData[$row]);
									 
							if( !$tempRowData ) 
							{
								$emptyRowCount++;
							}
							if($emptyRowCount >= 3)
							{ 
								break;
							} //Break, if 3 contineous Empty Rows round

							if( $row==$firstRow ) 
							{
								$excelcolumns = $rowData[$row]; //for some reason (?? : TODO) it is 0th row having array
									foreach($excelcolumns as $col=>$column_name)  { //for each column name

									$column_type = $this->Log->sheetDetectColumnType($sheet, $col, $row, $column_name);

									if(!$this->Log->is_extra_column($column_name))  {
										if(is_null($column_name)) $column_name = 'unnamed column';
										$this->Log->LogColumn->create();
										$this->Log->LogColumn->save(array('column_name'=>$column_name, 'log_id'=>$id, 'column_type'=>$column_type));
										$columns[$col] = array('id'=>$this->Log->LogColumn->id, 'column_name'=>$column_name, 'column_type'=>$column_type);
									} else  {
										$columns[$col] = $this->Log->getExtraColumnByName($column_name);
									}
								}
								//debug($columns);
								$this->Log->saveField('log_column_count', count($columns));
							}
							
							//exit;
							if($row > $firstRow)  {
								if($tempRowData)
								{
									$emptyRowCount = 0; //Reset Empty Rows Counter
									if($columns)  
									{
										$excelrow = $rowData[$row];
										//debug($excelrow);
										$i=0;
										foreach($excelrow as $col=>$log_entry)  
										{
											// we will parse column value format type to check whether it is special type, for now it supports only date type TODO: to support more format types, see "parseSheetColumnValue"
											$log_entry = $this->Log->LogEntry->parseSheetColumnValue($sheet, $col, $row, $log_entry);
											$data_to_insert[] = array(
												'log_id'=>$id,
												'log_column_id'=>$columns[$col]['id'],
												'log_entry'=> "$log_entry",
												'row_id'=>$row_id
											);
										}
										$row_id++;
										$number_of_rows_imported++; //if we can dare to say it ;)!!
									}
								}
							}
            }
						//exit;
						//debug($data_to_insert);
						//exit;
            if($data_to_insert) {
              $this->Log->LogEntry->create();
              if ($this->Log->LogEntry->saveMany($data_to_insert)) {
                $this->log("##authuser## created a new log ##action-view## from file", 'system');
                $this->Session->setFlash(sprintf(__('New Log from file with %d rows has been created'), $number_of_rows_imported));
                $this->Log->saveField('log_entry_count', $number_of_rows_imported);
                @unlink($fileuploaded); //delete the file after upload
                return $this->redirect(array('action' => 'index', $id));
              } else {
                $this->Session->setFlash(__('Log could not be created from file. Please, try again.'), 'default', array('class'=>'btn-danger'));
              }
            }
          }
        }

        $this->log("##authuser## created a new log ##action-view##", 'system');

				$this->Session->setFlash(__('New log has been created.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The log could not be saved. Please, try again.'));
			}
		}
		$userid = $this->Auth->user('id');
		$options = array('conditions'=>array('User.id'=>$userid));
		if($this->_isAdmin()){
			$options = array();
			}
		$users = $this->Log->User->find('list', $options);
		$this->set(compact('users'));
		$this->render('add');
	}

	/**
 * admin_add method
 *
 * @return void
 */
	public function admin_add() {
			return $this->add();
	}

/**
 * edit method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function edit($id = null) {
		if (!$this->Log->exists($id)) {
			throw new NotFoundException(__('Invalid log'));
		}
		if ($this->request->is(array('post', 'put'))) {
			if ($this->Log->save($this->request->data)) {
        $this->log("##authuser## updated log ##action-view##", 'system');
				$this->Session->setFlash(__('The log has been saved.'));
				return $this->redirect(array('action' => 'index'));
			} else {
				$this->Session->setFlash(__('The log could not be saved. Please, try again.'));
			}
		} else {
			$options = array('conditions' => array('Log.' . $this->Log->primaryKey => $id));
			$this->request->data = $this->Log->find('first', $options);
		}
		$userid = $this->Auth->user('id');
		$options = array('conditions'=>array('User.id'=>$userid));
		if($this->_isAdmin()){
			$options = array();
		}
		$users = $this->Log->User->find('list', $options);
		$this->set(compact('users'));
		$this->render('edit');
	}

/**
 * admin_edit method
 *
 * @return void
 */
	public function admin_edit($id = null) {
			return $this->edit($id);
	}


/**
 * delete method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function delete($id = null) {
		$this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->Log->delete()) {
      $this->log("##authuser## deleted log ##action-view##", 'system');
			$this->Session->setFlash(__('The log has been deleted.'));
		} else {
			$this->Session->setFlash(__('The log could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}


/**
 * purge method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function purge($id) {
		$this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid Control'));
		}
		$this->request->allowMethod('post', 'delete');

		$this->Log->beforePurge( $id );

		if ($this->Log->delete( $id )) {
			$this->Log->afterPurge( $id );
      $this->log("##authuser## permanently deleted a Control #" . $id, 'system');
			$this->Session->setFlash(__('A Log was delete permanently.'));
		} else {
			$this->Session->setFlash(__('The Control could not be deleted. Please, try again.'));
		}
		return $this->redirect(array('action' => 'index'));
	}

  public function export($id) {
    $this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}

    $attachments = array();

    $options = array(
      'conditions' => array('Log.id' =>  $id),
      'contain' => array(
        'LogColumn',
        'LogEntry' => array(
          'Upload'
        )
      )
    );

    $log = $this->Log->find('first', $options);
    $log = $this->Log->prepareLog($log, false, false);

    //pr($log);
    //exit;

    function test_alter(&$column, $key)
    {
        //$column['filter'] = true;
        $column['label']  = $column['column_name'];
        if($column['column_type']=='longtext')  {
          $column['width'] = 25;
        }
    }
    array_walk($log['LogColumn'], "test_alter");
    // define table cells

    //start building Excel data
    $table = $log['LogColumn'];

    $this->PhpExcel->createWorksheet()->setDefaultFont('Calibri', 12);

    $this->PhpExcel->addTableHeader($table, array('name' => 'The Controlist Log', 'bold' => true));

    foreach($log['entries'] as $row_id => $columns)  {
      //debug($columns);
      if($row_id==0) continue;
      $row = array();
      foreach($columns as $entry)  {
          if(isset($entry['Upload']) && count($entry['Upload']) > 0)  {
            foreach($entry['Upload'] as $upload)  {
              $attachments[] = $upload;
            }
          }
          array_push($row, $entry['log_entry']);
      }
     // debug($row);
			//exit;
      $this->PhpExcel->addTableRow($row);
    }

    $this->log("##authuser## exported log ##action-view##", 'system');

    $newfile_wo_ext = strtolower(Sanitize::paranoid($log['Log']['log_name'])). '-' . rand(2000, 10000);
    $filename = $newfile_wo_ext . '.xlsx';

    $downloadables = array();$filetmp = array();

    //save the downloadable into a single zip
    if($attachments)  {
      $zip = new ZipArchive;
      $zippedattachmentsfilename = 'log-' . $newfile_wo_ext . '-attachments.zip';
      $attachmentpath = WWW_ROOT . XLOGDIR . DS . $zippedattachmentsfilename;
      if ($zip->open($attachmentpath, ZipArchive::CREATE) === TRUE) {
        foreach($attachments as $attachment)  {
          $zip->addFile(WWW_ROOT . 'files' . DS . $attachment['name'], $attachment['filename']);
        }
        $zip->close();
        $downloadables[$zippedattachmentsfilename] = $attachmentpath;
        $filetmp[] = $attachmentpath;
      } else {
        echo 'failed downloading files';
      }
    }

    // close table and output
    $this->PhpExcel->addTableFooter()->save(WWW_ROOT . XLOGDIR . DS . $filename);
    $downloadables[$filename] = WWW_ROOT . XLOGDIR . DS . $filename;
    $filetmp[] = WWW_ROOT . XLOGDIR . DS . $filename;

    $zip = new ZipArchive;
    $bundle = 'log-' . $newfile_wo_ext . '-bundle.zip';
    $bundlepath = WWW_ROOT . XLOGDIR . DS . $bundle;
    $filetmp[] = $bundlepath;
    if ($zip->open($bundlepath, ZipArchive::CREATE) === TRUE) {
       foreach($downloadables as $file=>$downloadpath)  {
          $zip->addFile($downloadpath, $file);
        }
        $zip->close();
        $this->__download($bundle, $bundlepath);
    }

    if(count($filetmp) > 0)  {
      foreach($filetmp as $filepath) {
        unlink($filepath);
      }
    }
	}

  function __processSpreadsheetUpload()  {
    if(isset($this->request->data['Log']['file_to_import']))  {
			$error = $this->request->data['Log']['file_to_import']['error'];
      if($this->request->data['Log']['file_to_import']) {
				if( $error == UPLOAD_ERR_OK ) {
          $uploads_dir = WWW_ROOT . 'logexcel';
          $tmp_name = $this->request->data['Log']['file_to_import']["tmp_name"];
          $filename = $this->request->data['Log']['file_to_import']["name"];
          $filepath = "$uploads_dir/$filename";
          if(move_uploaded_file($tmp_name, $filepath))  {
						//echo "Hi! its in Process Spreadsheet Upload";
						//echo $filepath;
            return $filepath;
          }
        } else  {
          $this->Log->validationErrors = array(
            'file_to_import'=>__('File could not be uploaded. Try Again!')
          );
        }
      }
    }
  }


  public function import($id) {

    $this->Log->id = $id;
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		
		$this->loadModel('Spreadsheet.LogColumnsCustomList');
    if($this->request->is('post')) {
      
      $fileuploaded = $this->__processSpreadsheetUpload();

      if($fileuploaded) {

        $objPHPExcel = $this->PhpExcel->createReader($fileuploaded);
				//debug($objPHPExcel);
        //  Get worksheet dimensions
        $sheet = $objPHPExcel->getSheet(0);
        $highestRow = $sheet->getHighestDataRow(); 
        $highestColumn = $sheet->getHighestDataColumn();
				
				//debug($highestRow);
				//debug($highestColumn);
				//debug($highestColumn++);
				//exit;
        if($this->request->data['Log']['import_option']=='Overwrite') {
          $this->Log->LogEntry->deleteAll(array('LogEntry.log_id'=> $id));
          $this->Log->id = $id;
          $this->Log->saveField('log_entry_count', '0'); 
        }

        $columns = array();
        $row_id = $this->Log->LogEntry->getNextRowNumber($id);
        $data_to_insert = array();$number_of_rows_imported = 0;
				$new_list_entry_name = array(); $number_of_new_list_entries =0;
				$emptyRowCount = 0;  //Set Empty Row Counter
        //  Loop through each row of the worksheet in turn
        for ($row = 1; $row <= $highestRow; $row++) {
          //  Read a row of data into an array
         $tempRowData = $rowData = $sheet->rangeToArray('A' . $row . ':' . $highestColumn . $row,
          NULL,
          TRUE,
          FALSE, true);
				 //debug($tempRowData);
									 $tempRowData = array_filter($tempRowData[$row]);
										if(!$tempRowData){
											$emptyRowCount++;
												}
										if($emptyRowCount >= 3){ break; } //Break Loop, if got 3 contineous Empty Rows
					//pr($rowData);
          //  Insert row data array into your database of choice here
          if($row==1) {
            $excelcolumns = $rowData[$row]; //for some reason (?? : TODO) it is 0th row having array
						//debug($excelcolumns);
						//exit;
            $this->Log->LogColumn->unbindModel(array('hasMany'=>array('LogEntry'))); //remove unnecessary binding
            foreach($excelcolumns as $col=>$column_name)  { //for each column name

							$column_type = $this->Log->sheetDetectColumnType($sheet, $col, $row, $column_name);

              $log_column = $this->Log->find_log_column($id, $column_name);

              if($log_column) { //if found get the id, name
                $columns[] = $log_column;
              } else  { //else create an entry (TODO: Later one, maybe)
                $columns[] = array('id'=>null, 'column_name'=>$column_name, 'column_type'=>$column_type);
                //die('Columns does not match!!');
								$this->Session->setFlash(__('Columns does not match!!'));
								return $this->redirect(array('action' => 'import', $id));
              }
            }
						//debug($columns);
						//exit;
          }
          if($row>1)  {
						if($tempRowData){
									$emptyRowCount = 0; //Reset Empty Rows Counter
            if($columns)  {
              $excelrow = $rowData[$row];$i=0;
						//	pr($columns);
              foreach($excelrow as $col=>$log_entry)  {

								$log_entry = $this->Log->LogEntry->parseSheetColumnValue($sheet, $col, $row, $log_entry);
							
								//checking new entry for dropdown custom list
									if($columns[$i]['column_type'] == 'dropdown' && $log_entry != '')
									{
										$entries = array();
										$options = array('conditions' => array('LogColumnsCustomListEntry.log_columns_custom_list_id' => $columns[$i]['log_columns_custom_list_id'] ));
												$lists_entries = $this->LogColumnsCustomList->LogColumnsCustomListEntry->find('all', $options); 
			 								//pr($lists_entries);
													$j = 0;
												$listEntries= array();
												foreach($lists_entries as $lists_entry){
												$listEntries[$j]	= 	$lists_entry['LogColumnsCustomListEntry']['entry_name'];
												$j++;
													}
										
									if(!in_array($log_entry, $listEntries))	{
									
										//	echo $log_entry;
										$new_list_entry_name[] =  array(
                    'id'=>null,
                    'entry_name'=> "$log_entry",
                    'log_columns_custom_list_id'=>$columns[$i]['log_columns_custom_list_id']
										 );	

										$number_of_new_list_entries++;
										//echo $log_entry;
										//pr($new_list_entry_name);
										}
									
									}

								//new list enrty check ends

                $data_to_insert[] = array(
                    'log_id'=>$id,
                    'log_column_id'=>$columns[$i++]['id'],
                    'log_entry'=> "$log_entry",
                    'row_id'=>$row_id
                );
              }
              $row_id++;
              $number_of_rows_imported++; //if we can dare to say it ;)!!
							}
						}
          }
        }
				//pr($data_to_insert);
				//pr($new_list_entry_name);
				//exit;
		
	      if($data_to_insert) {
          $this->Log->LogEntry->create();
          if ($this->Log->LogEntry->saveMany($data_to_insert)) {
								//saving custom list new entry to custom list entry table, if any 
								if($new_list_entry_name){
									$this->LogColumnsCustomList->LogColumnsCustomListEntry->create();
										$this->LogColumnsCustomList->LogColumnsCustomListEntry->saveMany($new_list_entry_name);
								}
								//Code End for saving custom list new entry 
            $this->log(sprintf("##authuser## imported %d rows to log ##action-view##", $number_of_rows_imported), 'system');
            $this->Session->setFlash(sprintf(__('The log with %d rows has been imported'), $number_of_rows_imported));
            $this->Log->updateEntryCount($id, '+', $number_of_rows_imported);
            @unlink($fileuploaded); //delete the file after upload
            return $this->redirect(array('controller'=>'entries', 'action' => 'index', $id));
          } else {
            $this->Session->setFlash(__('Log could not be imported. Please, try again.'), 'default', array('class'=>'btn-danger'));
          }
        }
								
      }
    }

    $this->Log->unbindModel(array('hasMany'=>array('LogColumn', 'LogEntry'))); //remove unnecessary binding
    $log = $this->Log->find('first');
    //$log = $this->Log->prepareLog($log, false);
    $this->set(compact('log'));
  }
}
