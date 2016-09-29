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
class ColumnsController extends SpreadsheetAppController {

/**
 * Components
 *
 * @var array
 */
	public $components = array('Paginator', 'Session', 'Spreadsheet.PhpExcel', 'FileUpload.FileUpload');

  public $helpers = array('TinyMCE.TinyMCE', 'Spreadsheet.Util');

	public $uses = array('Spreadsheet.LogColumn');

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
      'doc', 'docx', 'xlsx', 'xls'
    ));
    $this->FileUpload->uploadDir('files');
    $this->FileUpload->fileModel('LogEntry');
    $this->FileUpload->fileVar('attachment');
    $this->FileUpload->fileNameFunction('sha1');
		$this->FileUpload->modelFieldCheck(true);

    parent::beforeFilter();
  }

	public function index( $log_id = null ) {
		
		if( (!$this->Log->id) && $log_id )	{
			$this->Log->id = $log_id;
		}
		//debug($this->Log->id);
		if($this->Log->id)	{
			$options = array('conditions'=>array('log_id'=>$this->Log->id));
			$this->paginate = $options;
		}	else	{
			throw new NotFoundException(__('Missing Course ID!'));
		}
		$this->LogColumn->recursive = 0;
		$this->set('log_columns', $this->Paginator->paginate());

    $column_types = $this->Log->column_types;

    $this->__modelClass = 'LogColumn';
    $this->set(compact('column_types'));
	}

/**
 * add log column
 *
 * @param int $log_id
 * @return redirect
 */

  public function add( $log_id = null ) {

		if( !$this->Log->id && $log_id )	{
			$this->Log->id = $log_id;
		}

		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}

		$userid = $this->Auth->user('id');
		$this->loadModel('Spreadsheet.LogColumnsCustomList');

		if ($this->request->is('post')) {
				$this->LogColumn->create();
			if ($this->LogColumn->save($this->request->data)) {
							
							// Code for Custom List start
									if(($this->request->data['LogColumn']['column_type'] == 'dropdown') &&  ($this->request->data['LogColumn']['log_columns_custom_list_id'] == ''))
										{
											if(!$this->request->data['LogColumnsCustomList']['user_id']){
													$this->request->data['LogColumnsCustomList']['user_id'] = $userid;
													}

										$newCustomList = array('LogColumnsCustomList' => $this->request->data['LogColumnsCustomList'], 'LogColumnsCustomListEntry' => $this->request->data['LogColumnsCustomListEntry']);
												
										//checking for empty value
										foreach($newCustomList['LogColumnsCustomListEntry'] as $key => $value){
												if(empty($value['entry_name'])){
														unset($newCustomList['LogColumnsCustomListEntry'][$key]);
													}
												}
											if($this->LogColumnsCustomList->saveAll($newCustomList)){
										
												//debug($this->LogColumnsCustomList->id);
												$newCustomListId = $this->LogColumnsCustomList->id;

											$this->LogColumn->saveField('log_columns_custom_list_id', $newCustomListId);
										}
									}
						// Code for Custom List Ends

				 $this->__modelClass = 'LogColumn';
        $this->log("##authuser## added a column ##action-view/{$this->LogColumn->id}## to log ##Log:{$this->Log->id}##", 'system');
				$this->Session->setFlash(__('The log column has been saved.'),'default', array());
				return $this->redirect(array('action' => 'index', '?'=>['log_id'=>$this->Log->id]));
			} else {
				$this->Session->setFlash(__('The column could not be saved. Please, try again.'), 'default', array('class'=>'btn-danger'));
			}
		}

    //$options = array('conditions' => array('Log.' . $this->Log->primaryKey => $this->Log->id));
		//$log = $this->Log->find('first', $options);

		// Code for Custom List start
				$departmentid = $this->Auth->user('department_id');
			$conditions = array('OR'=>array(array('LogColumnsCustomList.user_id'=>$userid), array('LogColumnsCustomList.department_id'=>$departmentid), array('LogColumnsCustomList.is_public'=>true)));
			if($this->_isAdmin()){
							$conditions = array();
							$this->set('is_admin', true);
				}
			$options = array('conditions'=> $conditions);
			$custom_lists = $this->LogColumnsCustomList->find('list', $options);
				$this->set(compact('custom_lists'));

		// Code for Custom List end


    $column_types = $this->Log->column_types;
    //pr($column_types);

    $this->set(compact('column_types'));
	}


/**
 * View log column
 *
 * @param int $column_id
 * @return redirect
 */
  public function view($column_id) {
    return $this->redirect(array('action' => 'edit', $column_id));
  }

	
/**
 * edit log column
 *
 * @param int $column_id
 * @return redirect
 */

  public function edit($column_id) {
		if( !$this->Log->id && $log_id )	{
			$this->Log->id = $log_id;
		}
		if (!$this->Log->exists()) {
			throw new NotFoundException(__('Invalid log'));
		}
		$userid = $this->Auth->user('id');
		$this->loadModel('Spreadsheet.LogColumnsCustomList');

    if ($this->request->is(array('post', 'put'))) {
			$this->LogColumn->create();
			//pr($this->request->data);
			//exit;
			if ($this->LogColumn->save($this->request->data)) {
					// Code for Custom List start
								if(($this->request->data['LogColumn']['column_type'] == 'dropdown') && $this->request->data['LogColumnsCustomList']['name'] !=='' )
										{
									if(!$this->request->data['LogColumnsCustomList']['user_id']){
													$this->request->data['LogColumnsCustomList']['user_id'] = $userid;
													}
										$newCustomList = array('LogColumnsCustomList' => $this->request->data['LogColumnsCustomList'], 'LogColumnsCustomListEntry' => $this->request->data['LogColumnsCustomListEntry']);
										//checking for empty value
										foreach($newCustomList['LogColumnsCustomListEntry'] as $key => $value){
												if(empty($value['entry_name'])){
														unset($newCustomList['LogColumnsCustomListEntry'][$key]);
													}
												}
										if($this->LogColumnsCustomList->saveAll($newCustomList)){
										
												//debug($this->LogColumnsCustomList->id);
												$newCustomListId = $this->LogColumnsCustomList->id;

											$this->LogColumn->saveField('log_columns_custom_list_id', $newCustomListId);
										}
									}
						// Code for Custom List Ends

        $this->__modelClass = 'LogColumn';
        $this->log("##authuser## updated column ##action-view/{$column_id}## to log ##Log:{$this->Log->id}##", 'system');
				$this->Session->setFlash(__('The log column has been saved.'),'default', array());
				return $this->redirect(array('action' => 'index', '?'=>['log_id'=>$this->Log->id]));
			} 
			else {
				$this->Session->setFlash(__('The column could not be saved. Please, try again.'), 'default', array('class'=>'btn-danger'));
			}
		}

    //$options = array('conditions' => array('Log.' . $this->Log->primaryKey => $this->Log->id));
		//$log = $this->Log->find('first', $options);

    $options = array('conditions' => array('LogColumn.' . $this->LogColumn->primaryKey => $column_id));
    $this->request->data = $this->LogColumn->find('first', $options);
		
		// Code for Custom List start
			$departmentid = $this->Auth->user('department_id');
			$conditions = array('OR'=>array(array('LogColumnsCustomList.user_id'=>$userid), array('LogColumnsCustomList.department_id'=>$departmentid), array('LogColumnsCustomList.is_public'=>true)));
			if($this->_isAdmin()){
							$conditions = array();
							$this->set('is_admin', true);
				}
		$options = array('conditions'=> $conditions);
		$custom_lists = $this->LogColumnsCustomList->find('list', $options);
		$this->set(compact('custom_lists'));

		// Code for Custom List end
    $column_types = $this->Log->column_types;
    //pr($column_types);

    $this->set(compact('column_types'));

		//	pr($this->request->data);
	}

/**
 * delete log column
 *
 * @param int $log_id
 * @param int $column_id
 * @return redirect
 */

  public function delete($column_id = null) {

    $this->LogColumn->id = $column_id;
		if (!$this->LogColumn->exists()) {
			throw new NotFoundException(__('Invalid log column'));
		}

		$this->request->allowMethod('post', 'delete');
		if ($this->LogColumn->delete($column_id)) {
      $this->log("##authuser## delete a column ##action-view/{$column_id}## from log ##Log:{$this->Log->id}##", 'system');
			$this->Session->setFlash(__('The log column has been deleted.'),'default', array());
		} else {
			$this->Session->setFlash(__('The log column could not be deleted. Please, try again.'), 'default', array('class'=>'btn-danger'));
		}
		return $this->redirect($this->referer());
	}

/**
 * undelete method
 *
 * @description Assign a Task to a User 
 * @param string $id, $tasks_assignee_id
 * @return void
 */

	public function undelete($column_id) {

    $this->LogColumn->id = $column_id;
		if (!$this->LogColumn->exists()) {
			throw new NotFoundException(__('Invalid log column'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->LogColumn->undelete($column_id)) {
      $this->log("##authuser## retrieved a column ##action-view/{$column_id}## from log ##Log:{$this->Log->id}##", 'system');
			$this->Session->setFlash(sprintf(__('The %s has been retrieved.'), $this->LogColumn->alias), 'default');
		} else {
			$this->Session->setFlash(sprintf(__('The %s could not be retrieved. Please, try again.'), $this->LogColumn->alias));
		}
		return $this->redirect($this->referer());
	}

/**
 * purge method
 *
 * @throws NotFoundException
 * @param string $id
 * @return void
 */
	public function purge($column_id) {
    $this->LogColumn->id = $column_id;
		if (!$this->LogColumn->exists()) {
			throw new NotFoundException(__('Invalid log column'));
		}
		$this->request->allowMethod('post', 'delete');
		$log_id = $this->LogColumn->field('log_id');
		if ($this->LogColumn->delete($column_id)) {
      $this->log(sprintf("##authuser## permanently deleted a column # %d from log ##Log:{$log_id}##", $this->LogColumn->alias, $column_id), 'system');
			$this->Session->setFlash(sprintf(__('The %s has permanently deleted a %s.'), $this->LogColumn->alias), 'default');
		} else {
			$this->Session->setFlash(sprintf(__('The %s could not be deleted. Please, try again.'), $this->LogColumn->alias));
		}
		return $this->redirect($this->referer());
		//return $this->redirect(array('action'=>'index', '?'=>array('log_id'=>$log_id)));
	}




}
