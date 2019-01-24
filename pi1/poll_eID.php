<?php
  if (!defined ('TYPO3_MODE')) die ('Access denied.');
  if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');
  if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') die('No Ajax request!');
  
  // Column definitions
  define('OPT_ID', 0);
  define('OPT_TITLE', 1);
  define('OPT_VOTES', 2);
  
//  $feUserObj = tslib_eidtools::initFeUser(); // Initialize FE user object
//  tslib_eidtools::connectDB(); //Connect to database
  
  $fid = $GLOBALS['TYPO3_DB']->quoteStr(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('fid'), 'tx_myquizpoll_voting');
  $uid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('qid'));
  $pid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pid'));
  $lang = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('lang'));
  $remoteIP = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('remote_ip'));
  $blockIP = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('block_ip');
  $vote = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('vote'));
  $antworten = 6; //intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('qnr'));
  $rowsArray = array();
  
  if ($lang == 0)
  	$where_lang = ' AND sys_language_uid IN (0, -1)';
  else
  	$where_lang = ' AND sys_language_uid = ' . $lang;
  
  if ($vote) {
//   $userCookie = intval($_COOKIE["my_vote_$uid_$fid_$lang"]);
//   if ($userCookie==-1) {
	// Insert new vote into database
	$timestamp = time();
	$block = false;
	if ($remoteIP) {
	  $ip=$_SERVER['REMOTE_ADDR'];
	} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
	  $ip=$_SERVER['HTTP_CLIENT_IP'];
	} elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
	  $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	} else {
	  $ip=$_SERVER['REMOTE_ADDR'];
	}
	if ($blockIP) {
		$ips = explode(',', $blockIP);
		foreach ($ips as $aip) {
			$len = strlen(trim($aip));
			if (substr($ip,0,$len) == trim($aip)) {
				$block = true;
			}
		}
	}
	if (!$block) {
		$insert = array('pid' => $pid,
				'tstamp' => $timestamp,
				'crdate' => $timestamp,
				'cruser_id' => intval($GLOBALS['TSFE']->fe_user->user['uid']),
				'sys_language_uid' => $lang,
				'hidden' => 0,
				'ip' => $ip,
				'answer_no' => $vote,
				'question_id' => $uid,
				'foreign_val' => $fid);
		$success = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_myquizpoll_voting', $insert);
		//if($success){
		//	$qtuid = $GLOBALS['TYPO3_DB']->sql_insert_id();
		//}
	}
 //  }
  }
  
  // (bis zu) 6 Antworten holen
  $queryResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('answer1,answer2,answer3,answer4,answer5,answer6',
		'tx_myquizpoll_question',
		'uid=' . $uid . $where_lang . ' AND hidden=0 AND deleted=0',
		'',
		'',
		'');
	$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult);
	for ($i=0; $i<$antworten; $i++) {
		$nr=$i+1;
		if ($row['answer'.$nr] || $row['answer'.$nr]===0) {
			$rowsArray[$i] = array();
			$rowsArray[$i][OPT_ID]=$nr;
			$rowsArray[$i][OPT_TITLE]=$row['answer'.$nr];
			$rowsArray[$i][OPT_VOTES]=0;
		}
	}
		
	// votes
	$where_fid = ($fid) ? " AND foreign_val='$fid'" : '';
	$res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('count(answer_no), answer_no',
		'tx_myquizpoll_voting',
		'question_id=' . $uid . $where_lang . ' AND hidden=0' . $where_fid,
		'answer_no',
		'answer_no',
		'');
	$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
	if ($rows>0) {
		while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)){
			$aAnswer=$row2['answer_no'];
			if ($aAnswer) {
				$rowsArray[($aAnswer-1)][OPT_VOTES]=$row2['count(answer_no)']; //.$userCookie;
			}
		}
	}
	
  $GLOBALS['TYPO3_DB']->sql_free_result($queryResult);
  $GLOBALS['TYPO3_DB']->sql_free_result($res2);

  print json_encode($rowsArray);
?>