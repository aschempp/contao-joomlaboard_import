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
 * @copyright  LU-Hosting 2010
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @package    random_module
 * @license    LGPL
 * @filesource
 */


/**
 * Class random_module
 *
 * @copyright  LU-Hosting 2010
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @package    random_module
 */
class joomla_forum_importer extends Module
{
	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_joomla_forum_importer';

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### joomla_forum_importer ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->import('Database');
		$strGroupName = 'forum-name';
		
		// generate a default group for all forum member
		$arrGroupExists = $this->Database->prepare('SELECT id from tl_member_group WHERE name=?')->execute($strGroupName);
		$intGroupId = $arrGroupExists->id;
		if ($arrGroupExists->numRows == '0')
		{
			$arrJGroups = $this->Database->prepare('INSERT INTO tl_member_group SET name=?,tstamp=?')->execute($strGroupName, time());
			$intGroupId = $arrJGroups->insertId;
		}
		
		// import the joomla users
		$this->Database->setDatabase('altes_forum');
		$arrJUser = $this->Database->prepare('SELECT id,name,username,email,password FROM jos_users')->executeUncached();
		$this->Database->setDatabase('contao-dev');
		
		while ($arrJUser->next())
		{
			$arrSet = array
			(
				'lastname' => $arrJUser->name,
				'username' => $arrJUser->username,
				'email' => $arrJUser->email,
				'password' => $arrJUser->password,
				'groups' => serialize(array($intGroupId))
			);
			
			$arrMemberLookup[$arrJUser->id] = $this->Database->prepare('INSERT INTO tl_member %s')->set($arrSet)->executeUncached()->insertId;
		}


		// import the toppics
		$intPidHelpdesk = 1;
		$this->Database->setDatabase('altes_forum');

		$arrAllData = $this->Database->prepare('select id,userid,thread,name as poster,time, jos_sb_messages_text.message from jos_sb_messages, jos_sb_messages_text WHERE jos_sb_messages.id = jos_sb_messages_text.mesid')
						->executeUncached();

		$this->Database->setDatabase('contao-dev');

		// insert the new conversation into the right table
		while ($arrAllData->next())
		{
			$arrSet = array
			(
				'id' => $arrAllData->id,
				'pid' => $arrAllData->thread,
				'poster' => $arrAllData->poster,
				'tstamp' => $arrAllData->time,
				'message' => $arrAllData->message,
				'published' => 1,
				'poster_cd' => ($arrMemberLookup[(int)$arrAllData->userid] != null) ? $arrMemberLookup[(int)$arrAllData->userid] : 0
			);
			$this->Database->prepare('INSERT INTO tl_helpdesk_messages %s')->set($arrSet)->execute();
		}

		// get all topics and add them to the table tl_helpdesk_tickets
		$this->Database->setDatabase('altes_forum');
		$arrTopics = $this->Database->prepare('select * from `jos_sb_messages` where parent=\'0\'')->executeUncached();
		$this->Database->setDatabase('contao-dev');

		while ($arrTopics->next())
		{
			$arrSet = array
			(
				'id' => $arrTopics->id,
				'pid' => '1', // hardcoded, maybe there is a better solution
				'tstamp' => $arrTopics->time,
				'published' => 1,
				'subject' => $arrTopics->subject
			);

			$this->Database->prepare('INSERT INTO tl_helpdesk_tickets %s')->set($arrSet)->execute();
		}

		
	}
}
?>