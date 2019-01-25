<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Kurt Gusbeth <info@quizpalme.de>
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

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\Resource\FilePathSanitizer;

require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('myquizpoll').'pi1/class.tx_myquizpoll_helper.php'); // PATH_BE_myquizpoll

/**
 * Plugin 'My quiz and poll' for the 'myquizpoll' extension.
 *
 * @author    Kurt Gusbeth <info@quizpalme.de>
 * @package    TYPO3
 * @subpackage    tx_myquizpoll
 */
class tx_myquizpoll_pi1 extends \TYPO3\CMS\Frontend\Plugin\AbstractPlugin {
    public $prefixId      = 'tx_myquizpoll_pi1';                // Same as class name
    public $scriptRelPath = 'pi1/class.tx_myquizpoll_pi1.php';    // Path to this script relative to the extension directory
    public $extKey        = 'myquizpoll';                        // The extension key.
    public $conf = array();
    public $answerChoiceMax = 6;                                // This max cannot be changed without changing the TCA (and DB)!
    public $lang = 0;                                            // language
    public $textType = array(3,5);                                // text-questions-types
    public $templateCode = '';
    public $origTemplateCode = '';
    public $tableQuestions = 'tx_myquizpoll_question';
    public $tableAnswers   = '';    // wird später entschieden
    public $tableRelation  = 'tx_myquizpoll_relation';
    public $tableCategory  = 'tx_myquizpoll_category';
    public $xajax;
    public $helperObj;

    /**
     * Main method of your PlugIn
     *
     * @param    string    $content: The content of the PlugIn
     * @param    array    $conf: The PlugIn Configuration
     * @return    string    The content that should be displayed on the website
     */
    function main($content,$conf) {
        $this->conf=$conf;
        $this->pi_setPiVarDefaults();
        $this->pi_initPIflexForm(); // Init FlexForm configuration for plugin
        $this->pi_loadLL();
        $this->pi_USER_INT_obj=1;    // Configuring so caching is not expected. This value means that no cHash params are ever set. We do this, because it's a USER_INT object!
        $thePID = 0;                // PID of the sysfolder with questions
        $resPID = 0;                // PID of the sysfolder with results
        $nextPID = 0;                // PID for forms
        $finalPID = 0;                // PID for the final page
        $listPID = 0;                // PID for highscore or poll result
        $startPID = 0;                // PID of the startpage
        $uid = 0;                    // quiz taker UID?!
        //$firsttime = 0;            // start time of the quiz
        $elapseTime = 0;            // intval($this->conf['quizTimeMinutes'])*60;            // Verflossene Zeit in Sekunden
        $joker1 = 0;                // Joker used?
        $joker2 = 0;
        $joker3 = 0;
        $startPage = false;            // start page which asks only for user data?
        $questionPage = false;        // question page?
        $answerPage = false;        // answer page?
        $finalPage = false;            // final page reached?
        $noQuestions = false;        // no more questions?
        $secondVisit = false;        // quiz already solved?
        $sendMail = false;            // email should be send?
        $error = false;                // was there an error?
        $nextCat = '';                // global next category
        $lastCat = '';				// default last category
        $catArray = array();        // array with category names
        $oldLoaded = false;			// old data loaded

        // global $TSFE;
        $this->lang = intval($GLOBALS['TSFE']->config['config']['sys_language_uid']);
        $this->copyFlex();            // copy Felxform-Variables to this->conf
        $this->tableAnswers = ($this->conf['tableAnswers']=='tx_myquizpoll_voting') ? 'tx_myquizpoll_voting' : 'tx_myquizpoll_result';

        if ( $this->conf['enableCaptcha'] && \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::isLoaded('sr_freecap') ) {        // load Captcha: Anti-Spam-Tool ??? only if enabled (16.10.2009)
            require_once(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('sr_freecap').'pi2/class.tx_srfreecap_pi2.php');
            $this->freeCap = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_srfreecap_pi2');
        }

        // TODO: Rekursiv-Flag berücksichtigen!
        if( !($this->cObj->data['pages'] == '') ) {        // PID (eine oder mehrere)
            $thePID = $this->cObj->data['pages'];
        } elseif( !($this->conf['sysPID'] == '') ) {
            $thePID = preg_replace('/[^0-9,]/','',$this->conf['sysPID']);
        } else {
            $thePID = $GLOBALS["TSFE"]->id;
        }
        $resPID = ($this->conf['resultsPID']) ? intval($this->conf['resultsPID']) : $thePID;
        $resPIDs = preg_replace('/[^0-9,]/','',$resPID);    // für den Highscore werden ggf. alle PIDs gebraucht
        if (strstr($resPID, ',')) {    // wenn mehrere Ordner ausgewählt, nimm den ersten
            $tmp = explode(",", $resPID);
            $resPID = intval(trim($tmp[0]));
        }
        $nextPID  = ($this->conf['nextPID'])  ? intval($this->conf['nextPID'])  : $GLOBALS['TSFE']->id;
        $finalPID = ($this->conf['finalPID']) ? intval($this->conf['finalPID']) : $GLOBALS['TSFE']->id;    // oder $nextPID;
        $listPID  = ($this->conf['listPID'])  ? intval($this->conf['listPID'])  : $GLOBALS['TSFE']->id;    // oder $finalPID;
        $startPID = intval($this->conf['startPID']);

        if ($this->conf['answerChoiceMax'])                // antworten pro fragen
            $this->answerChoiceMax = intval($this->conf['answerChoiceMax']);
        if (!$this->conf['myVars.']['separator'])        // separator bei den myVars
             $this->conf['myVars.']['separator'] = ',';

        mt_srand(hexdec(substr(md5(microtime()), -8)) & 0x7fffffff);  // Seed random number generator
        //$this->local_cObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance("tslib_cObj");    // Local cObj

        // Get post parameters: Submited Quiz-data
        if ($this->conf['CMD']=='archive')
        	$this->conf['ignoreSubmit']=true;	// submits in diesen Fällen igonieren
        $quizData = array();
        if (is_array(\TYPO3\CMS\Core\Utility\GeneralUtility::_GP($this->prefixId)) && !$this->conf['ignoreSubmit']) {
          if (is_array(\TYPO3\CMS\Core\Utility\GeneralUtility::_POST($this->prefixId)))
            $quizData = \TYPO3\CMS\Core\Utility\GeneralUtility::_POST($this->prefixId);
          else
            $quizData = \TYPO3\CMS\Core\Utility\GeneralUtility::_GET($this->prefixId);
          //$quizData = \TYPO3\CMS\Core\Utility\GeneralUtility::slashArray(\TYPO3\CMS\Core\Utility\GeneralUtility::GPvar($this->prefixId),"strip"); // deprecated
          if ($quizData['cmd']=='') {
            $quizData['cmd'] = $this->conf['CMD'];
          } elseif ($quizData['cmd']=='allanswers') {    // or $quizData['cmd']=='score' or $quizData['cmd']=='list'
            $quizData['cmd'] = '';                        // for security reasons we want that users can´t see everything
          }
        } else {
          $quizData['cmd'] = $this->conf['CMD'];        // get the CMD from the backend
        }
        if ($quizData["name"]) $quizData["name"] = htmlspecialchars($quizData["name"]);
        if ($quizData["email"]) $quizData["email"] = htmlspecialchars($quizData["email"]);
        if ($quizData["homepage"]) $quizData["homepage"] = htmlspecialchars($quizData["homepage"]);

        // Zurück navigieren?
        $back=intval($quizData["back"]);
        $back_hit=intval($quizData["back-hit"]);
        if ($back_hit) $quizData['cmd']='';
        $seite=0;
        if ($this->tableAnswers=='tx_myquizpoll_voting') $this->conf['allowBack']=0;

        // Load template
        $tempPath = $this->initTemplate();

        // Marker values
        $statisticsArray=array();
        $markerArray=array();
        $subpartArray=array();
        $wrappedSubpartArray=array();
        $markerArrayP=array();
        $markerArrayQ=array();
        $markerArrayP["###REF_HIGHSCORE###"] = '';
        $markerArrayP["###REF_HIGHSCORE_URL###"] = '';
        $markerArrayP["###REF_POLLRESULT_URL###"] = '';
        $markerArrayP["###REF_QUIZ_ANALYSIS###"] = '';
        $markerArrayP["###REF_NO_MORE###"] = '';
        $markerArrayP["###REF_ERRORS###"] = '';
        $markerArrayP["###REF_RES_ERRORS###"] = '';
        $markerArrayP["###REF_JOKERS###"] = '';
        $markerArrayP["###REF_QUESTIONS###"] = '';
        $markerArrayP["###REF_QRESULT###"] = '';
        $markerArrayP["###REF_INTRODUCTION###"] = '';
        $markerArrayP["###REF_QPOINTS###"] = '';
        $markerArrayP["###REF_SKIPPED###"] = '';
        $markerArrayP["###REF_NEXT###"] = '';
        $markerArrayP["###REF_POLLRESULT###"] = '';
        $markerArrayP["###SUBMIT_JSC###"] = '';
        $markerArrayP["###PREFIX###"] = $this->prefixId;
        $markerArrayP["###FORM_URL###"] = $this->pi_getPageLink($nextPID);
        $markerArrayP["###NO_NEGATIVE###"] = intval($this->conf['noNegativePoints']);
        $markerArrayP["###REMOTE_IP###"] = intval($this->conf['remoteIP']);
        $markerArrayP["###BLOCK_IP###"] = $this->conf['blockIP'];
        $markerArrayQ["###PREFIX###"] = $this->prefixId;
        $markerArray["###PREFIX###"] = $this->prefixId;
        $markerArray["###FORM_URL###"] = $markerArrayP["###FORM_URL###"];
        $markerArrayP["###VAR_RESPID###"] = $resPID;
        $markerArrayP["###VAR_LANG###"] = $this->lang;
        $markerArrayP["###VAR_NOW###"] = $markerArray["###VAR_NOW###"] = time() + 1;  // kleiner Zeitvorsprung (Seite muss ja geladen werden)
        $markerArray["###NAME###"] = $this->pi_getLL('name','name');
        $markerArray["###EMAIL###"] = $this->pi_getLL('email','email');
        $markerArray["###HOMEPAGE###"] = $this->pi_getLL('homepage','homepage');
        $markerArray["###GO_ON###"] = $this->pi_getLL('go_on','go_on');
        $markerArray["###SUBMIT###"] = $this->pi_getLL('submit','submit');
        $markerArray["###RESET###"] = $this->pi_getLL('reset','reset');
        $markerArray["###GO_BACK###"] = $this->pi_getLL('back','back');
        $markerArray["###CORRECT_ANSWERS###"] = $this->pi_getLL('correct_answers','correct_answers');
        $markerArray["###EXPLANATION###"] = $this->pi_getLL('listFieldHeader_explanation', 'listFieldHeader_explanation');
        //$markerArray["###VAR_ADDRESS_UID###"] = $quizData["address_uid"] = 0;
        $markerArray["###VAR_CATEGORY###"] = '';
        $markerArray["###VAR_NEXT_CATEGORY###"] = '';
        $markerArray["###VAR_TOTAL_POINTS###"] = '';
        $markerArray["###VAR_QUESTIONS_CORRECT###"] = '';
        $markerArray["###VAR_QUESTIONS_FALSE###"] = '';
        $markerArray["###VAR_QUESTIONS_ANSWERED###"] = 0;
        //if ($this->conf['enforceSelection']) {
            $markerArrayP["###QUESTION###"] = $this->pi_getLL('listFieldHeader_name','listFieldHeader_name');
            $markerArrayP["###MISSING_ANSWER###"] = $this->pi_getLL('missing_answer','missing_answer');
        //}
        if ($this->conf['pageTimeSeconds'] || $this->conf['quizTimeMinutes']) {
            $markerArray["###TIME_UP1###"] = $this->pi_getLL('time_up1','time_up1');
            $markerArray["###LIMIT1A###"] = $this->pi_getLL('limit1a','limit1a');
            $markerArray["###LIMIT1B###"] = $this->pi_getLL('limit1b','limit1b');
            $markerArray["###TIME_UP2###"] = $this->pi_getLL('time_up2','time_up2');
            $markerArray["###LIMIT2A###"] = $this->pi_getLL('limit2a','limit2a');
            $markerArray["###LIMIT2B###"] = $this->pi_getLL('limit2b','limit2b');
            $markerArray["###SECONDS###"] = $this->pi_getLL('seconds','seconds');
            $markerArray["###MINUTES###"] = $this->pi_getLL('minutes','minutes');
        }
        // Link to the Highscore list
          $urlParameters = array("tx_myquizpoll_pi1[cmd]" => "score", "tx_myquizpoll_pi1[qtuid]" => intval($quizData['qtuid']), "no_cache" => "1");
        $markerArray["###HIGHSCORE_URL###"] = $this->pi_linkToPage($this->pi_getLL('highscore_url','highscore_url'), $listPID, $target = '', $urlParameters);

        // Jokers and Details
        if (($this->conf['useJokers'] && $this->conf['pageQuestions']==1) || $this->conf['showDetailAnswers']) {
            /*
            *  Instantiate the xajax object and configure it
            */
            require_once (\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath('xajax') . 'class.tx_xajax.php');        // Include xaJax
            $this->xajax = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_xajax'); // Make the instance
            # $this->xajax->setRequestURI('xxx');         // nothing to set, we send to the same URI
            # $this->xajax->decodeUTF8InputOn(); // Decode form vars from utf8 ???
            # $this->xajax->setCharEncoding('utf-8'); // Encode of the response to utf-8 ???
            $this->xajax->setWrapperPrefix($this->prefixId); // To prevent conflicts, prepend the extension prefix
            $this->xajax->statusMessagesOff(); // messages in the status bar?
            $this->xajax->debugOff(); // Turn only on during testing
            if ($this->conf['useJokers'])
                $this->xajax->registerFunction(array('getAjaxData', &$this, 'getAjaxData')); // Register the names of the PHP functions you want to be able to call through xajax - $xajax->registerFunction(array('functionNameInJavascript', &$object, 'methodName'));
            if ($this->conf['showDetailAnswers'])
                $this->xajax->registerFunction(array('getAjaxDetails', &$this, 'getAjaxDetails'));
            $this->xajax->processRequests();// If this is an xajax request, call our registered function, send output and exit
            $GLOBALS['TSFE']->additionalHeaderData[$this->prefixId.'_2'] = $this->xajax->getJavascript(\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::siteRelPath('xajax'));// Else create javascript and add it to the header output

            $markerArrayJ=array();
            $markerArrayJ["###PREFIX###"] = $this->prefixId;
            $markerArrayJ["###USE_JOKERS###"] = $this->pi_getLL('use_jokers','use_jokers');
            $markerArrayJ["###ANSWER_JOKER###"] = $this->pi_getLL('answer_joker','answer_joker');
        }

        // Init
        $no_rights = 0;            // second entry of the quiz taker or not logged in if requiered?
        $captchaError = false;    // wrong Captcha?
//        $leftQuestions = 0;        // no. of questions to be shown
        $whereAnswered = '';    // questions that have been allready answered (UIDs)
        $whereSkipped = '';        // questions that have been skipped (UIDs)
        $content = '';            // content to be shown

        $this->helperObj = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance('tx_myquizpoll_helper',
            $thePID,
            $this->lang,
            $this->answerChoiceMax,
            $this->tableQuestions,
            $this->tableAnswers,
            $this->tableRelation,
            $this->conf
        );

        // enable dev logging if set
        if (TYPO3_DLOG || $this->conf['debug']) {
            $this->helperObj->writeDevLog = TRUE;
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('UID: '.$this->cObj->data['uid'].'; language: '.$this->lang.'; use cookies: '.$this->conf['useCookiesInDays'].'; use ip-check: '.$this->conf['doubleEntryCheck'].'; path to template: '.$tempPath, $this->extKey, 0);
        }

        // set some session-variables
        if ((!$this->conf['isPoll']) && ($quizData['cmd']=='') && (!$quizData['qtuid']))
            $this->helperObj->setQuestionsVars();

        // what to display?
        switch ($quizData['cmd']) {
        	case 'archive':
        		if ($this->conf['isPoll']) {
        			/* Display only a list of old polls	*/
        			return $this->pi_wrapInBaseClass( $this->showPollArchive( $listPID,$thePID,$resPID ) );
        		}
        	case 'list':
        		if (is_numeric($quizData['qid'])) {
        			/* Display an old poll	*/
        			return $this->pi_wrapInBaseClass( $this->showPollResult('', $quizData, $thePID,$resPID) );
        		}
        	// Andere Fälle später entscheiden
        }



        // get the startPID of a solved quiz
        if (!$startPID) $startPID = $this->helperObj->getStartUid($quizData['qtuid']);
        $quiz_name = $this->conf['quizName'];
        if (!$quiz_name) {
            $quiz_name = $this->helperObj->getPageTitle($startPID);
        }
        $markerArrayP["###QUIZ_NAME###"] = $markerArray["###QUIZ_NAME###"] = $quiz_name;

        //if ($this->conf['startCategory']) {        // Kategorie-Namen zwischenspeichern
            $res6 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,name,celement,pagetime',
                    $this->tableCategory,
                    'pid IN ('.$thePID.')');
            $catCount = $GLOBALS['TYPO3_DB']->sql_num_rows($res6);
            if ($catCount>0) {
                while($row6 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res6)){
                    $catUID = $row6['uid'];
                    $this->catArray[$catUID] = array();
                    $this->catArray[$catUID]['name'] = $row6['name'];
                    $this->catArray[$catUID]['celement'] = $row6['celement'];
                    $this->catArray[$catUID]['pagetime'] = $row6['pagetime'];
                }
                if ($this->helperObj->writeDevLog)
                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($catCount.' categories found.', $this->extKey, 0);
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res6);
        //}

        // check, if logged in
        if ( $this->conf['loggedInCheck'] && ($quizData['cmd']!='score' && $quizData['cmd']!='list') && !$GLOBALS['TSFE']->loginUser ) {
            $no_rights = 1;                    // noname user is (b)locked now
            $markerArray["###NOT_LOGGEDIN###"] = $this->pi_getLL('not_loggedin','not_loggedin');
            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_NOT_LOGGEDIN###");
            $content .= $this->templateService->substituteMarkerArray($template, $markerArray);      // Sonderfall !!!
            if ($this->helperObj->writeDevLog)
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Loggin check failes!', $this->extKey, 0);
        }

        $quiz_taker_ip_address = preg_replace('/[^0-9\.]/', '', $this->helperObj->getRealIpAddr());

        // Ignore all sumbits and old data?
        if (!$this->conf['ignoreSubmit']) {
	        // check for second entry ( based on the ip-address )
	        if ( $this->conf['doubleEntryCheck'] && ($quizData['cmd']!='score' && $quizData['cmd']!='list') && !$quizData['qtuid'] && $no_rights == 0 ) {
	            $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'tstamp, uid',
	                $this->tableAnswers,
	                'pid=' . $resPID . " AND ip='" . $quiz_taker_ip_address . "'" .  $this->helperObj->getWhereLang(),
	                //.' '.$this->cObj->enableFields($this->tableAnswers), auskommentiert am 7.11.10
	                '',
	                'tstamp DESC',
	                '1');
	            $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
	            if ($rows>0) {                            // DB entry found for current user?
	                $fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5);
	                $dateOld = $fetchedRow['tstamp'];
					if ($this->helperObj->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Entry found for IP '.$quiz_taker_ip_address.': '.$fetchedRow['uid'], $this->extKey, 0);
	                $period = intval($this->conf['doubleEntryCheck']);    // seconds
	                if ($period < 10000) $period *= 60*60*24;        // days
	                //if ($period==1) $period = 50000;        // approx. a half day is the quiz blocked for the same ip-address
	                if ((time() - $dateOld) < $period) {
	                    if ( $this->conf['doubleCheckMode'] || $this->conf['secondPollMode'] ) {
	                        $quizData['qtuid'] = intval($fetchedRow['uid']);
	                        $quizData['cmd']  = 'next';
	                        $quizData['secondVisit']  = 1;
	                        $secondVisit = true;
	                        if ($this->helperObj->writeDevLog)
	                            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('IP-check: cmd to next changed, because doubleCheckMode='.$this->conf['doubleCheckMode'].', secondPollMode='.$this->conf['secondPollMode'], $this->extKey, 0);
	                    } else {
	                        $no_rights = 1;                        // user is (b)locked now
	                        $markerArray["###DOUBLE_ENTRY###"] = $this->pi_getLL('double_entry','double_entry');
	                        $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_DOUBLE_ENTRY###");
	                        $content .= $this->templateService->substituteMarkerArray($template, $markerArray);         // Sonderfall !!!
	                        if ($this->helperObj->writeDevLog)
	                            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('User is blocked (ip-check), because doubleCheckMode='.$this->conf['doubleCheckMode'].', secondPollMode='.$this->conf['secondPollMode'], $this->extKey, 0);
	                    }
	                }
	                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
	                //$oldLoaded = true;
	            }
	            if ($this->helperObj->writeDevLog)
	                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('IP check for qtuid='.$quizData['qtuid'], $this->extKey, 0);
	        }

	        // check for second entry ( based on the fe_users-id )
	        if ( $this->conf['loggedInMode'] && ($quizData['cmd']!='score' && $quizData['cmd']!='list') && !$quizData['qtuid'] && $GLOBALS['TSFE']->loginUser && $this->tableAnswers=='tx_myquizpoll_result' && $no_rights == 0 ) {
	            $fe_uid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
	            $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery( 'uid, tstamp',
	                $this->tableAnswers,
	                'pid=' . $resPID . ' AND fe_uid=' . $fe_uid . $this->helperObj->getWhereLang(),
	                //.' '.$this->cObj->enableFields($this->tableAnswers), auskommentiert am 7.11.10
	                '',
	                'tstamp DESC',
	                '1');
	            $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
	            if ($rows>0) {                            // DB entry found for current user?
	                $fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5);
	                if ( $this->conf['doubleCheckMode'] || $this->conf['secondPollMode'] ) {
	                    $quizData['qtuid'] = intval($fetchedRow['uid']);
	                    $quizData['cmd']  = 'next';
	                    $secondVisit = true;
	                } else {
	                    $no_rights = 1;                        // user is (b)locked now
	                    $markerArray["###DOUBLE_ENTRY###"] = $this->pi_getLL('double_entry','double_entry');
	                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_DOUBLE_ENTRY###");
	                    $content .= $this->templateService->substituteMarkerArray($template, $markerArray);         // Sonderfall !!!
	                }
	                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
	                //$oldLoaded = true;
	            }
	            if ($this->helperObj->writeDevLog)
	                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('fe_users check for qtuid='.$quizData['qtuid'], $this->extKey, 0);
	        }

	        // check if the captcha is OK
	        if ((($quizData['cmd']  == 'submit' && $this->helperObj->getAskAtQ($quizData['qtuid'])) ||
	             ($quizData["fromStart"] && $this->conf['userData.']['askAtStart']) ||
	             ($quizData["fromFinal"] && $this->conf['userData.']['askAtFinal'])) &&
	                is_object($this->freeCap) && $this->conf['enableCaptcha'] &&
	                !$this->freeCap->checkWord($quizData['captcha_response']) && $no_rights == 0) {
	            if ($quizData["fromStart"] && $startPID!=$GLOBALS["TSFE"]->id) {    // Weiterleitung zurueck zur Extra-Startseite
	                $this->redirectUrl($startPID, array($this->prefixId.'[name]' => $quizData["name"],$this->prefixId.'[email]' => $quizData["email"],$this->prefixId.'[homepage]' => $quizData["homepage"], $this->prefixId.'[captchaError]' => '1'));
	                exit;    // hier kommt man eh nie hin...
	            }
	            if ($quizData["fromFinal"] && $finalPID!=$GLOBALS["TSFE"]->id) {    // Weiterleitung zurueck zur Extra-Endseite
	                $this->redirectUrl($finalPID, array($this->prefixId.'[qtuid]' => intval($quizData["qtuid"]),$this->prefixId.'[cmd]' => 'next',$this->prefixId.'[name]' => $quizData["name"],$this->prefixId.'[email]' => $quizData["email"],$this->prefixId.'[homepage]' => $quizData["homepage"], $this->prefixId.'[captchaError]' => '1'));
	                exit;    // hier kommt man eh nie hin...
	            }
	            $quizData['cmd']  = ($quizData["fromStart"]) ? '' : 'next';        // "nochmal" simulieren
	            //$quizData['qtuid'] = '';        // wieso wohl???
	            $markerArray["###CAPTCHA_NOT_OK###"] = $this->pi_getLL('captcha_not_ok','captcha_not_ok');
	            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_CAPTCHA_NOT_OK###");
	            $markerArrayP["###REF_ERRORS###"] .= $this->templateService->substituteMarkerArray($template, $markerArray);    // instead of $content
	            //if ($quizData["fromStart"])
	            $quizData["fromStart"] = 0;            // nichts wurde getan simulieren
	            $quizData["fromFinal"] = 0;
	            if ($this->helperObj->writeDevLog)
	                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('captcha check 1 not ok.', $this->extKey, 0);
	            $error=true;
	            $captchaError = true;
	        } else if ($quizData["captchaError"]) {
	            $markerArray["###CAPTCHA_NOT_OK###"] = $this->pi_getLL('captcha_not_ok','captcha_not_ok');
	            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_CAPTCHA_NOT_OK###");
	            $markerArrayP["###REF_ERRORS###"] .= $this->templateService->substituteMarkerArray($template, $markerArray);    // instead of $content
	            if ($this->helperObj->writeDevLog)
	                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('captcha check 2 not ok.', $this->extKey, 0);
	            $error=true;
	            $captchaError = true;
	        }

	        // check if used IP is blocked
	        if ($quizData['cmd'] == 'submit' && $this->conf['blockIP']) {
	            $ips = explode(',', $this->conf['blockIP']);
	            foreach ($ips as $aip) {
	                $len = strlen(trim($aip));
	                if (substr($quiz_taker_ip_address,0,$len) == trim($aip)) {
	                    //$markerArray["###IP_BLOCKED###"] = $this->pi_getLL('ip_blocked','ip_blocked');
	                    //$template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_IP_BLOCKED###");
	                    $markerArrayP["###REF_ERRORS###"] .= 'Your IP is blocked!'; //$this->templateService->substituteMarkerArray($template, $markerArray);
	                    if ($this->helperObj->writeDevLog)
	                        \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('IP '.$quiz_taker_ip_address.' blocked!', $this->extKey, 0);
	                    $error=true;
	                    $no_rights = 1;
	                }
	            }
	        }

	        // read quiz takers old data
	        $answeredQuestions = '';     // prev. answered Question(s)
	        $skipped = '';
	        $cids = '';
	        $fids = '';
	        if ((($this->conf['useCookiesInDays'] && !$quizData['qtuid']) ||
	             ($quizData['cmd']  == 'next') ||
	             ($quizData['cmd']  == 'submit' && !$this->conf['isPoll'] && ($this->conf['dontShowPoints']!=1 || $this->conf['quizTimeMinutes']))) && $no_rights == 0 ) {

	            $cookieRead = false;
	            if (!$quizData['qtuid'] && $this->conf['useCookiesInDays']) {   // !($quizData['cmd']  == 'next' || $quizData['cmd']  == 'submit')) warum das nur? auskommentiert am 27.12.2009
	                $cookieName = $this->getCookieMode($resPID, $thePID);
	                if ($this->conf['allowCookieReset'] && $quizData["resetcookie"]) {
	                    setcookie ($cookieName, "", time() - 3600);
	                    if ($this->helperObj->writeDevLog)
	                        \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Cookie reseted: '.$cookieName, $this->extKey, 0);
	                } else if ($this->conf['useCookiesInDays']) {
	                    $quizData['qtuid'] = intval($_COOKIE[$cookieName]);    // read quiz taker UID from a cookie
	                    if ($quizData['qtuid']) $cookieRead = true;
	                    if ($this->helperObj->writeDevLog)
	                        \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Cookie read: '.$cookieName.'='.$quizData['qtuid'], $this->extKey, 0);
	                    // oder? $HTTP_COOKIE_VARS["myquizpoll".$resPID];    oder?  $GLOBALS["TSFE"]->fe_user->getKey("ses","myquizpoll".$resPID);
	                }
	            }

	            if ($quizData['qtuid'] && $this->tableAnswers=='tx_myquizpoll_result') {
	                // load solved questions and quiz takers name, email, homepage, old points and last time
	                $uid = intval($quizData['qtuid']);
	                $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
	                    'name, email, homepage, qids,sids,cids,fids, p_or_a, p_max, percent, o_max, o_percent, firsttime, joker1,joker2,joker3, lastcat,nextcat',
	                    $this->tableAnswers,
	                    'uid=' . $uid . $this->helperObj->getWhereLang()); //.' '.$this->cObj->enableFields($this->tableAnswers), auskommentiert am 7.11.10
	                $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
	                if ($rows>0) {                            // DB entry found for current user?
	                    $fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5);
	                    $answeredQuestions = $fetchedRow['qids'];
	                    if (!$this->conf["isPoll"]) {
	                        $skipped = $fetchedRow['sids'];
	                        $joker1 = $fetchedRow['joker1'];
	                        $joker2 = $fetchedRow['joker2'];
	                        $joker3 = $fetchedRow['joker3'];
	                        $firsttime = intval($fetchedRow['firsttime']);
	                        $this->helperObj->setFirstTime($uid, $firsttime);
	                        if ($answeredQuestions) {    // 2.9.10: beantwortete poll-frage muss man auch speichern???
	                            if ($fetchedRow['nextcat']) {
	                                $nextCat = $fetchedRow['nextcat'];
	                                if ($this->conf['startCategory']) $this->conf['startCategory'] = $nextCat;    // kategorie der naechsten frage...
	                            }
	                            if ($quizData['cmd']  != 'submit') {            // namen nicht ueberschreiben
	                                $whereAnswered = ' AND uid NOT IN ('.preg_replace('/[^0-9,]/','',$answeredQuestions).')';    // exclude answered questions next time
	                                if (!($quizData["name"] || $quizData["email"] || $quizData["homepage"])) {
	                                    $quizData["name"] = $fetchedRow['name'];    // abgesendete daten nicht mit default-werten ueberschreiben!
	                                    $quizData["email"] = $fetchedRow['email'];
	                                    $quizData["homepage"] = $fetchedRow['homepage'];
	                                }
	                                //$markerArray["###VAR_ADDRESS_UID###"] = $quizData["address_uid"] = $fetchedRow['address_uid'];
	                            }
	                            $markerArray["###VAR_TOTAL_POINTS###"] = intval($fetchedRow['p_or_a']);        // save total points for the case there are no more questions
	                            $markerArray["###VAR_TMAX_POINTS###"] = intval($fetchedRow['p_max']);
	                            $markerArray["###VAR_TMISSING_POINTS###"] = intval($fetchedRow['p_max']) - intval($fetchedRow['p_or_a']);
	                            $markerArray["###VAR_PERCENT###"] = intval($fetchedRow['percent']);
	                            $markerArray["###VAR_OMAX_POINTS###"] = intval($fetchedRow['o_max']);
	                            $markerArray["###VAR_OVERALL_PERCENT###"] = intval($fetchedRow['o_percent']);
	                            $markerArray["###VAR_QUESTIONS_ANSWERED###"] = (($fetchedRow['qids']) ? (substr_count($fetchedRow['qids'],',')+1) : 0);
	                            if ($fetchedRow['cids'] || $fetchedRow['fids']) {    // if weglassen?
	                                $markerArray["###VAR_QUESTIONS_CORRECT###"] = (($fetchedRow['cids']) ? (substr_count($fetchedRow['cids'],',')+1) : 0);
	                                $markerArray["###VAR_QUESTIONS_FALSE###"] = (($fetchedRow['fids']) ? (substr_count($fetchedRow['fids'],',')+1) : 0);
	                            }
	                            $markerArray["###VAR_CATEGORY###"] = $this->catArray[$row['lastcat']]['name'];
	                            $markerArray["###VAR_NEXT_CATEGORY###"] = $this->catArray[$row['nextcat']]['name'];
	                            $elapseTime = time() - $firsttime;
	                        }
	                        if ($skipped && $quizData['cmd']  != 'submit') {
	                            $whereSkipped = ' AND uid NOT IN ('.preg_replace('/[^0-9,]/','',$skipped).')';    // exclude skipped questions next time
	                        }
	                    }
	                    if ($cookieRead) $secondVisit = true;  // es wurde erfolgreich ein cookie gelesen
	                }
	                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
	            } else if ($quizData['qtuid']) {
	                // load solved poll question from voting-table
	                $uid = intval($quizData['qtuid']);
	                $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('question_id',
	                    $this->tableAnswers,
	                    'uid=' . $uid . $this->helperObj->getWhereLang());
	                if ($GLOBALS['TYPO3_DB']->sql_num_rows($res5)>0) {
	                    $fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5);
	                    $answeredQuestions = $fetchedRow['question_id'];
	                    if ($cookieRead) $secondVisit = true;
	                }
	                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
	            } else if ($this->conf['quizTimeMinutes'] && $quizData["time"]) {
	                $elapseTime = time() - intval($quizData["time"]);        // before saving data
	            }
	            if ($quizData['qtuid'] && $this->conf["isPoll"] && $this->conf["secondPollMode"]==1) {
	                $quizData['cmd'] = 'list';
	                if ($this->helperObj->writeDevLog)
	                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("changing to list mode", $this->extKey, 0);
	            }
	            if ($quizData['qtuid']) $oldLoaded = true;
	            if ($this->helperObj->writeDevLog)
	                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Old data loaded with uid $uid: $answeredQuestions / $whereAnswered / $whereSkipped", $this->extKey, 0);
	        }
        }

        $markerArrayP["###QTUID###"] = intval($quizData['qtuid']);

        // check, if quiz is cancled
        if ( $this->conf['quizTimeMinutes'] && ((intval($this->conf['quizTimeMinutes'])*60 - $elapseTime)<=0) ) {     // oder $quizData['cancel'] == 1 ) {
            $markerArray["###REACHED1###"] = $this->pi_getLL('reached1','reached1');
            $markerArray["###REACHED2###"] = $this->pi_getLL('reached2','reached2');
            $markerArray["###SO_FAR_REACHED1###"] = $this->pi_getLL('so_far_reached1','so_far_reached1');
            $markerArray["###SO_FAR_REACHED2###"] = $this->pi_getLL('so_far_reached2','so_far_reached2');
            $markerArray["###QUIZ_END###"] = $this->pi_getLL('quiz_end','quiz_end');
            $markerArray["###RESTART_QUIZ###"] = $this->pi_linkToPage($this->pi_getLL('restart_quiz','restart_quiz'), $startPID, $target = '', array());
            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_END###");
            $content .= $this->templateService->substituteMarkerArray($template, $markerArray);        // sonderfall !!!

            if ( $this->conf['finalWhenCancel'] ) {
                $noQuestions = true;
            } else {
                if ( $this->conf['highscore.']['showAtFinal'] ) {
                    $quizData['cmd'] = 'score';        // show highscore
                } else {
                    $quizData['cmd'] = 'exit';        // display no more questions
                }
                $no_rights = 1;                    // cancel all
            }
            if ($this->helperObj->writeDevLog)
                \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("cancel check: $no_rights/".$quizData['cmd'], $this->extKey, 0);
        }

        // show only a start page?
        if ($this->conf['userData.']['askAtStart'] && !$quizData['qtuid'] && !$quizData['cmd'] && !$quizData["fromStart"]) {        // show only "ask for user data"?
            $startPage = true;
            $quizData['cmd'] = 'start';
        }

        // next page is a page with questions...
        if ($quizData['cmd'] == 'next') {
            $quizData['cmd'] = '';
        }


        if( $quizData['cmd'] == 'submit' && !$this->conf['ignoreSubmit'] && $no_rights == 0 ) {   /* ***************************************************** */
            /*
             * Display result page: answers and points
             */

            if (!$this->conf["isPoll"]) {    // neu seit 20.2.2011
                // Check quiz taker name and email
                if ( trim($quizData["name"]) == "" )
                    $quizData["name"] = $this->pi_getLL('no_name','no_name');
                if ( !(\TYPO3\CMS\Core\Utility\GeneralUtility::validEmail(trim($quizData["email"]))) )
                    $quizData["email"] = $this->pi_getLL('no_email','no_email');

                // Avoid bad characters in database request
                $quiz_taker_name = $GLOBALS['TYPO3_DB']->quoteStr($quizData['name'], $this->tableAnswers);
                $quiz_taker_email  = $GLOBALS['TYPO3_DB']->quoteStr($quizData['email'], $this->tableAnswers);
                $quiz_taker_homepage = $GLOBALS['TYPO3_DB']->quoteStr($quizData['homepage'], $this->tableAnswers);

                $markerArray["###REAL_NAME###"] = $quiz_taker_name;
                $markerArray["###REAL_EMAIL###"] = $quiz_taker_email;
                $markerArray["###REAL_HOMEPAGE###"] = $quiz_taker_homepage;
                $markerArray["###RESULT_FOR###"] = $this->pi_getLL('result_for','result_for');

                if ( $quiz_taker_email==$this->pi_getLL('no_email','no_email') )
                    $quiz_taker_email='';
                if ( $quiz_taker_homepage==$this->pi_getLL('no_homepage','no_homepage') )
                    $quiz_taker_homepage='';
            }

            $markerArray["###THANK_YOU###"] = $this->pi_getLL('thank_you','thank_you');
            if (!$this->conf['isPoll']) {
                $markerArray["###RES_QUESTION_POINTS###"] = $this->pi_getLL('result_question_points','result_question_points');
                $markerArray["###VAR_QUESTIONS###"] = $this->helperObj->getQuestionsNo();
                if ($whereAnswered)
                    $markerArray["###VAR_QUESTION###"] = $this->helperObj->getQuestionNo($whereAnswered);
                else
                    $markerArray["###VAR_QUESTION###"] = $this->helperObj->getQuestionNo('-');
            }

            // Begin HTML output
            $template_qr = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QRESULT###"); // full template
            if (!$this->conf["isPoll"]) {    // neu seit 20.2.2011
                $template_answer = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_CORR###");
                $template_okok = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_CORR_ANSW###");
                $template_oknot = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_CORR_NOTANSW###");
                $template_notok = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_NOTCORR_ANSW###");
                $template_notnot = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_NOTCORR_NOTANSW###");
                $template_qr_points = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_POINTS###");
                $template_expl = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_EXPLANATION###");
            }
            $template_delimiter = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_DELIMITER###");
            $template_image_begin = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_BEGIN###");
            $template_image_end = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_END###");

            // Get the answers from the user
            $answerArray = array();
            $questionNumber = 1;
            $lastUIDs = '';
            $whereUIDs = '';
            $whereCat = '';
            if ( $this->conf['onlyCategories'] ) {
                $whereCat = " AND category IN (".preg_replace('/[^0-9,]/','',$this->conf['onlyCategories']).")";
            }
            while( $quizData['uid'.$questionNumber] ) {
                $answerArray[$questionNumber] = $quizData['uid'.$questionNumber];
                $lastUIDs .= ','.intval($quizData['uid'.$questionNumber]);
                 $questionNumber++;
            }
            $maxQuestions = $questionNumber-1;
            if ($lastUIDs) {
                $whereUIDs = ' AND uid IN ('.substr($lastUIDs,1).')';
                //}    // kgb: geaendert am 6.9.2010

                // Get questions from the database
                $questionsArray = array();
                $tempNumber = 0;
                $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',        // alles aus der DB holen, auch was nicht gebraucht wird :-(
                        $this->tableQuestions,
                        'pid IN (' . $thePID . ')' . $whereUIDs . $whereCat . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions));
                $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                if ($rows>0) {
                    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                        $tempNumber = $row['uid'];            // save the uid for each question
                        $questionsArray[$tempNumber] = $row;     // save each question
                    }
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
            }

            $points=0;                // points from the user (quiz taker)
            $maxPoints=0;            // maximum points reachable per page
            $maxTotal=0;            // maximum total points reachable
            $percent=0;                // 100 * reached points / total points
            $overallMaximum = 0;    // overall points of all questions
            $overallPercent = 0;    // overall percent
            $questionUID=0;            // uid of a question
            $answered='';            // uids of answered questions
            $skipped='';            // uids of skipped questions
            $correctAnsw = '';        // uids of correct answered question
            $falseAnsw = '';        // uids of false answered question
            $skippedCount = 0;        // no. of skipped questions at this page
            $imgTSConfig = Array();    // image values
            if (is_array($this->conf['jokers.']))
                $halvePoints = $this->conf['jokers.']['halvePoints'];
            else
                $halvePoints = 0;

            // Old questions and answers
            for( $questionNumber=1; $questionNumber < $maxQuestions+1; $questionNumber++ ) {
                $questionUID = intval($answerArray[$questionNumber]);
                 $row = $questionsArray[$questionUID];
                $markerArray["###VAR_QUESTION_NUMBER###"] = $questionNumber;

                if ($this->conf['isPoll']) {    // link to the result page
                    $urlParameters = array("tx_myquizpoll_pi1[cmd]" => "list", "tx_myquizpoll_pi1[qid]" => $questionUID, "no_cache" => "1");
                    $markerArray["###POLLRESULT_URL###"] = $this->pi_linkToPage($this->pi_getLL('poll_url','poll_url'), $listPID, $target = '', $urlParameters);
                } else if ($quizData['answer'.$questionNumber]==-1) {
                    $skipped.=','.$questionUID;
                    $skippedCount++;
                    continue;
                }

                //$answerPointsBool = false;
                $answered.=','.$questionUID;        // nach unten geschoben...
                $questionPoints=0;
                $maxQuestionPoints=0;
                $markerArray["###VAR_QUESTION###"]++;
                $nextCat = '';
                $lastCat = $row['category'];
                if ($this->catArray[$lastCat]) $markerArray["###VAR_CATEGORY###"] = $this->catArray[$lastCat]['name'];

                // Output the result/explanations
                if ( !($this->conf['dontShowCorrectAnswers'] && $this->conf['dontShowPoints']==1) || $this->conf['startCategory'] || $this->conf['advancedStatistics'] || $this->conf['showAllCorrectAnswers'] || $this->conf['isPoll'] || $this->conf['email.']['answers'] != '' ) {

                    if ($row['image']) {  // && (substr_count($template_qr, 'REF_QUESTION_IMAGE_BEGIN')>0)) {
                        $markerArray["###VAR_QUESTION_IMAGE###"] = $this->helperObj->getImage($row['image'], $row["alt_text"]);
                        $markerArray["###REF_QUESTION_IMAGE_BEGIN###"] = $this->templateService->substituteMarkerArray($template_image_begin, $markerArray);
                        $markerArray["###REF_QUESTION_IMAGE_END###"] = $template_image_end;
                    } else {
                        $markerArray["###REF_QUESTION_IMAGE_BEGIN###"] = '';
                        $markerArray["###REF_QUESTION_IMAGE_END###"] = '';
                    }

                    $markerArray["###TITLE_HIDE###"] = ($row['title_hide']) ? '-hide' : '';
                    $markerArray["###VAR_QUESTION_TITLE###"] = $row['title'];
                    $markerArray["###VAR_QUESTION_NAME###"] = $this->formatStr($row['name']); // $this->pi_RTEcssText($row['name']);
                    $markerArray["###VAR_ANSWER_POINTS###"] = '';
                    $markerArray["###REF_QR_ANSWER_ALL###"] = '';
                    $markerArray["###REF_QR_ANSWER_CORR###"] = '';
                    $markerArray["###REF_QR_ANSWER_CORR_ANSW###"] = '';
                    $markerArray["###REF_QR_ANSWER_CORR_NOTANSW###"] = '';
                    $markerArray["###REF_QR_ANSWER_NOTCORR_ANSW###"] = '';
                    $markerArray["###REF_QR_ANSWER_NOTCORR_NOTANSW###"] = '';
                    $markerArray["###REF_QR_POINTS###"] = '';
                    $markerArray["###VAR_QUESTION_ANSWER###"] = '';
                    $markerArray["###REF_QR_EXPLANATION###"] = '';
                    $markerArray["###REF_DELIMITER###"] = '';
                    $markerArray["###P1###"] = '';
                    $markerArray["###P2###"] = '';
                    if ( $this->conf['dontShowPoints'] )
                        $markerArray["###NO_POINTS###"] = '';
                    else
                        $markerArray["###NO_POINTS###"] = '0';

                    if ( !$this->conf['isPoll'] ) {
                        if ( !$this->conf['dontShowPoints'] && $row['qtype']<5 ) {
                            $markerArray["###P1###"] = $this->pi_getLL('p1','p1');
                            $markerArray["###P2###"] = $this->pi_getLL('p2','p2');
                        }
                        /*    if ($this->conf['noNegativePoints']<3) {        // 20.01.10: wenn alle antworten richtig sein muessen, antwort-punkte ignorieren!
                            for ($currentValue=1; $currentValue <= $this->answerChoiceMax; $currentValue++) {
                                if (intval($row['points'.$currentValue])>0 ) {
                                    $answerPointsBool = true;
                                    exit;        // Punkte bei Antworten gefunden...
                                }
                            }
                        }  am 21.01.10 auskommentiert! */
                    } else {
                        $points=$quizData['answer'.$questionNumber];                        // points = SELECTED ANSWER !!!
                        $markerArray["###VAR_USER_ANSWER###"] = $row['answer'.$points];
                        if ($points>0 && $row['category'.$points]) {    // NEU && $this->conf['startCategory']
                            $nextCat = $row['category'.$points];    // next category from an answer
                        }
                        break;        // mehr braucht man nicht bei Umfragen!
                    }

                    // myVars for questions
                    $markerArray = array_merge($markerArray, $this->helperObj->setQuestionVars($questionNumber));

                    $allAnswersOK = true;    // alle Antworten richtig beantwortet?
                    //$correctBool = false;    // gibt es ueberhaupt korrekte antworten?
                    $realCorrectBool = false; // wurden ueberhaupt korrekte Antworten markiert?
                    $withAnswer = $this->conf['noAnswer'];    // gab es eine Antwort vom Benutzer?
                    $lastSelected = 0;

                    for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {

                        if ($row['answer'.$answerNumber] || $row['answer'.$answerNumber]==='0' || in_array($row['qtype'], $this->textType)) {    // was a answer set in the backend?

                            $selected=0;    // was the answer selected by the quiz taker?
                            $tempAnswer = ''; // text for answer $answerNumber
                            // myVars for answers
                            $markerArray = array_merge($markerArray, $this->helperObj->setAnswerVars($answerNumber, $row['qtype']));

                            //if ( !$this->conf['isPoll'] ) {    // show correct answers. Bug fixed: dontShowCorrectAnswers doesnt matter here
                                if ( !$this->conf['dontShowCorrectAnswers'] )
                                    $markerArray["###VAR_QUESTION_ANSWER###"] = $row['answer'.$answerNumber];

                                $thisCat = $row['category'.$answerNumber];
                                if ($this->catArray[$thisCat]) $markerArray['###VAR_QA_CATEGORY###'] = $this->catArray[$thisCat]['name'];

                                if ($row['correct'.$answerNumber]) {    // es gibt richtige Antworten
                                    $realCorrectBool = true;
                                }

                                $answerPoints = 0;        // answer points from the DB
                                if ( !$this->conf['dontShowPoints'] ) {
                                    //if ($answerPointsBool) {         // hier interessiert es nicht, ob eine Antwort als korrekt markiert wurde!
                                    if ($this->conf['noNegativePoints']<3)    // das ganze Verhalten am 21.01.10 geaendert...
                                        $answerPoints = intval($row['points'.$answerNumber]);
                                    if ($answerPoints > 0) {
                                        $row['correct'.$answerNumber] = true;    // ACHTUNG: falls Punkte zu einer Antwort gesetzt sind, dann wird die Antwort als RICHTIG bewertet!
                                    } else {
                                        $answerPoints = intval($row['points']);
                                    }
                                    if ($row['correct'.$answerNumber] || $row['qtype']==3) {
                                        if (($row['qtype'] == 0 || $row['qtype'] == 4) && $this->conf['noNegativePoints']<3)  // KGB, 20.01.10: weg: !$answerPointsBool || $row['qtype'] >= 3, neu: $this->conf['noNegativePoints']<3
                                            $maxQuestionPoints+=$answerPoints;
                                        else if ($answerPoints > $maxQuestionPoints)
                                            $maxQuestionPoints=$answerPoints;    // bei punkten pro antwort ODER wenn nicht addiert werden soll
                                    }
                                    if ($row['qtype']<5)
                                        $markerArray["###VAR_ANSWER_POINTS###"] = $answerPoints;
                                }

                                if ($quizData['answer'.$questionNumber.'_'.$answerNumber]) {  // !='') {    // type 0 und 4
                                    $selected=$answerNumber;
                                } else if ((($row['qtype']>0 && $row['qtype']<3) || $row['qtype']==7) && $quizData['answer'.$questionNumber]==$answerNumber) {
                                    $selected=$answerNumber;
                                } else if (($row['qtype']==3 && $quizData['answer'.$questionNumber]!='') || $row['qtype']==5) {    // type 3 und 5
                                    $selected=$answerNumber;            // sollte 1 sein
                                }
                            //}

                            $wrongText = 0;        // wrong text input?

/*                            if ($this->conf['isPoll']) {    // wurde aus der Schleife genommen!
                                $points=$quizData['answer'.$questionNumber];                        // points = SELECTED ANSWER !!!
                                $markerArray["###VAR_USER_ANSWER###"] .= $row['answer'.$points];
                            } else */
                            if ($row['qtype']==5 && !$this->conf['dontShowCorrectAnswers']) {
                                $markerArray["###VAR_QUESTION_ANSWER###"] = nl2br(htmlspecialchars($quizData['answer'.$questionNumber]));
                                $tempAnswer = $this->templateService->substituteMarkerArray($template_okok, $markerArray);
                                $markerArray["###REF_QR_ANSWER_CORR_ANSW###"] .= $tempAnswer;    // welches template soll man hier wohl nehmen?
                                if ($quizData['answer'.$questionNumber] || $quizData['answer'.$questionNumber]===0) $withAnswer = true;
                                if ($this->helperObj->writeDevLog)
                                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.'-'.$answerNumber.'=CORR_ANSW5->', $this->extKey, 0);
                            } else if ($selected>0 && (($row['qtype']==3 && strtolower($row['answer'.$answerNumber])==strtolower($quizData['answer'.$questionNumber])) ||
                                                ($row['qtype']!=3 && $row['correct'.$answerNumber]))) {    // korrekte Antwort
                                $questionPoints=$questionPoints+$answerPoints;    // $row['points']; geaendert am 16.9.2009
                                if ( !$this->conf['dontShowCorrectAnswers'] ) {
                                    $tempAnswer = $this->templateService->substituteMarkerArray($template_okok, $markerArray);
                                    $markerArray["###REF_QR_ANSWER_CORR_ANSW###"] .= $tempAnswer;
                                }
                                //$correctBool = true;
                                $withAnswer = true;
                                if ($this->helperObj->writeDevLog)
                                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.'-'.$answerNumber.'=CORR_ANSW->'.$questionPoints, $this->extKey, 0);
                            } else if ($selected>0) {    // falsche Antwort
                                $allAnswersOK = false;
                                if ($this->conf['noNegativePoints']!=2)
                                    $questionPoints=$questionPoints-$answerPoints;    // $row['points']; geaendert am 16.9.2009
                                if ( !$this->conf['dontShowCorrectAnswers'] ) {         // { added 8.8.09
                                    if ($row['qtype']==3) {        // since 0.1.8: falsche und richtige antwort ausgeben
                                        $tempAnswer = $this->templateService->substituteMarkerArray($template_oknot, $markerArray);
                                        $markerArray["###REF_QR_ANSWER_CORR_NOTANSW###"] .= $tempAnswer;
                                        $markerArray["###VAR_QUESTION_ANSWER###"] = htmlspecialchars($quizData['answer'.$questionNumber]);
                                    }
                                    $tempAnswer2 = $this->templateService->substituteMarkerArray($template_notok, $markerArray);
                                    $markerArray["###REF_QR_ANSWER_NOTCORR_ANSW###"] .= $tempAnswer2;
                                    $tempAnswer .= $tempAnswer2;    // hier gibt es 2 antworten !!!
                                }
                                if ($row['qtype']==3 && ($row['answer1'] || $row['answer1']==='0')) $wrongText=1;        // for statistics: a2 statt a1 bei falscher antwort
                                $withAnswer = true;
                                if ($this->helperObj->writeDevLog)
                                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.'-'.$answerNumber.'=NOTCORR_ANSW->'.$questionPoints, $this->extKey, 0);
                            } else if ($row['correct'.$answerNumber]) {    // nicht beantwortet, waere aber richtig gewesen
                                $allAnswersOK = false;
                                if (!$this->conf['dontShowCorrectAnswers']) {        // hierhin verschoben am 24.1.10
                                    $tempAnswer = $this->templateService->substituteMarkerArray($template_oknot, $markerArray);
                                    $markerArray["###REF_QR_ANSWER_CORR_NOTANSW###"] .= $tempAnswer;
                                }
                                if ($row['qtype']==3 && ($row['answer1'] || $row['answer1']==='0')) {
                                    $wrongText=1;        // for statistics: a2 statt a1 bei falscher antwort
                                    $selected=2;        // statistics: no answer = false answer !
                                }
                                //$correctBool = true;
                                if ($this->helperObj->writeDevLog)
                                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.'-'.$answerNumber.'=CORR_NOTANSW->', $this->extKey, 0);
                            } else if ( !$this->conf['dontShowCorrectAnswers'] ) {
                                $tempAnswer = $this->templateService->substituteMarkerArray($template_notnot, $markerArray);
                                $markerArray["###REF_QR_ANSWER_NOTCORR_NOTANSW###"] .= $tempAnswer;
                                if ($this->helperObj->writeDevLog)
                                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.'-'.$answerNumber.'=NOTCORR_NOTANSW->', $this->extKey, 0);
                            }

                            if (!$this->conf['dontShowCorrectAnswers']) {    // !$this->conf['isPoll'] &&
                                $markerArray["###REF_QR_ANSWER_ALL###"] .= $tempAnswer;        // all answers in correct order
                                if ($row['correct'.$answerNumber] || $row['qtype']==3 || $row['qtype']==5) {    // all correct answers
                                    $markerArray["###REF_QR_ANSWER_CORR###"] .= $this->templateService->substituteMarkerArray($template_answer, $markerArray);
                                }
                            }
                            if (($this->conf['advancedStatistics'] && $withAnswer) || $this->conf['email.']['answers'] != '') {        // for more statistics  && !$this->conf['isPoll']
                                $statisticsArray[$questionUID]['a'.($answerNumber+$wrongText)] = ($selected>0) ? 1 : 0;
                                if (($row['qtype']==3 || $row['qtype']==5) && ($quizData['answer'.$questionNumber] || $quizData['answer'.$questionNumber]==='0')) {
                                    $statisticsArray[$questionUID]['text'] = $GLOBALS['TYPO3_DB']->quoteStr($quizData['answer'.$questionNumber], $this->tableRelation);
                                } else {
                                    $statisticsArray[$questionUID]['text'] = '';
                                }
                            }

                            if ($selected>0 && $row['category'.$selected]) {    // && $this->conf['startCategory']
                                $nextCat = $row['category'.$selected];    // next category from an answer
                            }

                            if ($selected>0) $lastSelected=$selected;
                        }
                        if (in_array($row['qtype'], $this->textType)) break 1;    // nur erste Antwort ist hier moeglich
                    }

                    if ($catCount>0) {    // $this->conf['startCategory']
                        if (!$nextCat) $nextCat = $row['category_next'];    // next category of this question
                        if ($this->conf['advancedStatistics'] && $withAnswer)
                            $statisticsArray[$questionUID]['nextCat'] = $nextCat;
                    }

                    if (!$this->conf['dontShowPoints']) {        // Bug fixed: dontShowCorrectAnswers doesnt matter here  && !$this->conf['isPoll']
                        if ($questionPoints<0 && $this->conf['noNegativePoints'])
                            $questionPoints=0;        // keine neg. Punkte
                        if ($questionPoints>0 && $this->conf['noNegativePoints']==3) {
                            if (!$allAnswersOK)
                                $questionPoints=0;        // keine Punkte, wenn eine Antwort falsch war
                            else
                                $questionPoints=$answerPoints;        // KGB, 19.01.10: nur maximale Punkte vergeben, nicht pro Antwort
                        }
                        if ($this->conf['noNegativePoints']==4) {    // Punkte nur, wenn alles OK war
                            if ($allAnswersOK)
                                $questionPoints=0;
                            else
                                $questionPoints=$answerPoints;
                        }
                        if ($questionPoints>0 && $halvePoints && ($quizData["joker1"] || $quizData["joker2"] || $quizData["joker3"]))
                            $questionPoints = intval($questionPoints/2);    // halbe Punkte nach Joker-Benutzung

                        $points+=$questionPoints;
                        $maxPoints+=$maxQuestionPoints;

                        if ($this->conf['advancedStatistics'] && $withAnswer)
                            $statisticsArray[$questionUID]['points'] = $questionPoints;
                    } else {
                        if ($this->conf['advancedStatistics'] && $withAnswer)
                            $statisticsArray[$questionUID]['points'] = 0;
                    /*    if ($this->conf['dontShowPoints']==2){    // TODO: eine unausgereifte Idee
                            $cat = ($allAnswersOK) ? $nextCat : $row['category'];
                            $points=intval('/,|\./', '', $this->catArray[$cat]['name']);    // katgorie in punkte umwandeln
                        } */
                    }

                    // 20.4.10: $correctBool ersetzt durch:
                    if ($realCorrectBool && $withAnswer) {    // falls es richtige antworten gibt, merken welche man richtig/falsch beantwortet hat
                        if ($allAnswersOK) {
                            $correctAnsw .= ','.$questionUID;
                            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.' correct.', $this->extKey, 0);
                        } else {
                            $falseAnsw .= ','.$questionUID;
                            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.' not correct.', $this->extKey, 0);
                        }
                    } else {
                        if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($questionUID.' not counted.', $this->extKey, 0);
                    }

                    if (!$this->conf['dontShowCorrectAnswers']) {    //  && !$this->conf['isPoll']
                        if (!$this->conf['dontShowPoints'] && $row['qtype']<5) {
                            $markerArray["###VAR_QUESTION_POINTS###"] = $questionPoints;
                            $markerArray["###VAR_MAX_QUESTION_POINTS###"] = $maxQuestionPoints;
                            $markerArray["###REF_QR_POINTS###"] = $this->templateService->substituteMarkerArray($template_qr_points, $markerArray);
                        } else {
                            $markerArray["###VAR_QUESTION_POINTS###"] = '';
                            $markerArray["###VAR_MAX_QUESTION_POINTS###"] = '';
                        }

                        if ( $row['explanation']!='' || $row['explanation1']!='' || $row['explanation2']!='' ) {    // Explanation
                            $markerArray["###VAR_EXPLANATION###"] = '';
                            if ($row['explanation1']!='') {    // Nur wenn das addon myquizpoll_expl2 installiert ist
                                $markerArray["###VAR_EXPLANATION###"] = ($lastSelected && $row['explanation'.$lastSelected]) ? $this->formatStr($row['explanation'.$lastSelected]) : $this->formatStr($row['explanation']);
                            } else if ($row['explanation2']!='') {    // Nur wenn das addon myquizpoll_expl installiert ist
                                $markerArray["###VAR_EXPLANATION###"] = ($allAnswersOK) ? $this->formatStr($row['explanation']) : $this->formatStr($row['explanation2']);
                            } else
                                $markerArray["###VAR_EXPLANATION###"] = $this->formatStr($row['explanation']);
                            if ($markerArray["###VAR_EXPLANATION###"])
                                $markerArray["###REF_QR_EXPLANATION###"] = $this->templateService->substituteMarkerArray($template_expl, $markerArray);
                            else
                                $markerArray["###REF_QR_EXPLANATION###"] = '';
                        }

                        $markerArray["###REF_DELIMITER###"] = $this->templateService->substituteMarkerArray($template_delimiter, $markerArray);
                    }
                }
                if (!$this->conf['dontShowCorrectAnswers']) {    // bug fixed: points dont even matter  && !$this->conf['isPoll']
                    $markerArrayP["###REF_QRESULT###"] .= $this->templateService->substituteMarkerArray($template_qr, $markerArray);
                }
            }

            // hier wurde mal REF_INTRODUCTION befüllt

            if ($answered) $answered=substr($answered,1);    // now answered questions (UIDs)
            if ($skipped) $skipped=substr($skipped,1);    // now skipped questions (UIDs)
            if ($correctAnsw) $correctAnsw=substr($correctAnsw,1);
            if ($falseAnsw) $falseAnsw=substr($falseAnsw,1);

            $doUpdate=0;                    // insert or update?
            $pointsTotal=$points;            // total points, reached
            $maxTotal = $maxPoints;            // total points, maximum
            $markerArray["###VAR_NEXT_CATEGORY###"] = $this->catArray[$nextCat]['name'];

            //if ($this->conf['pageQuestions'] > 0 && !$this->conf['isPoll']) {    // more questions aviable? // auskommentiert am 27.12.2009, denn der Cheat-Test muss immer kommen!

                if (!$this->conf['showAnswersSeparate'])
                    $quizData['cmd'] = '';                        // show more/next questions!
                // Seek for old answered questions
                if ($quizData['qtuid'] && !$this->conf['isPoll']) {
                    $uid = intval($quizData['qtuid']);
                    $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('qids,cids,fids,sids, p_or_a, p_max, joker1,joker2,joker3',
                        $this->tableAnswers,
                        'uid=' . $uid . $this->helperObj->getWhereLang()); //.' '.$this->cObj->enableFields($this->tableAnswers), auskommentiert am 7.11.10
                    $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                    if ($rows>0) {                            // DB entry found for current user?
                        $fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5);
                        $answeredOld = $fetchedRow['qids'];
                        $correctOld = $fetchedRow['cids'];
                        $falseOld = $fetchedRow['fids'];
                        $skippedOld = $fetchedRow['sids'];
                        $pointsOld = $fetchedRow['p_or_a'];
                        $maxPointsOld = $fetchedRow['p_max'];
                        $joker1 =  $fetchedRow['joker1'];
                        $joker2 =  $fetchedRow['joker2'];
                        $joker3 =  $fetchedRow['joker3'];
                        if ($correctOld && $correctAnsw)
                            $correctAnsw = $correctOld.','.$correctAnsw;
                        else if ($correctOld)
                            $correctAnsw = $correctOld;
                        if ($falseOld && $falseAnsw)
                            $falseAnsw = $falseOld.','.$falseAnsw;
                        else if ($falseOld)
                            $falseAnsw = $falseOld;
                        if (!$answered) {                // all questions skipped  # Fall 1: bisher nur geskipped
                            $answered = $answeredOld;    // all answered questions
                            if ($skippedOld && $skipped)
                                $skipped = $skippedOld.','.$skipped;
                            else if ($skippedOld)
                                $skipped = $skippedOld;
                            $pointsTotal = $pointsOld;    // total points
                            $maxTotal = $maxPointsOld;
                            //if ($answeredOld) $doUpdate=1;
                            $doUpdate = 3;
                            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("do=3, points=$pointsTotal, max=$maxTotal", $this->extKey, 0);
                        } else if (stristr(','.$answeredOld.',', ','.$answered.',')) {  // Fall 2: somebody tries to cheat!
                            $answered = $answeredOld;    // all answered questions
                            $skipped = $skippedOld;        // all skipped questions
                            $pointsTotal = $pointsOld;    // total points
                            $maxTotal = $maxPointsOld;
                            if ($back > 0) {
                                $doUpdate = 1;    // back-seite: update mit alten daten
                            } else {
                                $doUpdate = 2;
                                $error = true;
                                $markerArray["###CHEATING###"] = $this->pi_getLL('cheating','cheating');
                                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_CHEATING###");
                                $markerArrayP["###REF_RES_ERRORS###"] .= $this->templateService->substituteMarkerArray($template, $markerArray);
                                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("Cheating error!", $this->extKey, 0);
                                /*$tempTime = $this->helperObj->getPageTime($quizData['qtuid']);
                                if ($tempTime) {
                                    $markerArrayP["###VAR_NOW###"] = $markerArray["###VAR_NOW###"] = $tempTime;
                                }*/
                            }
                            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("do=2, points=$pointsTotal, max=$maxTotal", $this->extKey, 0);
                        } else if ($answeredOld) {            // Fall 3: man hat schon was beantwortet
                            $answered = $answeredOld.','.$answered;    // all answered questions
                            if ($skippedOld && $skipped)
                                $skipped = $skippedOld.','.$skipped;
                            else if ($skippedOld)
                                $skipped = $skippedOld;
                            $pointsTotal = $points + $pointsOld;    // total points
                            //$maxTotal = $maxPoints;
                            if ($maxPointsOld > 0) $maxTotal += $maxPointsOld;
                            $doUpdate = 1;
                            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("do=1, points=$pointsTotal, max=$maxTotal", $this->extKey, 0);
                        } else if ($skippedOld) {        // Fall 4: skipped, dann beantwortet
                            if ($skippedOld && $skipped)
                                $skipped = $skippedOld.','.$skipped;
                            else
                                $skipped = $skippedOld;
                            $pointsTotal = $points;    // total points
                            $doUpdate = 1;
                        }
                    }
                }

                $whereAnswered = '';
                if ($answered) $whereAnswered = ' AND uid NOT IN ('.preg_replace('/[^0-9,]/','',$answered).')';    // exclude answered questions next time
                $whereSkipped = '';
                if ($skipped) $whereSkipped = ' AND uid NOT IN ('.preg_replace('/[^0-9,]/','',$skipped).')';    // exclude skipped questions next time

                if ($skippedCount > 0) {
                    $markerArray["###VAR_NO###"] = $skippedCount;
                    $markerArray["###SKIPPED###"] = $this->pi_getLL('skipped','skipped');
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_SKIPPED###");
                    $markerArrayP["###REF_SKIPPED###"] = $this->templateService->substituteMarkerArray($template, $markerArray);    // instead $content
                }

            //} else
            if (!$this->conf['pageQuestions']) {
                if ( $this->conf['highscore.']['showAtFinal'] )
                    $quizData['cmd'] = 'score';            // otherwise decide this later
                else
                    $quizData['cmd'] = 'nix';        // keine neuen Fragen...
            } else if ( $this->conf['isPoll'] && !$nextCat ) {
                $quizData['cmd'] = 'list';
            }

            $markerArray["###VAR_QUESTIONS_ANSWERED###"] = (($answered) ? (substr_count($answered,',')+1) : 0);
            if ($falseAnsw || $correctAnsw) {
                $markerArray["###VAR_QUESTIONS_CORRECT###"] = (($correctAnsw) ? (substr_count($correctAnsw,',')+1) : 0);
                $markerArray["###VAR_QUESTIONS_FALSE###"] = (($falseAnsw) ? (substr_count($falseAnsw,',')+1) : 0);
            }

            if ( $this->conf['dontShowPoints']!=1 && !$this->conf['isPoll'] ) {    // Total points yet. && !$this->conf['dontShowCorrectAnswers'] ??
                $markerArray["###RESULT_POINTS###"] = $this->pi_getLL('result_points','result_points');
                $markerArray["###TOTAL_POINTS###"] = $this->pi_getLL('total_points','total_points');
                $markerArray["###SO_FAR_REACHED1###"] = $this->pi_getLL('so_far_reached1','so_far_reached1');
                $markerArray["###SO_FAR_REACHED2###"] = $this->pi_getLL('so_far_reached2','so_far_reached2');
                $markerArray["###VAR_RESULT_POINTS###"] = $points;
                $markerArray["###VAR_MAX_POINTS###"] = $maxPoints;
                $markerArray["###VAR_MISSING_POINTS###"] = $maxPoints - $points;
                $markerArray["###VAR_TOTAL_POINTS###"] = $pointsTotal;
                $markerArray["###VAR_TMAX_POINTS###"] = $maxTotal;
                $markerArray["###VAR_TMISSING_POINTS###"] = $maxTotal - $pointsTotal;
                if ($maxTotal>0)
                    $percent = intval(round(100 * $pointsTotal / $maxTotal));
                $markerArray["###VAR_PERCENT###"] = $percent;
                $overallMaximum = $markerArray["###VAR_OMAX_POINTS###"];
                if ($overallMaximum) {
                    $overallPercent = 100 * $pointsTotal / $overallMaximum;
                    $markerArray["###VAR_OVERALL_PERCENT###"] = intval(round($overallPercent));
                    //$markerArray["###VAR_OMAX_POINTS###"] = $overallMaximum;
                }

                if ($doUpdate == 0) {
                    $overallMaximum = $this->helperObj->getQuestionsMaxPoints();        // maximum points of all questions
                    if ($overallMaximum > 0)
                        $overallPercent = 100 * $markerArray["###VAR_TOTAL_POINTS###"] / $overallMaximum;
                    $markerArray["###VAR_OVERALL_PERCENT###"] = intval(round($overallPercent));
                    $markerArray["###VAR_OMAX_POINTS###"] = $overallMaximum;

                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_RESULT_POINTS###");
                } else {
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_RESULT_POINTS_TOTAL###");
                }
                $markerArrayP["###REF_QPOINTS###"] = $this->templateService->substituteMarkerArray($template, $markerArray);  // instead $content
            }

            if ( $this->conf['startCategory'] && $nextCat ) {    //  && $this->conf['pageQuestions']==1
                $this->conf['startCategory'] = $nextCat;    // kategorie der naechsten frage
            }

            // myVars for page
            $markerArrayP = array_merge($markerArrayP, $this->helperObj->setPageVars());

            if ($points == '')
                $points = 0;
            if ($pointsTotal == '')
                $pointsTotal = 0;
            $hidden = intval($quizData["hidden"]);

            if ($doUpdate==0 && ($points>0 || !$this->conf['isPoll'])) {
                // Insert new results into database
                $timestamp = time();
                $firsttime = intval($quizData["time"]);
                $insert = array('pid' => $resPID,
                    'tstamp' => $timestamp,
                    'crdate' => $timestamp,
                    'hidden' => $hidden,
                    'ip' => $quiz_taker_ip_address,
                    'sys_language_uid' => $this->lang);
                if (!$this->conf['isPoll']) {
                    $insert['joker1'] = $joker1 = intval($quizData["joker1"]);
                    $insert['joker2'] = $joker2 = intval($quizData["joker2"]);
                    $insert['joker3'] = $joker3 = intval($quizData["joker3"]);
                    $insert['name'] = $quiz_taker_name;
                    $insert['email'] = $quiz_taker_email;
                    $insert['homepage'] = $quiz_taker_homepage;
                    $insert['p_max'] = $maxPoints;
                    $insert['percent'] = $percent;
                    $insert['o_max'] = $overallMaximum;
                    $insert['o_percent'] = $overallPercent;
                    $insert['cids'] = $correctAnsw;
                    $insert['fids'] = $falseAnsw;
                    $insert['sids'] = $skipped;
                    // Hook for tt_address
                    if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['setAdrHook'])&& $this->conf['userData.']['tt_address_pid']) {
                        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['setAdrHook'] as $_classRef) {
                            $_procObj = & \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($_classRef);
                            $markerArray["###VAR_ADDRESS_UID###"] = $quizData["address_uid"] =
                                intval($_procObj->setAdr($quizData, $this->conf['userData.']['tt_address_pid'], $this->conf['userData.']['tt_address_groups']));
                        }
                        $insert['address_uid'] = $quizData["address_uid"];
                    }
                }
                if ($this->tableAnswers=='tx_myquizpoll_result') {
                    $insert['p_or_a'] = $points;
                    $insert['qids'] = $answered;
                    $insert['firsttime'] = $firsttime;
                    $insert['lasttime'] = $timestamp;
                    $insert['lastcat'] = intval($lastCat);
                    $insert['nextcat'] = intval($nextCat);
                    $insert['fe_uid'] = intval($GLOBALS['TSFE']->fe_user->user['uid']);
                    $insert['start_uid'] = intval($quizData["start_uid"]);
                } else {
                    $insert['answer_no'] = $points;
                    $insert['question_id'] = $answered;
                }
                $success = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tableAnswers, $insert);
                if($success){
                    $quizData['qtuid'] = $GLOBALS['TYPO3_DB']->sql_insert_id();
                    if ($this->tableAnswers=='tx_myquizpoll_result')
                        $this->helperObj->setStartUid($quizData['qtuid'], intval($quizData["start_uid"]));
                    if ( $this->conf['useCookiesInDays'] ) {    // save quiz takers UID as cookie?
                        $cookieName = $this->getCookieMode($resPID, $thePID);
                        //if (!($this->conf['isPoll'] && $_COOKIE[$cookieName])) {    // bein Umfragen Cookie nicht ueberschreiben...
                            if (intval($this->conf['useCookiesInDays'])==-1)
                                $periode = 0;
                            else
                                $periode = time()+(3600*24*intval($this->conf['useCookiesInDays']));
                            setcookie($cookieName, $quizData['qtuid'], $periode);  /* cookie for x days */
                            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Storing Cookie: '.$cookieName.'='.$quizData['qtuid'].', period='.$periode, $this->extKey, 0);
                        //}
                    }
                    $this->helperObj->setFirstTime($quizData['qtuid'], $firsttime);
                } else {
                    $content.="<p>MySQL Insert-Error for table ".$this->tableAnswers . ': ' . $GLOBALS['TYPO3_DB']->sql_error() ." :-(</p>";
                }
            } else if ($doUpdate==1) {
                // update current user entry
                $uid=intval($quizData['qtuid']);
                $timestamp = time();
                $update = array('tstamp' => $timestamp,
                                'hidden' => $hidden,
                                'name' => $quiz_taker_name,
                                'email' => $quiz_taker_email,
                                'homepage' => $quiz_taker_homepage,
                                'p_or_a' => $pointsTotal,
                                'p_max' => $maxTotal,
                                'percent' => $percent,
                                'o_percent' => $overallPercent,
                                'qids' => $answered,
                                'cids' => $correctAnsw,
                                'fids' => $falseAnsw,
                                'sids' => $skipped,
                                'lastcat' => intval($lastCat),
                                'nextcat' => intval($nextCat),
                                'lasttime' => $timestamp);
                $success = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tableAnswers, 'uid='.$uid.' AND sys_language_uid='.$this->lang, $update);
                if(!$success){
                    $content.="<p>MySQL Update-Error :-(</p>";
                }
            } else if ($doUpdate==3) {
                // update current skipped entry (only skipped questions?!?!)
                $uid=intval($quizData['qtuid']);
                $timestamp = time();
                $update = array('tstamp' => $timestamp,
                                'hidden' => $hidden,
                                'sids' => $skipped,
                                'lasttime' => $timestamp);
                $success = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tableAnswers, 'uid='.$uid.' AND sys_language_uid='.$this->lang, $update);
                if(!$success){
                    $content.="<p>MySQL Update-Error :-(</p>";
                }
            }

            $markerArray["###QTUID###"] = intval($quizData["qtuid"]);

            if ( $this->conf['isPoll'] ) {
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_POLL_SUBMITED###");    // Output thank you
                $markerArray['###QUESTION_NAME###'] = $this->pi_getLL('poll_question','poll_question');
                $markerArray['###USER_ANSWER###'] = $this->pi_getLL('poll_answer','poll_answer');
                $markerArrayP["###REF_INTRODUCTION###"] = $this->templateService->substituteMarkerArray($template, $markerArray);  // instead $content
            } else if ( $this->conf['userData.']['showAtAnswer'] ) {
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_USER_SUBMITED###"); // Output the user name
                $markerArrayP["###REF_INTRODUCTION###"] = $this->templateService->substituteMarkerArray($template, $markerArray);    // instead $content
            }

            if ($this->conf['advancedStatistics'] && $doUpdate<2) {
                // write advanced Statistics to database
                $uid=intval($quizData['qtuid']);
                $timestamp = time();
                $firsttime = $this->helperObj->getFirstTime($uid);
                if ($this->conf['requireSession']) $where_time=' AND crdate='.$firsttime;
                    else $where_time='';
                if (is_array($statisticsArray)) {
                    foreach ($statisticsArray as $type => $element) {
                        // delete old entry in back-mode
                        if ($back>0) {
                            $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->tableRelation,
                                "pid=$resPID AND user_id=$uid AND question_id=$type $where_time AND sys_language_uid=".$this->lang);
                        }
                        $insert = array('pid' => $resPID,
                                        'tstamp' => $timestamp,
                                        'crdate' => $firsttime,
                                        'hidden' => $hidden,
                                        'user_id' => $uid,
                                        'question_id' => $type,
                                        'textinput' => $element['text'],
                                        'points' => (($element['points']) ? $element['points'] : 0),
                                        'nextcat' => (($element['nextCat']) ? $element['nextCat'] : 0),
                                        'sys_language_uid' => $this->lang);
                        //$array2=array();
                        for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                            $insert['checked'.$answerNumber] = ($element['a'.$answerNumber]) ? 1 : 0;
                        }
                        //$insert = array_merge($array1, $array2);
                        $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->tableRelation, $insert);
                    }
                }
            }

            if (!$this->conf['pageQuestions'] && !$this->conf['isPoll']) {
                $finalPage = true;
            }
            $answerPage = true;
            if ($back) $back--;
        }


        if( $this->conf['finishedMinPercent'] && $this->conf['pageQuestions']>0 && $markerArray["###VAR_TOTAL_POINTS###"]!=='' &&
          (!$this->conf['showAnswersSeparate'] || !$quizData['cmd']) && $no_rights==0 ) {  /* ***************************************************** */
            /*
             * Display positive cancel page: quiz taker has reached finishedMinPercent percent???
             */

            if ($markerArray["###VAR_OMAX_POINTS###"])
                $myPercent = 100 * $markerArray["###VAR_TOTAL_POINTS###"] / $markerArray["###VAR_OMAX_POINTS###"];
            else
                $myPercent = 0;
            $uidP = 0;
            $finishedMinPercent = $this->conf['finishedMinPercent'];
            if (strpos($finishedMinPercent, ':') !== false) {
                list($finishedMinPercent, $uidP) = explode(":", $finishedMinPercent);
            }
            if (intval($finishedMinPercent) <= $myPercent) {        // do we have enough points???
                if ($uidP)        // redirect to a URL with that UID?
                    $this->redirectUrl($uidP, array());
                $markerArray["###REACHED1###"] = $this->pi_getLL('reached1','reached1');
                $markerArray["###REACHED2###"] = $this->pi_getLL('reached2','reached2');
                $markerArray["###SO_FAR_REACHED1###"] = $this->pi_getLL('so_far_reached1','so_far_reached1');
                $markerArray["###SO_FAR_REACHED2###"] = $this->pi_getLL('so_far_reached2','so_far_reached2');
                //$markerArray["###VAR_OVERALL_PERCENT###"] = intval($myPercent);
                //$markerArray["###VAR_OMAX_POINTS###"] = $overallMaximum;

                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_FINISHEDMINPERCENT###");
                if ($template == '')    // if it is not in the template
                    $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_QUIZ_FINISHEDMINPERCENT###");
                $content .= $this->templateService->substituteMarkerArray($template, $markerArray);            // sonderfall !!!!!!!!!

                $answerPage = false;
                if ( $this->conf['highscore.']['showAtFinal'] ) {
                    $quizData['cmd'] = 'score';        // show highscore
                } else {
                    $quizData['cmd'] = 'exit';        // display no more questions
                }
            }
        }

        if( $this->conf['cancelWhenWrong'] && $this->conf['pageQuestions']>0 && ($markerArray["###VAR_TOTAL_POINTS###"]!=='') &&
          (!$this->conf['showAnswersSeparate'] || !$quizData['cmd']) && $no_rights==0 ) {  /* ***************************************************** */
            /*
             * Display negative cancel page: quiz taker has given a wrong answer???
             */
            if ($markerArray["###VAR_TOTAL_POINTS###"] < $markerArray["###VAR_TMAX_POINTS###"]) {
                $markerArray["###REACHED1###"] = $this->pi_getLL('reached1','reached1');
                $markerArray["###REACHED2###"] = $this->pi_getLL('reached2','reached2');
                $markerArray["###SO_FAR_REACHED1###"] = $this->pi_getLL('so_far_reached1','so_far_reached1');
                $markerArray["###SO_FAR_REACHED2###"] = $this->pi_getLL('so_far_reached2','so_far_reached2');
                $markerArray["###QUIZ_END###"] = $this->pi_getLL('quiz_end','quiz_end');
                $markerArray["###RESTART_QUIZ###"] = $this->pi_linkToPage($this->pi_getLL('restart_quiz','restart_quiz'), $startPID, $target = '', array());
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_END###");
                if ($template == '')    // if it is not in the template
                    $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_QUIZ_END###");
                $content .= $this->templateService->substituteMarkerArray($template, $markerArray);            // sonderfall !!!

                $answerPage = false;        // show no answers

                if ( $this->conf['finalWhenCancel'] ) {
                    $noQuestions = true;
                } else {
                    if ( $this->conf['highscore.']['showAtFinal'] ) {
                        $quizData['cmd'] = 'score';        // show highscore
                    } else {
                        $quizData['cmd'] = 'exit';        // display no more questions
                    }
                    $no_rights = 1;                    // cancel all
                }
            }
        }

        // Pre-fill quiz taker name and email if FE user logged in
        if ($GLOBALS['TSFE']->loginUser) {
            if (!$quizData["name"]) {
                $field = $this->conf['fe_usersName'];
                if (!$field) $field = 'name';
                $quizData["name"] = $GLOBALS['TSFE']->fe_user->user[$field];
            }
            if (!$quizData["email"]) { $quizData["email"] = $GLOBALS['TSFE']->fe_user->user['email']; }
            if (!$quizData["homepage"]) { $quizData["homepage"] = $GLOBALS['TSFE']->fe_user->user['www']; }
        }
        if ($quizData["name"]) {
            $markerArray["###DEFAULT_NAME###"] = htmlspecialchars($quizData["name"]);
         } else {
            $markerArray["###DEFAULT_NAME###"] = $this->pi_getLL('no_name','no_name');
        }
        if ($quizData["email"]) {
            $markerArray["###DEFAULT_EMAIL###"] = htmlspecialchars($quizData["email"]);
         } else {
            $markerArray["###DEFAULT_EMAIL###"] = $this->pi_getLL('no_email','no_email');
        }
        if ($quizData["homepage"]) {
            $markerArray["###DEFAULT_HOMEPAGE###"] = htmlspecialchars($quizData["homepage"]);
         } else {
            $markerArray["###DEFAULT_HOMEPAGE###"] = $this->pi_getLL('no_homepage','no_homepage');
        }
        $markerArray["###HIDE_ME###"] = $this->pi_getLL('hide_me','hide_me');
        $markerArray["###VISIBILITY###"] = $this->pi_getLL('visibility','visibility');

        // TODO: warum nur?
        // UID loeschen bei Umfragen
        if ($this->conf['isPoll']) {
            $quizData['qtuid'] = '';
        }
        if ($this->helperObj->writeDevLog)
        	\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Next cmd: '.$quizData['cmd'].', answer-page: '.$answerPage.', final-page: '.$finalPage, $this->extKey, 0);


        if( $quizData['cmd'] == '' && $no_rights==0 ) {           /* ***************************************************** */
            /*
             * Display initial page: questions and quiz taker name fields
             */

            $oldRelData=array();
            if ($this->conf['allowBack'] && $this->conf['pageQuestions'] && $quizData['qtuid'] && $back>0) {
                $where='';
                $where_rel='';
                $where_time='';
                $uid = intval($quizData['qtuid']);
                if ($this->conf['requireSession']) $where_time=' AND firsttime='.$this->helperObj->getFirstTime($uid);
                $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('name,email,homepage,qids',
                    $this->tableAnswers,
                    'uid=' . $uid . $where_time . $this->helperObj->getWhereLang()); //.' '.$this->cObj->enableFields($this->tableAnswers));
                $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                if ($rows>0) {
                    $fetchedRow = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5);
                    $qids=explode(',',$fetchedRow['qids']);
                    $bisher=count($qids);
                    $seiten=round($bisher/$this->conf['pageQuestions']);
                    $seite=$seiten-$back+1;
                    for ($k=0; $k<$bisher; $k++) {
                        if (ceil(($k+1)/$this->conf['pageQuestions'])==$seite) {
                            $where .= $qids[$k].',';
                        }
                    }
                    if ($where) {
                        $where=' AND uid IN ('.preg_replace('/[^0-9,]/','',rtrim($where,',')).')';
                        $where_rel=' AND user_id IN ('.preg_replace('/[^0-9,]/','',rtrim($where,',')).')';
                    }
                    $quizData["name"] = $fetchedRow['name'];
                    $quizData["email"] = $fetchedRow['email'];
                    $quizData["homepage"] = $fetchedRow['homepage'];
                    if ($quizData["name"]) {
                        $markerArray["###DEFAULT_NAME###"] = htmlspecialchars($quizData["name"]);
                     } else {
                        $markerArray["###DEFAULT_NAME###"] = $this->pi_getLL('no_name','no_name');
                    }
                    if ($quizData["email"]) {
                        $markerArray["###DEFAULT_EMAIL###"] = htmlspecialchars($quizData["email"]);
                     } else {
                        $markerArray["###DEFAULT_EMAIL###"] = $this->pi_getLL('no_email','no_email');
                    }
                    if ($quizData["homepage"]) {
                        $markerArray["###DEFAULT_HOMEPAGE###"] = htmlspecialchars($quizData["homepage"]);
                     } else {
                        $markerArray["###DEFAULT_HOMEPAGE###"] = $this->pi_getLL('no_homepage','no_homepage');
                    }
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
                $sortBy = $this->getSortBy();
                $questionsArray = array();
                $questionNumber = 0;
                if ($where) {
                    $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                            $this->tableQuestions,
                            'pid IN (' . $thePID . ')' . $where . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
                            '',
                            $sortBy);
                    $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                    if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($rows.' rows selected with: pid IN ('.$thePID.')'.$where . $this->helperObj->getWhereLang(), $this->extKey, 0);
                    if ($rows>0) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                            $questionNumber++;
                            $questionsArray[$questionNumber] = $row;
                        }
                    }
                    $GLOBALS['TYPO3_DB']->sql_free_result($res5);
                }
                $numOfQuestions=$questionNumber;
                $maxQuestions=$numOfQuestions;

                // bisherige Antworten noch holen
                if ($where_rel) {
                    if ($this->conf['requireSession']) $where_time=' AND crdate='.$this->helperObj->getFirstTime($uid);
                    $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                            $this->tableRelation,
                            'pid=' . $resPID . ' AND user_id=' . $uid . $where_time . $this->helperObj->getWhereLang());
                    $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                    if ($rows>0) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                            $temp_qid=$row['question_id'];
                            $oldRelData[$temp_qid]=array();
                            $oldRelData[$temp_qid]['textinput']=$row['textinput'];
                            for ($j=1;$j<=$this->answerChoiceMax;$j++)
                                $oldRelData[$temp_qid]['checked'.$j]=$row['checked'.$j];
                        }
                    }
                    $GLOBALS['TYPO3_DB']->sql_free_result($res5);
                }
                //print_r($oldRelData);

            } elseif (!$noQuestions) {
                // Order questions by & and limit to
                $limitTo = '';
                $whereCat = '';
                $sortBy = $this->getSortBy();
                // Limit questions?
                if ( $this->conf['pageQuestions']>0 && $this->conf['sortBy']!='random' ) {
                    $limitTo = preg_replace('/[^0-9,]/','',$this->conf['pageQuestions']);
                }
                // category
                if ( $this->conf['startCategory'] ) {
                    $whereCat = " AND category=".intval($this->conf['startCategory']);
                } else if ( $this->conf['onlyCategories'] ) {
                    $whereCat = " AND category IN (".preg_replace('/[^0-9,]/','',$this->conf['onlyCategories']).")";
                }

                // Get questions from the database
                $questionsArray = array();
                $questionNumber = 0;
                $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                        $this->tableQuestions,
                        'pid IN (' . $thePID . ')' . $whereCat . $whereAnswered . $whereSkipped . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
                        '',
                        $sortBy,
                        $limitTo);
                $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                if (!$rows && $whereSkipped) {        // now ignore the skipped questions
                    $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                        $this->tableQuestions,
                        'pid IN (' . $thePID . ')' . $whereCat . $whereAnswered . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
                        '',
                        $sortBy,
                        $limitTo);
                    $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                }
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($rows.' rows selected with: pid IN ('.$thePID.')'.$whereCat.$whereAnswered . $this->helperObj->getWhereLang(), $this->extKey, 0);
                if ($rows>0) {
                    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                        $questionNumber++;
                        $questionsArray[$questionNumber] = $row;
                    }
                }

                $numOfQuestions = $questionNumber;                // real questions
                $maxQuestions = $this->conf['pageQuestions'];    // should be questions

                if ($this->conf['isPoll']) {
                    $maxQuestions = 1;                    // only max. 1 poll-question
                } else if ( !$maxQuestions ) {
                    $maxQuestions = $numOfQuestions;
                } else if( $numOfQuestions < $maxQuestions ) {
                    $maxQuestions = $numOfQuestions; // no. of maximum question = no. of questions in the DB
                }

                if ( ($numOfQuestions > 0) and ($this->conf['sortBy']=='random') ) {        // any questions out there???    Random questions???
                    $randomQuestionsArray = array();
                    $questionNumber = 0;
                    $versuche = 0;
                    // Not very fast random method
                    while( $questionNumber < $maxQuestions ) {    // alle Fragen durchgehen
                        $random = mt_rand(1,$numOfQuestions);
                        $randomQuestionsArray[$questionNumber+1] = $questionsArray[$random];    // eine Frage uebernehmen
                        $duplicate = 0;
                        for($i=1 ; $i < $questionNumber+1; $i++) {
                            if ( $randomQuestionsArray[$questionNumber+1]['uid'] == $randomQuestionsArray[$i]['uid'] ) {
                                $duplicate = 1;    // doch nicht uebernehmen
                            }
                            if ( !$duplicate && $this->conf['randomCategories'] && $versuche < 5*$maxQuestions &&    // wir wollen keine endlosschleife!
                                 $randomQuestionsArray[$questionNumber+1]['category'] == $randomQuestionsArray[$i]['category'] ) {
                                $duplicate = 1;    // doch nicht uebernehmen
                            }
                        }
                        if( !$duplicate ) { $questionNumber++ ; }
                        $versuche++;
                    }
                    // rearange questions Array
                    for( $questionNumber=1; $questionNumber < $maxQuestions+1; $questionNumber++ ) {
                        $questionsArray[$questionNumber] = $randomQuestionsArray[$questionNumber];
                    }
                }

                if ($this->conf['finishAfterQuestions']) {        // finish after X questions?
                    $numOfQuestions = intval($this->conf['finishAfterQuestions']) - $this->helperObj->getQuestionNo($whereAnswered);
                    if( $numOfQuestions < $maxQuestions ) {
                        $maxQuestions = $numOfQuestions; // no. of maximum question = no. of questions in the DB
                    }
                }
            } else {
                $numOfQuestions = 0;
            }

            if ( $numOfQuestions > 0 ) {        // any questions out there???

                // Start des Output
                $lastUID = 0;        // remember the last question ID
                $pageTimeCat = 0;    // time per page from a category
                $questTillNow = 0;    // no. of questions answered till now
                $imgTSConfig = array();
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION###");
                $template_image_begin = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_BEGIN###");
                $template_image_end = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_END###");
                $template_answer = $this->templateService->getSubpart($template, "###TEMPLATE_QUESTION_ANSWER###");
                $template_delimiter = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_DELIMITER###");

                if (!$this->conf['isPoll']) {
                    $markerArray["###VAR_QUESTIONS###"] = $this->helperObj->getQuestionsNo();
                    if ($back && $seite)
                        $markerArray["###VAR_QUESTION###"] = $questTillNow = ($seite-1)*$this->conf['pageQuestions'];
                    else
                        $markerArray["###VAR_QUESTION###"] = $questTillNow = $this->helperObj->getQuestionNo($whereAnswered);
                }

                // Questions and answers
                for ($questionNumber=1; $questionNumber < $maxQuestions+1; $questionNumber++) {
                    $row = $questionsArray[$questionNumber];
                    $quid = $row['uid'];
                    $markerArray["###VAR_QUESTION_NUMBER###"] = $questionNumber;

                    $answerPointsBool = false;        // gibt es punkte bei einzelnen antworten?
                    $markerArray["###VAR_QUESTION###"]++;
                    if ($row['image']) {
                        $markerArray["###VAR_QUESTION_IMAGE###"] = $this->helperObj->getImage($row['image'], $row["alt_text"]);
                        $markerArray["###REF_QUESTION_IMAGE_BEGIN###"] = $this->templateService->substituteMarkerArray($template_image_begin, $markerArray);
                        $markerArray["###REF_QUESTION_IMAGE_END###"] = $template_image_end;
                    } else {
                        $markerArray["###REF_QUESTION_IMAGE_BEGIN###"] = '';
                        $markerArray["###REF_QUESTION_IMAGE_END###"] = '';
                    }
                    $markerArray["###VAR_QUESTION_TYPE###"] = $row['qtype'];
                    $markerArray["###TITLE_HIDE###"] = ($row['title_hide']) ? '-hide' : '';
                    $markerArray["###VAR_QUESTION_TITLE###"] = $row['title'];
                    $markerArray["###VAR_QUESTION_NAME###"] = $this->formatStr($row['name']); //$this->formatStr($this->local_cObj->stdWrap($row['name'],$this->conf["general_stdWrap."])); //$this->pi_RTEcssText($row['name']);
                    if ( !$this->conf['dontShowPoints'] && !$this->conf['isPoll'] && $row['qtype']<5) {
                        $markerArray["###VAR_QUESTION_POINTS###"] = 0;
                        if ($markerArray["###VAR_TOTAL_POINTS###"])
                            $markerArray["###VAR_NEXT_POINTS###"] = $markerArray["###VAR_TOTAL_POINTS###"];
                        else
                            $markerArray["###VAR_NEXT_POINTS###"] = 0;
                        for ($currentValue=1; $currentValue <= $this->answerChoiceMax; $currentValue++) {
                            if (intval($row['points'.$currentValue])>0 ) {
                                $answerPointsBool = true;
                                break;
                            }
                        }
                        if ($answerPointsBool)
                            $markerArray["###VAR_ANSWER_POINTS###"] = $this->pi_getLL('different_number_of','different_number_of');
                        else {
                            if (($row['qtype'] == 0 || $row['qtype'] == 4) && $this->conf['noNegativePoints']<3)
                                $markerArray["###VAR_ANSWER_POINTS###"] = $this->pi_getLL('each','each').' '.$row['points'];
                            else
                                $markerArray["###VAR_ANSWER_POINTS###"] = $row['points'];
                        }
                        $markerArray["###P1###"] = $this->pi_getLL('p1','p1');
                        $markerArray["###P2###"] = $this->pi_getLL('p2','p2');
                    } else {
                        $markerArray["###VAR_ANSWER_POINTS###"] = '';
                        $markerArray["###VAR_QUESTION_POINTS###"] = '';
                        $markerArray["###VAR_NEXT_POINTS###"] = '';
                        $markerArray["###P1###"] = '';
                        $markerArray["###P2###"] = '';
                    }
                    $markerArray["###REF_QUESTION_ANSWER###"] = '';
                    if ($row['qtype'] == 2) {
                        $input_id = ($this->conf['myVars.']['answers.']['input_id']) ? ' id="answer'.$questionNumber.'"' : '';
						if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) $input_id .= ' class="form-control"';
                        $markerArrayQ["###VAR_QUESTION_ANSWER###"] = '<select name="tx_myquizpoll_pi1[answer'.$questionNumber.
                            ']" ###MY_SELECT###'.$input_id.'>'."\n"; //  class="'.$this->prefixId.'-answer"
                    }

                    // my Variables of questions
                    $markerArray["###MY_SELECT###"] = '';
                    $markerArray = array_merge($markerArray, $this->helperObj->setQuestionVars($questionNumber));

                    // Jokers
                    if ($this->conf['useJokers'] && $this->conf['pageQuestions']==1) {
                        $temp_uid = intval($quizData['qtuid']);
                        $markerArrayJ["###JAVASCRIPT###"] = '';
                        $markerArrayJ["###JOKER_50###"] = $this->pi_getLL('joker_50','joker_50');
                        $markerArrayJ["###JOKER_AUDIENCE###"] = $this->pi_getLL('joker_audience','joker_audience');
                        $markerArrayJ["###JOKER_PHONE###"] = $this->pi_getLL('joker_phone','joker_phone');
                        if (is_array($this->conf['jokers.']))
                            $unlimited = $this->conf['jokers.']['unlimited'];
                        else
                            $unlimited = 0;
                        if ($joker1 && !$unlimited) {
                            $markerArrayJ["###JOKER_50_LINK###"] = '';
                            $markerArrayJ["###JAVASCRIPT###"] .= "document.getElementById('".$this->prefixId."-joker_50').style.display = 'none';\n";
                        } else
                            $markerArrayJ["###JOKER_50_LINK###"] = $this->prefixId.'getAjaxData('.$quid.','.$temp_uid.',1,\'joker_50\');';
                        if ($joker2 && !$unlimited) {
                            $markerArrayJ["###JOKER_AUDIENCE_LINK###"] = '';
                            $markerArrayJ["###JAVASCRIPT###"] .= "document.getElementById('".$this->prefixId."-joker_audience').style.display = 'none';\n";
                        } else
                            $markerArrayJ["###JOKER_AUDIENCE_LINK###"] = $this->prefixId.'getAjaxData('.$quid.','.$temp_uid.',2,\'joker_audience\');';
                        if ($joker3 && !$unlimited) {
                            $markerArrayJ["###JOKER_PHONE_LINK###"] = '';
                            $markerArrayJ["###JAVASCRIPT###"] .= "document.getElementById('".$this->prefixId."-joker_phone').style.display = 'none';\n";
                        } else
                            $markerArrayJ["###JOKER_PHONE_LINK###"] = $this->prefixId.'getAjaxData('.$quid.','.$temp_uid.',3,\'joker_phone\');';

                        $template_jokers = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_JOKERS###");
                        $markerArrayP["###REF_JOKERS###"] = $this->templateService->substituteMarkerArray($template_jokers, $markerArrayJ);
                    }

                    // Daten zur aktuellen und nächsten Kategorie
                    $thisCat = $row['category'];
                    if ($this->catArray[$thisCat]) {
                        $markerArray["###VAR_CATEGORY###"] = $this->catArray[$thisCat]['name'];
                        $pageTimeCat = $this->catArray[$thisCat]['pagetime'];
                    }
                    $thisCat = $row['category_next'];
                    if ($this->catArray[$thisCat]) $markerArray["###VAR_NEXT_CATEGORY###"] = $this->catArray[$thisCat]['name'];

                    // Display answers
                    $answers = 0;
                    $radmonArray = array();
                    for( $answerNumber=1; $answerNumber <= $this->answerChoiceMax; $answerNumber++) {
                        $text_type=in_array($row['qtype'], $this->textType);
                        // Answers in random order?
                        if ($this->conf['mixAnswers'] && !$text_type) {
                            $currentValue=0;
                            $leer=0;
                            while ($currentValue==0 && $leer<33) {
                                $random = mt_rand(1,$this->answerChoiceMax);
                                if (($row['answer'.$random] || $row['answer'.$random]==='0') && !(in_array($random, $radmonArray))) {
                                    $radmonArray[] = $random;
                                    $currentValue = $random;
                                } else {
                                    $leer++;
                                }
                            }
                        } else {
                            $currentValue=$answerNumber;
                        }
                        if( (!$text_type && ($row['answer'.$currentValue] || $row['answer'.$currentValue]==='0')) || ($text_type && $answerNumber==1) ) {
                            $answers++;
                            $input_id = '';
                            $input_label1 = '';
                            $input_label2 = '';

                            // my Variables for answers
                            $markerArrayQ["###MY_OPTION###"] = '';
                            $markerArrayQ["###MY_INPUT_RADIO###"] = '';
                            $markerArrayQ["###MY_INPUT_CHECKBOX###"] = '';
                            $markerArrayQ["###MY_INPUT_TEXT###"] = '';
                            $markerArrayQ["###MY_INPUT_AREA###"] = '';
                            $markerArrayQ["###MY_INPUT_WRAP###"] = '';
                            $markerArrayQ = array_merge($markerArrayQ, $this->helperObj->setAnswerVars($answerNumber, $row['qtype']));
                            $markerArrayQ["###VAR_QA_NR###"] = $currentValue;

                            if ($row['qtype']<3 || $row['qtype']==4 || $row['qtype']==7) {
                                //$answer_choice = $this->formatStr($row['answer'.$currentValue]);    // Problem: <tt> geht hier verloren!
                                $answer_choice = ($this->conf['general_stdWrap.']['notForAnswers']) ? $row['answer'.$currentValue] : $this->formatStr($row['answer'.$currentValue]);
                                if ($row['qtype']!=2 && !(strpos($markerArrayQ["###MY_INPUT_WRAP###"],'|') === false))
                                    $answer_choice = str_replace('|',$answer_choice,$markerArrayQ["###MY_INPUT_WRAP###"]);
                            }

                            // Questtion type
                            if ($row['qtype'] == 1) {    // radio-button
                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'_'.$answerNumber.'"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==3) $class_label = ' class="radio"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==4) $class_label = ' class="radio inline"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==6) $class_label = ' class="radio-inline"';
                                    else $class_label = '';
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) { $label_wrap1 = '<div class="radio">'; $label_wrap2 = '</div>'; }
                                    else { $label_wrap1 = ''; $label_wrap2 = ''; }
                                    $input_label1 = $label_wrap1 . '<label for="answer'.$questionNumber.'_'.$answerNumber.'"'.$class_label.'>';
                                    $input_label2 = '</label>' . $label_wrap2;
                                }
                                $answer_content = '<input type="radio" name="tx_myquizpoll_pi1[answer'.$questionNumber.']" value="'.$currentValue.'" '.$input_id.' ###MY_INPUT_RADIO###';
                                if (is_array($oldRelData[$quid]) && $oldRelData[$quid]['checked'.$currentValue]) $answer_content .= ' checked="checked"';
								elseif ($captchaError && $quizData['answer'.$questionNumber]==$currentValue) $answer_content .= ' checked="checked"';
                                $answer_content .= ' /> ';
                            } else if ($row['qtype'] == 2) {    // select-box
                                $answer_content = '<option value="'.$currentValue.'" ###MY_OPTION###';
                                if (is_array($oldRelData[$quid]) && $oldRelData[$quid]['checked'.$currentValue]) $answer_content .= ' selected="selected"';
								elseif ($captchaError && $quizData['answer'.$questionNumber]==$currentValue) $answer_content .= ' selected="selected"';
                                $answer_content .= '> ';
                            } else if ($row['qtype'] == 3) {    // input-text
                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) $input_id .= ' class="form-control"';
                                if (is_array($oldRelData[$quid]))
                                    $value = $oldRelData[$quid]['textinput'];
                                else if ($captchaError)
                                    $value = $quizData['answer'.$questionNumber];
                                else if ($row['answer2'] || $row['answer2']==='0')
                                    $value = $row['answer2'];
                                else
                                    $value = '';
                                $answer_content = '<input type="text" name="tx_myquizpoll_pi1[answer'.$questionNumber.']" value="'.$value.'" '.$input_id.' ###MY_INPUT_TEXT### /> ';
                            } else if ($row['qtype'] == 4) {    // ja/nein
                                $answer_content = $answer_choice;
                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'_'.$answerNumber.'_1"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==3) $class_label = ' class="radio"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==4) $class_label = ' class="radio inline"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==6) $class_label = ' class="radio-inline"';
                                    else $class_label = '';
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) { $label_wrap1 = '<div class="radio">'; $label_wrap2 = '</div>'; }
                                    else { $label_wrap1 = ''; $label_wrap2 = ''; }
                                    $input_label1 = $label_wrap1 . '<label for="answer'.$questionNumber.'_'.$answerNumber.'_1"'.$class_label.'>';
                                    $input_label2 = '</label>' . $label_wrap2;
                                }
                                $answer_content .= ' <span class="'.$this->prefixId.'-yesno"><span class="'.$this->prefixId.'-yes">';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"] > 1) $answer_content .= $input_label1;
                                $answer_content .= '<input type="radio" name="tx_myquizpoll_pi1[answer'.$questionNumber.'_'.$currentValue.']" '.$input_id.' value="'.$currentValue.'" ###MY_INPUT_RADIO###';
                                if (is_array($oldRelData[$quid]) && $oldRelData[$quid]['checked'.$currentValue]) $answer_content .= ' checked="checked"';
                                elseif ($captchaError && $quizData['answer'.$questionNumber.'_'.$currentValue]==$currentValue) $answer_content .= ' checked="checked"';
                                $answer_content .= ' /> ';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"] == 1) $answer_content .= $input_label1;
                                $answer_content .= $this->pi_getLL('yes','yes').$input_label2.'</span>';

                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'_'.$answerNumber.'_0"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==3) $class_label = ' class="radio"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==4) $class_label = ' class="radio inline"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==6) $class_label = ' class="radio-inline"';
                                    else $class_label = '';
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) { $label_wrap1 = '<div class="radio">'; $label_wrap2 = '</div>'; }
                                    else { $label_wrap1 = ''; $label_wrap2 = ''; }
                                    $input_label1 = $label_wrap1 . '<label for="answer'.$questionNumber.'_'.$answerNumber.'_0"'.$class_label.'>';
                                    $input_label2 = '</label>' . $label_wrap2;
                                }
                                $answer_content .= ' <span class="'.$this->prefixId.'-no">';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"] > 1) $answer_content .= $input_label1;
                                $answer_content .= '<input type="radio" name="tx_myquizpoll_pi1[answer'.$questionNumber.'_'.$currentValue.']" '.$input_id.' value="0" ###MY_INPUT_RADIO###';
                                if (is_array($oldRelData[$quid]) && !$oldRelData[$quid]['checked'.$currentValue]) $answer_content .= ' checked="checked"';
                                elseif ($captchaError && $quizData['answer'.$questionNumber.'_'.$currentValue]==0) $answer_content .= ' checked="checked"';
                                $answer_content .= ' /> ';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"] == 1) $answer_content .= $input_label1;
                                $answer_content .= $this->pi_getLL('no','no').$input_label2.'</span></span>';
                            } else if ($row['qtype'] == 5) {    // textarea
                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) $input_id .= ' class="form-control"';
                                if (is_array($oldRelData[$quid]))
                                    $value = str_replace('\r\n', "\r\n", $oldRelData[$quid]['textinput']);
                                else if ($captchaError)
                                    $value = $quizData['answer'.$questionNumber];
                                else if ($row['answer2'] || $row['answer2']==='0')
                                    $value = $row['answer2'];
                                else
                                    $value = '';
                                $answer_content = '<textarea name="tx_myquizpoll_pi1[answer'.$questionNumber.']" '.$input_id.' ###MY_INPUT_AREA###>'.$value.'</textarea> ';
                            } else if ($row['qtype'] == 7) {    // star-rating
                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'_'.$answerNumber.'"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                    $input_label1 = '<label for="answer'.$questionNumber.'_'.$answerNumber.'">';
                                    $input_label2 = '</label>';
                                }
                                $answer_content = '<input type="radio" class="star" name="tx_myquizpoll_pi1[answer'.$questionNumber.']" value="'.$currentValue.'" '.$input_id;
                                if (is_array($oldRelData[$quid]) && $oldRelData[$quid]['checked'.$currentValue]) $answer_content .= ' checked="checked"';
                                elseif ($captchaError && $quizData['answer'.$questionNumber]==$currentValue) $answer_content .= ' checked="checked"';
                                $answer_content .= ' title="'.$answer_choice.'" /> ';
                            } else {    // checkbox
                                if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = 'id="answer'.$questionNumber.'_'.$answerNumber.'"';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==3) $class_label = ' class="checkbox"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==4) $class_label = ' class="checkbox inline"';
                                    elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==6) $class_label = ' class="checkbox-inline"';
                                    else $class_label = '';
                                    if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) { $label_wrap1 = '<div class="checkbox">'; $label_wrap2 = '</div>'; }
                                    else { $label_wrap1 = ''; $label_wrap2 = ''; }
                                    $input_label1 = $label_wrap1 . '<label for="answer'.$questionNumber.'_'.$answerNumber.'"'.$class_label.'>';
                                    $input_label2 = '</label>' . $label_wrap2;
                                }
                                $answer_content = '<input type="checkbox" name="tx_myquizpoll_pi1[answer'.$questionNumber.'_'.$currentValue.']" value="'.$currentValue.'" '.$input_id.' ###MY_INPUT_CHECKBOX###';
                                if (is_array($oldRelData[$quid]) && $oldRelData[$quid]['checked'.$currentValue]) $answer_content .= ' checked="checked"';
                                elseif ($captchaError && $quizData['answer'.$questionNumber.'_'.$currentValue]==$currentValue) $answer_content .= ' checked="checked"';
                                $answer_content .= ' /> ';
                            }

                            if (!$this->conf['dontShowPoints'] && !$this->conf['isPoll'] && $row['qtype']<5) {
                                $tmpPoints = 0;
                                if ($this->conf['noNegativePoints']<3 && $answerPointsBool)
                                    $tmpPoints = intval($row['points'.$currentValue]);
                                if ($tmpPoints>0)
                                    $row['correct'.$currentValue] = true;        // ACHTUNG: falls Punkte zu einer Antwort gesetzt sind, dann wird die Antwort als RICHTIG bewertet!
                                else
                                    $tmpPoints = intval($row['points']);
                                if ($row['correct'.$currentValue] || $row['qtype']==3) {
                                    if (($row['qtype'] == 0 || $row['qtype'] == 4) && $this->conf['noNegativePoints']<3) {
                                        $markerArray["###VAR_QUESTION_POINTS###"]+=$tmpPoints;    // points for each answer
                                    } else if ($tmpPoints > $markerArray["###VAR_QUESTION_POINTS###"]) {
                                        $markerArray["###VAR_QUESTION_POINTS###"]=$tmpPoints;    // points for each answer
                                    }
                                }
                            }

                            $thisCat = $row['category'.$currentValue];
                            if ($this->catArray[$thisCat]) $markerArrayQ['###VAR_QA_CATEGORY###'] = $this->catArray[$thisCat]['name'];
                            if ($row['qtype'] < 3) {
                                if ($markerArrayQ["###MY_INPUT_LABEL###"] > 1)
                                    $answer_content = $input_label1.$answer_content.$answer_choice.$input_label2;
                                else
                                    $answer_content .= $input_label1.$answer_choice.$input_label2;
                            }
                            if ($row['qtype'] == 2) {
                                $answer_content .= "</option>\n";
                                $markerArrayQ["###VAR_QUESTION_ANSWER###"] .= $this->templateService->substituteMarkerArray($answer_content, $markerArrayQ);
                            } else {
                                $markerArrayQ['###VAR_QUESTION_ANSWER###'] = $this->templateService->substituteMarkerArray($answer_content, $markerArrayQ);
                                $markerArray["###REF_QUESTION_ANSWER###"] .= $this->templateService->substituteMarkerArray($template_answer, $markerArrayQ);
                            }
                        }
                    }

                    if ( !$this->conf['dontShowPoints'] && !$this->conf['isPoll'] && $row['qtype']<5 )
                        $markerArray["###VAR_NEXT_POINTS###"]+=$markerArray["###VAR_QUESTION_POINTS###"];

                    $markerArray["###VAR_QUESTION_ANSWERS###"] = $answers;

                    if ($this->conf['allowSkipping']) {        // skip answers
                        $markerArrayQ["###VAR_QA_NR###"] = -1;
                        // Questtion type
                        if ($row['qtype'] == 1) {
                            if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = ' id="answer'.$questionNumber.'_x"';
                            if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]==3) $class_label = ' class="radio"';
                                elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==4) $class_label = ' class="radio inline"';
                                elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==6) $class_label = ' class="radio-inline"';
                                else $class_label = '';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) { $label_wrap1 = '<div class="radio">'; $label_wrap2 = '</div>'; }
                                else { $label_wrap1 = ''; $label_wrap2 = ''; }
                                $input_label1 = $label_wrap1 . '<label for="answer'.$questionNumber.'_x"'.$class_label.'>';
                                $input_label2 = '</label>' . $label_wrap2;
                            }
                            $answer_content = '<input type="radio" name="tx_myquizpoll_pi1[answer'.$questionNumber.']" value="-1"'.$input_id.' /> ';
                            if ($markerArrayQ["###MY_INPUT_LABEL###"] > 1)
                                $answer_content = $input_label1.$answer_content.$this->pi_getLL('skip','skip').$input_label2;
                            else
                                $answer_content .= $input_label1.$this->pi_getLL('skip','skip').$input_label2;
                        } else if ($row['qtype'] == 2) {
                            $answer_content = '<option value="-1"> '.$this->pi_getLL('skip','skip').'</option>';
                        } else if ($row['qtype'] < 6) {
                            if ($markerArrayQ["###MY_INPUT_ID###"]) $input_id = ' id="answer'.$questionNumber.'_x"';
                            if ($markerArrayQ["###MY_INPUT_LABEL###"]) {
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]==3) $class_label = ' class="checkbox"';
                                elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==4) $class_label = ' class="checkbox inline"';
                                elseif ($markerArrayQ["###MY_INPUT_LABEL###"]==6) $class_label = ' class="checkbox-inline"';
                                else $class_label = '';
                                if ($markerArrayQ["###MY_INPUT_LABEL###"]==5) { $label_wrap1 = '<div class="checkbox">'; $label_wrap2 = '</div>'; }
                                else { $label_wrap1 = ''; $label_wrap2 = ''; }
                                $input_label1 = $label_wrap1 . '<label for="answer'.$questionNumber.'_x"'.$class_label.'>';
                                $input_label2 = '</label>' . $label_wrap2;
                            }
                            $answer_content = '<input type="checkbox" name="tx_myquizpoll_pi1[answer'.$questionNumber.']" value="-1"'.$input_id.' /> ';
                            if ($markerArrayQ["###MY_INPUT_LABEL###"] > 1)
                                $answer_content = $input_label1.$answer_content.$this->pi_getLL('skip','skip').$input_label2;
                            else
                                $answer_content .= $input_label1.$this->pi_getLL('skip','skip').$input_label2;
                        }
                        $markerArrayQ['###VAR_QA_CATEGORY###'] = '';
                        if ($row['qtype'] == 2) {
                            $markerArrayQ['###VAR_QUESTION_ANSWER###'] .= $answer_content;
                        } else {
                            $markerArrayQ['###VAR_QUESTION_ANSWER###'] = $answer_content;
                            $markerArray["###REF_QUESTION_ANSWER###"] .= $this->templateService->substituteMarkerArray($template_answer, $markerArrayQ);
                        }
                    }

                    if ($row['qtype'] == 2) {
                        $markerArrayQ["###VAR_QUESTION_ANSWER###"] .= "</select>\n";
                        $markerArray["###REF_QUESTION_ANSWER###"] .= $this->templateService->substituteMarkerArray($template_answer, $markerArrayQ);
                    }
                    if ( ($this->helperObj->getAskAtQ($quizData['qtuid']) || $questionNumber<$maxQuestions) && !$this->conf['isPoll'] ) {
                        $markerArray["###REF_DELIMITER###"] = $this->templateService->substituteMarkerArray($template_delimiter, $markerArray);
                    } else {
                        $markerArray["###REF_DELIMITER###"] = '';
                    }

                    if ($this->conf['enforceSelection']) {    // 25.1.10: antwort erzwingen?
                        $this->helperObj->addEnforceJsc($questionNumber,$answers,$row['qtype']);
                    }

                    //$this->subpart = $template;
                    $template2 = $this->templateService->substituteSubpart($template, '###TEMPLATE_QUESTION_ANSWER###', $markerArray["###REF_QUESTION_ANSWER###"], 0);
                    $markerArrayP["###REF_QUESTIONS###"] .= $this->templateService->substituteMarkerArray($template2, $markerArray);    // statt $content .= since 0.1.8
                    $markerArrayP["###REF_QUESTIONS###"] .= '<input type="hidden" name="tx_myquizpoll_pi1[uid'.$questionNumber.']" value="'.$quid.'" class="input_hidden" />';
                    if ($this->conf['isPoll']) {    // link to the result page
                        $urlParameters = array("tx_myquizpoll_pi1[cmd]" => "list", "tx_myquizpoll_pi1[qid]" => $quid, "no_cache" => "1");
                        $markerArray["###POLLRESULT_URL###"] = $this->pi_linkToPage($this->pi_getLL('poll_url','poll_url'), $listPID, $target = '', $urlParameters);
                    }
                    $lastUID=$quid;
                }

                $markerArrayP["###REF_SUBMIT_FIELDS###"] = '';
                $markerArrayP["###HIDDENFIELDS###"] = '';
                $markerArrayP["###VAR_QID###"] = $lastUID;    // last question uid, added on 23.1.2011
                if ($this->conf['isPoll']) {
                    $markerArrayP["###VAR_FID###"] = '';
                    $markerArrayP["###POLLRESULT###"] = $this->pi_getLL('poll_url','poll_url');
                    $markerArrayP["###NUM_VOTES###"] = $this->pi_getLL('num_votes','num_votes');
                    if ($this->conf['rating.']['parameter']) {    // rating
                        $markerArrayP["###VAR_FID###"] = $this->helperObj->getForeignId();            // a foreign uid, added on 23.1.2011
                    }
                }
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('last question: '.$lastUID, $this->extKey, 0);

                // back-button
                if ($this->conf['allowBack'] && $this->conf['pageQuestions'] && $quizData['qtuid']) {
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[back]" value="'.$back.'" />';
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[back-hit]" value="0" />';
                    $markerArray['###BACK_STYLE###'] = ($seite == 1) ? ' style="display:none;"' : '';
                } else $markerArray['###BACK_STYLE###'] = ' style="display:none;"';

                // Submit/User-Data Template
                if ( $this->helperObj->getAskAtQ($quizData['qtuid']) && !$this->conf['isPoll'] ) {
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_USER_TO_SUBMIT###");
                    if (is_object($this->freeCap) && $this->conf['enableCaptcha']) {
                        $markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
                    } else {
                        $subpartArray['###CAPTCHA_INSERT###'] = '';
                    }
                    $markerArrayP["###REF_SUBMIT_FIELDS###"] = $this->templateService->substituteMarkerArrayCached($template,$markerArray,$subpartArray,$wrappedSubpartArray);
                } else if (!($answeredQuestions==$lastUID && $this->conf["isPoll"])) {
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_SUBMIT###");
                    $markerArrayP["###REF_SUBMIT_FIELDS###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="name" value="'.$markerArray["###DEFAULT_NAME###"].'" /> ';    // fürs Template?
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[name]" value="'.htmlspecialchars($quizData["name"]).'" />';
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[email]" value="'.htmlspecialchars($quizData["email"]).'" />';
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[homepage]" value="'.htmlspecialchars($quizData["homepage"]).'" />';
                                    } else {
                    $markerArray["###NO_SUBMIT###"] = $this->pi_getLL('no_submit','no_submit');
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_NO_SUBMIT###");
                    $markerArrayP["###REF_SUBMIT_FIELDS###"] = $this->templateService->substituteMarkerArray($template, $markerArray); // new in 0.1.8
                }

                if (!$this->conf['isPoll'])        // when is Poll, do not update result-table
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[qtuid]" value="'.intval($quizData["qtuid"]).'" />';
                if ($this->conf['hideByDefault'])
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[hidden]" value="1" />';
                if (!$quizData['qtuid'])
                    $markerArrayP["###HIDDENFIELDS###"] .= '
  <input type="hidden" name="'.$this->prefixId.'[start_uid]" value="'.$GLOBALS['TSFE']->id.'" />
  <input type="hidden" name="'.$this->prefixId.'[time]" value="'.time().'" />';
                $markerArrayP["###HIDDENFIELDS###"] .= '
  <input type="hidden" name="'.$this->prefixId.'[cmd]" value="submit" />';
//  <input type="hidden" name="no_cache" value="1" />  KGB: auskommentiert am 30.5.2015

                if ($this->conf['useJokers'] && $this->conf['pageQuestions']==1) {
                    $markerArrayP["###HIDDENFIELDS###"] .= '
  <input type="hidden" name="'.$this->prefixId.'[joker1]" value="0" />
  <input type="hidden" name="'.$this->prefixId.'[joker2]" value="0" />
  <input type="hidden" name="'.$this->prefixId.'[joker3]" value="0" />';
                }

                $markerArrayP["###MAX_PAGES###"] = $this->helperObj->getMaxPages();
                $markerArrayP["###PAGE###"] = $this->helperObj->getPage($questTillNow);
                $markerArrayP["###FE_USER_UID###"] = intval($GLOBALS['TSFE']->fe_user->user['uid']);
                $markerArrayP["###SUBMIT_JSC###"] = $this->helperObj->getSubmitJsc();

                $questionPage = true;

            } else {

                // final page: no more questions left, if pageQuestions > 0
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('0 questions found. Entering final page...', $this->extKey, 0);
                // if ($oldLoaded) $secondVisit = true;	// Keine Email schicken, wenn alle Fragen schon beantwortet wurden: NE, so einfach ist das nicht :-(
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_NO_MORE###");
                $markerArray["###NO_MORE###"] = $this->pi_getLL('no_more','no_more');
                $markerArray['###YOUR_EVALUATION###'] = $this->pi_getLL('your_evaluation','your_evaluation');
                $markerArray['###REACHED1###'] = $this->pi_getLL('reached1','reached1');
                $markerArray['###REACHED2###'] = $this->pi_getLL('reached2','reached2');
                $markerArray['###POINTS###'] = $this->pi_getLL('points','points');
            /*    if ($this->conf['startPID'])        // wir sind nicht auf der Startseite, also parent holen...
                    $restart_id = $this->conf['startPID']; // $GLOBALS['TSFE']->page['pid'];
                else
                    $restart_id = $GLOBALS['TSFE']->id;    */
                $markerArray["###RESTART_QUIZ###"] = $this->pi_linkToPage($this->pi_getLL('restart_quiz','restart_quiz'), $startPID, $target = '', array());
                if ($this->conf['allowCookieReset'])
                     $markerArray["###RESET_COOKIE###"] = $this->pi_linkToPage($this->pi_getLL('reset_cookie','reset_cookie'),
                             $startPID, $target = '', array($this->prefixId . '[resetcookie]' => 1));
                else $markerArray["###RESET_COOKIE###"] = '';
                $questions = '';

                if ($this->conf['showAllCorrectAnswers'] && !$this->conf['isPoll']) {        // show all answers...
                    /*if ($this->conf['finishAfterQuestions'])
                        $fragen = intval($this->conf['finishAfterQuestions']);
                    else
                        $fragen = $this->helperObj->getQuestionsNo();*/
                    $questions = $this->showAllAnswers( $quizData, $thePID,$resPID, false, 0 ); // 24.01.10: 0 statt $fragen
                }

                $subpart = $template;
                $template = $this->templateService->substituteSubpart($subpart, '###QUIZ_ANSWERS###', $questions);
                $markerArrayP["###REF_NO_MORE###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
                $markerArrayP["###RESTART_QUIZ###"] = $markerArray["###RESTART_QUIZ###"];
                $markerArrayP["###RESET_COOKIE###"] = $markerArray["###RESET_COOKIE###"];

                if ( $this->conf['highscore.']['showAtFinal'] ) {
                    $quizData['cmd'] = 'score';
                }
                $finalPage = true;

            }

            // myVars for page
            $markerArrayP = array_merge($markerArrayP, $this->helperObj->setPageVars());

        } else if ($this->conf['showAnswersSeparate'] && $quizData['cmd']=='submit' && !$this->conf['isPoll'] && $no_rights==0) { /* ***************************************************** */
            /*
             * Display only a next button
             */

            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_NEXT###");
            $markerArray["###HIDDENFIELDS###"] = '';
            // back-button
            if ($this->conf['allowBack'] && $this->conf['pageQuestions'] && $quizData['qtuid']) {
                $markerArray['###BACK_STYLE###'] = '';
                $markerArray["###HIDDENFIELDS###"] .= '<input type="hidden" name="'.$this->prefixId.'[back]" value="'.$back.'" />
    <input type="hidden" name="'.$this->prefixId.'[back-hit]" value="0" />';
            } else $markerArray['###BACK_STYLE###'] = ' style="display:none;"';
             if ($template == '')    // if it is not in the template
                $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_NEXT###");
            $markerArrayP["###REF_NEXT###"] = $this->templateService->substituteMarkerArray($template, $markerArray);    // instead $content
        }




        /** Redirect to the final page? **/
        if ($finalPage && $quizData['cmd'] != 'exit' && $this->conf['finalPID'] && ($finalPID != intval($GLOBALS["TSFE"]->id))) {
            $this->redirectUrl($finalPID, array($this->prefixId . '[qtuid]' => intval($quizData["qtuid"]), $this->prefixId . '[cmd]' => 'next'));
             exit;    // unnoetig...
        }

        /** Poll result? **/
        if ($answerPage && $this->conf['isPoll']) {
            $quizData['cmd'] = 'list';
        }


        // Make a page-layout
        if ( $quizData['cmd'] == 'allanswers' && !$this->conf['isPoll'] ) {    /* ***************************************************** */
            /*
             * Display all questions and correct answers
             */
            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog("show answers of: $thePID,$resPID", $this->extKey, 0);
            $content .= $this->showAllAnswers( $quizData, $thePID,$resPID, false, 0 ); // 24.01.10: 0 statt $this->helperObj->getQuestionsNo()
        }


        if ( $quizData['cmd'] == 'score' && !$this->conf['isPoll'] ) {         /* ***************************************************** */
            /*
             * Display highscore page: top 10 table
             */
            if ($quizData["setUserData"] && $quizData['qtuid'] && $quizData["name"]) {    // ggf. erst Benutzerdaten in der DB setzen...
                $uid = intval($quizData['qtuid']);
                $firsttime = $this->helperObj->getFirstTime($uid);
                if ($firsttime == intval($quizData["setUserData"])) {        // Security test bestanden?
                    // update current user entry
                    $timestamp = time();
                    // Avoid bad characters in database request
                    $quiz_taker_name = $GLOBALS['TYPO3_DB']->quoteStr($quizData["name"], $this->tableAnswers);
                    $quiz_taker_email  = $GLOBALS['TYPO3_DB']->quoteStr($quizData["email"], $this->tableAnswers);
                    $quiz_taker_homepage = $GLOBALS['TYPO3_DB']->quoteStr($quizData["homepage"], $this->tableAnswers);
                    $hidden = intval($quizData["hidden"]);
                    $update = array('tstamp' => $timestamp,
                                    'name' => $quiz_taker_name,
                                    'email' => $quiz_taker_email,
                                    'homepage' => $quiz_taker_homepage,
                                    'hidden' => $hidden);
                    // Hook for tt_address
                    if (is_array($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['setAdrHook'])&& $this->conf['userData.']['tt_address_pid']) {
                        foreach ($GLOBALS['TYPO3_CONF_VARS']['EXTCONF'][$this->extKey]['setAdrHook'] as $_classRef) {
                            $_procObj = & \TYPO3\CMS\Core\Utility\GeneralUtility::getUserObj($_classRef);
                            $update['address_uid'] =
                                intval($_procObj->setAdr($quizData, $this->conf['userData.']['tt_address_pid'], $this->conf['userData.']['tt_address_groups']));
                        }
                    }
                    $success = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->tableAnswers, 'uid='.$uid.' AND sys_language_uid='.$this->lang, $update);
                    if(!$success){
                        $content.="<p>MySQL Update-Error :-(</p>";
                    }
                }
                if (($this->conf['email.']['send_admin']==2) || ($this->conf['email.']['send_user']==2))
                    $sendMail = true;
            }
            if ($finalPage)
                $markerArrayP["###REF_HIGHSCORE###"] = $this->showHighscoreList($quizData, $resPIDs, $listPID);
            else
                $content .= $this->showHighscoreList($quizData, $resPIDs, $listPID);

        } else if ( $quizData['cmd'] == 'list' && $this->conf['isPoll'] ) {    /* ***************************************************** */
            /*
             * Display poll result
             */
            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Poll result: searching question in '.$thePID.' and results in '.$resPID, $this->extKey, 0);
            if ($answerPage)
                $markerArrayP["###REF_POLLRESULT###"] = $this->showPollResult($answered, $quizData, $thePID,$resPID);
            else
                $content .= $this->showPollResult($answered, $quizData, $thePID,$resPID);
        }


        if ( !$this->conf['isPoll'] && $quizData['cmd']!='score' ) {    // show highscore link?
              $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_HIGHSCORE_URL###");
            $markerArrayP["###REF_HIGHSCORE_URL###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
          }

        if ( $this->conf['isPoll'] && $quizData['cmd']!='list' ) {    // show poll result link?
              $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_POLLRESULT_URL###");
                $markerArrayP["###REF_POLLRESULT_URL###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
          }


        /** Layout for the start page **/
        if ($startPage) {
            $markerArrayP["###REF_PAGE_LIMIT###"] = '';
            $markerArrayP["###REF_QUIZ_LIMIT###"] = '';
            $markerArrayP["###HIDDENFIELDS###"] = '<input type="hidden" name="'.$this->prefixId.'[fromStart]" value="1" />';
            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_USER_TO_SUBMIT###");
            $markerArray['###BACK_STYLE###'] = ' style="display:none;"';
            if (is_object($this->freeCap) && $this->conf['enableCaptcha']) {
                $markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
            } else {
                $subpartArray['###CAPTCHA_INSERT###'] = '';
            }
            $markerArrayP["###REF_SUBMIT_FIELDS###"] = $this->templateService->substituteMarkerArrayCached($template,$markerArray,$subpartArray,$wrappedSubpartArray);

            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_PAGE###");
            if ($template == '')    // if it is not in the template
                $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_QUESTION_PAGE###");
            $content .= $this->templateService->substituteMarkerArray($template, $markerArrayP);
        }


        /** Layout for a result page **/
        if ($answerPage && $quizData['cmd'] != 'exit') {
            if ( $this->conf['quizTimeMinutes'] ) {
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_TIME_LIMIT###");
                if (!$quizData['qtuid']) {
                    $markerArray["###VAR_SECS###"] = intval($this->conf['quizTimeMinutes'])*60;
                    $markerArray["###VAR_MIN###"] = $this->conf['quizTimeMinutes'];
                } else {
                    $markerArray["###VAR_SECS###"] = intval($this->conf['quizTimeMinutes'])*60 - $elapseTime;
                    $markerArray["###VAR_MIN###"] = round($markerArray["###VAR_SECS###"]/60);
                }
                $markerArrayP["###REF_QUIZ_LIMIT###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
            } else {
                $markerArrayP["###REF_QUIZ_LIMIT###"] = '';
            }

            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Displaying result-page...', $this->extKey, 0);
            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_RESULT_PAGE###");
            if ($template == '')    // if it is not in the (old custom) template
                $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_RESULT_PAGE###");
            $content .= $this->templateService->substituteMarkerArray($template, $markerArrayP);

            if ($this->conf['isPoll'] && $this->conf['email.']['send_admin'])
                $sendMail = true;
        }


        /** Layout for a questions page **/
        if ($questionPage && $quizData['cmd'] != 'exit') {
            if ( $this->conf['pageTimeSeconds'] ) {
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_PAGE_TIME_LIMIT###");
                $markerArray["###VAR_SECS###"] = ($pageTimeCat) ? $pageTimeCat : $this->conf['pageTimeSeconds'];
                $markerArrayP["###REF_PAGE_LIMIT###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
            } else {
                $markerArrayP["###REF_PAGE_LIMIT###"] = '';
            }

            if ( $this->conf['quizTimeMinutes'] ) {
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_TIME_LIMIT###");
                if (!$quizData['qtuid']) {
                    $markerArray["###VAR_SECS###"] = intval($this->conf['quizTimeMinutes'])*60;
                    $markerArray["###VAR_MIN###"] = $this->conf['quizTimeMinutes'];
                } else {
                    $markerArray["###VAR_SECS###"] = intval($this->conf['quizTimeMinutes'])*60 - $elapseTime;
                    $markerArray["###VAR_MIN###"] = round($markerArray["###VAR_SECS###"]/60);
                }
                $markerArrayP["###REF_QUIZ_LIMIT###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
            } else {
                $markerArrayP["###REF_QUIZ_LIMIT###"] = '';
            }

            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_PAGE###");
            if ($template == '')    // if it is not in the template
                $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_QUESTION_PAGE###");
            $content .= $this->templateService->substituteMarkerArray($template, $markerArrayP);
        }


        /** Layout for the last/final page **/
        $uidP = 0;        // page UID
        $uidC = 0;        // content UID
        if ($finalPage && $quizData['cmd'] != 'exit') {  // nicht noetig: && !$this->conf['isPoll']) {

           	if (($this->conf['showAnalysis'] || $this->conf['showEvaluation']) && !$this->conf['dontShowPoints']) {    // Analysis depends on points
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Displaying Analysis: '.$this->conf['showAnalysis'].' or Evaluation: '.$this->conf['showEvaluation'], $this->extKey, 0);
                if (!$markerArray["###VAR_TMAX_POINTS###"]) {
                    $this->helperObj->setQuestionsVars();
                    $markerArray["###VAR_TMAX_POINTS###"]=$this->helperObj->getQuestionsMaxPoints();    // omax und tmax sollten hier gleich sein
                    if (!$markerArray["###VAR_TMAX_POINTS###"]) $markerArray["###VAR_TMAX_POINTS###"]=100000;    // notfalls halt irgendein wert
                }
                $points = $markerArray["###VAR_TOTAL_POINTS###"];
                $percent = 100 * $points / $markerArray["###VAR_TMAX_POINTS###"];    // genauerer wert...
                $markerArray["###VAR_PERCENT###"] = intval($percent);
                $markerArray["###REACHED1###"] = $this->pi_getLL('reached1','reached1');
                $markerArray["###REACHED2###"] = $this->pi_getLL('reached2','reached2');
                if ($this->helperObj->writeDevLog)
                    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Total points='.$markerArray["###VAR_TOTAL_POINTS###"].' tmax points='.$markerArray["###VAR_TMAX_POINTS###"].' percent='.$markerArray["###VAR_PERCENT###"], $this->extKey, 0);
                if ($this->conf['showAnalysis'])
                    $dataArray = explode(',', $this->conf['showAnalysis']);
                else
                    $dataArray = explode(',', $this->conf['showEvaluation']);
                $templateNo = '';
                $p = 0;
                while ($p < count($dataArray)) {
                    $templateNo = $dataArray[$p];
                    if (strpos($templateNo, ':') !== false) {
                        if ($this->conf['showAnalysis'])
                            list($templateNo, $uidP) = explode(":", $templateNo);
                        else
                            list($templateNo, $uidC) = explode(":", $templateNo);
                    }
                    if ($this->conf['showAnalysis']) {
                        if ($percent <= floatval($templateNo)) {        // wann abbrechen?
                        //    if ($uidP) ...    // redirect to a URL with that UID  -> weiter nach unten verschoben (nach email Versendung!!)
                            break;
                        }
                    } else {
                        if ($points <= floatval($templateNo)) {        // wann abbrechen?
                            break;
                        }
                    }
                    $p++;
                }
                if (!($uidP || $uidC)) {
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_ANALYSIS_$templateNo###");
                    $markerArrayP["###REF_QUIZ_ANALYSIS###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
                } else if ($uidC) {
                    $confC = array('tables' => 'tt_content','source' => $uidC, 'dontCheckPid' => 1);
                    $markerArrayP["###REF_QUIZ_ANALYSIS###"] = $this->cObj->RECORDS($confC);
                }
            } else if ($this->conf['showCategoryElement']) {
                $showCategoryElement = intval($this->conf['showCategoryElement']);
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Displaying category element: '.$showCategoryElement.' nextcat: '.$nextCat, $this->extKey, 0);
                if (($showCategoryElement==1) && ($nextCat>0) && ($this->catArray[$nextCat]['celement'])) {
                    $uidC = $this->catArray[$nextCat]['celement'];
                    $confC = array('tables' => 'tt_content','source' => $uidC, 'dontCheckPid' => 1);
                    $markerArrayP["###REF_QUIZ_ANALYSIS###"] = $this->cObj->RECORDS($confC);    // last category element
                } else if (($showCategoryElement>1) && ($this->conf['advancedStatistics'])) {
                    $tmpCat='';
                    $usedCatArray = array();
                    $catCount = 0;
                    $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('DISTINCT nextcat, COUNT(nextcat) anzahl',
                        $this->tableRelation,
                        'nextcat>0 AND user_id=' . intval($quizData['qtuid']) . $this->helperObj->getWhereLang(), //.' '.$this->cObj->enableFields($this->tableRelation), auskommentiert am 7.11.10
                        'nextcat',
                        'anzahl DESC',
                        '');
                    $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                    if ($rows>0) {
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                            $tmpCat = $row['nextcat'];
                            if ($catCount==0) $firstCount = $row['anzahl'];
                            if ($tmpCat && ($this->catArray[$tmpCat]['celement']) && (($showCategoryElement==4) || (($showCategoryElement==3) && ($firstCount==$row['anzahl'])) || ($catCount==0)))
                                $usedCatArray[$tmpCat] = $row['anzahl'];
                            $catCount += $row['anzahl'];
                        }
                    }
                    if (count($usedCatArray)>0) {
                        $markerArrayC = array();
                        $markerArrayC['###PREFIX###'] = $markerArrayP['###PREFIX###'];
                        $markerArrayC['###ANSWERS###'] = $this->pi_getLL('answers','answers');
                        $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_CATEGORY_ELEMENT###");
                        foreach ($usedCatArray as $key => $value) {
                            $uidC = $this->catArray[$key]['celement'];
                            $confC = array('tables' => 'tt_content','source' => $uidC, 'dontCheckPid' => 1);
                            $markerArrayC['###CATEGORY###'] = $this->catArray[$key]['name'];
                            $markerArrayC['###CONTENT###'] = $this->cObj->RECORDS($confC);    // most category element
                            $markerArrayC['###VAR_COUNT###'] = $value;
                            $markerArrayC['###VAR_PERCENT###'] = round(100*$value/$catCount);
                            $markerArrayP["###REF_QUIZ_ANALYSIS###"] .= $this->templateService->substituteMarkerArray($template, $markerArrayC);
                        }
                    }
                    $GLOBALS['TYPO3_DB']->sql_free_result($res5);
                }
            }

            if (!$uidP) {            // no redirect
                $markerArrayP["###FORM_URL###"] = $this->pi_getPageLink($listPID);        // zur endgueltig letzten seite wechseln
                $markerArrayP["###REF_SUBMIT_FIELDS###"] = '';
                $markerArrayP["###HIDDENFIELDS###"] = '';
                $markerArrayP["###FE_USER_UID###"] = intval($GLOBALS['TSFE']->fe_user->user['uid']);
                if ( $this->conf['userData.']['showAtFinal'] ) {
                    $quiz_taker_name = $GLOBALS['TYPO3_DB']->quoteStr($quizData["name"], $this->tableAnswers);
                    $quiz_taker_email  = $GLOBALS['TYPO3_DB']->quoteStr($quizData["email"], $this->tableAnswers);
                    $quiz_taker_homepage = $GLOBALS['TYPO3_DB']->quoteStr($quizData["homepage"], $this->tableAnswers);
                    $markerArray["###REAL_NAME###"] = $quiz_taker_name;
                    $markerArray["###REAL_EMAIL###"] = $quiz_taker_email;
                    $markerArray["###REAL_HOMEPAGE###"] = $quiz_taker_homepage;
                    $markerArray["###RESULT_FOR###"] = $this->pi_getLL('result_for','result_for');
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_USER_SUBMITED###"); // Output the user name
                    $markerArrayP["###REF_INTRODUCTION###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
                }

                // User-Data Template
                if ( $this->conf['userData.']['askAtFinal'] && $quizData['qtuid'] ) {
                    $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_USER_TO_SUBMIT###");
                    $markerArray['###BACK_STYLE###'] = ' style="display:none;"';
                    if (is_object($this->freeCap) && $this->conf['enableCaptcha']) {
                        $markerArray = array_merge($markerArray, $this->freeCap->makeCaptcha());
                    } else {
                        $subpartArray['###CAPTCHA_INSERT###'] = '';
                    }
                    $firsttime = $this->helperObj->getFirstTime($quizData['qtuid']);
                    $markerArrayP["###REF_SUBMIT_FIELDS###"] = $this->templateService->substituteMarkerArrayCached($template,$markerArray,$subpartArray,$wrappedSubpartArray);
                    $markerArrayP["###HIDDENFIELDS###"] = "\n".'  <input type="hidden" name="'.$this->prefixId.'[qtuid]" value="'.intval($quizData["qtuid"]).'" />'."\n";
                     $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[setUserData]" value="'.$firsttime.'" />'."\n";
                    $markerArrayP["###HIDDENFIELDS###"] .= '  <input type="hidden" name="'.$this->prefixId.'[fromFinal]" value="1" />';
                }

                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_FINAL_PAGE###");
                if ($template == '')    // if it is not in the template
                    $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_QUIZ_FINAL_PAGE###");
                $content .= $this->templateService->substituteMarkerArray($template, $markerArrayP);
            }

            if (($this->conf['email.']['send_admin']==1) || ($this->conf['email.']['send_user']==1))
                $sendMail = true;
        }

        /* Change begin */
        /***************************************************************
         *  Extension for sending emails depending on users answers
         *  (c) 2016 Marcel Utz
        ***************************************************************/

        $emailAnswers = false;
        $flexformEmailAnswers = ($this->conf['email.']['answers']);
        if($flexformEmailAnswers != '') {
        	$emailAnswers = json_decode($flexformEmailAnswers, true);
        	if(json_last_error() === 0) {
        		if(is_array($emailAnswers))
        			$emailQuestionIds = implode(',', array_keys($emailAnswers));
        	} else {
        		if ($this->helperObj->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Error: Flexform "E-Mail" > "Send e-mail if specific answer(s)": Invalid or malformed JSON.', $this->extKey, 0);
        		$emailAnswers = false;
        	}
        }
        $answersEmail = false;

        // Get answers of user from table $this->tableRelation
        if (isset($emailQuestionIds) and is_array($emailAnswers) and $quizData['qtuid']) {

            // Get answers of users from statisticsArray
            if (is_array($statisticsArray)) {
                    foreach ($statisticsArray as $questionId => $values) {
                        for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                            $userHash[$questionId]['c'.$answerNumber] = ($values['a'.$answerNumber]) ? 1 : 0;
                        }
                        $userHash[$questionId]['points'] = (($values['points']) ? $values['points'] : 0);
                        $userHash[$questionId]['textinput'] = $values['text'];
                    }
            }

            // Transform users answers to array with only answered answers
            $answersSelected = false;
            if(is_array($userHash)) {
                foreach($userHash as $questionId => $answers) {
                    foreach($answers as $answerCheckedId => $answer) {
                        if($answer == '1') {
                            $answerId = substr($answerCheckedId, 1, (strlen($answerCheckedId)-1));
                            $answersSelected[$questionId][] = $answerId;
                        }
                    }
                }
            }

            // Transform users answers to email addresses
            if(is_array($answersSelected)) {
                foreach($answersSelected as $questionId => $answers) {
                    foreach($answers as $answer) {
                        if(isset($emailAnswers[$questionId][$answer]))
                            $answersEmail[] = $emailAnswers[$questionId][$answer];
                    }
                }
            }
        }

        /***************************************************************
         *  Extension for sending emails depending on users answers
         *  End
        ***************************************************************/

        /** Send one/two email(s)? **/
        if (($sendMail || (is_array($answersEmail))) && !$error && !$secondVisit) {
            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Entering "send emails"...', $this->extKey, 0);
            $markerArrayP["###EMAIL_TAKEN###"] = $this->pi_getLL('email_taken','email_taken');
            if (!$markerArrayP["###REF_INTRODUCTION###"] && !$this->conf['isPoll']) {
                if (!$markerArray["###RESULT_FOR###"]) {
                    $markerArray["###REAL_NAME###"] = htmlspecialchars($quizData['name']);
                    $markerArray["###REAL_EMAIL###"] = htmlspecialchars($quizData['email']);
                    $markerArray["###REAL_HOMEPAGE###"] = htmlspecialchars($quizData['homepage']);
                    $markerArray["###RESULT_FOR###"] = $this->pi_getLL('result_for','result_for');
                }
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUIZ_USER_SUBMITED###"); // Output the user name
                $markerArrayP["###REF_INTRODUCTION###"] = $this->templateService->substituteMarkerArray($template, $markerArray);
            }

            if ($this->conf['showAllCorrectAnswers'] && !$this->conf['isPoll']) {        // show all answers...
                $quizData['sendEmailNow'] = true;            // noetig um zu wissen, welches Template genommen werden soll
                $markerArrayP["###REF_EMAIL_ALLANSWERS###"] = $this->showAllAnswers( $quizData, $thePID,$resPID, true, 0 );
            } else {
                $markerArrayP["###REF_EMAIL_ALLANSWERS###"] = '';
            }

            if ($this->conf['email.']['send_admin'] && \TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($this->conf['email.']['admin_mail']) && $this->conf['email.']['admin_subject']) {
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Sending email to admin: '.$this->conf['email.']['admin_mail'], $this->extKey, 0);
                $markerArrayP["###SUBJECT###"] = $this->conf['email.']['admin_subject'];
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_ADMIN_EMAIL###");
                if ($template == '')    // if it is not in the template
                    $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_ADMIN_EMAIL###");
                $mailcontent = $this->templateService->substituteMarkerArray($template, $markerArrayP);
                $this->helperObj->sendEmail($mailcontent,'',$this->conf['email.']['admin_mail'],$this->conf['email.']['admin_name'],$this->conf['email.']['admin_subject']);
            }
            if ($this->conf['email.']['send_user'] && \TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($quizData["email"]) && $this->conf['email.']['user_subject']) {
                if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Sending email to user: '.$quizData["email"], $this->extKey, 0);
                $markerArrayP["###SUBJECT###"] = $this->conf['email.']['user_subject'];
                $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_USER_EMAIL###");
                if ($template == '')    // if it is not in the template
                    $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_USER_EMAIL###");
                $mailcontent = $this->templateService->substituteMarkerArray($template, $markerArrayP);
                $this->helperObj->sendEmail($mailcontent,'',$quizData["email"],$quizData["name"],$this->conf['email.']['user_subject']);
            }

            if(isset($answersEmail) and is_array($answersEmail)) {
                foreach($answersEmail as $emailSettings) {
                    $email = false;
                    $name = false;
                    $subject = false;
                    $templatePostfix = '';
                    $email = $emailSettings['email'];
                    if(isset($emailSettings['name']))
                        $name = $emailSettings['name'];
                    else
                        $name = $email;
                    if(isset($emailSettings['subject']))
                        $subject = $emailSettings['subject'];
                    if(isset($emailSettings['template']))
                        $templatePostfix = '_'.strtoupper($emailSettings['template']);
                    if ($email && \TYPO3\CMS\Core\Utility\GeneralUtility::validEmail($email) && $subject) {
                        if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Sending email to: '.$email, $this->extKey, 0);
                        $markerArrayP["###SUBJECT###"] = $subject;
                        $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_ANSWER_EMAIL".$templatePostfix."###");
                        if ($template == '')    // if it is not in the template
                            $template = $this->templateService->getSubpart($this->origTemplateCode, "###TEMPLATE_ANSWER_EMAIL".$templatePostfix."###");
                        $mailcontent = $this->templateService->substituteMarkerArray($template, $markerArrayP);
                        $this->helperObj->sendEmail($mailcontent,'',$email,$name,$subject);
                        //var_dump($mailcontent,'',$email,$name,$subject);
                    }
                }
            }
        }

        /** Delete user result at the end of the quiz? **/
        if ($finalPage && $this->conf['deleteResults']) {    // 60*60*24 = 86400 = 1 tag
            $loesche='';
            $counter=0;
            $where = 'pid='.$resPID;
            $where .= ' AND (crdate<'.(time()-86400).' OR uid='.intval($quizData['qtuid']).')';
            if ($this->conf['deleteResults'] == 2) $where .= ' AND fe_uid=0';
            if ($this->conf['deleteResults'] == 3) $where .= " AND name='".$GLOBALS['TYPO3_DB']->quoteStr($this->pi_getLL('no_name','no_name'), $this->tableAnswers)."'";
            $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid',$this->tableAnswers,$where,'','crdate DESC','255');
            if ($GLOBALS['TYPO3_DB']->sql_num_rows($res5) > 0) {
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                    $loesche .= ', '.$row['uid'];
                    $counter++;
                }
                $loesche=substr($loesche,1);
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res5);
            if ($this->conf['deleteDouble'] && $counter<250) {
                // mehrfache eintraege eines users zu einem quiz loeschen
                $where2='';
                $fe_uid = intval($GLOBALS['TSFE']->fe_user->user['uid']);
                if ($this->conf['deleteResults'] == 2 && $fe_uid>0) {
                    $where2 = ' AND fe_uid='.$fe_uid;
                } else if ($this->conf['deleteResults'] == 3 && $quizData["name"]!=$this->pi_getLL('no_name','no_name') && $quizData["name"]) {
                    $where2 = " AND name='".$GLOBALS['TYPO3_DB']->quoteStr($quizData["name"], $this->tableAnswers)."'";
                }
                if ($where2) {
                    $start_uid = $this->helperObj->getStartUid($quizData['qtuid']);
                    $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid, o_percent, crdate',
                        $this->tableAnswers,
                        'pid=' . $resPID . $this->helperObj->getWhereLang() . ' AND start_uid=' . $start_uid . $where2,
                        '',
                        'o_percent DESC, crdate DESC',
                        '200');
                    $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                    if ($rows>0) {
                        $counter=0;
                        while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                            $counter++;
                            if ($counter>1) {
                                $loesche .= ($loesche) ? ','.$row['uid'] : $row['uid'];
                            }
                        }
                    }
                    $GLOBALS['TYPO3_DB']->sql_free_result($res5);
                }
            }
            if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Deleting result data with: '.$where.$where2.' => '.$loesche, $this->extKey, 0);
            if ($loesche) {
                $res = $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->tableRelation, 'user_id IN ('.preg_replace('/[^0-9,]/','',$loesche).')');
                $res = $GLOBALS['TYPO3_DB']->exec_DELETEquery($this->tableAnswers, 'uid IN ('.preg_replace('/[^0-9,]/','',$loesche).')');
            }
        }

        /** Redirect at the end of the quiz? **/
        if ($finalPage && $uidP) {        // redirect to a URL with that UID
            $this->redirectUrl($uidP, array($this->prefixId.'[name]'=>$quizData["name"],$this->prefixId.'[email]'=>$quizData["email"],$this->prefixId.'[homepage]'=>$quizData["homepage"]));
        }

		if ($this->helperObj->writeDevLog) \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('return the content to the TYPO3 core', $this->extKey, 0);
        return $this->pi_wrapInBaseClass($content);
    }




    /**
     * show all answers
     *
     * @param    array    $quizData: quiz-data
     * @param    mixed    $thePID: uid of the folder(s) with the questions
     * @param    int        $resPID: uid of the folder with user-results
     * @param    boolean    $isEmail: for email?
     * @param    int        $questionsTotal: not in use?
     * @return    string    The content that should be displayed on the website
     */
    function showAllAnswers($quizData, $thePID,$resPID, $isEmail, $questionsTotal) {
        $questions = '';
        $temp_output = '';
        $statHash = array();
        $userHash = array();
        if ($this->conf['advancedStatistics']) {    // load and show advanced statistics
            $select = '';
            for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                $select .=     'sum(checked'.$answerNumber.'),';
            }
            $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select.' question_id',
                $this->tableRelation,
                'pid=' . $resPID . $this->helperObj->getWhereLang() . ' AND (hidden=0 OR user_id=' . intval($quizData['qtuid']) . ')',    // statt .$this->cObj->enableFields($this->tableRelation), am 15.11.10
                'question_id');
            $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
            if ($rows>0) {
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                    $qno = $row['question_id'];
                    for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                        $statHash[$qno]['c'.$answerNumber] = $row['sum(checked'.$answerNumber.')'];
                    }
                }
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res5);
            //print_r($statHash);

            if ($quizData['qtuid']) {
                $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                    $this->tableRelation,
                    'user_id=' . intval($quizData['qtuid']) . ' AND pid=' . $resPID . $this->helperObj->getWhereLang()); //.' '.$this->cObj->enableFields($this->tableRelation), // auskommentiert am 15.11.10
                $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
                if ($rows>0) {
                    while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                        $qno = $row['question_id'];
                        for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                            $userHash[$qno]['c'.$answerNumber] = $row['checked'.$answerNumber];
                        }
                        $userHash[$qno]['textinput'] = $row['textinput'];
                        $userHash[$qno]['points'] = $row['points'];
                    }
                }
                $GLOBALS['TYPO3_DB']->sql_free_result($res5);
            }
            //print_r($userHash);

            $template_okok = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_CORR_ANSW###");
            $template_notok = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_NOTCORR_ANSW###");
        }
        $template_oknot = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_CORR_NOTANSW###");
        $template_notnot = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_NOTCORR_NOTANSW###");

        // welches Template nehmen???
        if ($quizData["sendEmailNow"]) {
            $template_entry = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_EMAIL_ALLANSWERS###");
        } else if ($quizData['cmd'] == 'allanswers') {
            $template_entry = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_ALLANSWERS###");
        } else {
            $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_NO_MORE###");
            $template_entry = $this->templateService->getSubpart($template, "###QUIZ_ANSWERS###");
        }
        $template_answer = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_CORR###");
        $template_star_average = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QR_STAR_AVERAGE###");
        $template_expl = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_EXPLANATION###");
        $template_delimiter = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_DELIMITER###");
        $template_image_begin = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_BEGIN###");
        $template_image_end = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_END###");
        if ($this->conf['advancedStatistics'] && $this->conf['showDetailAnswers']) {
            $template_hidden = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_DETAILS###");
            $template_details_link = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_DETAILS_LINK###");
        }
        if($this->conf['starRatingDetails']) {
            $template_star_rating_details_link = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_STAR_RATING_DETAILS_LINK###");
        }

        $imgTSConfig = array();
        $myAArray = array();
        $markerArrayS = array();
        $markerArrayS["###PREFIX###"] = $this->prefixId;
        $markerArrayS["###EXPLANATION###"] = $this->pi_getLL('listFieldHeader_explanation','listFieldHeader_explanation');
        $markerArrayS["###VAR_QUESTIONS###"] = $questionsTotal;

        $whereUIDs = '';
        $whereCat='';
        if ($quizData['qtuid']) {            // nur beantwortete Fragen anzeigen...
            $res4 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('qids,cids,fids',
                    $this->tableAnswers,
                    'uid='.intval($quizData['qtuid']));
            $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res4);
            if ($rows>0) {
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res4)){
                    $whereUIDs = ' AND uid IN (';
                    if ($this->conf['showAllCorrectAnswers']==2)
                        $whereUIDs .= preg_replace('/[^0-9,]/','',$row['cids']);
                    else if ($this->conf['showAllCorrectAnswers']==3)
                        $whereUIDs .= preg_replace('/[^0-9,]/','',$row['fids']);
                    else $whereUIDs .= preg_replace('/[^0-9,]/','',$row['qids']);
                    $whereUIDs .= ')';
                }
            }
        } elseif ($this->conf['startCategory']) {        // oder nur Fragen mit Kat.
            $whereCat = ' AND category='.intval($this->conf['startCategory']);
        } elseif ( $this->conf['onlyCategories'] ) {
            $whereCat = " AND category IN (".preg_replace('/[^0-9,]/','',$this->conf['onlyCategories']).")";
        }

        // Get all questions and answers from the database
        $questionsArray = array();
        $questionNumber = 0;
        $sortBy = $this->getSortBy();
        $res5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                $this->tableQuestions,
                'pid IN (' . $thePID . ')' . $whereUIDs . $whereCat . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
                '',
                $sortBy);
        $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res5);
        if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Showing result: '.$rows.' with whereUIDs='.$whereUIDs.' AND whereCat='.$whereCat . $this->helperObj->getWhereLang(), $this->extKey, 0);
        if ($rows>0) {
            if (!$questionsTotal)
                $markerArrayS["###VAR_QUESTIONS###"] = $rows;
            $runNumber = 0;
            while($rowA = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res5)){
                $questionUID = $rowA['uid'];

                if ($this->conf['advancedStatistics'] && $quizData['qtuid'] && !is_array($userHash[$questionUID]))
                    continue;                // vorher nicht angezeigte Fragen auch jetzt nicht anzeigen!

                $questionNumber++;
                $answerPointsBool = false;
                $markerArrayS["###VAR_QUESTION###"] = $questionNumber;
                $markerArrayS["###TITLE_HIDE###"] = ($rowA['title_hide']) ? '-hide' : '';
                $markerArrayS["###VAR_QUESTION_TITLE###"] = $rowA['title'];
                $markerArrayS["###VAR_QUESTION_NAME###"] = $this->formatStr($rowA['name']); // $this->pi_RTEcssText($rowA['name']);
                $markerArrayS["###REF_QR_ANSWER_CORR###"] = '';
                $markerArrayS["###REF_QR_ANSWER_ALL###"] = '';
                $markerArrayS["###REF_QR_EXPLANATION###"] = '';
                if ($questionNumber < $rows) {
                    $markerArrayS["###REF_DELIMITER###"] = $this->templateService->substituteMarkerArray($template_delimiter, $markerArrayS);
                } else {
                    $markerArrayS["###REF_DELIMITER###"] = '';
                }
                if ( !$this->conf['dontShowPoints'] && ($rowA['qtype']<5 || $rowA['qtype']==7) ) {
                    $markerArrayS["###P1###"] = $this->pi_getLL('p1','p1');
                    $markerArrayS["###P2###"] = $this->pi_getLL('p2','p2');
                    $markerArrayS["###NO_POINTS###"] = '0';
                } else {
                    $markerArrayS["###P1###"] = '';
                    $markerArrayS["###P2###"] = '';
                    $markerArrayS["###NO_POINTS###"] = '';
                }
                if ( $rowA['explanation']!='' ) {    // Explanation
                    $markerArrayS["###VAR_EXPLANATION###"] = $this->formatStr($rowA['explanation']);
                    $markerArrayS["###REF_QR_EXPLANATION###"] = $this->templateService->substituteMarkerArray($template_expl, $markerArrayS);
                }
                if ($rowA['image']) { // && !$isEmail) {
                    $markerArrayS["###VAR_QUESTION_IMAGE###"] = $this->helperObj->getImage($rowA['image'], $rowA["alt_text"], $isEmail);
                    $markerArrayS["###REF_QUESTION_IMAGE_BEGIN###"] = $this->templateService->substituteMarkerArray($template_image_begin, $markerArrayS);
                    $markerArrayS["###REF_QUESTION_IMAGE_END###"] = $template_image_end;
                } else {
                    $markerArrayS["###VAR_QUESTION_IMAGE###"] = '';
                    $markerArrayS["###REF_QUESTION_IMAGE_BEGIN###"] = '';
                    $markerArrayS["###REF_QUESTION_IMAGE_END###"] = '';
                }
            /*    if ( !$this->conf['dontShowPoints'] && !$this->conf['isPoll'] && $this->conf['noNegativePoints']<3 ) { // 20.01.10: wenn alles richtig sein muss, antwort-punkte ignorieren
                    for ($currentValue=1; $currentValue <= $this->answerChoiceMax; $currentValue++) {
                        if (intval($rowA['points'.$currentValue])>0 ) {
                            $answerPointsBool = true;
                            break;
                        }
                    }
                }*/

                //Star rating:
                if($rowA['qtype']==7) {
                    //count numbers of answers/stars
                    unset($totalAnswers);
                    for( $answerNumberCount=1; $answerNumberCount < $this->answerChoiceMax+1; $answerNumberCount++ ) {
                        if($rowA['answer'.$answerNumberCount])
                            $totalAnswers = $answerNumberCount;
                    }

                    //Star rating: count average
                    $totalPoints = 0;
                    for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                        $totalPoints += $statHash[$questionUID]['c'.$answerNumber];
                    }

                    if ($totalPoints > 0) {
                        $answerNumber = 1;
                        $totalStars = 0;
                        while($answerNumber <= $totalAnswers) {
                            //$rowA['answer'.$answerNumber]
                            $points[$answerNumber] = $statHash[$questionUID]['c'.$answerNumber] * $answerNumber;
                            $totalStars += $points[$answerNumber];
                            $answerNumber++;
                        }
                        $average = number_format(($totalStars/$totalPoints), 2, ',', ' ');
                        $averageUnformatted = $totalStars/$totalPoints;
                    }

                    $temp_average = '';
                    $i = 1;
                    while($i <= $totalAnswers) {
                        $temp_average .= '<input name="qid'.$questionUID.'average" type="radio" class="star {split:2}" disabled="disabled" ';
                        $temp_average .= ( ( ($i-$averageUnformatted) <= 0.5 ) && ( ($i-$averageUnformatted) > 0 ) )  ? 'checked="checked"' : '';
                        $temp_average .= ' />';
                        $temp_average .= '<input name="qid'.$questionUID.'average" type="radio" class="star {split:2}" disabled="disabled" ';
                        $temp_average .= ( ( ($averageUnformatted-$i) < 0.5 ) && ( ($averageUnformatted-$i) >= 0 ) )  ? 'checked="checked"' : '';
                        $temp_average .= "  />\n";
                        $i++;
                    }
                    $markerArrayA = array();
                    $markerArrayA["###PREFIX###"] = $this->prefixId;
                    $markerArrayA["###VAR_QUESTION_STARS###"] = $temp_average;
                    $markerArrayA["###VAR_QUESTION_STARS_AVERAGE###"] = $average;
                    $markerArrayA["###VAR_COUNTS###"] = $totalPoints;
                    $temp_output = $this->templateService->substituteMarkerArray($template_star_average, $markerArrayA);
                    unset($markerArrayA);
                    if($this->conf['starRatingDetails']) {
                        if($this->conf['alwaysShowStarRatingDetails']) {
                            $temp_output .= '<script type="text/javascript">';
                            $temp_output .= ($runNumber == 0) ? 'var starRatingQIDs = new Array(\''.$questionUID.'\');' : 'starRatingQIDs.push(\''.$questionUID.'\')';
                            $temp_output .= '</script><div id="show_details-'.$questionUID.'"></div>';
                            $runNumber++;
                        } else {
                            $markerArrayD = array();
                            $markerArrayD['###PREFIX###'] = $this->prefixId;
                            $markerArrayD['###ID###'] = $questionUID;
                            $markerArrayD['###SHOW_DETAILS###'] = $this->pi_getLL('show_details','show_details');
                            $temp_output .= $this->templateService->substituteMarkerArray($template_star_rating_details_link, $markerArrayD);
                            unset($markerArrayD);
                        }
                    }
                }

                // myVars for questions
                $markerArrayS = array_merge($markerArrayS, $this->helperObj->setQuestionVars($questionNumber));

                for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                    if (($rowA['answer'.$answerNumber] || $rowA['answer'.$answerNumber] === '0' || in_array($rowA['qtype'], $this->textType)) && $rowA['qtype'] != 7) {    // was a answer set in the backend?

                        // myVars for answers
                        $markerArrayS = array_merge($markerArrayS, $this->helperObj->setAnswerVars($answerNumber, $rowA['qtype']));
                        $markerArrayS["###VAR_QA_NR###"] = $answerNumber;

                    //    if ($this->conf['advancedStatistics'] and $quizData['qtuid']) {
                            $tempAnswer = '';
                            $markerArrayS['###VAR_QUESTION_ANSWER###'] = $rowA['answer'.$answerNumber];
                            if ( !$this->conf['dontShowPoints'] && $rowA['qtype']<5 ) {    // keine Punkte bei Sternen
                                $markerArrayS["###VAR_ANSWER_POINTS###"] = 0;
                                if ($this->conf['noNegativePoints']<3)    // das ganze Verhalten am 21.01.10 geaendert...
                                    $markerArrayS['###VAR_ANSWER_POINTS###'] = intval($rowA['points'.$answerNumber]);
                                if ($markerArrayS['###VAR_ANSWER_POINTS###'] > 0) {
                                    $rowA['correct'.$answerNumber] = true;        // ACHTUNG: falls Punkte zu einer Antwort gesetzt sind, dann wird die Antwort als RICHTIG bewertet!
                                } else {
                                    $markerArrayS['###VAR_ANSWER_POINTS###'] = intval($rowA['points']);
                                }
                            /*    if ($answerPointsBool) {        // Bug fixed at 10.11.2009
                                    $markerArrayS["###VAR_ANSWER_POINTS###"] = intval($rowA['points'.$answerNumber]);
                                    if ($markerArrayS["###VAR_ANSWER_POINTS###"] > 0)
                                        $rowA['correct'.$answerNumber] = true;        // ACHTUNG: falls Punkte zu einer Antwort gesetzt sind, dann wird die Antwort als RICHTIG bewertet!
                                } else {
                                    $markerArrayS["###VAR_ANSWER_POINTS###"] = $rowA['points']; //intval($rowA['points']);
                                }*/
                            } else {
                                $markerArrayS["###VAR_ANSWER_POINTS###"] = '';
                            }
                            if ($this->conf['advancedStatistics']) {
                                if ($rowA['qtype']<5 && ($rowA['qtype']!=3 || ($answerNumber==1 && $rowA['correct1']))) {    // keine Prozente bei Sternen
                                    if ($statHash[$questionUID]['c1']+$statHash[$questionUID]['c2']+$statHash[$questionUID]['c3']+$statHash[$questionUID]['c4']+$statHash[$questionUID]['c5']+$statHash[$questionUID]['c6'] > 0) {
                                        $markerArrayS['###VAR_PERCENT###'] = number_format(100 * $statHash[$questionUID]['c'.$answerNumber] / ($statHash[$questionUID]['c1']+$statHash[$questionUID]['c2']+$statHash[$questionUID]['c3']+$statHash[$questionUID]['c4']+$statHash[$questionUID]['c5']+$statHash[$questionUID]['c6']), 2, ',', ' ');
                                        $markerArrayS['###VAR_PERCENT_INT###'] = round(100 * $statHash[$questionUID]['c'.$answerNumber] / ($statHash[$questionUID]['c1']+$statHash[$questionUID]['c2']+$statHash[$questionUID]['c3']+$statHash[$questionUID]['c4']+$statHash[$questionUID]['c5']+$statHash[$questionUID]['c6']));
                                    } else {
                                        $markerArrayS['###VAR_PERCENT###'] = '0';
                                        $markerArrayS['###VAR_PERCENT_INT###'] = '0';
                                    }
                                    $markerArrayS['###VAR_COUNTS###'] = $statHash[$questionUID]['c'.$answerNumber];
                                } else {
                                    $markerArrayS['###VAR_PERCENT###'] = '?';
                                    $markerArrayS['###VAR_PERCENT_INT###'] = '0';
                                    $markerArrayS['###VAR_COUNTS###'] = '?';
                                }
                            }

                            if ($this->conf['advancedStatistics'] && $quizData['qtuid'] && $userHash[$questionUID]['c'.$answerNumber] &&
                                    ( ($rowA['qtype']==3 && (!$rowA['answer'.$answerNumber] || strtolower($rowA['answer'.$answerNumber])==strtolower($userHash[$questionUID]['textinput']))) ||
                                      ($rowA['qtype']!=3 && $rowA['correct'.$answerNumber]) || ($rowA['qtype']==5) )) {
                                if ($rowA['qtype']==5 || !$rowA['answer'.$answerNumber]) {
                                    $textinput = htmlspecialchars($userHash[$questionUID]['textinput']);
                                    if ($textinput)
                                        $textinput = str_replace('\r\n', "<br />", $textinput);
                                    else
                                        $textinput = '&nbsp;';
                                    $markerArrayS["###VAR_QUESTION_ANSWER###"] = $textinput;
                                }
                                $tempAnswer = $this->templateService->substituteMarkerArray($template_okok, $markerArrayS);
                            } else if ($this->conf['advancedStatistics'] and $quizData['qtuid'] && $userHash[$questionUID]['c'.$answerNumber]) {
                                $tempAnswer = $this->templateService->substituteMarkerArray($template_notok, $markerArrayS);
                            } else if ($rowA['correct'.$answerNumber] || ($rowA['qtype']==3 && $rowA['answer1'])) {
                                $tempAnswer = $this->templateService->substituteMarkerArray($template_oknot, $markerArrayS);
                                if ($this->conf['advancedStatistics'] && $quizData['qtuid'] && $rowA['qtype']==3 && ($userHash[$questionUID]['textinput'] || $userHash[$questionUID]['textinput']==='0')) {        // falsche antwort und richtige antwort ausgeben; hier gibt es 2 antworten !!!
                                    $textinput = str_replace('\r\n', "<br />", htmlspecialchars($userHash[$questionUID]['textinput']));
                                    $markerArrayS["###VAR_QUESTION_ANSWER###"] = $textinput;        // falschen werte berechnen...
                                /*    if ($statHash[$questionUID]['c1']+$statHash[$questionUID]['c2']+$statHash[$questionUID]['c3']+$statHash[$questionUID]['c4']+$statHash[$questionUID]['c5'] > 0)
                                        $markerArrayS['###VAR_PERCENT###'] = number_format(100 * $statHash[$questionUID]['c2'] / ($statHash[$questionUID]['c1']+$statHash[$questionUID]['c2']), 2, ',', ' ');
                                    else
                                        $markerArrayS['###VAR_PERCENT###'] = '0';
                                    $markerArrayS['###VAR_COUNTS###'] = $statHash[$questionUID]['c2']; */
                                    $markerArrayS['###VAR_PERCENT###'] = '?';    // man weiss ja nicht die Daten zu der eigenen Antwort
                                    $markerArrayS['###VAR_PERCENT_INT###'] = '0';
                                    $markerArrayS['###VAR_COUNTS###'] = '?';
                                    $tempAnswer .= $this->templateService->substituteMarkerArray($template_notok, $markerArrayS);
                                }
                            } else if (($rowA['answer'.$answerNumber] || $rowA['answer'.$answerNumber]===0) && $rowA['qtype'] != 7) {
                                $tempAnswer = $this->templateService->substituteMarkerArray($template_notnot, $markerArrayS);
                            }
                            if (in_array($rowA['qtype'], $this->textType) && $this->conf['advancedStatistics'] && $this->conf['showDetailAnswers'] && !$isEmail) {
                                $markerArrayD = array();
                                $markerArrayD['###PREFIX###'] = $this->prefixId;
                                $markerArrayD['###ID###'] = $questionUID;
                                $markerArrayD['###SHOW_DETAILS###'] = $this->pi_getLL('show_details','show_details');
                                $markerArrayD["###URL###"] = $this->pi_getPageLink($GLOBALS['TSFE']->id);
                                $tempAnswer.=$this->templateService->substituteMarkerArray($template_details_link, $markerArrayD);
                                $tempAnswer.=$this->templateService->substituteMarkerArray($template_hidden, $markerArrayD);
                                unset($markerArrayD);
                            }
                            $markerArrayS["###REF_QR_ANSWER_ALL###"] .= $tempAnswer;        // all answers in correct order

                    //    } else
                        if ($rowA['correct'.$answerNumber] || in_array($rowA['qtype'], $this->textType)) { // all correct answers
                            if ($rowA['correct'.$answerNumber])        // bug fixed at 25.4.10
                                $markerArrayS["###VAR_QUESTION_ANSWER###"] = $rowA['answer'.$answerNumber];
                            $markerArrayS["###REF_QR_ANSWER_CORR###"] .= $this->templateService->substituteMarkerArray($template_answer, $markerArrayS);
                        }
                    }
                    if (in_array($rowA['qtype'], $this->textType)) break 1;
                }

                if($rowA['qtype'] == 7) {
                    $markerArrayS["###REF_QR_ANSWER_ALL###"] .= $temp_output;
                    $markerArrayS["###REF_QR_ANSWER_CORR###"] .= $temp_output;
                    $temp_output = '';
                }

                if ($this->conf['advancedStatistics'] and $quizData['qtuid']) {
                    $markerArrayS['###RESULT_QUESTION_POINTS###'] = $this->pi_getLL('result_question_points','result_question_points');
                    $markerArrayS['###VAR_POINTS###'] = $userHash[$questionUID]['points'];
                }

                $questions .= $this->templateService->substituteMarkerArray($template_entry, $markerArrayS);
            }
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res5);
        return $questions;
    }


    /**
     * show highscore list
     *
     * @param    array    $quizData: quiz-data
     * @param    mixed    $resPID: uid(s) of the folder with user-results
     * @param    int        $listPID: uid of the detail highscore site
     * @return    string    The content that should be displayed on the website
     */
    function showHighscoreList($quizData, $resPID, $listPID) {

        $nr=0;
        $limit='';
        $date_format='m-d-Y';
        if ( $this->conf['highscore.']['dateFormat'] ) { $date_format=$this->conf['highscore.']['dateFormat']; } // date format
        $toID=0;
        $linkTo = $this->conf['highscore.']['linkTo'];
        if ($linkTo) {
            // Link zu einer front-end-user-seite
            $pos = strpos($linkTo, '&');
            $toID = intval(substr($linkTo,3,$pos-3));
            $params = explode('&', substr($linkTo,$pos+1));
            $toArray = array();
            $toParam = '';
            foreach ($params as $val) {
                $temp = explode('=', $val);
                if (!$toParam) $toParam=$temp[0];
                else $toArray[$temp[0]]=$temp[1];
            }
        }

        $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_HIGHSCORE###");
        $template_entry = $this->templateService->getSubpart($template, "###TEMPLATE_HIGHSCORE_ENTRY###");
        $template_caption = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_HIGHSCORE_CAPTION###");
        $template_quiz_taker = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_HIGHSCORE_QUIZ_TAKER###");
        $folderArray = array();
        $pageArray = array();
        $seekPages = preg_match("/###VAR_PAGE_NAME###/", $template_entry);

        if (!$this->conf['highscore.']['groupBy'] && preg_match("/###VAR_FOLDER_NAME###/", $template_entry)) {
            // Name der Ordner mit den Result-Ergebnissen holen
            // Alternative Methode siehe weiter unten
            if ($this->lang > 0) {
                $select = 'pid AS die_id,title';
                $from = 'pages_language_overlay';
                $where = 'pid IN ('.$resPID.') AND sys_language_uid='.$this->lang;
            } else {
                $select = 'uid AS die_id,title';
                $from = 'pages';
                $where = 'uid IN ('.$resPID.')';
            }
            $res3 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,$from,$where,'','','');
            if ($GLOBALS['TYPO3_DB']->sql_num_rows($res3)>0) {
                while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res3)){
                    $folderArray[$row['die_id']] = $row['title'];
                }
            }
            $GLOBALS['TYPO3_DB']->sql_free_result($res3);
        }

        $markerArray=array();
        $markerArrayH=array();
        $markerArrayS=array();
        $markerArray["###PREFIX###"] = $this->prefixId;
        $markerArray["###REF_HIGHSCORE_CAPTION###"] = '';
        $markerArray["###REF_HIGHSCORE_ENTRY###"] = '';

        if ( $this->conf['highscore.']['entries']>0 ) {
            $markerArray["###HIGHSCORE_CAPTION###"] = $this->pi_getLL('highscore_caption','highscore_caption');
            $markerArray["###VAR_HIGHSCORE_LIMIT###"] = $this->conf['highscore.']['entries'];
            $markerArray["###REF_HIGHSCORE_CAPTION###"] = $this->templateService->substituteMarkerArray($template_caption, $markerArray);
            $limit = intval($this->conf['highscore.']['entries']);
        }

        $markerArray["###NUMBER###"] = $this->pi_getLL('number','number');
        $markerArray["###POINTS###"] = $this->pi_getLL('points','points');
        $markerArray["###PERCENT###"] = $this->pi_getLL('percent','percent');
        $markerArray["###TIME###"] = $this->pi_getLL('time','time');
        $markerArray["###NAME###"] = $this->pi_getLL('name','name');
        $markerArray["###DATE###"] = $this->pi_getLL('date','date');

        if ( $this->conf['highscore.']['sortBy']=='percent' ) {
            $orderBy='percent';
            $flag = ' DESC';
        } else if ( $this->conf['highscore.']['sortBy']=='o_percent' ) {
            $orderBy='o_percent';
            $flag = ' DESC';
        } else if ( $this->conf['highscore.']['sortBy']=='time' ) {
            $orderBy='(lasttime-firsttime)';
            $flag = ' ASC';
        } else if ( $this->conf['highscore.']['sortBy']=='date' ) {
            $orderBy='crdate';
            $flag = ' DESC';
        } else if ( $this->conf['highscore.']['sortBy']=='lastcat' ) {
            $orderBy='lastcat';
            $flag = ' DESC';
        } else if ( $this->conf['highscore.']['sortBy']=='nextcat' ) {
            $orderBy='nextcat';
            $flag = ' DESC';
        } else {
            $orderBy='p_or_a';
            $flag = ' DESC';
        }

        $groupBy = $this->conf['highscore.']['groupBy'];
        if ($groupBy) {
            $addParams = array($this->prefixId.'[detail]' => 1);
            //$orderBy='sum('.$orderBy.')';
            $select = 'count(*) AS anzahl, sum(p_or_a) AS p_or_a, sum(p_max) AS p_max, sum(o_max) AS o_max, sum(percent)/count(*) AS percent, sum(o_percent)/count(*) AS o_percent';
            if ($groupBy=='fe_uid') {
                $select .= ', fe_uid, fe_users.'.$GLOBALS['TYPO3_DB']->quoteStr($this->conf['fe_usersName'], 'fe_users').' AS name, fe_users.email AS email, fe_users.www AS homepage';
                $from = ',fe_users';
                $where = ($this->conf['highscore.']['showUser'] && $GLOBALS['TSFE']->fe_user->user['uid']) ?
                    ' AND fe_uid='.intval($GLOBALS['TSFE']->fe_user->user['uid']) :    ' AND fe_uid>0';
                $where .= ' AND fe_uid=fe_users.uid';
            } else {
                $select .= ', name';
                $from = '';
                $where = '';
            }
            $groupBy = $GLOBALS['TYPO3_DB']->quoteStr($groupBy, $this->tableAnswers);
        } else {
            $select = 'uid,pid,p_or_a,p_max,percent,o_max,o_percent,lasttime,firsttime,name,email,homepage,crdate,fe_uid,start_uid,lastcat,nextcat';
            $from='';
            if ($quizData['detail'] && ($quizData['name'] || $quizData['fe_uid'])) {
                $where = ($quizData['fe_uid']) ? " AND fe_uid=".intval($quizData['fe_uid']) :
                                                 " AND name='".$GLOBALS['TYPO3_DB']->quoteStr($quizData['name'], $this->tableAnswers)."'";
            } else if ($this->conf['highscore.']['showUser'] && $GLOBALS['TSFE']->fe_user->user['uid'])
                $where = ' AND fe_uid='.intval($GLOBALS['TSFE']->fe_user->user['uid']);
            else $where = '';
        }
        if (!$this->conf['highscore.']['ignorePid'])
            $where .= ' AND '.$this->tableAnswers.'.pid IN ('.$resPID.')';

        $res4 = $GLOBALS['TYPO3_DB']->exec_SELECTquery($select,
                $this->tableAnswers.$from,
                $this->tableAnswers.'.sys_language_uid='.$this->lang.' '.$this->cObj->enableFields($this->tableAnswers).$where,
                $groupBy,
                $orderBy.$flag,
                $limit);
        $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res4);
        if ($this->helperObj->writeDevLog)    \TYPO3\CMS\Core\Utility\GeneralUtility::devLog($rows.' rows selected for list with: SELECT '.$select.' FROM '.$this->tableAnswers.$from.' WHERE '.$this->tableAnswers.'.pid IN ('.$resPID.') AND '.$this->tableAnswers.'.sys_language_uid='.$this->lang.' '.$this->cObj->enableFields($this->tableAnswers).$where.' GROUP BY '.$groupBy.' ORDER BY '.$orderBy.$flag.' LIMIT '.$limit, $this->extKey, 0);
        if ($rows>0) {
            while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res4)){
                if (!$groupBy) {
                    $aDate = date($date_format, $row['crdate']);
                    $aTime = mktime(0, 0, 0, 1, 1, 2009);
                    $aTime += intval($row['lasttime'])-intval($row['firsttime']); // - 60*60;    // - 1 Stunde, warum auch immer das noetig ist...
                    $aTime = date('G:i:s', $aTime);
                    $aPid = $row['pid'];
                }

                if (!$groupBy && $row['uid'] == $quizData['qtuid']) {        // aktueller eintrag
                    $markerArrayS["###EVEN_ODD###"] = $markerArrayS["###MY_EVEN_ODD###"] = '-act';
                } else {
                    if (is_array($this->conf['myVars.']['list.'])) {
                        foreach($this->conf['myVars.']['list.'] as $key => $value) {
                            $myQArray = explode($this->conf['myVars.']['separator'], $value);
                            $myKey = ($nr)%count($myQArray);
                            $markerArrayS["###MY_".strtoupper($key)."###"] = $myQArray[$myKey];
                        }
                    } else {        // aus kompatibilitaetsgruenden
                        if (($nr % 2) == 0) {
                            $markerArrayS["###EVEN_ODD###"] = ' tr-even';
                        } else {
                            $markerArrayS["###EVEN_ODD###"] = ' tr-odd';
                        }
                        $markerArrayS["###MY_EVEN_ODD###"] = $markerArrayS["###EVEN_ODD###"];    // hinzugefuegt am 2.9.2009
                    }
                }

                if ($row['fe_uid'] && $toID && $toParam) {
                    $toArray[$toParam] = $row['fe_uid'];
                    $markerArrayH["###VAR_NAME###"] = $this->pi_linkToPage($row['name'], $toID, $target = '', $toArray);
                } else {
                    $markerArrayH["###VAR_NAME###"] = $row['name'];
                }
                $markerArrayH["###VAR_EMAIL###"] = $row['email'];
                $markerArrayH["###VAR_HOMEPAGE###"] = $row['homepage'];
                if ($row['homepage'] != "" && $row['homepage'] != $this->pi_getLL('no_homepage','no_homepage')) {
                    $url_http=$row['homepage'];
                    if (substr($url_http, 0,4) != 'http')
                        $url_http='http://'.$url_http;
                    $markerArrayH["###VAR_TO_HOMEPAGE###"] = "<a href=\"".$url_http."\" target=\"hp\">-&gt; ".$this->pi_getLL('homepage','homepage')."</a><br/>";
                } else {
                    $markerArrayH["###VAR_TO_HOMEPAGE###"] = '';
                }
                if ($row['email'] != "" && $row['email'] != $this->pi_getLL('no_email','no_email')) {
                    $email_crypt = $GLOBALS['TSFE']->encryptEmail('mailto:'.$row['email']);
                    if ($email_crypt == 'mailto:'.$row['email']) {
                        $nameandemail = $email_crypt;
                    } else {
                        $nameandemail = "javascript:linkTo_UnCryptMailto('".$email_crypt."')";
                    }
                    $markerArrayH["###VAR_NAME_AND_EMAIL###"] = '<a href="'.$nameandemail.'">'.$row['name']."</a><br/>\n";
                } else {
                    $markerArrayH["###VAR_NAME_AND_EMAIL###"] = $row['name']."<br/>\n";
                }
                if ($groupBy) {
                    if ($groupBy=='fe_uid') $addParams[$this->prefixId.'[fe_uid]'] = $row['fe_uid'];
                    else $addParams[$this->prefixId.'[name]'] = $row['name'];
                    $markerArrayH["###LINK_DETAIL###"] = $this->pi_getPageLink($listPID, '', $addParams);
                } else {
                    $markerArrayH["###LINK_DETAIL###"] = '#';
                }
                $markerArrayS["###REF_HIGHSCORE_QUIZ_TAKER###"] = $this->templateService->substituteMarkerArray($template_quiz_taker, $markerArrayH);

                $markerArrayS["###VAR_CATEGORY###"] = $this->catArray[$row['lastcat']]['name'];
                $markerArrayS["###VAR_NEXT_CATEGORY###"] = $this->catArray[$row['nextcat']]['name'];
                $markerArrayS["###VAR_COUNT###"] = $nr+1;
                $markerArrayS["###VAR_POINTS###"] = $row['p_or_a'];
                $markerArrayS["###VAR_MAX###"] = $row['p_max'];
                $markerArrayS["###VAR_O_MAX###"] = $row['o_max'];
                $markerArrayS["###VAR_PERCENT###"] = intval($row['percent']);
                $markerArrayS["###VAR_O_PERCENT###"] = intval($row['o_percent']);
                $markerArrayS["###VAR_FE_UID###"] = intval($row['fe_uid']);
                if ($groupBy) {
                    $markerArrayS["###VAR_NUM_QUIZ###"] = $row['anzahl'];
                    $markerArrayS["###VAR_TIME###"] = '???';
                    $markerArrayS["###VAR_DATE###"] = '???';
                    $markerArrayS['###VAR_FOLDER_NAME###'] = '???';
                } else {
                    $markerArrayS["###VAR_NUM_QUIZ###"] = 1;
                    $markerArrayS["###VAR_TIME###"] = $aTime;
                    $markerArrayS["###VAR_DATE###"] = $aDate;
                    if ($row['start_uid'] && $seekPages) {
                        // Name der Seite mit dem Quiz holen
                        $start_uid=$row['start_uid'];
                        if (!$pageArray[$start_uid])
                             $pageArray[$start_uid] = $this->helperObj->getPageTitle($start_uid);
                        $markerArrayS['###VAR_PAGE_NAME###'] = $pageArray[$start_uid];
                    } else $markerArrayS['###VAR_PAGE_NAME###'] = '???';
                    $markerArrayS['###VAR_FOLDER_NAME###'] = $folderArray[$aPid];
                }
                $markerArray["###REF_HIGHSCORE_ENTRY###"] .= $this->templateService->substituteMarkerArray($template_entry, $markerArrayS);

                $nr++;
            }
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($res4);
        $subpart = $template;
        $template = $this->templateService->substituteSubpart($subpart, '###TEMPLATE_HIGHSCORE_ENTRY###', $markerArray["###REF_HIGHSCORE_ENTRY###"], 0);
        return $this->templateService->substituteMarkerArray($template, $markerArray);
    }

    /**
     * show poll result
     *
     * @param    int        $answered: question-uid
     * @param    array    $quizData: quiz-data
     * @param    mixed    $thePID: uid of the folder(s) with the questions
     * @param    int        $resPID: uid of the folder with user-results
     * @return    string    The content that should be displayed on the website
     */
    function showPollResult($answered, $quizData, $thePID,$resPID) {
        // poll answers
        $votes=0;
        $hits=0;
        $percent=0;
        $withStartCat=false;

        if ($this->tableAnswers=='tx_myquizpoll_result') {
            $dbanswer = 'p_or_a';
            $dbquestion = 'qids';
        } else {
            $dbanswer = 'answer_no';
            $dbquestion = 'question_id';
        }

        if (!$answered) {    // link clicked?
            $answered = $quizData["qid"];
        } else if (strpos($answered, ',')) {
            $alle = explode(",", $answered);
            $answered = $alle[0];
        }
        if ($answered) {
            $answeredP = ' AND uid='.intval($answered);
            $answeredQ = " AND $dbquestion=".intval($answered);
        } else if ($this->conf['startCategory']) {
            $withStartCat=true;
            $answeredP = ' AND category='.intval($this->conf['startCategory']);
        } else if ( $this->conf['onlyCategories'] ) {
            $answeredP = " AND category IN (".preg_replace('/[^0-9,]/','',$this->conf['onlyCategories']).")";
        }

        $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_POLLRESULT###");
        $template_entry = $this->templateService->getSubpart($template, "###TEMPLATE_POLLRESULT_ENTRY###");

        $markerArray=array();
        $markerArrayS=array();
        $markerArray["###PREFIX###"] = $this->prefixId;
        $markerArray["###REF_POLLRESULT_ENTRY###"] = '';
        $markerArray["###SECOND_VISIT###"] = ($quizData['secondVisit']) ? $this->pi_getLL('second_visit','second_visit') : '';
        $markerArray["###ANSWER###"] = $this->pi_getLL('answer','answer');
        $markerArray["###VOTES###"] = $this->pi_getLL('votes','votes');
        $markerArray["###VAR_RESPID###"] = $resPID;
        $markerArray["###VAR_LANG###"] = $this->lang;
		$markerArray["###VAR_CID###"] = $this->cObj->data['uid'];
        $markerArray["###VAR_FID###"] = '';
        $markerArray["###REMOTE_IP###"] = intval($this->conf['remoteIP']);
        $markerArray["###BLOCK_IP###"] = $this->conf['blockIP'];
        if ($this->conf['rating.']['parameter']) {    // rating
            $markerArray["###VAR_FID###"] = $this->helperObj->getForeignId();        // a foreign uid, added on 13.5.2012
        }
        $where_fid = ($markerArray["###VAR_FID###"]) ?
            " AND foreign_val='".$GLOBALS['TYPO3_DB']->quoteStr($markerArray["###VAR_FID###"], 'tx_myquizpoll_voting')."'" : '';
        $pollStart = intval($this->conf['pollStart']);

        // poll question (only latest question!)
        $queryResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('*',
                $this->tableQuestions,
                'pid IN (' . $thePID . ')' . $answeredP . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
                '',
                $this->getSortBy(), //'uid DESC',
                "$pollStart,1");
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult);
        if ($this->helperObj->writeDevLog)
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Poll result: selected question with pid IN ('.$thePID.')'.$answeredP . $this->helperObj->getWhereLang().': '.$row['uid'], $this->extKey, 0);

        $markerArray["###VAR_QID###"] = $row['uid'];
        $markerArray["###TITLE_HIDE###"] = ($row['title_hide']) ? '-hide' : '';
        $markerArray["###VAR_QUESTION_TITLE###"] = $row['title'];
        $markerArray["###VAR_EXPLANATION###"] = $this->formatStr($row['explanation']);
        if ( $row['explanation']!='' ) {    // Explanation
            $template_expl = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_EXPLANATION###");
            $markerArray["###EXPLANATION###"] = $this->pi_getLL('listFieldHeader_explanation', 'listFieldHeader_explanation');
            $markerArray["###VAR_EXPLANATION###"] = $this->formatStr($row['explanation']);
            $markerArray["###REF_QR_EXPLANATION###"] = $this->templateService->substituteMarkerArray($template_expl, $markerArray);
        } else {
            $markerArray["###VAR_EXPLANATION###"] = '';
            $markerArray["###REF_QR_EXPLANATION###"] = '';
        }
        $markerArray["###VAR_QUESTION_NAME###"] = $this->formatStr($row['name']); // $this->pi_RTEcssText($row['name']);
        if ($withStartCat && $row['uid']) {    // nur antworten zur frage einer kategorie nehmen
            $answeredQ = " AND $dbquestion=".intval($row['uid']);
        }
        if ($row['image']) {
            $template_image_begin = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_BEGIN###");
            $template_image_end = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_QUESTION_IMAGE_END###");
            $markerArray["###VAR_QUESTION_IMAGE###"] = $this->helperObj->getImage($row['image'], $row["alt_text"]);
            $markerArray["###REF_QUESTION_IMAGE_BEGIN###"] = $this->templateService->substituteMarkerArray($template_image_begin, $markerArray);
            $markerArray["###REF_QUESTION_IMAGE_END###"] = $template_image_end;
        } else {
            $markerArray["###VAR_QUESTION_IMAGE###"] = '';
            $markerArray["###REF_QUESTION_IMAGE_BEGIN###"] = '';
            $markerArray["###REF_QUESTION_IMAGE_END###"] = '';
        }

        // votes
        //************ HOTFIX von S. Rotheneder: only current question *************
        if((!$answeredQ || $answeredQ=='') && $row['uid'])
            $answeredQ = " AND $dbquestion = ".intval($row['uid']);
        //************ HOTFIX *************
        $res2 = $GLOBALS['TYPO3_DB']->exec_SELECTquery("count($dbanswer) AS anzahl, $dbanswer",
                $this->tableAnswers,
                'pid=' . $resPID . $answeredQ . $where_fid . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableAnswers),
                $dbanswer,
                $dbanswer,
                '');
        $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($res2);
        if ($this->helperObj->writeDevLog)
            \TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Poll result: selected answers from '.$this->tableAnswers.' with pid='.$resPID.$answeredQ.$where_fid . $this->helperObj->getWhereLang().': '.$rows, $this->extKey, 0);
        if ($rows>0) {
            while($row2 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($res2)){
                $aAnswer=$row2[$dbanswer];
                if ($aAnswer) {        // since 0.2.1
                    $row['votes'.$aAnswer]=$row2["anzahl"];    // Votes for a answer
                    $votes+=$row2["anzahl"];
                }
            }
        }

        // show the answers and votes
        $field = ($this->conf['votedOnly']) ? 'votes' : 'answer';
        for ($nr=1; $nr<=$this->answerChoiceMax; $nr++) {
          if ($row[$field . $nr] || $row[$field . $nr]==='0') {
            if (is_array($this->conf['myVars.']['list.'])) {
                foreach($this->conf['myVars.']['list.'] as $key => $value) {
                    $myQArray = explode($this->conf['myVars.']['separator'], $value);
                    $myKey = ($nr-1)%count($myQArray);
                    $markerArrayS["###MY_".strtoupper($key)."###"] = $myQArray[$myKey];
                }
            } else {
                $markerArrayS["###EVEN_ODD###"] = (($nr % 2) == 0) ? ' tr-even' : ' tr-odd';
            }

            $markerArrayS["###VAR_ANSWER###"] = $row['answer' . $nr];
            if ($row['votes'.$nr]) {
                $hits=$row['votes'.$nr];
                $percent=round(100*$row['votes'.$nr]/$votes, 2);
            } else {
                $hits=0;
                $percent=0;
            }
            $markerArrayS["###VAR_HITS###"] = $hits;
            $markerArrayS["###VAR_PERCENT###"] = $percent;
            $markerArrayS["###VAR_PERCENT_INT###"] = round($percent);
            $markerArrayS["###VAR_COUNT###"] = $nr;
            $markerArrayS["###VAR_SELECTED###"] = intval($quizData['answer1']);
            $markerArray["###REF_POLLRESULT_ENTRY###"] .= $this->templateService->substituteMarkerArray($template_entry, $markerArrayS);
            $markerArray["###VAR_ANSWER$nr###"] = $markerArrayS["###VAR_ANSWER###"];
            $markerArray["###VAR_HITS$nr###"] = $markerArrayS["###VAR_HITS###"];
            $markerArray["###VAR_PERCENT$nr###"] = $markerArrayS["###VAR_PERCENT###"];
            $markerArray["###VAR_ANSWERS###"] = $nr;
          } else {
            $markerArray["###VAR_ANSWER$nr###"] = 0;
            $markerArray["###VAR_HITS$nr###"] = 0;
            $markerArray["###VAR_PERCENT$nr###"] = 0;
            $markerArray["###VAR_PERCENT_INT$nr###"] = 0;
          }
        }
        $markerArray["###VAR_SELECTED###"] = intval($quizData['answer1']);
        $markerArray["###VAR_VOTES###"] = $votes;
        $this->subpart = $template;
        $template = $this->templateService->substituteSubpart($this->subpart, '###TEMPLATE_POLLRESULT_ENTRY###', $markerArray["###REF_POLLRESULT_ENTRY###"], 0);
        $out = $this->templateService->substituteMarkerArray($template, $markerArray);
        $GLOBALS['TYPO3_DB']->sql_free_result($queryResult);
        $GLOBALS['TYPO3_DB']->sql_free_result($res2);
        return $out;
    }


    /**
     * show a poll archive
     *
     * @param    int        $listPID: uid of the poll result page
     * @param    mixed    $thePID: uid of the folder(s) with the questions
     * @param    int        $resPID: uid of the folder with user-results
     * @return   string    The content that should be displayed on the website
     */
    function showPollArchive( $listPID,$thePID,$resPID ) {
    	$template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_ARCHIVE###");
        $template_entry = $this->templateService->getSubpart($template, "###TEMPLATE_ARCHIVE_ENTRY###");
        $markerArray = array();
        $markerArrayS = array();
        $markerArray["###REF_ARCHIVE_ENTRY###"] = '';

    	$queryResult = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,title',
			$this->tableQuestions,
			'pid IN (' . $thePID . ')' . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
			'',
			$this->getSortBy(), // 'tstamp DESC',
			"1,100");
		$rows = $GLOBALS['TYPO3_DB']->sql_num_rows($queryResult);
		if ($this->helperObj->writeDevLog)
			\TYPO3\CMS\Core\Utility\GeneralUtility::devLog('Archive Poll result: selected questions with pid IN ('.$thePID.')' . $this->helperObj->getWhereLang().': '.$rows, $this->extKey, 0);
		if ($rows>0) {
			while($row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult)){
				$typolink_conf = array(
	                'parameter' => intval($listPID),
	                'additionalParams' => '&'.$this->prefixId.'[cmd]=list&'.$this->prefixId.'[qid]='.$row['uid'],
	                'useCacheHash' => 0);
	            $markerArrayS['###VAR_LINKTAG###'] = $this->cObj->typolink($row['title'], $typolink_conf);
	            $markerArrayS['###VAR_LINK###'] = $this->pi_getPageLink($listPID, '', array($this->prefixId.'[qid]' => $row['uid'], $this->prefixId.'[cmd]' => 'list'));
            	$markerArrayS['###VAR_TITLE###'] = $row['title'];
				$markerArray["###REF_ARCHIVE_ENTRY###"] .= $this->templateService->substituteMarkerArray($template_entry, $markerArrayS);
			}
		}
		$GLOBALS['TYPO3_DB']->sql_free_result($queryResult);
		$subpart = $template;
        $template = $this->templateService->substituteSubpart($subpart, '###TEMPLATE_ARCHIVE_ENTRY###', $markerArray["###REF_ARCHIVE_ENTRY###"], 0);
        return $this->templateService->substituteMarkerArray($template, $markerArray);
    }



    /**
     * Format string with nl2br and htmlspecialchars()
     *
     * @param    string    $str: input-string
     * @return    string    formatted string
     */
    function formatStr($str)    {
        if (is_array($this->conf["general_stdWrap."]))    {
            $str = $this->cObj->stdWrap($str, $this->conf["general_stdWrap."]); // statt local_cObj
        }
        return $str;
    }

    /**
     * Returns the sorting order of questions for SQL
     *
     * @return    string    sort by for SQL
     */
    function getSortBy() {
        $sortBy = 'sorting';
        if ( $this->conf['sortBy'] ) {
            switch ($this->conf['sortBy']) {
                case "uid": $sortBy = 'uid'; break;
                case "title": $sortBy = 'title'; break;
                case "uid-desc": $sortBy = 'uid DESC'; break;
                case "title": $sortBy = 'title DESC'; break;
                case "sorting-desc": $sortBy = 'sorting DESC'; break;
                default: $sortBy = 'sorting';
            }
        }
        return $sortBy;
    }

    /**
     * Returns the name of the cookie
     *
     * @param    int        $resPID: results-PID
     * @param    int        $thePID: questions-PID
     * @return    string    cookie name
     */
    function getCookieMode($resPID, $thePID) {
        if ($this->conf['cookieMode']==1)    // mit sprache
            $cookieName = "myquizpoll".$resPID.'_'.$this->lang;
        else if ($this->conf['cookieMode']==2 && $GLOBALS['TSFE']->fe_user->user['uid'])    // mit fe-user
            $cookieName = "myquizpoll".$resPID.'_'.$GLOBALS['TSFE']->fe_user->user['uid'];
        else if ($this->conf['cookieMode']==3)    // mit fragen-pid
            $cookieName = "myquizpoll".$resPID.'_'.$thePID;
        else if ($this->conf['cookieMode']==4) {    // mit categorie
            $tmp = str_replace(' ', '', $this->conf['onlyCategories']);
            $tmp = str_replace(',', '', $tmp);
            $cookieName = "myquizpoll".$resPID.'_'.$tmp;
        } else if  ($this->conf['cookieMode']==5) {    // mit question-uid und fe-user
            $answeredP = '';
            if ($this->conf['startCategory']) {
                $answeredP = ' AND category='.intval($this->conf['startCategory']);
            } else if ( $this->conf['onlyCategories'] ) {
                $answeredP = " AND category IN (".preg_replace('/[^0-9,]/','',$this->conf['onlyCategories']).")";
            }
            // poll question (only latest question!)
            $queryResult5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid',
                $this->tableQuestions,
                'pid IN (' . $thePID . ')' . $answeredP . $this->helperObj->getWhereLang() . ' ' . $this->cObj->enableFields($this->tableQuestions),
                '',
                'uid DESC',
                '1');
            $row5 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult5);
            $cookieName = "myquizpoll".$resPID.'_'.intval($GLOBALS['TSFE']->fe_user->user['uid']).'_'.intval($row5['uid']);
            $GLOBALS['TYPO3_DB']->sql_free_result($queryResult5);
        } else
            $cookieName = "myquizpoll".$resPID;
        return $cookieName;
    }

    /**
    * read the template file, fill in global wraps and markers and write the result
    * to '$this->templateCode'
    *
    * @return string   path to the template
    */
    function initTemplate() {
       // read template-file
       $templateFile = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'templateFile');
       $origTemplateFile = 'EXT:myquizpoll/pi1/tx_myquizpoll_pi1.tmpl';
       if ($templateFile) {
           if (false === strpos($templateFile, '/')) {
           	if (strpos($templateFile, ':') > 0) {
           		/* SOLLTE SO GEHEN:
           		if ($version >= 6002000) {
           			$templateFile = ResourceFactory::retrieveFileOrFolderObject($templateFile);
           		} else { */
           			// Referenz
           			$explode = explode(':', $templateFile);
           			$queryResult5 = $GLOBALS['TYPO3_DB']->exec_SELECTquery('identifier',
           					'sys_file',
           					'uid = ' . intval($explode[1]));
           			$row5 = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($queryResult5);
           			$templateFile = 'fileadmin' . $row5['identifier'];
           			$GLOBALS['TYPO3_DB']->sql_free_result($queryResult5);
           		//}
           	} else {
           		// Uploads
               $templateFile = 'uploads/tx_myquizpoll/' . $templateFile;
           	}
           }
       } else if ($this->conf['templateFile']) {
           $templateFile = $this->conf['templateFile'];
       } else {
              $templateFile = $origTemplateFile;
       }
       $templateFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($templateFile);
       $this->templateCode = file_get_contents($templateFile);
       $origTemplateFile = GeneralUtility::makeInstance(FilePathSanitizer::class)->sanitize($origTemplateFile);
       $this->origTemplateCode = file_get_contents($origTemplateFile);
       return $templateFile;
    }


    /**
    * Sets one flexform-value (if set)
    *
    * @param    string    $pre: pre-name
    * @param    string    $name: name
    * @param    string    $sheet: sheet
    * @param    int        $type: 0-3
    */
    function setFlexValue($pre,$name,$sheet,$type) {
        $value = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], $name, $sheet);
        $new = '';
        if ($type==3) {            // select text
            if ($value!='TS') $new = $value;
        } else if ($type==2) {    // enter text
            if ($value || $value==='0' || $value===0) $new = $value;
        } else if ($type==1) {    // enter number
            if ($value || $value==='0' || $value===0) $new = intval($value);
        } else {                // select number
            if ($value!='TS' && ($value || $value==='0' || $value===0)) $new = intval($value);
        }
        if ($new || $new==='0' || $new===0) {
            if ($type==3 && ($new==='0' || $new===0)) $new = '';
            if ($pre) {
                $this->conf[$pre.'.'][$name] = $new;
            } else {
                $this->conf[$name] = $new;
            }
            //echo "setzte $name = $new\n";
        }
    }

    /**
    * copy flexform-values to '$this->conf'
    */
    function copyFlex() {
        // aus Kompatibilitaetsgruenden
        if ($this->conf['dontShowUserData']) $this->conf['userData.']['askAtQuestion'] = 0;
        if ($this->conf['highscoreEntries']) $this->conf['highscore.']['entries'] = $this->conf['highscoreEntries'];
        if ($this->conf['sortHighscoreBy'])  $this->conf['highscore.']['sortBy'] = $this->conf['sortHighscoreBy'];
        if ($this->conf['showHighscore'])      $this->conf['highscore.']['showAtFinal'] = 1;
        if ($this->conf['dateFormat'])          $this->conf['highscore.']['dateFormat'] = $this->conf['dateFormat'];

        $value = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'what_to_display');
        if ($value and $value!='TS') {
            if ($value=='NORMAL')
                $this->conf['CMD'] = '';
            else
                $this->conf['CMD'] = $value;
        }
        $this->setFlexValue('','isPoll', 'sDEF', 0);
        $this->setFlexValue('','tableAnswers', 'sDEF', 3);
        $this->setFlexValue('','nextPID', 'sDEF', 1);
        $this->setFlexValue('','finalPID', 'sDEF', 1);
        $this->setFlexValue('','startPID', 'sDEF', 1);
        $this->setFlexValue('','listPID', 'sDEF', 1);
        $this->setFlexValue('','pageQuestions', 'sDEF', 1);
        $this->setFlexValue('','answerChoiceMax', 'sDEF', 1);

        $this->setFlexValue('','sortBy', 'sNEXT', 3);
        $this->setFlexValue('','pollStart', 'sNEXT', 1);
        $this->setFlexValue('','mixAnswers', 'sNEXT', 0);
        $this->setFlexValue('','showAnswersSeparate', 'sNEXT', 0);
        $this->setFlexValue('','dontShowCorrectAnswers', 'sNEXT', 0);
        $this->setFlexValue('','showDetailAnswers', 'sNEXT', 0);
        $this->setFlexValue('','showAllCorrectAnswers', 'sNEXT', 0);
        $this->setFlexValue('','starRatingDetails', 'sNEXT', 0);
        $this->setFlexValue('','alwaysShowStarRatingDetails', 'sNEXT', 0);
        $this->setFlexValue('','allowBack', 'sNEXT', 0);
        $this->setFlexValue('','allowSkipping', 'sNEXT', 0);
        $this->setFlexValue('','dontShowPoints', 'sNEXT', 0);
        $this->setFlexValue('','noNegativePoints', 'sNEXT', 0);
        $this->setFlexValue('','userSession', 'sNEXT', 0);
        $this->setFlexValue('','deleteResults', 'sNEXT', 0);
        $this->setFlexValue('','deleteDouble', 'sNEXT', 0);
        $this->setFlexValue('','enforceSelection', 'sNEXT', 0);
        $this->setFlexValue('','doubleEntryCheck', 'sNEXT', 1);
        $this->setFlexValue('','doubleCheckMode', 'sNEXT', 0);
        $this->setFlexValue('','secondPollMode', 'sNEXT', 0);
        $this->setFlexValue('','useCookiesInDays', 'sNEXT', 1);
        $this->setFlexValue('','cookieMode', 'sNEXT', 0);
        $this->setFlexValue('','loggedInMode', 'sNEXT', 0);
        $this->setFlexValue('','disableIp', 'sNEXT', 0);
        $this->setFlexValue('','hideByDefault', 'sNEXT', 0);

        $this->setFlexValue('userData','askAtStart', 'sUSERDATA', 0);
        $this->setFlexValue('userData','askAtQuestion', 'sUSERDATA', 0);
        $this->setFlexValue('userData','askAtFinal', 'sUSERDATA', 0);
        $this->setFlexValue('userData','showAtAnswer', 'sUSERDATA', 0);
        $this->setFlexValue('userData','showAtFinal', 'sUSERDATA', 0);
        $this->setFlexValue('userData','tt_address_pid', 'sUSERDATA', 1);
        $this->setFlexValue('userData','tt_address_groups', 'sUSERDATA', 2);

        $this->setFlexValue('','pageTimeSeconds', 'sTERMINATION', 1);
        $this->setFlexValue('','quizTimeMinutes', 'sTERMINATION', 1);
        $this->setFlexValue('','cancelWhenWrong', 'sTERMINATION', 0);
        $this->setFlexValue('','finalWhenCancel', 'sTERMINATION', 0);
        $this->setFlexValue('','finishedMinPercent', 'sTERMINATION', 1);
        $this->setFlexValue('','finishAfterQuestions', 'sTERMINATION', 1);

        $value1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till1p', 'sEVALUATION');
        $value2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till1c', 'sEVALUATION');
        if ($value2) {
            $this->conf['showEvaluation'] = intval($value1).':'.intval($value2);
            $value1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till2p', 'sEVALUATION');
            $value2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till2c', 'sEVALUATION');
            if ($value2) {
                $this->conf['showEvaluation'] .= ','.intval($value1).':'.intval($value2);
                $value1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till3p', 'sEVALUATION');
                $value2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till3c', 'sEVALUATION');
                if ($value2) {
                    $this->conf['showEvaluation'] .= ','.intval($value1).':'.intval($value2);
                    $value1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till4p', 'sEVALUATION');
                    $value2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till4c', 'sEVALUATION');
                    if ($value2) {
                        $this->conf['showEvaluation'] .= ','.intval($value1).':'.intval($value2);
                        $value1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till5p', 'sEVALUATION');
                        $value2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till5c', 'sEVALUATION');
                        if ($value2) {
                            $this->conf['showEvaluation'] .= ','.intval($value1).':'.intval($value2);
                            $value1 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till6p', 'sEVALUATION');
                            $value2 = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'till6c', 'sEVALUATION');
                            if ($value2) {
                                $this->conf['showEvaluation'] .= ','.intval($value1).':'.intval($value2);
                            }
                        }
                    }
                }
            }
        }
        $this->setFlexValue('','showCategoryElement', 'sEVALUATION', 0);

        $this->setFlexValue('highscore','showAtFinal', 'sHIGHSCORE', 0);
        $this->setFlexValue('highscore','entries', 'sHIGHSCORE', 1);
        $this->setFlexValue('highscore','sortBy', 'sHIGHSCORE', 3);
        $this->setFlexValue('highscore','groupBy', 'sHIGHSCORE', 3);
        $this->setFlexValue('highscore','linkTo', 'sHIGHSCORE', 2);
        $this->setFlexValue('highscore','dateFormat', 'sHIGHSCORE', 2);

        $this->setFlexValue('email','send_admin', 'sEMAIL', 0);
        $this->setFlexValue('email','send_user', 'sEMAIL', 0);
        $this->setFlexValue('email','admin_subject', 'sEMAIL', 2);
        $this->setFlexValue('email','user_subject', 'sEMAIL', 2);
        $this->setFlexValue('email','from_name', 'sEMAIL', 2);
        $this->setFlexValue('email','from_mail', 'sEMAIL', 2);
        $this->setFlexValue('email','admin_name', 'sEMAIL', 2);
        $this->setFlexValue('email','admin_mail', 'sEMAIL', 2);
        $this->setFlexValue('email','answers', 'sEMAIL', 2);

        $this->setFlexValue('','advancedStatistics', 'sFEATURES', 0);
        $this->setFlexValue('','enableCaptcha', 'sFEATURES', 0);
        $this->setFlexValue('','loggedInCheck', 'sFEATURES', 0);
        $this->setFlexValue('','debug', 'sFEATURES', 0);
        $this->setFlexValue('','startCategory', 'sFEATURES', 1);
        $this->setFlexValue('','onlyCategories', 'sFEATURES', 2);
        $this->setFlexValue('','randomCategories', 'sFEATURES', 0);
        $this->setFlexValue('','ignoreSubmit', 'sFEATURES', 0);
        $this->setFlexValue('','useJokers', 'sFEATURES', 0);
        $value = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'jokersUnlimited', 'sFEATURES');
        if ($value!='TS' && ($value || $value==='0' || $value===0))
            $this->conf['jokers.']['unlimited'] = intval($value);
        $value = $this->pi_getFFvalue($this->cObj->data['pi_flexform'], 'jokersHalvePoints', 'sFEATURES');
        if ($value!='TS' && ($value || $value==='0' || $value===0))
            $this->conf['jokers.']['halvePoints'] = intval($value);
    }

    /**
     * does a redirect to a URL specified in the parameter and exits the request
     * @param    mixed    the URL to redirect to, if this is a numeric, it's supposed to be a
     *                     page ID, we should substitute this process at some point with the typoLink function
     * @param    array    Parameters for the link
     */
    function redirectUrl($url, $addParams) {
        if (is_numeric($url)) {
            // these parameters have to be added to the redirect url: $addParams = array();
            if (\TYPO3\CMS\Core\Utility\GeneralUtility::_GP('L')) {    // ob das noetig ist?
                $addParams['L'] = \TYPO3\CMS\Core\Utility\GeneralUtility::_GP('L');
            }
            $url = $this->pi_getPageLink($url, '', $addParams);
        }
        if ($this->conf['correctRedirectUrl']) {    // ob das Sinn macht?
            $url = str_replace('&amp;', '&', $url);
        }
        header('Location: ' . \TYPO3\CMS\Core\Utility\GeneralUtility::locationHeaderUrl($url));
        exit();
    }

    /**
     * Returns an XML document
     * @param    int        $quid: question UID
     * @param    int        $ruid: relation UID
     * @param    int        $jokerNo: Joker number
     * @param    string    $jokerName: Joker name
     * @return    string    xml-document
     */
    function getAjaxData($quid,$ruid,$jokerNo,$jokerName) {
        $sOut = '';
        $jsc = '';
        $nCount = 0;
        if ($this->conf['answerChoiceMax'])                    // antworten pro frage
            $this->answerChoiceMax = intval($this->conf['answerChoiceMax']);

        $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            '*', // SELECT ...
            'tx_myquizpoll_question', // FROM ...
            'uid='.intval($quid), // WHERE...
            '', // GROUP BY...
            '', // ORDER BY...
            '' // LIMIT ...
        );
        $row = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
        if ($jokerNo==1) {
            for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                if ($row['joker1_'.$answerNumber]) {
                    $sOut .= '; '.$row['answer'.$answerNumber];
                } else if ($row['answer'.$answerNumber]) {
                    $jsc .= ", 'quiz_antwort$answerNumber'";
                }
            }
            $sOut = substr($sOut,2);
        } else if ($jokerNo==2) {
            for( $answerNumber=1; $answerNumber < $this->answerChoiceMax+1; $answerNumber++ ) {
                if ($row['answer'.$answerNumber]) {
                    $sOut .= '; '.$row['answer'.$answerNumber].': '.$row['joker2_'.$answerNumber].'%';
                }
            }
            $sOut = substr($sOut,2);
        } else if ($jokerNo==3) {
            $sOut = $row['joker3'];
        }
        if (!$sOut) $sOut = $this->pi_getLL('no_joker_answer','no_joker_answer');;

        if ($ruid > 0) {                // which Joker used?
            $timestamp = time();
            $update = array('tstamp' => $timestamp,
                            'joker'.intval($jokerNo) => 1,
                            'lasttime' => $timestamp);
            $success = $GLOBALS['TYPO3_DB']->exec_UPDATEquery('tx_myquizpoll_result', 'uid='.intval($ruid).' AND sys_language_uid='.$this->lang, $update);
        }

        /* instantiate the xajaxResponse object */
        $objResponse = new tx_xajax_response();
        $objResponse->addScript("document.getElementById('".$this->prefixId."-joker').style.display = \"block\"");
        $objResponse->addScript("document.getElementById('".$this->prefixId."-$jokerName').style.display = \"none\"");

//        if(!$ruid)    {            // 9.8.09: man soll immer feststellen koennen, ob ein Joker benutzt wurde
            $objResponse->addScript('document.forms["myquiz"].elements["'.$this->prefixId.'[joker'.$jokerNo.']"].value = 1');
//            $objResponse->addScript("document.getElementById('".$box."').value = 1");
//        }

        if ($jsc) {
            $jsc = substr($jsc,2);
            $objResponse->addScript('hideByJoker ('.$jsc.')');
        }

        /* We alter the contents of the 'joker' HTML element. The property 'innerHTML' is the html code inside this element. We replace it with the result in our $sOut variable */
        $objResponse->addAssign($this->prefixId."-joker_answer", "innerHTML", $sOut);
        /* With the getXML() method on the xajaxResponse objectwe send everything back to the client */
        return $objResponse->getXML();
    }

    /**
     * Returns an XML document
     * @param    int        $quid: question UID
     * @return    string    xml-document
     */
    function getAjaxDetails($quid) {
        $quid = intval($quid);
        $nr = 0;
        $sOut = '';
        //$template = str_replace('\&quot;', '', $template);
        $template = $this->templateService->getSubpart($this->templateCode, "###TEMPLATE_DETAILS_ITEM###");
        //$template_item = preg_replace('/\r?\n/', " ", $template_item);
        //$template_item = str_replace("'", '"', $template_item);
        $template = str_replace('###PREFIX###', $this->prefixId, $template);

        $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
            'COUNT(*) anzahl', // SELECT ...
            'tx_myquizpoll_relation', // FROM ...
            'LENGTH(textinput)>0 AND question_id='.$quid, // WHERE...
            '', // GROUP BY...
            '', // ORDER BY...
            '' // LIMIT ...
        );
        $rowA = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result);
        $gesamt = $rowA['anzahl'];
        $GLOBALS['TYPO3_DB']->sql_free_result($result);
        if ($gesamt>0) {
            $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery(
                'DISTINCT textinput texte, COUNT( textinput ) anzahl', // SELECT ...
                'tx_myquizpoll_relation', // FROM ...
                'LENGTH(textinput)>0 AND question_id='.$quid, // WHERE...
                'textinput', // GROUP BY...
                '', // ORDER BY...
                '' // LIMIT ...
            );
            $rows = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
            if ($rows>0) {
                while($rowA = $GLOBALS['TYPO3_DB']->sql_fetch_assoc($result)){
                    if (($nr % 2) == 0)
                        $even_odd = 'even';
                    else
                        $even_odd = 'odd';
                    $prozent = number_format((100*$rowA['anzahl'])/$gesamt, 2, ',', ' ');
                    //$texte = preg_replace("/(\r\n)+|(\n|\r)+/", "<br />", $rowA['texte']);    // nl2br funktioniert auch nicht
                    $texte = str_replace('\r\n', '<br />', htmlspecialchars($rowA['texte']));
                    $temp = str_replace('###ITEM_EVEN_ODD###', $even_odd, $template);
                    $temp = str_replace('###ITEM_ANSWER###', $texte, $temp);
                    $temp = str_replace('###ITEM_PERCENT###', $prozent, $temp);
                    $temp = str_replace('###ITEM_COUNTS###', $rowA['anzahl'], $temp);
                    $sOut .= $temp;
                    $nr++;
                }
            }
        }

        /* instantiate the xajaxResponse object */
        $objResponse = new tx_xajax_response();
        $objResponse->addScript("document.getElementById('details_hidden-$quid').style.display='block'; document.getElementById('show_details-$quid').style.display='none';");

        /* We alter the contents of the HTML element. The property 'innerHTML' is the html code inside this element. We replace it with the result in our $sOut variable */
        $objResponse->addAssign("details-$quid", "innerHTML", $sOut);
        /* With the getXML() method on the xajaxResponse objectwe send everything back to the client */
        return $objResponse->getXML();
    }
}

if (defined('TYPO3_MODE') && $TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/pi1/class.tx_myquizpoll_pi1.php'])    {
    include_once($TYPO3_CONF_VARS[TYPO3_MODE]['XCLASS']['ext/myquizpoll/pi1/class.tx_myquizpoll_pi1.php']);
}

?>
