<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2009 Kurt Gusbeth <info@myquizandpoll.de>
*  All rights reserved
*
*  This script is part of the TYPO3 project. The TYPO3 project is
*  free software; you can redistribute it and/or modify
*  it under the terms of the GNU General Public License as published by
*  the Free Software Foundation; either version 2 of the License, or
*  (at your option) any later version.
*
*  The GNU General Public License can be found at
*  http://www.gnu.org/copyleft/gpl.html.
*
*  This script is distributed in the hope that it will be useful,
*  but WITHOUT ANY WARRANTY; without even the implied warranty of
*  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
*  GNU General Public License for more details.
*
*  This copyright notice MUST APPEAR in all copies of the script!
***************************************************************/

//$LANG->includeLLFile('EXT:myquizpoll/mod1/locallang.xml');
// require_once(PATH_t3lib . 'class.t3lib_scbase.php');
//$BE_USER->modAccess($MCONF,1);
$GLOBALS['LANG']->includeLLFile('EXT:myquizpoll/mod1/locallang.xml');
$GLOBALS['BE_USER']->modAccess($GLOBALS['MCONF'],1);	// This checks permissions and exits if the users has no permission for entry.

/**
 * Module 'My quiz and poll' for the 'myquizpoll' extension.
 *
 * @author	Kurt Gusbeth <info@myquizandpoll.de>
 * @package	TYPO3
 * @subpackage	tx_myquizpoll
 */
class  tx_myquizpoll_module1 extends \TYPO3\CMS\Backend\Module\BaseScriptClass {
	var $pageinfo;
	var $path;

	/**
	 * Initializes the Module
	 * @return	void
	 */
	function init()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		parent::init();

		/*
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('clear_all_cache'))	{
			$this->include_once[] = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('core') . 'Classes/DataHandling/DataHandler.php';
		}
		*/
		$this->path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath("myquizpoll").'mod1/';
	}

	/**
	 * Adds items to the ->MOD_MENU array. Used for the function menu selector.
	 *
	 * @return	void
	 */
	function menuConfig()	{
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('user_id')) {
			$fArray = Array();
//				'1' => $LANG->getLL('function1a'),
//				'2' => $LANG->getLL('function2a')
//			);
		} else {
			$fArray = Array (
				'1' => $GLOBALS['LANG']->getLL('function1'),
				'2' => $GLOBALS['LANG']->getLL('function2'),
				'3' => $GLOBALS['LANG']->getLL('function3'),
				'4' => $GLOBALS['LANG']->getLL('function4'),
//				'5' => $GLOBALS['LANG']->getLL('function5'),
				'7' => $GLOBALS['LANG']->getLL('function7'),
				'8' => $GLOBALS['LANG']->getLL('function8'),
				'9' => $GLOBALS['LANG']->getLL('function9')
			);
		}
		$this->MOD_MENU = Array (
			'function' => $fArray 
		);
		parent::menuConfig();
	}
	
	/**
	 * Inserts a line to the database
	 *
	 * @param	string	$names: fields
	 * @param	string	$values: default field-values
	 * @param	string	$Felder: field-values
	 * @return	string	inserted values
	 */
	function insertLine($names, $values, $Felder) {
		$result = '';
		$convert = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('convert'));
		$show_it = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('show_it'));
		for ($i=0; $i<count($names); $i++) {
			$feld = $names[$i];
			if ($feld=='correct') {	// Ausnahmefall
				$feld = 'correct'.$Felder[$i];
				$values[$feld] = 1;
			} else {
				if ($convert)
					$values[$feld] = iconv('iso-8859-1','utf-8',$Felder[$i]);
				else
					$values[$feld] = $Felder[$i];
			}
			if ($show_it)
				$result .= $feld .' => "'.$values[$feld].'", ';
		}
		if ($show_it) {
			$feld='sorting';
			$result .= $feld .' => "'.$values[$feld].'", ';
			$feld='pid';
			$result .= $feld .' => "'.$values[$feld].'", ';
			$feld='sys_language_uid';
			$result .= $feld .' => "'.$values[$feld].'", ';
			$feld='cruser_id';
			$result .= $feld .' => "'.$values[$feld].'", ';
		}
		if ($show_it<2) {
			$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_myquizpoll_question',$values);
			$insert_id = $GLOBALS['TYPO3_DB']->sql_insert_id();
			if ($insert_id && $show_it) $result .= 'uid => '.$insert_id;
			else if ($insert_id) $result=1;
			else $result=0;
		}
		return $result;
	}
	
	/**
	 * Replace textftrenner and feldtrenner
	 * @param	string	$feldtrenner
	 * @param	string	$texttrenner
	 * @param	string	$text
	 *
	 * @return	string	replaced string
	 */
	function myReplace($feldtrenner, $texttrenner, $text) {
		if ($texttrenner)
			$out = str_replace ($texttrenner, '-', $text);
		else
			$out = str_replace ($feldtrenner, '_', $text);
		return $out;
	}

	/**
	 * Main function of the module. Write the content to $this->content
	 * If you chose "web" as main module, you will need to consider the $this->id parameter which will contain the uid-number of the page clicked in the page tree
	 *
	 * @return	[type]		...
	 */
	function main()	{
		global $BE_USER,$LANG,$BACK_PATH,$TCA_DESCR,$TCA,$CLIENT,$TYPO3_CONF_VARS;

		// Access check!
		// The page will show only if there is a valid page and if this page may be viewed by the user
		$this->pageinfo = \TYPO3\CMS\Backend\Utility\BackendUtility::readPageAccess($this->id,$this->perms_clause);
		$access = is_array($this->pageinfo) ? 1 : 0;
		
		if (($this->id && $access) || ($BE_USER->user['admin'] && !$this->id))	{

				// Draw the header.
			//$link = htmlspecialchars(\TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript());
			$link = $_SERVER['PHP_SELF'].'?'.htmlspecialchars($_SERVER['QUERY_STRING']);
			// nö: $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('mediumDoc');
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\DocumentTemplate');
			$this->doc->backPath = $BACK_PATH;
			$this->doc->form='<form action="'.$link.'" method="post" enctype="multipart/form-data" name="myquiz">';
//			$this->doc->form.='<input type="hidden" name="M" value="tools_txextensionlistM1" />'."\n";
//			$this->doc->form.='<input type="hidden" name="id" value="'.\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id').'" />'."\n";
			$this->doc->form.='<input type="hidden" name="delall" value="0" />'."\n";
			
			// Styles
			//$this->doc->styleSheetFile2=$GLOBALS["temp_modPath"].'style.css' ODER "../".substr(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($this->extName),strlen(PATH_site))."mod1/style.css";
			if (file_exists(\TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/uploads/tx_myquizpoll/style.css'))
				$filename = '../uploads/tx_myquizpoll/style.css';
			else
				$filename = $this->path.'style.css';
			$this->doc->styleSheetFile2 = $filename;
			
				// JavaScript
			$this->doc->JScode = '
				<script language="javascript" type="text/javascript">
					script_ended = 0;
					function jumpToUrl(URL)	{
						document.location = URL;
					}
				</script>
			';
			$this->doc->postCode='
				<script language="javascript" type="text/javascript">
					script_ended = 1;
					if (top.fsMod) top.fsMod.recentIds["web"] = 0;
				</script>
			';

			$headerSection = $this->doc->getHeader('pages',$this->pageinfo,$this->pageinfo['_thePath']).'<br />'.$LANG->sL('LLL:EXT:lang/locallang_core.xml:labels.path').': '.\TYPO3\CMS\Core\Utility\GeneralUtility::fixed_lgd_cs($this->pageinfo['_thePath'],55);

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->section('',$this->doc->funcMenu($headerSection,\TYPO3\CMS\Backend\Utility\BackendUtility::getFuncMenu($this->id,'SET[function]',$this->MOD_SETTINGS['function'],$this->MOD_MENU['function'])));
			$this->content.=$this->doc->divider(5);


			// Render content:
			$this->moduleContent();


			// ShortCut
			if ($BE_USER->mayMakeShortcut())	{
				$this->content.=$this->doc->spacer(20).$this->doc->section('',$this->doc->makeShortcutIcon('id',implode(',',array_keys($this->MOD_MENU)),$this->MCONF['name']));
			}

			$this->content.=$this->doc->spacer(10);
		} else {
				// If no access or if ID == zero

			// $this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('mediumDoc');
			$this->doc = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\\CMS\\Backend\\Template\\DocumentTemplate');
			$this->doc->backPath = $BACK_PATH;

			$this->content.=$this->doc->startPage($LANG->getLL('title'));
			$this->content.=$this->doc->header($LANG->getLL('title'));
			$this->content.=$this->doc->spacer(5);
			$this->content.=$this->doc->spacer(10);
		}
	
	}

	/**
	 * Prints out the module HTML
	 *
	 * @return	void
	 */
	function printContent()	{
		$this->content.=$this->doc->endPage();
		echo $this->content;
	}

	/**
	 * Generates the module content
	 *
	 * @return	void
	 */
	function moduleContent()	{
		global $LANG,$BE_USER;
		$id = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('id'));
		$lid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('lid'));
		$ep = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('ep'));
		if (!$ep) $ep=10;
		$pointer = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pointer'));
		$path = \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath("myquizpoll").'mod1/';
		$path_abs = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/'.\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath("myquizpoll").'mod1/';
		$filename_new = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT').'/uploads/tx_myquizpoll/style.css';	// statt $_SERVER["DOCUMENT_ROOT"]
		$filename_old = $path_abs.'style.css';
		$head = '';
		$content = '';
		$questions='';
		$mode = $this->MOD_SETTINGS['function'];
		$columns = array();
		$columns['tx_myquizpoll_result'] = array();
		$columns['tx_myquizpoll_result']['qno'] = 'qids';
		$columns['tx_myquizpoll_result']['ano'] = 'p_or_a';
		$columns['tx_myquizpoll_result']['fno'] = '';
		$columns['tx_myquizpoll_voting'] = array();
		$columns['tx_myquizpoll_voting']['qno'] = 'question_id';
		$columns['tx_myquizpoll_voting']['ano'] = 'answer_no';
		$columns['tx_myquizpoll_voting']['fno'] = ',foreign_val';
		$table = (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('table')=='tx_myquizpoll_voting') ? 'tx_myquizpoll_voting' : 'tx_myquizpoll_result';
				
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delall')==1) {	// delete all rows
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('1',
				$table,
				'PID='.$id.' AND sys_language_uid='.$lid);
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			if ($table=='tx_myquizpoll_result')
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_myquizpoll_relation', "PID=$id AND sys_language_uid=$lid");
			$GLOBALS['TYPO3_DB']->exec_DELETEquery($table, "PID=$id AND sys_language_uid=$lid");
			$content .= '<p><strong>'.$LANG->getLL('action').": $rows ".$LANG->getLL('deleted').'</strong></p><br /><br />';		
		} else if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('del_some') && is_array(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit')) && (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit') === array_filter(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit'),'is_numeric'))) {	// deleting rows... 
			$delArray = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit');
			$delString = implode(",", $delArray);
			if ($table=='tx_myquizpoll_result')
				$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_myquizpoll_relation', "PID=$id AND user_id IN ($delString)");
			$GLOBALS['TYPO3_DB']->exec_DELETEquery($table, "PID=$id AND uid IN ($delString)");
			$content .= '<p><strong>'.$LANG->getLL('action').': '.count($delArray).' '.$LANG->getLL('deleted').'</strong></p><br /><br />';
		} else if ((\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('hide_some') || \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('release_some')) && is_array(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit')) && (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit') === array_filter(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit'),'is_numeric'))) {	// hiding/releasing rows... 
			$eArray = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('delit');
			$eString = implode(",", $eArray);
			$hide = (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('hide_some')) ? 1 : 0;
			$uArray = array('hidden' => $hide);
			if ($table=='tx_myquizpoll_result')
				$GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_myquizpoll_relation', "PID=$id AND user_id IN ($eString)", $uArray);
			$GLOBALS['TYPO3_DB']->exec_UPDATEquery($table, "PID=$id AND uid IN ($eString)", $uArray);
			$content .= '<p><strong>'.$LANG->getLL('action').': '.count($eArray).' ';
			$content .= (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('hide_some')) ? $LANG->getLL('hidden') : $LANG->getLL('released');
			$content .= '</strong></p><br /><br />';
		} else if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('user_id')) {
			$uid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('user_id'));
			$detail = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('detail'));
			$userHash = array();
			$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
				'tx_myquizpoll_relation',
				'user_id='.$uid.' AND pid='.$id.' AND sys_language_uid='.$lid);
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
			if ($rows>0) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
					$qno = $row['question_id'];
					if (!is_array($userHash[$qno])) $userHash[$qno] = array();
					for ($i=1; $i<=12; $i++) {
						$userHash[$qno]['c'.$i] = $row['checked'.$i];
					}
					$userHash[$qno]['textinput'] = $row['textinput'];
					$userHash[$qno]['points'] = $row['points'];
					$questions.=','.intval($qno);
				}
				$questions=substr($questions,1);
				
				// Get all questions and answers from the database
				$questionsArray = array();
				$questionNumber = 0;
				$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
						'tx_myquizpoll_question',
						'uid IN ('.$questions.')',	//pid='.$id.' AND sys_language_uid='.$lid,
						'',
						'sorting',
						'');
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
				if ($rows>0) {
					$this->content.='<div class="quizpoll_stat_total">';
					$lastQuestion=0;
					if (!$detail) {
						$this->content.='<div class="quizpoll_stat2_question_h">'.$LANG->getLL('question').'</div>';
						$this->content.='<div class="quizpoll_stat2_answer_h">'.$LANG->getLL('answer').'</div>';
						$this->content.='<div class="quizpoll_stat2_correct_h">'.$LANG->getLL('correct_answer').'</div>';
					}
					
					while($rowA = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
						$questionUID = $rowA['uid'];
						$questionNumber++;					
						if (!is_array($userHash[$questionUID]))	// eigentlich Kaese, da nur beantw. Fragen geholt werden!
							continue;				// vorher nicht angezeigte Fragen auch jetzt nicht anzeigen!
						
						$this->content.='<div class="quizpoll_stat_around">';
						if ($detail) {
							$this->content.=$questionNumber.'.) '.$rowA['title'].': '.$userHash[$questionUID]['points'].' '.$LANG->getLL('points').'<br />';
							$this->content.='<div class="quizpoll_stat_question">'.$rowA['name'].'</div>';
							for( $answerNumber=1; $answerNumber <= 12; $answerNumber++ ) {
								if ($userHash[$questionUID]['c'.$answerNumber]) {	// was a answer checked by the user?									
									if ($rowA['qtype']!=3 && $rowA['qtype']!=5) {
										$this->content.='<div class="quizpoll_stat_answer">'.$rowA['answer'.$answerNumber]."</div>\n";
									} else {							
										$this->content.='<div class="quizpoll_stat_answer">'.str_replace('\r\n','<br />',$userHash[$questionUID]['textinput'])."</div>\n";
									}
								}
								if ($rowA['qtype']==5) break;
							}
						} else {
							for( $answerNumber=1; $answerNumber <= 12; $answerNumber++ ) {								
								if ($userHash[$questionUID]['c'.$answerNumber]) {
									if ($lastQuestion!=$questionNumber && $lastQuestion>0)
										$this->content.='<hr style="clear:both;width:415px;" />'."\n";
									$this->content.='<div class="quizpoll_stat2_question">'.$questionNumber.'</div>';
									$this->content.='<div class="quizpoll_stat2_answer">'.$answerNumber.'</div>';
									$this->content.='<div class="quizpoll_stat2_correct">';
									if (($rowA['qtype']!=3 || ($answerNumber==1 && $rowA['correct1'])) && $rowA['qtype']!=5) {
										if ($rowA['correct'.$answerNumber]) {											
											$this->content.='<span class="quizpoll_yes">'.$LANG->getLL('yes').'</span>';
										} else {
											$this->content.='<span class="quizpoll_no">'.$LANG->getLL('no').'</span>';
										}
									} else {							
										$this->content.=$userHash[$questionUID]['textinput'];
									}
									$this->content.='</div>';
									$lastQuestion=$questionNumber;
								}
								if ($rowA['qtype']==5) break;
							}
						}
						$this->content.="</div>\n";
					}
					$this->content.="</div>\n";
				}				
			} else {
				$this->content.='<div align="center"><strong>0 '.$LANG->getLL('quiz_results')."</strong></div><br />\n";
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res5);
			return;
		} else if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('mystyles')) {
			$datei = fopen($filename_new,"w+");
			fputs($datei, \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('mystyles'));
			fclose ($datei);
		} else if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('daten_id') && $_FILES['uploadfile'] && trim($_FILES['uploadfile']['name'])) {
			$daten_id = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('daten_id'));
			$lang_id = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('lang_id'));
			$show_it = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('show_it'));
			$feldtrenner = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('feldtrenner');
			$texttrenner = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('texttrenner');
			//$content .= '<p><strong>'.$LANG->getLL('action').': importiere...</strong></p><br /><br />';
			//$file = $GLOBALS["HTTP_POST_FILES"]["uploadfile"];
			//$file = $_FILES["uploadfile"];
			$filename = $_FILES["uploadfile"]["tmp_name"];
			if (file_exists($filename)) {
			//	$filename =  $GLOBALS["HTTP_SERVER_VARS"]["DOCUMENT_ROOT"]."/uploads/tx_myquizpoll/".$file["name"];
				$target_path = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_DOCUMENT_ROOT') .'/uploads/tx_myquizpoll/import.csv';	//. basename( $file['name']);
				if(!move_uploaded_file($filename, $target_path)){
					$content.='<div><strong>Error:</strong> '."Upload verreckt! $filename -&gt; $target_path</div><br />";
				}
				$lines = file($target_path);
				if ( count($lines) > 1 ) {
					$nr=0;
					$sorting=0;
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('max(sorting)',
						'tx_myquizpoll_question',
						'PID='.$daten_id.' AND sys_language_uid='.$lang_id,
						'',
						'',
						'');
					$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
					if ($rows>0) {							// DB entries found?
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$sorting = intval($row['max(sorting)']);
						}
					}
					$fields_values = array();
					$fields_values['pid'] = $daten_id;
					$fields_values['sys_language_uid'] = $lang_id;
					$fields_values['tstamp'] = time();
					$fields_values['crdate'] = time();
					$fields_values['cruser_id'] = $BE_USER->user["uid"];
					$handle = fopen ($target_path, "r");              // Datei zum Lesen oeffnen
					if ($texttrenner) {
						while ( ($Felder = fgetcsv ($handle, 1000, $feldtrenner, $texttrenner)) !== FALSE ) {
							$nr++;
							if ($nr==1) {
								$fields_names = $Felder;
							} else {
								$sorting++;
								$fields_values['sorting'] = $sorting;
								$ergebnis=$this->insertLine($fields_names, $fields_values, $Felder);
								if ($ergebnis) $output.=$ergebnis."<br />\n"; else $nr--;
							}
						}
					} else {
						while ( ($Felder = fgetcsv ($handle, 1000, $feldtrenner)) !== FALSE ) {
							$nr++;
							if ($nr==1) {
								$fields_names = $Felder;
							} else {
								$sorting++;
								$fields_values['sorting'] = $sorting;
								$ergebnis=$this->insertLine($fields_names, $fields_values, $Felder);
								if ($ergebnis) $output.=$ergebnis."<br />\n"; else $nr--;
							}
						}
					}
					fclose ($handle);
					unlink($target_path);
					if ($show_it)
						$content .= '<div>'.$output."</div><br />\n";
					if ($show_it<2)
						$content .= '<div><strong>'.$LANG->getLL('action').'</strong>: '.($nr-1).' '.$LANG->getLL('imp_imported')."</div><br />\n";
				} else {
					$content .= '<div><strong>Error:</strong> no lines found!</div><br />';
				}
			}
		}
		
		
		
		// begin output
		$link = htmlspecialchars(\TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript()); // . "?M=tools_txextensionlistM1&id=$id";
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('sortBy')) {
			$sortBy = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('sortBy');
			if (!(($sortBy=='crdate DESC') || ($sortBy=='crdate ASC') || ($sortBy=='name DESC') || ($sortBy=='name ASC') || ($sortBy=='p_or_a DESC') || ($sortBy=='p_or_a ASC')))
				$sortBy = 'crdate DESC';
		} else {
			$sortBy = 'crdate DESC';
		}
		if ($pointer>0) {
			$limitTo = ($pointer*$ep).','.$ep;
		} else {
			$limitTo = $ep;
		}
		$table = 'tx_myquizpoll_result';
		if ($GLOBALS['BE_USER']->user['admin']) {	// nur Admins haben Zugriff auf Fremd-Tabellen
			$foreign_table = addslashes(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('foreign_table'));
			$foreign_title = addslashes(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('foreign_title'));
		} else {
			$foreign_table = '';
			$foreign_title = '';
		}
		$foreign_vals = '';
		$foreign_array = array();
		$category = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('category'));
		$selectOnly = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('selectonly');
		$categories = array();
		
		if ($mode<3 || $mode==4 || $mode==7) {
			// voting- oder result-Tabelle?
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(1)',
								'tx_myquizpoll_result',
								'PID='.$id.' AND sys_language_uid='.$lid);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
			$rowsTotal=$row['count(1)'];
			$GLOBALS['TYPO3_DB']->sql_free_result($res);
			if (!$rowsTotal && $mode >= 2) {
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(1)',
									'tx_myquizpoll_voting',
									'PID='.$id.' AND sys_language_uid='.$lid);
				$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
				$rowsTotal=$row['count(1)'];
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				if ($rowsTotal) {
					$table = 'tx_myquizpoll_voting';
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT foreign_val',
						'tx_myquizpoll_voting',
						'PID='.$id.' AND sys_language_uid='.$lid);
					$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
					//echo $rows.' verschiedene foreign values gefunden...';
					if ($rows<2)
						$columns['tx_myquizpoll_voting']['fno'] = '';  // foreign value ist dann uninteressant
					else if ($foreign_table && $foreign_title) {
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							if ($row['foreign_val']) {
								if ($foreign_vals) $foreign_vals.=',';
								$foreign_vals .= $row['foreign_val'];
							}
						}
					}
					$GLOBALS['TYPO3_DB']->sql_free_result($res);
					if ($foreign_vals) {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, '.$foreign_title,
							$foreign_table,
							'sys_language_uid='.$lid);
						if ($GLOBALS['TYPO3_DB']->sql_num_rows($res)>0) {
							while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
								$foreign_array[$row['uid']] = $row[$foreign_title];
							}
						}
						$GLOBALS['TYPO3_DB']->sql_free_result($res);
					}
				}
			}
		} else if ($mode==3) {
			//read categories from db
			$resCategory = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,name',
				'tx_myquizpoll_category',
				'PID='.$id);
			if ($GLOBALS['TYPO3_DB']->sql_num_rows($resCategory) > 0) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($resCategory)){
					$categories[$row['uid']] = $row['name'];
				}
			}
		}
		
		switch($mode)	{
			case 1:			
				$head=$LANG->getLL('function1');
				$catArray = array();
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,name',
								'tx_myquizpoll_category',
								'1=1');	//'PID='.$id
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$uid = $row['uid'];
						$catArray[$uid] = $row['name'];
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				
				$userAray = array();
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT user_id',
					'tx_myquizpoll_relation',
					'PID='.$id.' AND sys_language_uid='.$lid);
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {							// DB entries found?
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$user_id = $row['user_id'];
						$userAray[$user_id] = 1;
					}
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
				
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
								'tx_myquizpoll_result',
								'PID='.$id.' AND sys_language_uid='.$lid,
								'',
								$sortBy,
								$limitTo);
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {							// DB entries found?
					$nr = 0;
					$content .= '<div align="center"><strong>'.$rowsTotal.' '.$LANG->getLL('quiz_results').":</strong></div><br />\n";
					$content .= '<table style="border:1px #333 solid;"><tr style="background-color:#fff;"><th>'.$LANG->getLL('number').'</th><th>-</th><th><a href="';
//					$sorting = ($sortBy == 'sys_language_uid DESC') ? 'sys_language_uid ASC' : 'sys_language_uid DESC';
//					$content .= $link.'&sortBy='.urlencode($sorting).'">'.$LANG->getLL('lang').'</a></th><th><a href="';
					$sorting = ($sortBy == 'crdate DESC') ? 'crdate ASC' : 'crdate DESC';
					$content .= $link.'&sortBy='.urlencode($sorting).'">'.$LANG->getLL('date').'</a></th><th><a href="';
					$sorting = ($sortBy == 'name ASC') ? 'name DESC' : 'name ASC';
					$content .= $link.'&sortBy='.urlencode($sorting).'">'.$LANG->getLL('user_data').'</a></th><th><a href="';
					$sorting = ($sortBy == 'p_or_a ASC') ? 'p_or_a DESC' : 'p_or_a ASC';
					$content .= $link.'&sortBy='.urlencode($sorting).'">'.$LANG->getLL('points_percent').'</a></th><th>'.$LANG->getLL('opoints_opercent').'</th>';
					$content .= '<th>'.$LANG->getLL('answ_skipped').'</th><th>'.$LANG->getLL('answ_corr_false').'</th>';
					$content .= '<th>'.$LANG->getLL('last_next_cat').'</th></tr>'."\n";
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$nr++;
						$uid = $row['uid'];
						$cat1 = $row['lastcat'];
						$cat2 = $row['nextcat'];
						if ($nr%2 == 0)
							$style='even';
						else
							$style='odd';
						$content .= '<tr class="quizpoll_'.$style.'"><td align="center">'.$nr.':<br /><br />';
						$content .= '<input type="checkbox" name="delit[]" value="'.$uid.'" /></td>';
						$content .= '<td><a href="alt_doc.php?returnUrl=db_list.php%3Fid%3D43%'.$id.'table%3D&edit[tx_myquizpoll_result]['.$uid.']=edit"><img src="'.$path.'icon-edit.jpg" alt="'.$LANG->getLL('function0a').'" title="'.$LANG->getLL('function0a').'" /></a> ';
						if ($userAray[$uid]) {
							$content .= '<a href="javascript:nix();" onclick="popup(\''.$link.'&amp;user_id='.$uid.'&amp;lid='.$lid.'\');"><img src="'.$path.'icon-show1.jpg" alt="'.$LANG->getLL('function1a').'" title="'.$LANG->getLL('function1a').'" /></a> ';
							$content .= '<a href="javascript:nix();" onclick="popup(\''.$link.'&amp;user_id='.$uid.'&amp;lid='.$lid.'&amp;detail=1\');"><img src="'.$path.'icon-show2.jpg" alt="'.$LANG->getLL('function2a').'" title="'.$LANG->getLL('function2a').'" /></a> ';
						}
						$content .= '</td><td';
						if ($row['hidden']) $content .= ' class="quizpoll_hidden"';
						$content .= '>'.date("d.m.Y G:i:s", $row['firsttime']).' - '.date("G:i:s", $row['lasttime']).'</td><td>'.$row['name'];
						if ($row['fe_uid']) $content .= ' (fe_user&nbsp;'.$row['fe_uid'].')<br />';
						$content .= (($row['email']) ? '<br /><span style="font-size:85%;">'.$row['email'].'</span>' : '');
						$content .= (($row['homepage']) ? '<br /><span style="font-size:85%;">'.$row['homepage'].'</span>' : '').'</td>';
						$content .= '<td align="center">'.$row['p_or_a'].'/'.$row['p_max'].'<br /><div style="font-size:90%;">'.$row['percent'].'/100%</div></td>';
						$content .= '<td align="center">'.$row['p_or_a'].'/'.$row['o_max'].'<br /><div style="font-size:90%;">'.$row['o_percent'].'/100%</div></td>';
						$content .= '<td align="center">'.(($row['qids']) ? (substr_count($row['qids'],',')+1) : '0').'/'.(($row['sids']) ? (substr_count($row['sids'],',')+1) : '0').'</td>';
						$content .= '<td align="center">'.(($row['cids']) ? (substr_count($row['cids'],',')+1) : '0').'/'.(($row['fids']) ? (substr_count($row['fids'],',')+1) : '0').'</td>';
						$content .= '<td>'.(($cat1) ? $catArray[$cat1] : '').' '.(($cat1 && $cat2) ? '/' : '').' '.(($cat2) ? $catArray[$cat2] : '').'</td>';
						$content .= "</tr>\n";
//						$jsc .= "document.forms.myquiz.delit[0].checked='true';\n";
					}
					$content .= "</table><br />\n";
					$content .= '<input type="button" name="select_all" value="'.$LANG->getLL('select_all').'" onclick="markAll();" />'."\n";
					$content .= ' <input type="submit" name="del_some" value="'.$LANG->getLL('delete').'" />'."\n";
					$content .= ' <input type="button" name="del_all" value="'.$LANG->getLL('delete_all').'" onclick="delAllRes();" />';
					$content .= ' <input type="submit" name="hide_some" value="'.$LANG->getLL('hide').'" />'."\n";
					$content .= ' <input type="submit" name="release_some" value="'.$LANG->getLL('release').'" />'."\n";
				} else {
					$content .= '<div align="center"><strong>0 '.$LANG->getLL('quiz_results')."</strong></div><br />\n";
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			break;
			case 2:
				$head = $LANG->getLL('function2');
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT '.$columns[$table]['qno'].' AS qno',
					$table,
					'PID='.$id.' AND sys_language_uid='.$lid);
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$questions.=','.$row['qno'];
					}
					$questions=substr($questions,1);
					$GLOBALS['TYPO3_DB']->sql_free_result($res);
				
					$questArray = array();
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
						'tx_myquizpoll_question',
						'uid IN ('.$questions.')'); //	//'PID='.$id.' AND sys_language_uid='.$lid
					$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
					if ($rows>0) {
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$uid = $row['uid'];
							//$questArray[$uid] = array();	// KGB:noetig?
							$questArray[$uid] = $row;
						}
					}
					$GLOBALS['TYPO3_DB']->sql_free_result($res);
				}
		
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					'uid,sys_language_uid,hidden,crdate,'.$columns[$table]['ano'].' AS ano,'.$columns[$table]['qno'].' AS qno'.$columns[$table]['fno'],
					$table,
					'PID='.$id.' AND sys_language_uid='.$lid,
					'',
					$sortBy,
					$limitTo);
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {							// DB entries found?
					$nr = 0;
					$content .= '<div align="center"><strong>'.$rowsTotal.' '.$LANG->getLL('poll_results').":</strong></div><br />\n";
					$content .= '<table style="border:1px #333 solid;"><tr style="background-color:#fff;"><th>'.$LANG->getLL('number').'</th><th><a href="';
//					$sorting = ($sortBy == 'sys_language_uid DESC') ? 'sys_language_uid ASC' : 'sys_language_uid DESC';
//					$content .= $link.'&sortBy='.urlencode($sorting).'">'.$LANG->getLL('lang').'</a></th><th><a href="';
					$sorting = ($sortBy == 'crdate DESC') ? 'crdate ASC' : 'crdate DESC';
					$content .= $link.'&sortBy='.urlencode($sorting).'">'.$LANG->getLL('date').'</a></th>';
					if ($columns[$table]['fno']) $content .= '<th>'.$LANG->getLL('foreign').'</th>';
					$content .= '<th>'.$LANG->getLL('question').'</a></th>';
					$content .= '<th>'.$LANG->getLL('answer').'</th></tr>'."\n";
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$nr++;
						$uid = $row['uid'];
						if ($nr%2 == 0)
							$style='even';
						else
							$style='odd';
						$content .= '<tr class="quizpoll_'.$style.'"><td align="center">';
						$content .= '<a href="alt_doc.php?returnUrl=db_list.php%3Fid%3D43%'.$id.'table%3D&edit['.$table.']['.$uid.']=edit"><img src="'.$path.'icon-edit.jpg" alt="'.$LANG->getLL('function0a').'" title="'.$LANG->getLL('function0a').'" /></a> ';
						$content .= $nr.': <input type="checkbox" name="delit[]" value="'.$uid.'" /></td><td';
						if ($row['hidden']) $content .= ' class="quizpoll_hidden"';
						$content .= '>'.date("d.m.Y G:i:s", $row['crdate']).'</td>';
						if ($columns[$table]['fno']) {
							$fval=$row['foreign_val'];
							if ($foreign_array[$fval]) $fval = $foreign_array[$fval];
							$content .= '<td>'.$fval.'</td>';
						}
						$question = intval($row['qno']);
						$content .= '<td>'.$questArray[$question]['title'].'</td>';
						$answer = intval($row['ano']);
						$content .= '<td>'.$questArray[$question]['answer'.$answer]."</td></tr>\n";
					}
					$content .= "</table><br />\n";
					$content .= '<input type="button" name="select_all" value="'.$LANG->getLL('select_all').'" onclick="markAll();" />'."\n";
					$content .= ' <input type="submit" name="del_some" value="'.$LANG->getLL('delete').'" />'."\n";
					$content .= ' <input type="button" name="del_all" value="'.$LANG->getLL('delete_all').'" onclick="delAllRes();" />';
					$content .= ' <input type="submit" name="hide_some" value="'.$LANG->getLL('hide').'" />'."\n";
					$content .= ' <input type="submit" name="release_some" value="'.$LANG->getLL('release').'" />'."\n";
					$content .= "\n".'<input type="hidden" name="table" value="'.$table.'" />';
				} else {
					$content .= '<div align="center"><strong>0 '.$LANG->getLL('poll_results')."</strong></div><br />\n";
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res);
			break;
			case 3:
				$head = $LANG->getLL('function3');
				$content .= '<div align="center"><strong>'.$LANG->getLL('evaluation')."</strong></div><br />\n";
				$whereRel = '';
				if($category>0 && count($categories)>0) {
					$content .= '<h3>'.$LANG->getLL('category').': '.$categories[$category].'</h3>';
					$whereRel = ' AND quest.category='.$category;
				}
				
				/* user-select - begin */
				$where = '';
				$selectAnswers = array();
				$tmpArray = explode('u', $selectOnly);
				foreach ($tmpArray as $value) {
					preg_match('/q(\d+)a(\d+)e/', $value, $matches);
					if ($matches[2])
						$selectAnswers[intval($matches[1])][] = intval($matches[2]);
				}
				
				if(count($selectAnswers)>0) {
					// return selection: Fragen und Antworten merken
					$questionsArray = array();
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
						'tx_myquizpoll_question',
						'PID='.$id.' AND sys_language_uid='.$lid
					);
					$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
					if ($rows>0) {
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$questionsArray[$row['uid']]['title'] = $row['title'];
							$i = 1;
							while($i <= 12) {
								if($row['answer'.$i] != '')
									$questionsArray[$row['uid']]['answer'.$i] = $row['answer'.$i];
								$i++;
							}
						}
					}
					
					$content .= '<h4>'.$LANG->getLL('only_select').':</h4>';
					$content .= '<p>';
					foreach($selectAnswers as $questionId => $answerArray) {
						foreach($answerArray as $answerNumber) {
							$content .= $questionsArray[$questionId]['title'].': '.$questionsArray[$questionId]['answer'.$answerNumber].' <br />';
						}
					}
					$content .= "</p><br />\n";
					
					// all answers with correct id answer
					foreach($selectAnswers as $questionId => $answerArray) {
						foreach($answerArray as $answerNumber) 
							$where .= '(question_id = '.$questionId.' AND checked'.$answerNumber.' = 1) OR ';
					}
					$where = '('.substr($where, 0, (strlen($where)-3)).')';
					$answerArray = array();
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
						'tx_myquizpoll_relation',
						'PID='.$id.' AND '.$where
						);
					$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
					if ($rows>0) {
						// array with userid, question id and number of answer
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$i = 1;
							if (!is_array($answerArray[$row['user_id']]))
								$answerArray[$row['user_id']] = array();
							if (!is_array($answerArray[$row['user_id']][$row['question_id']]))
								$answerArray[$row['user_id']][$row['question_id']] = array();
							while($i <= 12) {
								// Problem: funktioniert nur bei Radio-Buttons
								if($row['checked'.$i] == 1)
									$answerArray[$row['user_id']][$row['question_id']][$i] = 1;
								$i++;
							}
						}
					}
					// only select users where whole selection true
					$userIds = array();
					foreach($answerArray as $userId => $array) {
						$check = true;
						foreach($selectAnswers as $questionId => $arraySelectAnswers) {
							$checkA = false;
							foreach($arraySelectAnswers as $answerNumber) {
								if($array[$questionId][$answerNumber]==1)
									$checkA = true;
							}
							if(!$checkA)
								$check = false;
								
						}
						if($check)
							$userIds[] = $userId;
					}
					if (count($userIds)>0) $whereRel .= ' AND rel.user_id IN ('.implode(',',$userIds).')';					
				}
				/* user-select - end */
				
				$ansArray = array();
				$ansArray[0] = array();
				$votesTotal = 0;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('rel.*',
					'tx_myquizpoll_relation rel, tx_myquizpoll_question quest',
					'rel.PID='.$id.' AND rel.sys_language_uid='.$lid.' AND rel.question_id=quest.uid '.$whereRel);
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {							// DB entries found?
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$qid = intval($row['question_id']);
						if (!is_array($ansArray[0][$qid])) $ansArray[0][$qid] = array();	// KG: noetig?
						for ($i=1; $i<=12; $i++) {
							if ($row['checked'.$i]) {
								$ansArray[0][$qid][$i]++;
								$ansArray[0][$qid]['votes']++;
								$votesTotal++;
							}
						}
						/* add textanswers to $ansArray form question type 3: textfield, 5: textarea */
						if($row['textinput'] != '') {
							$ansArray[0][$qid]['textinput'][] = $row['textinput'];
						}
						$questions.=",$qid";
					}
					$questions=substr($questions,1);
				}
			break;
			case 4:
				$head = $LANG->getLL('function4');
				$content .= '<div align="center"><strong>'.$LANG->getLL('evaluation')."</strong></div><br />\n";
				
				$ansArray = array();
				$votesTotal = 0;
				$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
					$columns[$table]['ano'].' AS ano,'.$columns[$table]['qno'].' AS qno'.$columns[$table]['fno'],
					$table,
					'PID='.$id.' AND sys_language_uid='.$lid);
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
				if ($rows>0) {							// DB entries found?
					while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
						$qid = intval($row['qno']);
						$aid = intval($row['ano']);
						if ($columns[$table]['fno'])
							$fval=$row['foreign_val'];
						else $fval=0;
						if (!is_array($ansArray[$fval])) $ansArray[$fval] = array();
						if (!is_array($ansArray[$fval][$qid])) $ansArray[$fval][$qid] = array();
						$ansArray[$fval][$qid][$aid]++;
						$ansArray[$fval][$qid]['votes']++;
						$votesTotal++;
						$questions.=",$qid";
					}
					$questions=substr($questions,1);
				}
			break;
			case 7:
			  $head = $LANG->getLL('function7');
			  if ($GLOBALS['BE_USER']->user['admin']) {
				$once = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('once');
				$once_yes = ($once) ? ' checked' : '';
				$once_no = (!$once) ? ' checked' : '';
				$content .= '<div align="center"><strong>CSV-Export</strong></div><br />'."\n";
				$content .= '<p>'.$LANG->getLL('display_result').' <input type="radio" name="once" value="1"'.$once_yes.' /> '.$LANG->getLL('yes');
				$content .= ' &nbsp; <input type="radio" name="once" value="0"'.$once_no.' /> '.$LANG->getLL('no')."<br /><br />\n";
				$content .= $LANG->getLL('imp_delimiter').': <input name="feldtrenner" type="text" value=";" size="5" /> &nbsp; ';
				$content .= $LANG->getLL('imp_enclosure').': <input name="texttrenner" type="text" value="" size="5" /> &nbsp; ';
				$content .= $LANG->getLL('dateformat').': <input name="dateformat" type="text" value="d.m.Y H:i:s" size="16" /> &nbsp; ';
				$content .= ' <input type="submit" name="export" value="Export" /></p>'."\n";
				if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('export')) {
					$maxAnswers=1;
					$feldtrenner = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('feldtrenner');
					$texttrenner = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('texttrenner');
					$dateformat =  \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('dateformat');
					
					$catArray = array();
					$userHash = array();
					$questHash = array();
					$questions = '';
					
/*					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(*) AS anzahl',
									'tx_myquizpoll_result',
									'PID='.$id.' AND sys_language_uid='.$lid,
									'',	'',	'');
					$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res);
					if (!$row['anzahl']) {
						$table = 'tx_myquizpoll_voting';*/
					if ($table == 'tx_myquizpoll_voting') {
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT question_id',
							'tx_myquizpoll_voting',
							'PID='.$id.' AND sys_language_uid='.$lid);
						$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
						if ($rows>0) {
							while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
								$questHash[$qno] = $row['question_id'];
							}
							$questions = implode(',', $questHash);
						}
					} else {
						//$table = 'tx_myquizpoll_result';
						$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,name',
										'tx_myquizpoll_category',
										'1=1'); 	//'PID='.$id,
						$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
						if ($rows>0) {
							while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
								$uid = $row['uid'];
								$catArray[$uid] = $row['name'];
							}
						}
						
						$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
							'tx_myquizpoll_relation',
							'pid='.$id.' AND sys_language_uid='.$lid,
							'',
							'uid',
							'');
						$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
						if ($rows>0) {
							while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
								$qno = $row['question_id'];
								$u_id = $row['user_id'];
								if (!is_array($userHash[$u_id])) $userHash[$u_id] = array();
								if (!is_array($userHash[$u_id][$qno])) $userHash[$u_id][$qno] = array();
								for ($i=1; $i<=12; $i++) {
									if ($row['checked'.$i] || $row['checked'.$i]===0)
										$userHash[$u_id][$qno]['c'.$i] = $row['checked'.$i];
								}
								$userHash[$u_id][$qno]['textinput'] = $row['textinput'];
								$userHash[$u_id][$qno]['points'] = $row['points'];
								$userHash[$u_id][$qno]['next_cat'] = $row['next_cat'];
								//$content .= $u_id.'-'.$qno.'_'.$userHash[$u_id][$qno]['points'].'*';
								$questHash[$qno] = $qno;
							}
							$questions = implode(',', $questHash);
						}
					}
					
					if ($questions) {
						// Get all questions and answers from the database
						$questionsArray = array();
						$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
								'tx_myquizpoll_question',
								'uid IN ('.$questions.')',	//pid='.$id.' AND sys_language_uid='.$lid,
								'',
								'sorting',
								'');
						$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
						if ($rows>0) {
							while($rowA = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
								$questionUID = $rowA['uid'];
								$questionsArray[$questionUID] = array();
								$questionsArray[$questionUID]['title'] = $rowA['title'];
								//$questionsArray[$questionUID]['name'] = $rowA['name'];
								$questionsArray[$questionUID]['qtype'] = $rowA['qtype'];
								for ($i=1; $i<=12; $i++) {
									if ($rowA['answer'.$i] || $rowA['answer'.$i]==='0') {
										$questionsArray[$questionUID]['c'.$i] = $rowA['checked'.$i];
										$questionsArray[$questionUID]['a'.$i] = $rowA['answer'.$i];
										if ($i>$maxAnswers) $maxAnswers=$i;
									}
								}
							/*	$category_next = $rowA['category_next']; 
								if ($category_next)
									$questionsArray[$questionUID]['category_next'] = $catArray[$category_next];
								else
									$questionsArray[$questionUID]['category_next'] = '';	*/
							}
						}
					}
		
					$resCols = 16;
					$output=$texttrenner.'UID'.$texttrenner.$feldtrenner;
					if ($table == 'tx_myquizpoll_result') {
						$output.=$texttrenner.'name'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'email'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'www'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'scores or answer #'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'max. points'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'percent'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'overall max. points'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'overall percent'.$texttrenner.$feldtrenner;
						if (count($userHash)==0) {
							$output.=$texttrenner.'answ. question IDs'.$texttrenner.$feldtrenner;
							$output.=$texttrenner.'corr. question IDs'.$texttrenner.$feldtrenner;
							$output.=$texttrenner.'skip. question IDs'.$texttrenner.$feldtrenner;
						}
						$output.=$texttrenner.'joker 1'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'joker 2'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'joker 3'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'start time'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'end time'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'last categ.'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'next categ.'.$texttrenner;
						if (count($userHash)>0) {
							$output.=$feldtrenner;
							$output.=$texttrenner.'question'.$texttrenner.$feldtrenner;
							for ($i=1; $i<=$maxAnswers; $i++) {
								$output.=$texttrenner.'answer #'.$i.$texttrenner.$feldtrenner;
								$output.=$texttrenner.'checked? #'.$i.$texttrenner.$feldtrenner;
							}
							$output.=$texttrenner.'textinput'.$texttrenner.$feldtrenner;
							$output.=$texttrenner.'scores'.$texttrenner.$feldtrenner;
							$output.=$texttrenner.'next categ.'.$texttrenner.$feldtrenner;
							//$output.=$texttrenner.'answered?'.$texttrenner.$feldtrenner;
							$output.=$texttrenner.'corr. answered?'.$texttrenner.$feldtrenner;
							$output.=$texttrenner.'skipped?'.$texttrenner;
						}
					} else {
						$output.=$texttrenner.'date'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'question'.$texttrenner.$feldtrenner;
						$output.=$texttrenner.'answer'.$texttrenner;
						if ($columns['tx_myquizpoll_voting']['fno'])
							$output.=$feldtrenner.$texttrenner.'foreign value'.$texttrenner;
					}
					$output.="\n";
					
					$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
									$table,
									'PID='.$id.' AND sys_language_uid='.$lid,
									'',
									'crdate DESC',
									'');
					$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
					if ($rows>0) {							// DB entries found?
						$nr = 0;
						while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
							$nr++;
							$uid = $row['uid'];
							$entry=$texttrenner.$uid.$texttrenner.$feldtrenner;
							if ($table == 'tx_myquizpoll_result') {
								$cat1 = $row['lastcat'];
								$cat2 = $row['nextcat'];
								$qids = $row['qids'];
								$sids = $row['sids'];
								$cids = $row['cids'];
								//$entry.=$texttrenner.date('d M Y H:i:s', $row['crdate']).$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$this->myReplace($feldtrenner, $texttrenner, $row['name']).$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['email'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['homepage'].$texttrenner.$feldtrenner;
								//$entry.=$texttrenner.$row['ip'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['p_or_a'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['p_max'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['percent'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['o_max'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['o_percent'].$texttrenner.$feldtrenner;
								if (count($userHash)==0) {
									$entry.=$texttrenner.$qids.$texttrenner.$feldtrenner;
									$entry.=$texttrenner.$cids.$texttrenner.$feldtrenner;
									$entry.=$texttrenner.$sids.$texttrenner.$feldtrenner;
								} else {
									if ($qids) $qids = '0,'.$qids.',';
									if ($sids) $qids = '0,'.$sids.',';
									if ($cids) $qids = '0,'.$cids.',';
								}
								$entry.=$texttrenner.$row['joker1'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['joker2'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.$row['joker3'].$texttrenner.$feldtrenner;
								$entry.=$texttrenner.date($dateformat, $row['firsttime']).$texttrenner.$feldtrenner;
								$entry.=$texttrenner.date($dateformat, $row['lasttime']).$texttrenner.$feldtrenner;
								$entry.=$texttrenner.(($cat1) ? $catArray[$cat1] : '').$texttrenner.$feldtrenner;
								$entry.=$texttrenner.(($cat2) ? $catArray[$cat2] : '').$texttrenner;
								$output.=$entry;
								if (is_array($userHash[$uid])) {
									$output.=$feldtrenner;
									$entry.=$feldtrenner;
									$j=0;
									foreach ($userHash[$uid] as $key => $value) {
										$j++;
										if ($j>1) {	// es wurde mehr als 1 Frage beantwortet
											$output.="\n";
											if ($once) {		// user data only once?
												for ($k=1; $k<=$resCols; $k++)
													$output.=$texttrenner.$texttrenner.$feldtrenner;
											} else {
												$output.=$entry;
											}
										}
										$output.=$texttrenner.$this->myReplace($feldtrenner, $texttrenner, $questionsArray[$key]['title']).$texttrenner.$feldtrenner;
										for ($i=1; $i<=$maxAnswers; $i++) {
											//if ($questionsArray[$key]['a'.$i]) {
												$output.=$texttrenner.$this->myReplace($feldtrenner, $texttrenner, $questionsArray[$key]['a'.$i]).$texttrenner.$feldtrenner;
												$output.=$texttrenner.intval($userHash[$uid][$key]['c'.$i]).$texttrenner.$feldtrenner;
											//}
										}
										$output.=$texttrenner.$this->myReplace($feldtrenner, $texttrenner, $userHash[$uid][$key]['textinput']).$texttrenner.$feldtrenner;
										$output.=$texttrenner.intval($userHash[$uid][$key]['points']).$texttrenner.$feldtrenner;
										$cat1=$userHash[$uid][$key]['nextcat'];
										$output.=$texttrenner.(($cat1) ? $catArray[$cat1] : '').$texttrenner.$feldtrenner;
									/*	if ($qids && strpos($qids,",$key,")) {
											$output.=$texttrenner.'1'.$texttrenner.$feldtrenner;
										} else $output.=$texttrenner.'0'.$texttrenner.$feldtrenner;*/
										if ($cids && strpos($cids,",$key,")) {
											$output.=$texttrenner.'1'.$texttrenner.$feldtrenner;
										} else $output.=$texttrenner.'0'.$texttrenner.$feldtrenner;
										if ($sids && strpos($sids,",$key,")) {
											$output.=$texttrenner.'1'.$texttrenner;
										} else $output.=$texttrenner.'0'.$texttrenner;
									}
								}
							} else {
								$output.=$entry;
								$key = $row['question_id'];
								$output.=$texttrenner.date($dateformat, $row['crdate']).$texttrenner.$feldtrenner;
								$output.=$texttrenner.$this->myReplace($feldtrenner, $texttrenner, $questionsArray[$key]['title']).$texttrenner.$feldtrenner;
								$output.=$texttrenner.$this->myReplace($feldtrenner, $texttrenner, $questionsArray[$key]['a'.$row['answer_no']]).$texttrenner;
								if ($columns['tx_myquizpoll_voting']['fno']) {
									$fval=$row['foreign_val'];
									if ($foreign_array[$fval]) $fval = $this->myReplace($feldtrenner, $texttrenner, $foreign_array[$fval]);
									$output.=$feldtrenner.$texttrenner.$fval.$texttrenner;
								}
							}					
							$output.="\n";
						}
					}
					$content .= "<br /><br />\n".'<textarea name="temp" cols="100" rows="25">'."\n".$output."</textarea>\n<br />\n";
				}
			  } else $content .= '<div align="center"><strong>Only for Admins!!!</strong></div><br />';
			break;
			case 8:
			  $head = $LANG->getLL('function8');
			  if ($GLOBALS['BE_USER']->user['admin']) {
				$content .= '<div align="center"><strong>CSV-Import</strong></div><br />'."\n";
				$content .= '<p>'.$LANG->getLL('imp_delimiter').': <input name="feldtrenner" type="text" value=";" size="5" /> &nbsp; ';
				$content .= $LANG->getLL('imp_enclosure').': <input name="texttrenner" type="text" value="" size="5" /></p><br />';
				$content .= '<p>'.$LANG->getLL('imp_sys_id').' (Sysfolder): <input name="daten_id" type="text" size="5" value="'.$id.'" /></p><br />';
				$content .= '<p>'.$LANG->getLL('imp_lang_id').': <input name="lang_id" type="text" size="5" value="" /></p><br />';
				$content .= '<p>'.$LANG->getLL('imp_convert').' <input name="convert" type="checkbox" value="1" /></p><br />';
				$content .= '<p>'.$LANG->getLL('imp_show_it').' <input name="show_it" type="radio" value="0" checked="checked" /> '.$LANG->getLL('no');
				$content .= ' &nbsp; <input name="show_it" type="radio" value="1" /> '.$LANG->getLL('yes');
				$content .= ' &nbsp; <input name="show_it" type="radio" value="2" /> '.$LANG->getLL('imp_test_mode')."</p><br />\n";
				$content .= '<p>'.$LANG->getLL('imp_csv_example').':</p><pre>'."\n";
				$content .= 'title;name;qtype;answer1;correct1;answer2;correct2;answer3;correct3;answer4;correct4;answer5;correct5;explanation;points;image;alt_text'."\n";
				$content .= 'Natur;Nationalparks in Kroatien?;0;Biokovo;1;Bihor;0;Plitvice;1;Tatra;0;Krka;1;Muss man wissen...;3;example.jpg;Nationalpark';
				$content .= "\n</pre><br /><p>".$LANG->getLL('imp_csv_example2').":</p><pre>\n";
				$content .= '"title","name","qtype","answer1","answer2","answer3","answer4","correct","explanation","points"'."\n";
				$content .= '"Natur","Nationalpark in Polen?","1","Biokovo","Bihor","Plitvice","Tatra","4","Muss man nicht wissen...","10"'."\n";
				$content .= "\n</pre><br />\n";
				$content .= '<input type="file" name="uploadfile" size="40" /><br /><br />';
				$content .= '<input type="submit" name="import" value="Import" />';
			  } else $content .= '<div align="center"><strong>Only for Admins!!!</strong></div><br />';
			break;
			case 9:
			  $head = $LANG->getLL('function9');
			  if ($GLOBALS['BE_USER']->user['admin']) {
				$content .= '<div align="center"><strong>'.$LANG->getLL('mystyles')."</strong></div><br />\n";
				
				if (file_exists($filename_new))
					$filename = $filename_new;
				else
					$filename = $filename_old;
				if (file_exists($filename)) {
					$datei = fopen($filename,"r");
					$content .= '<textarea name="mystyles" rows="15" cols="50">';
					while (!feof($datei)) {
						$content .= fgets($datei);
					}
					$content .= "</textarea><br /><br />\n";
					fclose ($datei);
				} else $content .= '<p>Error: can not find file: '.$filename.'<br /></p>';
				$content .= '<input type="submit" name="save" value="'.$LANG->getLL('save_styles').'" />';
			  } else $content .= '<div align="center"><strong>Only for Admins!!!</strong></div><br />';
			break;
		}
		
		if (($mode==3 || $mode==4) && $questions) {
			$where = ($category>0) ? ' AND category = '.$category : '';
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
				'tx_myquizpoll_question',
				'uid IN ('.$questions.')'.$where,	//'PID='.$id.' AND sys_language_uid='.$lid.' AND deleted=0',
				'',
				'sys_language_uid, sorting');
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			if ($rows>0) {
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$uid = intval($row['uid']);
					$content .= '<div class="quizpoll_stat_around"><div class="quizpoll_stat_question"><strong>'.$row['title']."</strong>";
					if(count($categories)>0 && $category == 0) {
						$content .= ' ('.$categories[$row['category']].')';
					}
					$content .= "</div>\n";
					foreach ($ansArray as $key => $valArray) {
						if ($key) {
							if ($columns[$table]['fno'] && $foreign_array[$key])
								$content .= '<br /><i>'.$foreign_array[$key].'</i><br />';
							else
								$content .= '<br /><i>'.$key.'</i><br />';
						}
						$votes = $valArray[$uid]['votes'];
						if (!($row['qtype']==3 || $row['qtype']==5)) {
							for ($i=1; $i<=12; $i++) {
								if ($valArray[$uid][$i]) {
									$percent2=round(100*$valArray[$uid][$i]/$votes, 2);
									$percent0=round($percent2);
									if ($percent0>0) {
										$content .= '<img src="'.$path.'percent-l.jpg" alt="'.$percent2.'%" title="'.$percent2.'%" width="'.$percent0.'" height="10" />';
										$percent0 = 100 - $percent0;
										$content .= '<img src="'.$path.'percent-r.jpg" alt="'.$percent2.'%" title="'.$percent2.'%" width="'.$percent0.'" height="10" />';
									} else {
										$content .= '<img src="'.$this->path.'percent-r.jpg" alt="0%" title="0%" width="100" height="10" />';
									}
									$content .= ' <span class="quizpoll_stat_count">'.$valArray[$uid][$i]." ($percent2%) </span>&nbsp;";
								} else if ($row['answer'.$i]) {
									$content .= '<img src="'.$this->path.'percent-r.jpg" alt="0%" title="0%" width="100" height="10" />';
									$content .= ' <span class="quizpoll_stat_count">0 </span>&nbsp;';
								}
								if ($row['answer'.$i]) {
									if ($mode==3)
										$content .= '<a href="javascript:addOnly('.$row['uid'].','.$i.');">'.$row['answer'.$i]."</a><br />\n";
									else
										$content .= $row['answer'.$i]."<br />\n";
								}
							}
						}
						/* add textanswers to $ansArray form question type 3: textfield, 5: textarea */
						$textinput = $valArray[$uid]['textinput'];
						if(is_array($textinput)) {
							$content .= '<ol>';
							foreach($textinput as $key => $value)
								$content .= '<li class="quizpoll-textinput-answer">'.nl2br(stripcslashes($value))."</li>\n";
							$content .= '</ol>';
						}
					}
					$content .= "</div>\n";
				}
				$content .= '<br /><p>'.$LANG->getLL('votesTotal').": $votesTotal</p>";
				if ($mode==3) {
					$content .= '<p>'.$LANG->getLL('clickAnswers').'</p>
<input type="hidden" name="selectonly" value="'.$selectOnly.'" />
<script language="JavaScript">
  function addOnly(qno,ano) {
	var temp = document.forms.myquiz.selectonly.value;
	if (temp.length>0) temp+="u";
	document.forms.myquiz.selectonly.value=temp+"q"+qno+"a"+ano+"e";
	document.forms.myquiz.submit();
	return;
  }
</script>';
				}
				$content .= "<br />\n";
			}
			//add selectbox for categories
			if (count($categories) > 0) {
				$content .= $LANG->getLL('category').': <select name="category">';
				$content .= '<option value="0">-</option>';
				foreach ($categories as $catID => $catName){
					$content .= '<option value="'.$catID.'"'.(($category == $catID) ? ' selected="selected" ' : '' ).'>'.$catName.'</option>';
				}
				$content .= '</select><br />';
			}
		} else if ($mode<3) {	
			if ($rowsTotal>$ep) {
				$fin = ceil($rowsTotal/$ep)-1;
				$content .= '<hr />'.$LANG->getLL('page').': ';
				if ($pointer>0) {
					$content .= '<a href="'.$link.'&pointer=0&ep='.$ep.'&lid='.$lid.'&foreign_table='.$foreign_table.'&foreign_title='.$foreign_title.'">'.$LANG->getLL('begin').'</a> . ';
					$content .= '<a href="'.$link.'&pointer='.($pointer-1).'&ep='.$ep.'&lid='.$lid.'&foreign_table='.$foreign_table.'&foreign_title='.$foreign_title.'">'.$pointer.'</a> . ';
				}
				$content .= '<b>'.($pointer+1).'</b> ';
				if ($pointer<$fin) {
					$content .= '. <a href="'.$link.'&pointer='.($pointer+1).'&ep='.$ep.'&lid='.$lid.'&foreign_table='.$foreign_table.'&foreign_title='.$foreign_title.'">'.($pointer+2).'</a> ';
					$content .= '. <a href="'.$link.'&pointer='.$fin.'&ep='.$ep.'&lid='.$lid.'&foreign_table='.$foreign_table.'&foreign_title='.$foreign_title.'">'.$LANG->getLL('end').'</a>';
				}
			}
			$content .= "<hr />\n".$LANG->getLL('entriesPage').': <input type="text" name="ep" value="'.$ep.'" size="4" /> ';
		}
		
		if ($mode<8) {
			if ($columns[$table]['fno']) {
				$content .= "<br />\n".$LANG->getLL('foreign_table');
				$content.=': <input type="text" name="foreign_table" value="'.$foreign_table.'" size="20" />';
				$content.=', <input type="text" name="foreign_title" value="'.$foreign_title.'" size="10" />';
			}
			$content .= "<br />\n".$LANG->getLL('language').': <select name="lid"><option value="0">'.$LANG->getLL('default')."</option>\n";
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT sys_language_uid, sys_language.title',
				$table.', sys_language',
				$table.'.PID='.$id.' AND sys_language_uid>0 AND sys_language_uid=sys_language.uid',
				'',
				'sys_language_uid',
				'');
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			if ($rows>0) {							// DB entries found?
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$content .= '<option value="'.$row['sys_language_uid'].'"';
					if ($lid==$row['sys_language_uid']) $content .= ' selected';
					$content .= '>'.$row['title']."</option>\n";
				}
			}
			$content .= "</select>\n";
			$content .= '<input type="hidden" name="pointer" value="'.$pointer.'" />';
			$content .= "<br /><br />\n".'<input type="button" name="refresh" value="'.$LANG->getLL('refresh').'" onclick="refreshit();" />';
			$content .= '<script type="text/javascript">
function delAllRes() {
  Check = confirm("'.$LANG->getLL('sure').'");
  if (Check == true) {
    document.forms.myquiz.delall.value=1;
    document.forms.myquiz.submit();
  } else {
    return;
  }
}
function markAll() {
  for(var i=0; i < document.myquiz.elements["delit[]"].length; i++){
    document.forms.myquiz.elements["delit[]"][i].checked="true";
  }
  return;
}
function popup (url) {
 fenster = window.open(url, "Popupfenster", "width=492,height=500,resizable=yes");
 fenster.focus();
 return false;
}
function refreshit() {
  document.forms.myquiz.pointer.value=0;
  document.forms.myquiz.submit();
}
function nix() {
  return;
}
</script>
';
		}
		
		$this->content.=$this->doc->section($head.':',$content,0,1);
	}
}



if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/mod1/index.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/mod1/index.php']);
}



// Make instance:
$SOBE = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_myquizpoll_module1');
$SOBE->init();

// Include files? Welche denn???
// foreach($SOBE->include_once as $INC_FILE)	include_once($INC_FILE);

$SOBE->main();
$SOBE->printContent();
?>