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
App::uses('AppController', 'Controller');

/**
 * Spreadsheet AppController
 *
 * @package Spreadsheet
 */
class SpreadsheetAppController extends AppController {

	function beforeFilter() {

		if(!isset($this->Log))	{
			$this->loadModel('Spreadsheet.Log');
		}

		if($this->name != 'Logs')	{ //just dont want it to run on it's own controller
			//$this->Auth->allow(array('*'));
			if(isset($this->request->query['log_id']) && (int)$this->request->query['log_id'])	{
				$this->Log->id = $this->request->query['log_id'];
				$this->Session->write('Spreadsheet.log_id', $this->request->query['log_id']);
			}	else	{
				if($this->Session->check('Spreadsheet.log_id'))	{
					$this->Log->id = $this->Session->read('Spreadsheet.log_id');
				}
			}
			if((int)$this->Log->id)	{
				$this->Log->recursive=-1;
				$log = $this->Log->read();
				//debug($log);
				$this->set('log', $log);
				$this->set('log_id', $this->log_id);
			}
		}
		parent::beforeFilter();
	}
}
