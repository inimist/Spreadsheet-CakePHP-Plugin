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
 * LogColumn Model
 *
 */
class LogColumn extends SpreadsheetAppModel {

  public $actsAs = array('SoftDelete');
/**
 * Display field
 *
 * @var string
 */
	public $displayField = 'column_name';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array(
		'column_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Column name must not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'column_type' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'Column type must not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	/**
	 * belongsTo associations
	 *
	 * @var array
	 */
		public $belongsTo = array(
			'Log' => array(
				'className' => 'Log',
				'foreignKey' => 'log_id',
				'recursive'=>-1,
				'counterCache' => true,
			)
		);


/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
    'LogEntry' => array(
			'className' => 'LogEntry',
			'foreignKey' => 'log_column_id',
			'dependent' => true,
			'conditions' => '',
			'fields' => '',
			'order' => '',
			'limit' => '',
			'offset' => '',
			'exclusive' => '',
			'finderQuery' => '',
			'counterQuery' => ''
		)
	);

  public $_defaultColumnType = 'longtext';

  function afterDelete() {
		//return $this->redirect('spreadsheet/columns');
    //debug($this->request);
		//exit;
  }
}
