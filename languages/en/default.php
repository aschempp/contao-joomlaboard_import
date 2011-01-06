<?php if (!defined('TL_ROOT')) die('You can not access this file directly!');

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2010 Leo Feyer
 *
 * Formerly known as TYPOlight Open Source CMS.
 *
 * This program is free software: you can redistribute it and/or
 * modify it under the terms of the GNU Lesser General Public
 * License as published by the Free Software Foundation, either
 * version 3 of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * Lesser General Public License for more details.
 * 
 * You should have received a copy of the GNU Lesser General Public
 * License along with this program. If not, please visit the Free
 * Software Foundation website at <http://www.gnu.org/licenses/>.
 *
 * PHP version 5
 * @copyright  Andreas Schempp 2010
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


/**
 * Fields
 */
$GLOBALS['TL_LANG']['joomlaboard_import']['source']				= array('Source database', 'Select the database where Joomlaboard is installed.');
$GLOBALS['TL_LANG']['joomlaboard_import']['prefix']				= array('Database prefix', 'Please enter your Joomla database prefix. The default is jos_.');
$GLOBALS['TL_LANG']['joomlaboard_import']['mlookup']			= array('Member lookup', 'Select if existing members should be detected.');
$GLOBALS['TL_LANG']['joomlaboard_import']['mgroup']				= array('Member groups', 'Select the member groups to set for imported Joomla users.');


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['joomlaboard_import']['import']				= array('Import', 'Import Joomlaboard Forum');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['joomlaboard_import']['confirm']			= 'All data in your helpdesk will be lost! Continue?';
$GLOBALS['TL_LANG']['joomlaboard_import']['success']			= 'All data has been imported sucessfully!';
$GLOBALS['TL_LANG']['joomlaboard_import']['errorHelpdesk']		= 'You must install the Contao Helpdesk/Forum extension to use this tool.';
$GLOBALS['TL_LANG']['joomlaboard_import']['errorTables']		= 'The selected source database does not seem to contain a Joomlaboard installation!';
$GLOBALS['TL_LANG']['joomlaboard_import']['errorTruncate']		= 'Please truncate your helpdesk tables before importing.';
$GLOBALS['TL_LANG']['joomlaboard_import']['nolookup']			= 'Do not search members';
$GLOBALS['TL_LANG']['joomlaboard_import']['email']				= 'Search by email address';
$GLOBALS['TL_LANG']['joomlaboard_import']['username']			= 'Search by username';
