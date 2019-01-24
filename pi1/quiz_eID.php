<?php
  if (!defined ('TYPO3_MODE')) die ('Access denied.');
  if (!defined ('PATH_typo3conf')) die ('Could not access this script directly!');
  if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) || $_SERVER['HTTP_X_REQUESTED_WITH'] != 'XMLHttpRequest') die('No Ajax request!');

  // Column definitions
  define('OPT_ID', 0);				// selected answer no.
  define('OPT_TITLE', 1);			// title of the answer
  define('OPT_CORRECT', 2);			// is this answer correct?
  define('OPT_POINTS', 3);			// points for this answer
  define('OPT_EXPLANATION', 4);		// explanation for this answer
  define('OPT_QTID', 5);			// quiz taker ID, only for array key 0
  define('OPT_TOTAL_POINTS', 6);	// total points, only for array key 0
  define('OPT_TOTAL_CORRECT', 7);	// all correct answers, only for array key 0
  define('OPT_ALL_POINTS', 8);		// all points till now, only for array key 0
  
//  tslib_eidtools::connectDB(); //Connect to database
  
  $qtuid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('qtid'));	// quiz taker
  $uid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('qid'));		// question
  $pid = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('pid'));		// parent id
  $lang = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('lang'));	// language
  $remoteIP = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('remote_ip'));	// take remote IP?
  $blockIP = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('block_ip');	// block some IPs?
  $no_negative = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('no_negative'));	// no negative points?
  $joker1 = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('joker1'));	// Jokers
  $joker2 = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('joker2'));
  $joker3 = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('joker3'));
  $vote = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('vote');			// selected answer(s)
  $antworten = intval(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('qnr'));
  if (!$antworten) $antworten=6;			// default value for no. of answers
  $rowsArray = array();
  $rowsArray[0] = array();
  $rowsArray[0][OPT_QTID] = $qtuid;
  $block = false;
  
  if ($lang == 0)
  	$where_lang = ' AND sys_language_uid IN (0, -1)';
  else
  	$where_lang = ' AND sys_language_uid = ' . $lang;
  
  if ($vote) {
	$timestamp = time();
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
	
	// Die komplette Frage holen
	$queryResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*', // statt "answer$vote, correct$vote, points$vote, points, explanation",
		'tx_myquizpoll_question',
		'uid=' . $uid . $where_lang . ' AND hidden=0 AND deleted=0');
	$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult);
	$cids = '';
	$fids = '';
	$qids = '';
	$sids = '';
	$total_points = 0;
	$p_max = 0;
	$votes = explode(',', $vote);
	$corArray = array();
	$usrArray = array();
	$allCorrect = true;
	
	for ($i=1; $i<=$antworten; $i++) { // alle Fragen-Antworten pruefen, ob sie korrekt sind
		if ($row['correct'.$i]) {
			$rowsArray[0][OPT_TOTAL_CORRECT] = ($rowsArray[0][OPT_TOTAL_CORRECT]) ? $rowsArray[0][OPT_TOTAL_CORRECT].",$i" : $i;
			$p_max += $row['points'];
		}
		$corArray[$i]=intval($row['correct'.$i]);
		$usrArray[$i]=0;
	}
	
	//$i=0;
	foreach ($votes as $i => $one_vote) {		// alle ausgewaehlten Antworten der Frage durchgehen
		$one_vote = intval($one_vote);
		if ($i>0) $rowsArray[$i] = array();
		$rowsArray[$i][OPT_ID]=$one_vote;
		if ($one_vote==-1) {
			$sids = $uid;
			break;
		}
		$rowsArray[$i][OPT_TITLE]=$row['answer'.$one_vote];
		$rowsArray[$i][OPT_CORRECT]=intval($row['correct'.$one_vote]);
		$rowsArray[$i][OPT_EXPLANATION]='';
		$points = ($row['points'.$one_vote]) ? $row['points'.$one_vote] : $row['points'];
		if ($row["correct$one_vote"]) {
			$rowsArray[$i][OPT_POINTS] = $points;
			//$cids = ($i==0) ? $uid : "$cids,$uid";
		} else {
			$rowsArray[$i][OPT_POINTS] = ($no_negative==2) ? 0 : -1*$points;
			//$fids = ($i==0) ? $uid : "$fids,$uid";
		}
		//$qids = ($i==0) ? $uid : "$qids,$uid";
		$total_points += $rowsArray[$i][OPT_POINTS];
		if ( $row['explanation']!='' || $row['explanation1']!='' || $row['explanation2']!='' ) {	// Explanation
			if ($row['explanation1']!='') {			// Nur wenn das addon myquizpoll_expl2 installiert ist
				$rowsArray[$i][OPT_EXPLANATION] = ($one_vote && $row['explanation'.$one_vote]) ? $row['explanation'.$one_vote] : $row['explanation'];
			} else if ($row['explanation2']!='') {	// Nur wenn das addon myquizpoll_expl installiert ist
				$rowsArray[$i][OPT_EXPLANATION] = ($row["correct$one_vote"]) ? $row['explanation'] : $row['explanation2'];
			} else
				$rowsArray[$i][OPT_EXPLANATION] = $row['explanation'];
		}
		$usrArray[$one_vote]=1;
		//$rowsArray[0][OPT_EXPLANATION] .= " ";	// debug
		//$i++;
	}
	
	if (!$sids) {
		for ($i=1; $i<=$antworten; $i++) { // checken, ob alles korrekt beantwortet wurde
			if ($usrArray[$i] != $corArray[$i]) {
				$allCorrect=false;
			}
		}
		if ($allCorrect) $cids=$uid; else $fids=$uid;
		$qids = $uid;
	}
	
	if (!$rowsArray[0][OPT_EXPLANATION]) $rowsArray[0][OPT_EXPLANATION] = $row['explanation'];	// falls man keine Antwort gewaehlt hatte
	$GLOBALS['TYPO3_DB']->sql_free_result($queryResult);
	if ($total_points<0 && $no_negative==1) $total_points=0;
	if ($fids && $no_negative==3) $total_points=0;
	if ($no_negative==4) $total_points = ($fids) ? $total_points : 0;
	$rowsArray[0][OPT_TOTAL_POINTS] = $total_points;	// total points nur bei 0 !
	
	if (!$block) {
		if ($qtuid > 0) {
			// update
			$queryResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('p_or_a,p_max,percent,qids,cids,fids,sids',
				'tx_myquizpoll_result',
				'uid='.$qtuid);
			$row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult);
			$total_points += intval($row['p_or_a']);
			if ($row['cids']) 
			  $cids = ($cids) ? $row['cids'].','.$cids : $row['cids'];
			if ($row['fids']) 
			  $fids = ($fids) ? $row['fids'].','.$fids : $row['fids'];
			if ($row['sids']) 
			  $sids = ($sids) ? $row['sids'].','.$sids : $row['sids'];
			if ($row['qids']) 
			  $qids = ($qids) ? $row['qids'].','.$qids : $row['qids'];
			$p_max = intval($row['p_max']) + $p_max;
			$GLOBALS['TYPO3_DB']->sql_free_result($queryResult);
			
			$update = array('lasttime' => $timestamp,
							'p_or_a' => $total_points,
							'p_max' => $p_max,
							'percent' => 0,
							'qids' => $qids,
							'cids' => $cids,
							'fids' => $fids,
							'sids' => $sids);
			$success = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_myquizpoll_result', 'uid='.$qtuid.' AND sys_language_uid='.$lang, $update);
		} else {
			// insert
			$insert = array('pid' => $pid,
							'tstamp' => $timestamp,
							'crdate' => $timestamp,
							'firsttime' => $timestamp,
							'cruser_id' => intval($GLOBALS['TSFE']->fe_user->user['uid']),
							'sys_language_uid' => $lang,
							'hidden' => 0,
							'ip' => $ip,
							'name' => '???',
							'p_or_a' => $total_points,
							'p_max' => $p_max,
							'percent' => 0,
							'joker1' => $joker1,
							'joker2' => $joker2,
							'joker3' => $joker3,
							'qids' => $qids,
							'cids' => $cids,
							'fids' => $fids,
							'sids' => $sids);
			$success = $GLOBALS['TYPO3_DB']->exec_INSERTquery('tx_myquizpoll_result', $insert);
			if ($success)
				$rowsArray[0][OPT_QTID] = $GLOBALS['TYPO3_DB']->sql_insert_id();
		}
	}
	$rowsArray[0][OPT_ALL_POINTS] = $total_points;
  }

  print json_encode($rowsArray);
?>