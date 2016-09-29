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
class LogColumnsCustomListEntry extends SpreadsheetAppModel {

//public $displayField = 'custom_list_entry';
	public $useTable = 'log_columns_custom_list_entries';
}