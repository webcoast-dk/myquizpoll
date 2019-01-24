<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Kurt Gusbeth <info@myquizandpoll.de>
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

class tx_myquizpoll_helper {

	private $maximumPoints;		// Total maximum points of all questions of a quiz
	private $numberQuestions;	// Number of all questions
	private $maxPages;			// Number of all pages
	private $firsttime;			// Start time of the quiz
	private $pid;
	private $lang;
	private $where_lang;		// where-Abfrage Sprache
	private $answerChoiceMax;
	private $tableQuestions;
	private $tableAnswers;
	private $tableRelation;
	private $setQuestionsBool = false;
	private $settings;
	private $submitJsc;		// javascript
	private $cObj = null; // for making typo3 links
	public $writeDevLog = FALSE;	// ausnahmsweise public, wegen dem speed
	
  /**
    * Constructor
    * 
    * @param	int		$pid: PID
    * @param	int		$lang: lanugage-ID
    * @param	int		$answerChoiceMax: max no. of answers
    * @param	string	$tableQuestions: DB-table
    * @param	string	$tableAnswers: DB-table
    * @param	string	$tableRelation: DB-table
    * @param	array	$settings: is $conf
    */
	public function __construct($pid, $lang, $answerChoiceMax, $tableQuestions, $tableAnswers, $tableRelation, $settings) { 
		$this->pid 	= $pid;
		$this->lang = $lang;
		$this->answerChoiceMax	= $answerChoiceMax;
		$this->tableQuestions 	= $tableQuestions;
		$this->tableAnswers		= $tableAnswers;
		$this->tableRelation 	= $tableRelation;
		$this->settings 		= $settings;
		$this->submitJsc		= '';
		$this->cObj 			= \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer');
//		$this->typoVersion = t3lib_div::int_from_ver($GLOBALS['TYPO_VERSION']);
		if ($lang == 0)
			$this->where_lang = ' AND sys_language_uid IN (0, -1)';
		else
			$this->where_lang = ' AND sys_language_uid = ' . intval($lang);
	}	
	
  /**
    * Where für Sprach-Abfrage
    * 
    * @return	string	where-Abfrage
    */
	public function getWhereLang() {
		return $this->where_lang;
	}
	
  /**
    * Sets JavaScript text
    * 
    * @param	int	$no: number of the answer
    * @param	int	$answers: number of  answers
    * @param	int	$type: type of the question
    */
	function addEnforceJsc($no,$answers,$type) {
		switch ($type) {
			case 0:	$this->submitJsc .= "if (!quizcheck0(quizform,$no,$answers)){ quizerror($no); return false; }\n";
					break;
			case 1:	$this->submitJsc .= "if (!quizcheck1(quizform,$no,$answers)){ quizerror($no); return false; }\n";
					break;
			case 3:	$this->submitJsc .= "if (!quizcheck3(quizform,$no)){ quizerror($no); return false; }\n";
					break;
			case 4:	$this->submitJsc .= "if (!quizcheck4(quizform,$no,$answers)){ quizerror($no); return false; }\n";
					break;
		}
	}
	
  /**
    * Gets JavaScript text
    * 
    * @return	string	JS-text
    */
	function getSubmitJsc() {
		return $this->submitJsc;
	}
	
  /**
    * Sets the starttime of a page
    * 
    * @param	int	$uid: uid
    * @param	int	$time: unix-time
    */
	function setPageTime($uid, $time) {
		if ($uid && $this->settings['pageTimeSeconds'] && $this->settings['userSession']) {
			$GLOBALS['TSFE']->fe_user->setKey('ses','pagetime'.$uid, $time);
			$GLOBALS["TSFE"]->fe_user->storeSessionData();
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Session: pagetime='.$time, 'myquizpoll', 0);
		}
	}
	
  /**
    * Gets the starttime of a page
    * 
    * @param	int	$uid: uid
    * @return	int	unix-time
    */
	function getPageTime($uid) {
		if ($uid && $this->settings['pageTimeSeconds'] && $this->settings['userSession']) {
			return $GLOBALS['TSFE']->fe_user->getKey('ses','pagetime'.$uid);
		} else {
			return 0;
		}
	}

  /**
    * Sets the starttime of a quiz
    * 
    * @param	int	$uid: uid
    * @param	int	$time: unix-time
    */
	function setFirstTime($uid, $time) {
		$this->firsttime = $time;		
		if ($this->settings['userSession']) {
			$GLOBALS['TSFE']->fe_user->setKey('ses','firsttime'.$uid, $time);
			$GLOBALS["TSFE"]->fe_user->storeSessionData();
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Object and Session: firsttime='.$time, 'myquizpoll', 0);
		} else {
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Object: firsttime='.$time, 'myquizpoll', 0);
		}
	}
	
  /**
    * Gets the starttime of a quiz
    * 
    * @param	int	$uid: uid
    * @return	int	unix-time
    */
	function getFirstTime($uid) {
		if ($uid) {
			$time = $this->firsttime;
			if (!$time && $this->settings['userSession']) {
				$time = $GLOBALS['TSFE']->fe_user->getKey('ses','firsttime'.$uid);
			}
			if (!$time && !$this->settings['requireSession']) {
				$res6 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('firsttime',
					$this->tableAnswers,
					'uid=' . intval($uid) . $this->where_lang); //.' '.$this->cObj->enableFields($this->tableAnswers));
				$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res6);
				if ($rows>0) {							// DB entry found for current user?
					$fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res6);
					$time = $fetchedRow['firsttime'];
				}
				$GLOBALS['TYPO3_DB']->sql_free_result($res6);
				if ($this->settings['userSession']) {
					$GLOBALS['TSFE']->fe_user->setKey('ses','firsttime'.$uid, $time);
					$GLOBALS["TSFE"]->fe_user->storeSessionData();
					if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Session: firsttime='.$time, 'myquizpoll', 0);
				}
			}
			$this->firsttime = $time;
		} else {
			$time = 0;
		}
		return intval($time);
	}
	
  /**
    * Sets the start_uid of a quiz
    * 
    * @param	int	$uid: uid
    * @param	int	$start_uid: uid
    */
	function setStartUid($uid, $start_uid) {
		if ($uid && $this->settings['userSession']) {
			$GLOBALS['TSFE']->fe_user->setKey('ses','start_uid'.$uid, $start_uid);
			$GLOBALS["TSFE"]->fe_user->storeSessionData();
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Session: start_uid='.$start_uid, 'myquizpoll', 0);
		}
	}
	
  /**
    * Gets the start_uid of a quiz
    * 
    * @param	int	$uid: uid
    * @return	int	start_uid
    */
	function getStartUid($uid) {
		if ($uid && $this->settings['userSession']) {
			$start_uid = $GLOBALS['TSFE']->fe_user->getKey('ses','start_uid'.$uid);
		} else if ($uid) {
			$res6 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('start_uid',
				'tx_myquizpoll_result',
				'uid=' . intval($uid) . $this->where_lang); //.' '.$this->cObj->enableFields($this->tableAnswers));
			$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res6);
			if ($rows>0) {							// DB entry found for current user?
				$fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res6);
				$start_uid = $fetchedRow['start_uid'];
			}
			$GLOBALS['TYPO3_DB']->sql_free_result($res6);
		}
		if (!$start_uid) $start_uid = $GLOBALS['TSFE']->id;
		return intval($start_uid);
	}
	
	/**
	 * Get the page title
	 * 
	 * @param	int		$uid: uid einer seite
	 * @return	string	page title
	 */
	function getPageTitle($uid) {
		if ($uid == $GLOBALS["TSFE"]->id) return $GLOBALS["TSFE"]->page['title'];
		$OLmode = ($GLOBALS['TSFE']->config['config']['sys_language_mode'] == 'strict' ? 'hideNonTranslated' : '');	// oder $GLOBALS['TSFE']->sys_language_mode;
		$rowsP = $GLOBALS['TYPO3_DB']->exec_SELECTgetRows('*', 'pages', 'uid=' . intval($uid));
		$rowP = $rowsP[0];
		// TODO: get the translated record if the content language is not the default language. Funktioniert nicht!!!
		if ($this->lang > 0)
			$rowP = $GLOBALS['TSFE']->sys_page->getRecordOverlay('pages', $rowP, $this->lang, $OLmode);
		return $rowP['title'];
	}
	
  /**
    * Calculate the total maximum scores of all questions of a quiz and no. of all questions
    */
	function setQuestionsVars() {
		$maxPoints = 0;
		$numberQuestions = 0;
		$pages = 0;
		$whereCat = ( $this->settings['onlyCategories'] ) ? " AND category IN (".preg_replace('/[^0-9,]/','',$this->settings['onlyCategories']).")" : '';
				
		// Get all questions from the database
		$res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
				$this->tableQuestions,
				'pid IN ('.$this->pid.')' . $this->where_lang . ' ' . $this->cObj->enableFields($this->tableQuestions) . $whereCat);
		$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
		if ($rows>0) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){ 
				$numberQuestions++;
				if (!$this->settings['dontShowPoints']) {
					$points = 0;
					for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
						if ($row['answer'.$answerNumber] || $row['answer'.$answerNumber]==='0') {	// was a answer set in the backend?
							$answPoints = 0;
							if ($this->settings['noNegativePoints']<3)
								$answPoints = intval($row['points'.$answerNumber]);
							if ($answPoints > 0) {
								$row['correct'.$answerNumber] = true;		// ACHTUNG: falls Punkte zu einer Antwort gesetzt sind, dann wird die Antwort als RICHTIG bewertet!
							} else {
								$answPoints = intval($row['points']);
							}
							if ($row['correct'.$answerNumber] || $row['qtype']==3) {
								if (($row['qtype'] == 0 || $row['qtype'] == 4) && $this->settings['noNegativePoints']<3) {
									$points+=$answPoints;
								} else if ($answPoints > $points) {
									$points=$answPoints;	// bei punkten pro antwort ODER wenn nicht addiert werden soll
								}
							}
						}
					}
					$maxPoints+=$points;
				}
			}
			$maxqno = ($this->settings['finishAfterQuestions']) ? $this->settings['finishAfterQuestions'] : $rows;
			$pageqno = ($this->settings['pageQuestions']) ? $this->settings['pageQuestions'] : $maxqno;
			$pages = ceil($maxqno/$pageqno);
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($res5);
		
		// doppelt speichern (falls jemand cookies nicht mag)
		$this->maximumPoints = intval($maxPoints);
		$this->numberQuestions = intval($numberQuestions);
		$this->maxPages = intval($pages);
		if ($this->settings['userSession']) {
			$GLOBALS['TSFE']->fe_user->setKey('ses','maximumPoints', (string) $this->maximumPoints);
			$GLOBALS['TSFE']->fe_user->setKey('ses','numberQuestions', (string) $this->numberQuestions); // Bug. Klappt nur, wenn man die Zahl direkt Ã¼bergibt
			$GLOBALS['TSFE']->fe_user->setKey('ses','maxPages', (string) $this->maxPages);
			//$GLOBALS["TSFE"]->fe_user->sesData_change = true;
			$GLOBALS["TSFE"]->fe_user->storeSessionData();
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Storing in Object and Session: maximumPoints=$maxPoints, numberQuestions=$numberQuestions, maxPages=$pages", 'myquizpoll', 0);
		} else {
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Storing in Object: maximumPoints=$maxPoints, numberQuestions=$numberQuestions, maxPages=$pages", 'myquizpoll', 0);
		}
		$this->setQuestionsBool = true;
    }
		
		
  /**
    * Returns the max. points of all questions
	*
	* @return	int	max. points
    */
	function getQuestionsMaxPoints() {
		if ($this->settings['userSession'])
			$max = intval($GLOBALS['TSFE']->fe_user->getKey('ses','maximumPoints'));
		else
			$max = 0;
		if (!$max && $this->setQuestionsBool)			// wenn keine cookies erlaubt sind...
			$max = $this->maximumPoints;
		else if (!$max && !$this->setQuestionsBool) {	// wenn setQuestionsVars noch nicht aufgerufen wurde
			$this->setQuestionsVars();
			$max = $this->maximumPoints;
		}
		if (!$max) $max = 0;						// wenn alles umsonst war...
		if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Loading: maximumPoints=" . $max, 'myquizpoll', 0);
		return $max;
	}
	
  /**
    * Returns how many questios are there in the DB
	*
	* @return	int	no. of questions
    */
	function getQuestionsNo() {
		if ($this->settings['userSession'])
			$no = intval($GLOBALS['TSFE']->fe_user->getKey('ses','numberQuestions'));
		else
			$no = 0;
		if (!$no && $this->setQuestionsBool)			// wenn keine cookies erlaubt sind...
			$no = $this->numberQuestions;
		else if (!$no && !$this->setQuestionsBool) {	// wenn setQuestionsVars noch nicht aufgerufen wurde
			$this->setQuestionsVars();
			$no = $this->numberQuestions;
		}
		if (!$no) $no = 0;							// wenn alles umsonst war...
		if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Loading: numberQuestions=" . $no, 'myquizpoll', 0);
		return $no;
	}
	
  /**
    * Returns how many questions have been answered yet
	*
	* @param	string	list of answered question
	* @return	int		no. of questions
    */
	function getQuestionNo($answered) {
		if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Seeking question no. for: '.$answered, 'myquizpoll', 0);
		if ($answered=='-') {
			if ($this->settings['userSession'])
				$no = $GLOBALS['TSFE']->fe_user->getKey('ses','numberQuestion');
			else $no = 0;		// unknown
			if ($no==='') $no = 0;	// unknown
			if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Loading: numberQuestion=" . $no, 'myquizpoll', 0);
		} else if (!$answered) {
			$no = 0;
			if ($this->settings['userSession']) {
				$GLOBALS['TSFE']->fe_user->setKey('ses','numberQuestion', $no);
				$GLOBALS["TSFE"]->fe_user->storeSessionData();
				if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Session: numberQuestion='.$no, 'myquizpoll', 0);
			}
		} else {		// mind. 1 Frage wurde beantwortet oder uebersprungen!
			$no = substr_count($answered, ',') + 1;
			if ($this->settings['userSession']) {
				$GLOBALS['TSFE']->fe_user->setKey('ses','numberQuestion', $no);
				$GLOBALS["TSFE"]->fe_user->storeSessionData();
				if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing in Session: numberQuestion='.$no, 'myquizpoll', 0);
			}
		}
		return $no;
	}

  /**
    * Returns the current page number
	*
	* @return	int	page no.
    */
	function getPage($questTillNow) {
		$pageqno = ($this->settings['pageQuestions']) ? $this->settings['pageQuestions'] : 1000;
		$page = floor($questTillNow/$pageqno);
		return $page+1;
	}

  /**
    * Returns the number of all pages
	*
	* @return	int	max. pages
    */
	function getMaxPages() {
		if ($this->settings['userSession'])
			$max = intval($GLOBALS['TSFE']->fe_user->getKey('ses','maxPages'));
		else
			$max = 0;
		if (!$max && $this->setQuestionsBool)			// wenn keine cookies erlaubt sind...
			$max = $this->maxPages;
		else if (!$max && !$this->setQuestionsBool) {	// wenn setQuestionsVars noch nicht aufgerufen wurde
			$this->setQuestionsVars();
			$max = $this->maxPages;
		}
		if (!$max) $max = 0;	// wenn alles umsonst war...
		if ($this->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Loading: maxPages=" . $max, 'myquizpoll', 0);
		return $max;
	}
	
  /**
    * Returns an image-tag for a question
	*
	* @param	string	$image: image-name
	* @param	string	$alt_text: alt. text
	* @param	boolean	$for_email: for email?
	* @return	string	image tag
    */
	function getImage($image, $alt_text, $for_email = false) {
		$imgTSConfig['file'] = 'uploads/tx_myquizpoll/'.$image; //The image field name
		$imgTSConfig['altText'] = $alt_text;
		$imgTSConfig['titleText'] = $alt_text;
		$vorher = $GLOBALS['TSFE']->absRefPrefix;
		if ($for_email && !$GLOBALS['TSFE']->absRefPrefix)	// im email mode braucht man auch die domain
			$GLOBALS['TSFE']->absRefPrefix = \TYPO3\CMS\Core\Utility\GeneralUtility::getIndpEnv('TYPO3_SITE_URL');
		if (is_array($this->settings['images.']))
			$imgTSConfig['file.'] = $this->settings['images.'];
		$bild = $this->cObj->IMAGE($imgTSConfig);
		$GLOBALS['TSFE']->absRefPrefix = $vorher;
		return $bild;
	}
	
  /**
    * Returns the current foreign ID
	*
	* @return	mixed	foreign ID as string or integer
    */
	function getForeignId() {
		$foreign_id = '';
		if ($this->settings['rating.']['extKey']) {
			$temp = $this->settings['rating.']['extKey'];
			$foreignExt = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP($temp);
			$temp = $this->settings['rating.']['parameter'];
			$foreign_id = $foreignExt[$temp];
		} else {
			$temp = $this->settings['rating.']['parameter'];
			$foreign_id = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP($temp);
			if ($temp == 'id' && !$foreign_id)	// 30.3.12: bugfix. Problem bleibt: text-ids wie "home"
				$foreign_id = $GLOBALS['TSFE']->id;
		}
		return $foreign_id;
	}
	
	/**
	 * Function makePlain() removes html tags and add linebreaks
	 * 		Easy generate a plain email bodytext from a html bodytext
	 *
	 * @param	string		$content: HTML Mail bodytext
	 * @return	string		$content: Plain Mail bodytext
	 */
	private function makePlain($content) {
		// config
		$htmltagarray = array ( // This tags will be added with linebreaks
			'</p>',
			'</tr>',
			'</li>',
			'</h1>',
			'</h2>',
			'</h3>',
			'</h4>',
			'</h5>',
			'</h6>',
			'</div>',
			'</legend>',
			'</fieldset>',
			'</dd>',
			'</dt>'
		);
		$notallowed = array ( // This array contains not allowed signs which will be removed
			'&nbsp;',
			'&szlig;',
			'&Uuml;',
			'&uuml;',
			'&Ouml;',
			'&ouml;',
			'&Auml;',
			'&auml;',
		);

		// let's go
		$content = nl2br($content);
		$content = str_replace($htmltagarray, $htmltagarray[0] . '<br />', $content); // 1. add linebreaks on some parts (</p> => </p><br />)
		$content = strip_tags($content, '<br><address>'); // 2. remove all tags but not linebreaks and address (<b>bla</b><br /> => bla<br />)
		$content = preg_replace('/\s+/', ' ', $content); // 3. removes tabs and whitespaces
		$content = $this->br2nl($content); // 4. <br /> to \n
		$content = implode("\n", \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode("\n", $content)); // 5. explode and trim each line and implode again (" bla \n blabla " => "bla\nbla")
		$content = str_replace($notallowed, '', $content); // 6. remove not allowed signs

		return $content;
	}

	/**
	 * Function br2nl is the opposite of nl2br
	 *
	 * @param	string		$content: Anystring
	 * @return	string		$content: Manipulated string
	 */
	private function br2nl($content) {
		$array = array(
			'<br >',
			'<br>',
			'<br/>',
			'<br />'
		);
		$content = str_replace($array, "\n", $content); // replacer

		return $content;
	}
	
  /**
    * Send email to...
	*
	* @param	string	$html_content: html-content
	* @param	string	$text_content: text-content. wird nicht mehr benutzt.
	* @param	string	$to_mail: email
	* @param	string	$to_name: name
	* @param	string	$to_subject: subject
    */
	function sendEmail($html_content,$text_content,$to_mail,$to_name,$to_subject) {
		$version = class_exists('t3lib_utility_VersionNumber') ?
			t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version) : \TYPO3\CMS\Core\Utility\VersionNumberUtility::convertVersionNumberToInteger(TYPO3_version);
		if ($version >= 4005000) {
			$mail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('TYPO3\CMS\Core\Mail\MailMessage');
			$mail->setFrom(array($this->settings['email.']['from_mail'] => $this->settings['email.']['from_name']))
			  ->setTo(array($to_mail => $to_name))
			  ->setSubject($to_subject)
			  ->setBody($html_content, 'text/html')
			  ->addPart($this->makePlain($html_content), 'text/plain')
			  ->send();
		} else {
			require_once(PATH_t3lib.'class.t3lib_htmlmail.php');		
			$htmlMail = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('t3lib_htmlmail');
			$htmlMail->start();
			$htmlMail->recipient = $to_mail;
			$htmlMail->replyto_email = $this->settings['email.']['from_mail'];
			$htmlMail->replyto_name = $this->settings['email.']['from_name'];
			$htmlMail->from_email = $this->settings['email.']['from_mail'];
			$htmlMail->from_name = $this->settings['email.']['from_name'];
			$htmlMail->returnPath = $this->settings['email.']['from_mail'];
			$htmlMail->subject = $to_subject;
			$htmlMail->addPlain($this->makePlain($html_content));
			$htmlMail->setHTML($htmlMail->encodeMsg($html_content));
			$htmlMail->send($to_mail);
		}
	}
	
  /**
    * Get the real IP address
	*
	* @return	string	IP address
    */
	function getRealIpAddr() {
		if ($this->settings['disableIp']) {
		  $ip=0;
		} elseif ($this->settings['remoteIP']) {
	      $ip=$_SERVER['REMOTE_ADDR'];
		} elseif (!empty($_SERVER['HTTP_CLIENT_IP'])) {   //check ip from share internet
	      $ip=$_SERVER['HTTP_CLIENT_IP'];
	    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {  //to check ip is pass from proxy
	      $ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
	    } else {
	      $ip=$_SERVER['REMOTE_ADDR'];	// oder t3lib_div::getIndpEnv("REMOTE_ADDR");	// Collect ip address of quiz taker
	    }
	    return $ip;
	}

   /**
    * Set a myVars array for whole page
	*
	* @return	array	myVars array
    */
	function setPageVars() {
		$result = array();
		if (is_array($this->settings['myVars.']['page.'])) {
			foreach ($this->settings['myVars.']['page.'] as $key => $value) {
				if ($value) {
					$result["###MY_".strtoupper($key)."###"] = $value;
					//if ($this->writeDevLog) t3lib_div::devLog("MY page: $key=$value", 'myquizpoll', 0);
				}
			}
		}
		return $result;
	}
	
   /**
    * Set a myVars array for questions
	*
	* @param	int		$nr: number of the question
	* @return	array	myVars array
    */
	function setQuestionVars($nr) {
		$result = array();
		if (is_array($this->settings['myVars.']['questions.'])) {
			foreach ($this->settings['myVars.']['questions.'] as $key => $value) {
				if ($value) {
					$myQArray = explode($this->settings['myVars.']['separator'], $value);
					$myKey = ($nr-1)%count($myQArray);
					$result["###MY_".strtoupper($key)."###"] = $myQArray[$myKey];
					//if ($this->writeDevLog) t3lib_div::devLog("MY question: $key=".$myQArray[$myKey]." ($value)", 'myquizpoll', 0);
				}
			}
		}
		return $result;
	}
	
   /**
    * Set a myVars array for answers
	*
	* @param	int		$nr: number of the answer
	* @param	int		$qtype: question type
	* @return	array	myVars array
    */
	function setAnswerVars($nr, $qtype) {
		$result = array();
		if (is_array($this->settings['myVars.']['answers.']) && ($qtype != 2 || $nr == 1)) {
			foreach($this->settings['myVars.']['answers.'] as $key => $value) {
				if ($value) {
					$myAArray = explode($this->settings['myVars.']['separator'], $value);
					$myKey = ($nr-1)%count($myAArray);
					$result["###MY_".strtoupper($key)."###"] = $myAArray[$myKey];
					//if ($this->writeDevLog) t3lib_div::devLog("MY answer: $key=".$myAArray[$myKey]." ($value)", 'myquizpoll', 0);
				}
			}
		}
		return $result;
	}
	
   /**
    * Get: ask for user data at question?
	*
	* @param	int		$qtuid: quiz taker uid
	* @return	boolean
    */
	function getAskAtQ($qtuid) {
		return (($this->settings['userData.']['askAtQuestion']=='1') ||
				($this->settings['userData.']['askAtQuestion']=='2' && !$qtuid));
	}
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/pi1/class.tx_myquizpoll_helper.php'])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/pi1/class.tx_myquizpoll_helper.php']);
}
?>