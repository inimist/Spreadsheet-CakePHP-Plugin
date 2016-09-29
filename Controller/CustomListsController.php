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
 * CustomLists Controller
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
class CustomListsController extends SpreadsheetAppController {

/**
 * Components
 *
 * @var array
 */

public $components = array('Paginator', 'Session', 'Spreadsheet.PhpExcel', 'FileUpload.FileUpload');

  public $helpers = array('TinyMCE.TinyMCE', 'Spreadsheet.Util');

	public $uses = array('Spreadsheet.LogColumnsCustomList');

	public function index(){
	
	//	$custom_lists = $this->LogColumnsCustomList->find('all');
		//debug($this->_isAdmin());
		$userid = $this->Auth->user('id');
		$departmentid = $this->Auth->user('department_id');
		$conditions = array('OR'=>array(array('LogColumnsCustomList.user_id'=>$userid), array('LogColumnsCustomList.department_id'=>$departmentid), array('LogColumnsCustomList.is_public'=>true)));
		if($this->_isAdmin()){
					$conditions = array();
		}
		$options = array('conditions'=> $conditions);
		$this->paginate = array('LogColumnsCustomList' => $options);
		$this->set('custom_lists', $this->Paginator->paginate('LogColumnsCustomList'));
		//$this->set(compact('custom_lists'));

	}

/**
 * View Custom List
 *
 * @param int $customList_id
 * @return redirect
 */

	public function view($customList_id){
	if (!$this->LogColumnsCustomList->exists($customList_id)) {
			throw new NotFoundException(__('Invalid Custom List'));
		}
	$customList = $this->LogColumnsCustomList->findById($customList_id);
	$this->set(compact('customList'));
	//return $this->redirect(array('action' => 'edit', $customList_id));  // to open list directly in edit mode.

	}

	
	/**
 * Add Custom List
 *
 * @return redirect
 */

	public function add(){
		
		if ($this->request->is('post')) {
			if(!$this->request->data['LogColumnsCustomList']['user_id']){
			$this->request->data['LogColumnsCustomList']['user_id'] = $this->Auth->user('id');
			}
			foreach($this->request->data['LogColumnsCustomListEntry'] as $key => $value){
							if(empty($value['entry_name'])){
                   unset($this->request->data['LogColumnsCustomListEntry'][$key]);
                }
						}
			if ($this->LogColumnsCustomList->saveAll($this->request->data)) {
				$this->log("##authuser## added a Log Custom List ##action-view##", "system");
				$this->Session->setFlash(__('The Custom List has been saved.'),'default', array());
					$this->redirect(array('action' => 'index'));
				}
				else{
					$this->Session->setFlash(__('The Custom List could not be saved. Please, try again.'), 'default', array('class'=>'btn-danger'));
				}
		}
		if($this->_isAdmin()){
			$this->set('is_admin', true);
		}
	}


/**
 * Edit Custom List
 *
 * @param int $customList_id
 * @return redirect
 */
	public function edit( $id = null )	{
		
		if($this->request->is('ajax'))
		{
			$this->layout = 'ajax';			
		}
		
		$this->LogColumnsCustomList->id = $id;
		if (!$this->LogColumnsCustomList->exists()) {
			throw new NotFoundException(__('Invalid Custom List'));
		}	
		
		if ($this->request->is(array('post', 'put'))) {
			if(!$this->request->data['LogColumnsCustomList']['user_id']){
			$this->request->data['LogColumnsCustomList']['user_id'] = $this->Auth->user('id');
			}
			//pr($this->request->data);
				foreach($this->request->data['LogColumnsCustomListEntry'] as $key => $value){
							if(empty($value['entry_name'])){
                   unset($this->request->data['LogColumnsCustomListEntry'][$key]);
                }
						}
						//pr($this->request->data);
				$remove_Entries = $this->request->data['LogColumnsCustomList']['remove_Entries'];
			if ($this->LogColumnsCustomList->saveAssociated($this->request->data, array('deep' => true, 'atomic'=>false))) {
					
					if($remove_Entries !==''){
						$this->LogColumnsCustomList->LogColumnsCustomListEntry->deleteAll(array('id'=>explode(',',$remove_Entries)));
						}
					$this->log("##authuser## updated a Log Custom List ##action-view##", "system");
				if($this->request->is('ajax')){
						echo '1';
						//unset($id);
							}
						else{
						$this->Session->setFlash(__('The Custom List has been saved.'),'default', array());
						return $this->redirect(array('action' => 'index'));
						}

					}
				else{
					if($this->request->is('ajax'))
					{ echo '-1';
					}
					else{
					$this->Session->setFlash(__('The Custom List could not be saved. Please, try again.'), 'default', array('class'=>'btn-danger'));
				}
				}
		}
		//$this->LogColumnsCustomList->id = $id;			
		$this->request->data = $this->LogColumnsCustomList->findById( $id );
		if($this->_isAdmin()){
			$this->set('is_admin', true);
		}
	if($this->request->is('ajax'))
		{
			$this->request->data['LogColumnsCustomList']['request_type'] = 'ajax';
			
		}
		//pr($this->request->data);
		
	}


/**
 * delete Custom List
 *
 * @param int $customList_id
 * @return redirect
 */

 public function delete($customList_id = null){
	
	$this->LogColumnsCustomList->id = $customList_id;
		if (!$this->LogColumnsCustomList->exists()) {
			throw new NotFoundException(__('Invalid Custom List'));
		}
		$this->request->allowMethod('post', 'delete');
		if($this->Auth->user('id') != $this->LogColumnsCustomList->field('user_id')){
				$this->Session->setFlash(__('Sorry!! You can\'t delete this List'),'default');
		return $this->redirect($this->referer());
		}
		if ($this->LogColumnsCustomList->delete($customList_id)) {
					$this->log("##authuser## added a Log Custom List #".$customList_id, "system");
    			$this->Session->setFlash(__('The Custom List has been deleted.'),'default', array());
		} else {
			$this->Session->setFlash(__('The Custom List could not be deleted. Please, try again.'), 'default', array('class'=>'btn-danger'));
		}
		return $this->redirect($this->referer());
	}


/**
 * undelete Custom List
 *
 * @param int $customList_id
 * @return redirect
 */

	public function undelete($customList_id){

    $this->LogColumnsCustomList->id = $customList_id;
		if (!$this->LogColumnsCustomList->exists()) {
			throw new NotFoundException(__('Invalid Custom List'));
		}
		$this->request->allowMethod('post', 'delete');
		if ($this->LogColumnsCustomList->undelete($customList_id)) {
      	$this->Session->setFlash(sprintf(__('The %s has been retrieved.'), $this->LogColumnsCustomList->alias), 'default');
		} else {
			$this->Session->setFlash(sprintf(__('The %s could not be retrieved. Please, try again.'), $this->LogColumnsCustomList->alias));
		}
		return $this->redirect($this->referer());
	}
	
/**
 * delete List Entry
 *
 * @param int $customList_id
 * @return redirect
 */


}