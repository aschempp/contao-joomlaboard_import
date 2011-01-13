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
$GLOBALS['TL_LANG']['joomlaboard_import']['source']				= array('Quell Datenbank', 'Bitte geben Sie die Quell Datenbank an welche die Joomla-Installation beinhaltet. Beachten Sie bitte das der Contao-Mysql-Account Zugriffsrechte auf diese Datenbank besitzen muss.');
$GLOBALS['TL_LANG']['joomlaboard_import']['prefix']				= array('Quell Datenbank Prefix', 'Bitte geben Sie den Tabellenprefix der Tabellen in der Quell Datenbank ein. Der Standardwert ist jos_.');
$GLOBALS['TL_LANG']['joomlaboard_import']['mlookup']			= array('Mitglieder abfragen', 'Wählen Sie ob bestehende Mitglieder automatisch erkannt werden sollen.');
$GLOBALS['TL_LANG']['joomlaboard_import']['mgroup']				= array('Mitgliedergruppen', 'Wählen Sie die Mitgliedergruppe aus in welche alle Joomla User importiert werden sollen.');


/**
 * Buttons
 */
$GLOBALS['TL_LANG']['joomlaboard_import']['import']				= array('Import', 'Import Joomlaboard Forum');


/**
 * Reference
 */
$GLOBALS['TL_LANG']['joomlaboard_import']['confirm']			= 'Alle bestehenden Daten Ihrer Helpdesk-Installation werden überschreiben. Wollen Sie fortfahren?';
$GLOBALS['TL_LANG']['joomlaboard_import']['success']			= 'Alle Daten wurden erfolgreich importiert.';
$GLOBALS['TL_LANG']['joomlaboard_import']['errorHelpdesk']		= 'Sie müssen die Contao Extension "Helpdesk" installieren bevor Sie den Importer verwenden können.';
$GLOBALS['TL_LANG']['joomlaboard_import']['errorTables']		= 'Die angegebene Quell-Datenbank scheint kein passendes Joomlaboard zu enthalten.';
$GLOBALS['TL_LANG']['joomlaboard_import']['errorTruncate']		= 'Bitte leeren (truncate) Sie ihre Helpdesk-Tabellen bevor Sie den import starten.';
$GLOBALS['TL_LANG']['joomlaboard_import']['nolookup']			= 'Keine mitglieder suchen.';
$GLOBALS['TL_LANG']['joomlaboard_import']['email']				= 'Suche anhand der E-Mail Adresse';
$GLOBALS['TL_LANG']['joomlaboard_import']['username']			= 'Suche anhand des Benutzernamens';
