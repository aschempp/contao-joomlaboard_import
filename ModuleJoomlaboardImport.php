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
 * @copyright  LU-Hosting 2010, Andreas Schempp 2010
 * @author     Leo Unglaub <leo@leo-unglaub.net>
 * @author     Andreas Schempp <andreas@schempp.ch>
 * @license    http://opensource.org/licenses/lgpl-3.0.html
 * @version    $Id$
 */


class ModuleJoomlaboardImport extends BackendModule
{

	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (!$this->Database->tableExists('tl_helpdesk_tickets') || !$this->Database->tableExists('tl_helpdesk_messages'))
		{
			return '<p class="tl_gerror">' . $GLOBALS['TL_LANG']['joomlaboard_import']['errorHelpdesk'] . '</p>';
		}
		
		// Find available databases
		$arrDBs = array();
		$objDBs = $this->Database->query("SHOW DATABASES");
		
		while( $objDBs->next() )
		{
			$arrDBs[] = $objDBs->Database;
		}
		
		$objSource = new SelectMenu($this->prepareForWidget(array('label'=>&$GLOBALS['TL_LANG']['joomlaboard_import']['source'], 'options'=>$arrDBs, 'eval'=>array('mandatory'=>true, 'includeBlankOption'=>true)), 'source'));
		$objPrefix = new TextField($this->prepareForWidget(array('label'=>&$GLOBALS['TL_LANG']['joomlaboard_import']['prefix'], 'eval'=>array('mandatory'=>true)), 'prefix', 'jos_'));
		$objMGroup = new CheckBoxWizard($this->prepareForWidget(array('label'=>&$GLOBALS['TL_LANG']['joomlaboard_import']['mgroup'], 'foreignKey'=>'tl_member_group.name', 'eval'=>array('multiple'=>true)), 'mgroup'));
		$objMLookup = new RadioButton($this->prepareForWidget(array('label'=>&$GLOBALS['TL_LANG']['joomlaboard_import']['mlookup'], 'options'=>array('nolookup','email','username'), 'reference'=>&$GLOBALS['TL_LANG']['joomlaboard_import']), 'mlookup', 'nolookup'));
		
		if ($this->Input->post('FORM_SUBMIT') == 'tl_joomlaboard_import')
		{
			$objSource->validate();
			$objPrefix->validate();
			$objMGroup->validate();
			$objMLookup->validate();
			
			if (!$objSource->hasErrors() && !$objPrefix->hasErrors() && !$objMGroup->hasErrors() && !$objMLookup->hasErrors())
			{
				if (($strMessage = $this->importTables($objSource->value, $objPrefix->value, $objMGroup->value, $objMLookup->value)) === true)
				{
					$_SESSION['TL_CONFIRM'][] = $GLOBALS['TL_LANG']['joomlaboard_import']['success'];
				}
				else
				{
					$_SESSION['TL_ERROR'][] = $strMessage;
				}
				
				$this->reload();
			}
		}
		
		// Return form
		return '
<div id="tl_buttons">
&nbsp;
</div>

<h2 class="sub_headline">'.$GLOBALS['TL_LANG']['joomlaboard_import']['import'][1].'</h2>'.$this->getMessages().'

' . $this->getMessages() . '

<form action="'.ampersand($this->Environment->request, true).'" id="tl_joomlaboard_import" class="tl_form" method="post">
<div class="tl_formbody_edit">
<input type="hidden" name="FORM_SUBMIT" value="tl_joomlaboard_import" />

<div class="tl_tbox block">
  <div class="w50">
    '.$objSource->parse().((strlen($GLOBALS['TL_LANG']['joomlaboard_import']['source'][1] && !$objSource->hasErrors())) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['joomlaboard_import']['source'][1].'</p>' : '').'
  </div>
  <div class="w50">
    '.$objPrefix->parse().((strlen($GLOBALS['TL_LANG']['joomlaboard_import']['prefix'][1] && !$objPrefix->hasErrors())) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['joomlaboard_import']['prefix'][1].'</p>' : '').'
  </div>
</div>

<div class="tl_box block">
  <div class="clr">
    '.$objMLookup->parse().((strlen($GLOBALS['TL_LANG']['joomlaboard_import']['mlookup'][1] && !$objMLookup->hasErrors())) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['joomlaboard_import']['mlookup'][1].'</p>' : '').'
  </div>
  <div class="clr">
    '.$objMGroup->parse().((strlen($GLOBALS['TL_LANG']['joomlaboard_import']['mgroup'][1] && !$objMGroup->hasErrors())) ? '
    <p class="tl_help tl_tip">'.$GLOBALS['TL_LANG']['joomlaboard_import']['mgroup'][1].'</p>' : '').'
  </div>
</div>

</div>

<div class="tl_formbody_submit">

<div class="tl_submit_container">
  <input type="submit" name="save" id="save" class="tl_submit" accesskey="s" value="'.specialchars($GLOBALS['TL_LANG']['joomlaboard_import']['import'][0]).'" onclick="return confirm(\'' . specialchars($GLOBALS['TL_LANG']['joomlaboard_import']['confirm']) . '\')" />
</div>

</div>
</form>';
	}


	/**
	 * Run the import
	 */
	private function importTables($strSource, $strPrefix, $arrMGroup, $strMLookup)
	{
		//!Step 1: Validate tables
		if (!$this->Database->tableExists($strPrefix.'users', $strSource) || !$this->Database->tableExists($strPrefix.'sb_messages', $strSource) || !$this->Database->tableExists($strPrefix.'sb_messages_text', $strSource))
		{
			$this->Database->setDatabase($GLOBALS['TL_CONFIG']['dbDatabase']);
			return $GLOBALS['TL_LANG']['joomlaboard_import']['errorTables'];
		}
		
		
		//!Step 2: initialize system
		$time = time();
		include_once(TL_ROOT.'/system/modules/helpdesk/HelpdeskConstants.php');
		
		
		//!Step 3: import Joomla users
		$arrMemberLookup = array();
		$this->Database->setDatabase($strSource);
		$objJUsers = $this->Database->query("SELECT u.*, sbu.signature FROM ".$strPrefix."users u LEFT OUTER JOIN ".$strPrefix."sb_users sbu ON u.id=sbu.userid");
		$this->Database->setDatabase($GLOBALS['TL_CONFIG']['dbDatabase']);
		
		while ($objJUsers->next())
		{
			if ($strMLookup == 'email' || $strMLookup == 'username')
			{
				$objMember = $this->Database->prepare("SELECT id, groups FROM tl_member WHERE $strMLookup=?")->limit(1)->execute($objJUsers->$strMLookup);
				
				if ($objMember->numRows)
				{
					if (is_array($arrMGroups) && count($arrMGroups))
					{
						$arrGroups = array_merge(deserialize($objMember->groups, true), $arrMGroups);
						$this->Database->prepare("UPDATE tl_member SET groups=? WHERE id=?")->executeUncached(serialize($arrMGroups), $objMember->id);
					}
					
					$arrMemberLookup[$objJUsers->id] = $objMember->id;
					continue;
				}
			}
			
			$arrName = explode(' ', $objJUsers->username);
			$strLastname = array_pop($arrName);
			$strFirstname = implode(' ', $arrName);
			
			$arrSet = array
			(
				'tstamp'				=> $time,
				'firstname'				=> (string)$strFirstname,
				'lastname'				=> (string)$strLastname,
				'username'				=> $objJUsers->name,
				'email'					=> $objJUsers->email,
				'password'				=> $objJUsers->password,
				'groups'				=> serialize($arrMGroup),
				'helpdesk_signature'	=> $objJUsers->signature,
				'dateAdded'				=> strtotime($objJUsers->registerDate),
				'lastLogin'				=> strtotime($objJUsers->lastvisitDate),
				'login'					=> 1,
				'disable'				=> ($objJUsers->block ? '1' : ''),
			);
			
			$arrMemberLookup[$objJUsers->id] = $this->Database->prepare("INSERT INTO tl_member %s")->set($arrSet)->executeUncached()->insertId;
		}
		

		//!Step 4: import categories
		$arrCategoryLookup = array();
		$this->Database->setDatabase($strSource);
		$objCategories = $this->Database->query("SELECT *, (SELECT COUNT(*) FROM ".$strPrefix."sb_messages WHERE catid=c.id) AS tickets, (SELECT COUNT(*) FROM ".$strPrefix."sb_messages WHERE catid=c.id AND parent>0) AS replies, (SELECT MAX(id) FROM ".$strPrefix."sb_messages WHERE catid=c.id) AS latest FROM ".$strPrefix."sb_categories c ORDER BY ordering,parent");
		$this->Database->setDatabase($GLOBALS['TL_CONFIG']['dbDatabase']);
		$intSorting = (int)$this->Database->query("SELECT MAX(sorting) AS sorting FROM tl_helpdesk_categories")->sorting;
		
		$this->loadDataContainer('tl_helpdesk_categories');
		$arrDefault = array();
		foreach( $GLOBALS['TL_DCA']['tl_helpdesk_categories']['fields'] as $field => $arrData )
		{
			if (!is_null($arrData['default']))
			{
				$arrDefault[$field] = $arrData['default'];
			}
		}
		
		while( $objCategories->next() )
		{
			$intSorting += 128;
			
			$arrSet = array_merge($arrDefault, array
			(
				'sorting'		=> $intSorting,
				'tstamp'		=> $time,
				'header'		=> $objCategories->name,
				'title'			=> $objCategories->name,
				'access'		=> 4,
				'pub_tickets'	=> $objCategories->tickets,
				'pub_replies'	=> $objCategories->replies,
				'pub_latest'	=> (int)$objCategories->latest,
				'all_tickets'	=> $objCategories->tickets,
				'all_replies'	=> $objCategories->replies,
				'all_latest'	=> (int)$objCategories->latest,
			));
			
			$arrCategoryLookup[$objCategories->id] = $this->Database->prepare("INSERT INTO tl_helpdesk_categories %s")->set($arrSet)->executeUncached()->insertId;
		}


		//!Step 5: get all topics/threads and add them to the table tl_helpdesk_tickets
		$this->Database->setDatabase($strSource);
		$objTopics = $this->Database->prepare("SELECT m1.*, (SELECT COUNT(*) FROM ".$strPrefix."sb_messages m2 WHERE m2.thread=m1.id AND m2.id!=m1.id) AS replies, (SELECT MAX(id) FROM ".$strPrefix."sb_messages m3 WHERE m3.thread=m1.id) AS latest FROM ".$strPrefix."sb_messages m1 WHERE parent=0")->executeUncached();
		$this->Database->setDatabase($GLOBALS['TL_CONFIG']['dbDatabase']);

		while ($objTopics->next())
		{
			$arrSet = array
			(
				'pid'			=> (int)$arrCategoryLookup[(int)$objTopics->catid],
				'tstamp'		=> $objTopics->time,
				'published'		=> 1,
				'status'		=> 1,
				'subject'		=> $objTopics->subject,
				'client'		=> $objTopics->name,
				'views'			=> $objTopics->hits,
				'pub_replies'	=> $objTopics->replies,
				'pub_latest'	=> $objTopics->latest,
				'all_replies'	=> $objTopics->replies,
				'all_latest'	=> $objTopics->latest,
			);

			$arrTopicLookup[$objTopics->id] = $this->Database->prepare("INSERT INTO tl_helpdesk_tickets %s")->set($arrSet)->executeUncached()->insertId;
		}
		

		//!Step 6: import messages
		$this->Database->setDatabase($strSource);
		$objMessages = $this->Database->query("SELECT * FROM ".$strPrefix."sb_messages, ".$strPrefix."sb_messages_text WHERE ".$strPrefix."sb_messages.id=".$strPrefix."sb_messages_text.mesid");
		$this->Database->setDatabase($GLOBALS['TL_CONFIG']['dbDatabase']);

		while ($objMessages->next())
		{
			$arrSet = array
			(
				'pid'		=> $arrTopicLookup[$objMessages->thread],
				'tstamp'	=> $objMessages->time,
				'reply'		=> ($objMessages->parent > 0 ? 1 : 0),
				'by_email'	=> 0,
				'poster'	=> $objMessages->name,
				'poster_cd'	=> (int)$arrMemberLookup[(int)$objMessages->userid],
				'message'	=> $objMessages->message,
				'published'	=> 1,
			);
			
			$this->Database->prepare("INSERT INTO tl_helpdesk_messages %s")->set($arrSet)->executeUncached();
		}
		
		
		return true;
	}
	
	
	/**
	 * not in use but parent is abstract
	 */
	protected function compile() {}
}

