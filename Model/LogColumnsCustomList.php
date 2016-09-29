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
class LogColumnsCustomList extends SpreadsheetAppModel {

public $displayField = 'name';
/**
 * Validation 
 *
 * @var array
 */
/*
	public $validate = array(
		'column_name' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'List name must not be empty',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
		'column_type' => array(
			'notEmpty' => array(
				'rule' => array('notEmpty'),
				'message' => 'List must contain atleast one entry',
				//'allowEmpty' => false,
				//'required' => false,
				//'last' => false, // Stop validation after this rule
				//'on' => 'create', // Limit validation to 'create' or 'update' operations
			),
		),
	);

	*/
	
	/**
 * hasMany associations
 *
 * @var array
 */
	public $hasMany = array(
		'LogColumnsCustomListEntry' => array(
			'className' => 'Spreadsheet.LogColumnsCustomListEntry',
			'foreignKey' => 'log_columns_custom_list_id',
			'dependent' => true
		)
	);


}