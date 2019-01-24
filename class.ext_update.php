<?php
/***************************************************************
*
*  This script is part of the Typo3 project. The Typo3 project is
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


/**
 * Class for updating the db
 */
class ext_update  {

	/**
	 * Main function, returning the HTML content of the module
	 *
	 * @return	string		HTML
	 */
	function main()	{

		$content = '';
		$update040a = false;
		$update040b = false;
		$update040c = false;
		
		$tableNames = $GLOBALS['TYPO3_DB']->admin_get_tables();
		if (!isset($tableNames['tx_myquizpoll_relation_user_id_mm'])) {
			$update040a = true;
			$update040b = true;
			$update040c = true;
		}

		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('update040a')) {
			$content .= "<br />Executing: Update relations-table for advanced statistics\n";
			
			$mmArray = array();
			$res = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local, uid_foreign',
				'tx_myquizpoll_relation_user_id_mm',
				'',
				'',
				'',
				'');
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res);
			if ($rows>0) {							// DB entries found?
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res)){
					$local = $row['uid_local'];
					$mmArray[$local] = array();
					$mmArray[$local]['user'] = $row['uid_foreign'];
				}
			}
			$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid_local, uid_foreign',
				'tx_myquizpoll_relation_question_id_mm',
				'',
				'',
				'',
				'');
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
			if ($rows>0) {							// DB entries found?
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)){
					$local = $row['uid_local'];
					$mmArray[$local]['quest'] = $row['uid_foreign'];
				}
			}
			
			$updateArray = array();
			foreach($mmArray as $key => $value) {
				//$content .= "- $key: ".$value['user'].'/'.$value['quest']."<br />\n";
				$updateArray = array('user_id' => $value['user'], 'question_id' => $value['quest']);
				$success = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_myquizpoll_relation', 'uid='.$key, $updateArray);
				if(!$success){
					$content.="<p>MySQL Update-Error :-(</p>";
				}
			}
			$update040a = true;
		}
		
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('update040b')) {
			$content .= "<br />Executing: - Delete no longer needed relation-data\n";
			
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_myquizpoll_relation_user_id_mm',
				''
			);
			$GLOBALS['TYPO3_DB']->exec_DELETEquery(
				'tx_myquizpoll_relation_question_id_mm',
				''
			);
			$update040b = true;
		}
		
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('update040c')) {
			$content .= "<br />Executing: - Delete no longer needed relation-tables\n";
			
			mysql( TYPO3_db, 'DROP TABLE tx_myquizpoll_relation_user_id_mm' );
			mysql( TYPO3_db, 'DROP TABLE tx_myquizpoll_relation_question_id_mm' );
			$update040c = true;
		}
		
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('updatepoll') && \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pollpid')) {
			$thePID=intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pollpid'));
			$timestamp = time();
			$content .= "<br />Executing: - Converting basic poll data to advanced poll data (folder $thePID)\n";
			$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, cruser_id,sys_language_uid,hidden, p_or_a, qids',
				'tx_myquizpoll_result',
				'pid='.$thePID,
				'',
				'',
				'');
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
			if ($rows>0) {
				$statisticsArray = array();
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){ 
					$theUID = $row['uid'];
					if (intval($row['p_or_a'])>0 && intval($row['p_or_a'])<13) {
							$statisticsArray[$theUID] = array(
							'pid' => $thePID,
							'tstamp' => $timestamp,
							'crdate' => $timestamp,
							'cruser_id' => $row['cruser_id'],
							'hidden' => $row['hidden'],
							'user_id' => $theUID,
							'question_id' => $row['qids'],
							'checked'.$row['p_or_a'] => 1,
							'sys_language_uid' => $row['sys_language_uid']
						);
					}
				}
			}
			if (is_array($statisticsArray)) {
				foreach ($statisticsArray as $type => $element) {
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_myquizpoll_relation', $element);
				}
				$content .= "<br />".count($statisticsArray)." elements inserted. done.<br />\n";			
			}
		}
		
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('updatepoll2a') && \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pollpid2a')) {
			$thePID=intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pollpid2a'));
			$timestamp = time();
			$content .= "<br />Executing: - Copy basic poll data to tx_myquizpoll_voting (folder $thePID)\n";
			$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, crdate,cruser_id,sys_language_uid,hidden, p_or_a, qids, ip',
				'tx_myquizpoll_result',
				'pid='.$thePID,
				'',
				'',
				'');
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
			if ($rows>0) {
				$votingArray = array();
				while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){ 
					$theUID = $row['uid'];
					if (intval($row['p_or_a'])>0 && intval($row['p_or_a'])<13) {
							$votingArray[$theUID] = array(
							'pid' => $thePID,
							'tstamp' => $timestamp,
							'crdate' => $row['crdate'],
							'cruser_id' => $row['cruser_id'],
							'hidden' => $row['hidden'],
//							'user_id' => $theUID,
							'question_id' => intval($row['qids']),
							'answer_no' => $row['p_or_a'],
							'ip' => $row['ip'],
							'sys_language_uid' => $row['sys_language_uid']
						);
					}
				}
			}
			if (is_array($votingArray)) {
				foreach ($votingArray as $type => $element) {
					$GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_myquizpoll_voting', $element);
				}
				$content .= "<br />".count($votingArray)." elements inserted into tx_myquizpoll_voting. done.<br />\n";			
			}
		}
		if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('updatepoll2b') && \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pollpid2a')) {
			$thePID=intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pollpid2a'));
			$content .= "<br />Executing: - Deleting old basic poll data (folder $thePID)\n";
			$GLOBALS['TYPO3_DB']->exec_DELETEquery('tx_myquizpoll_result', 'pid='.$thePID);
		}
		
		
		
		// formular
		if ($content) $content .= "<br /><br />\n";
		$linkScript = \TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript(); // htmlspecialchars()
		//$content.=$linkScript;
		$content.='<form name="myquiz" action="'.$linkScript.'" method="post">';
		if (!($update040a && $update040b && $update040c)) {
			$content.='<br /><p>Updates from Version 0.3.0-0.4.2 to 1.0.0:<br />';
			if (!$update040a)
				$content.='<input type="checkbox" name="update040a" value="1" checked="checked" /> Update relation-table for advanced statistics<br />';
			if (!$update040b)
				$content.='<input type="checkbox" name="update040b" value="1" checked="checked" /> - Delete no longer needed relation-data<br />';
			if (!$update040c)
				$content.='<input type="checkbox" name="update040c" value="1" checked="checked" /> - Delete no longer needed relation-tables<br />';
			$content.='</p><br />';
		}
		$content.='<p><input type="checkbox" name="updatepoll" value="1"  /> Optional: convert basic poll data to advanced poll data. ID of the folder: ';
		$content.='<input type="text" name="pollpid" value="" /> (be carefully, there is no check if there are really basic poll data). Execute it only once!</p><br />';
		$content.='<p><input type="checkbox" name="updatepoll2a" value="1"  /> Optional: copy basic poll data to the table tx_myquizpoll_voting. ID of the folder: ';
		$content.='<input type="text" name="pollpid2a" value="" /> (be carefully, there is no check if there are really basic poll data). Execute it only once!';
		$content.='<br />&nbsp; -&nbsp; <input type="checkbox" name="updatepoll2b" value="1"  /> Delete old entries in the table tx_myquizpoll_result with the above ID.</p><br />';
		
		//$linkScript = \TYPO3\CMS\Core\Utility\GeneralUtility::slashJS(\TYPO3\CMS\Core\Utility\GeneralUtility::linkThisScript());
		//$content.=$linkScript;
		// this.form.action=\''.$linkScript.'\';
		$content.='<input type="button" onclick="this.form.submit();" name="send" value="Start" />';
		$content.='</form>';
		
		return $content;
	}

	/**
	 * access is always allowed
	 *
	 * @return	boolean		Always returns true
	 */
	function access() {
		return true;
	}

}

// Include extension?
if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/class.ext_update.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/class.ext_update.php']);
}	// statt  $GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['ext/myquizpoll/class.ext_update.php'])

?>
