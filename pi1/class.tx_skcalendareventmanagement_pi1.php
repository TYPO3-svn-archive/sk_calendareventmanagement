<?php
/***************************************************************
*  Copyright notice
*  
*  (c) 2004 Sven Wilhelm (wilhelm@icecrash.com)
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
/** 
 * Plugin 'VCE Event Registration' for the 'sk_calendar_event_management' extension.
 *
 * @author	Sven Wilhelm <wilhelm@icecrash.com>
 */


require_once(PATH_tslib."class.tslib_pibase.php");

class tx_skcalendareventmanagement_pi1 extends tslib_pibase {
	var $prefixId = "tx_skcalendareventmanagement_pi1";		// Same as class name
	var $scriptRelPath = "pi1/class.tx_skcalendareventmanagement_pi1.php";	// Path to this script relative to the extension dir.
	var $extKey = "sk_calendar_event_management";	// The extension key.

    var $eventTable = 'tx_skcalendareventmanagement_events';
    var $registrationTable = 'tx_skcalendareventmanagement_registration';

    var $event_uid;
    var $events;
    var $user;

	/**
	 * [Put your description here]
	 */
	function main($content,$conf)	{
		$this->conf=$conf;
		$this->pi_setPiVarDefaults();
		$this->pi_loadLL();

        if(is_array($GLOBALS['TSFE']->fe_user->user)) { 
            $this->user = $GLOBALS['TSFE']->fe_user->user;
        } else { $this->user = 'UNAUTHENTICATED'; }
        //the submitted uid of an event if any
        $parameters = t3lib_div::GPvar('tx_skcalendareventmanagement_pi1');
         switch($parameters['registration']) {
        case 'new':
            $listSubscription = ($parameters['uid_listsubscription'] == 'on') ? 'on' : 'off';
            $content = $this->addRegistration($parameters['uid'],$listSubscription);
            break;
        case 'delete':
            $confirm = (empty($parameters['confirmDelete'])) ? false : $parameters['confirmDelete'];
            $content = $this->deleteRegistration($parameters['uid'],$confirm);
            break;
        case 'list':
        default:
            $content = $this->listEvents();
            break;
        }
        
		return $this->pi_wrapInBaseClass($content);
	}



    /**
     *
     */
    function addRegistration($uid,$listSubscription) {
        if($this->user == 'UNAUTHENTICATED') {
            $content = $this->loginMessage(true);
            return $content;
        } else {
            $subscribed = $this->checkSubscription($uid, $this->user['uid']);
            if($subscribed > 0) {
                $content = sprintf('<p>Sie sind bereits angemeldet zur Veranstaltung <strong>%s<strong>'.
                                   'angemeldet!</p>',
                                   $this->getEventTitle($uid)
                                   );
            } else {
                $regValues = array('event' => intVal($uid),
                                   'subscriber' => intVal($this->user['uid'])
                                   );
                
                if($listSubscription == 'on') { $regValues['listsubscription'] = 1; }
                
                $result = $GLOBALS['TYPO3_DB']->exec_INSERTquery($this->registrationTable,$regValues);
                if($GLOBALS['TYPO3_DB']->sql_affected_rows() == 1) {
                    $content = sprintf('<p>Sie wurden zur Veranstaltung <strong>%s<strong> angemeldet.</p>',
                                       $this->getEventTitle($uid));
                }
            
            $linkBack = $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,array());
            $content .= sprintf('<p><a href="%s">Zur&uuml;ck</a></p>',$linkBack);
            return $content;
            }
        }
    }



    /**
     *
     */
    function deleteRegistration($uid,$confirm = false) {
        if($this->user == 'UNAUTHENTICATED') {
            $content = $this->loginMessage(true);
            return $content;
        } else {
            if($confirm == false) {
                $eventTitle = $this->getEventTitle($uid);

                $deleteParams['tx_skcalendareventmanagement_pi1[uid]'] = $uid;
                $deleteParams['tx_skcalendareventmanagement_pi1[registration]'] = 'delete';
                $deleteParams['tx_skcalendareventmanagement_pi1[confirmDelete]'] = true;
                $linkDelete = sprintf('<a href="%s"><strong>Abmelden</strong></a>',
                                      $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,$deleteParams));
                $cancelParams['tx_skcalendareventmanagement_pi1[registration]'] = 'list';
                $linkCancel = sprintf('<a href="%s"><strong>Nicht Abmelden</strong></a>',
                                      $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,$cancelParams));

                $content = sprintf('<p>Sind Sie sicher dass Sie sich von der Veranstaltung <strong>%s</strong> '.
                                   'abmelden wollen?',
                                   $eventTitle);
                //$content .= sprintf('<p>%s &nbsp;&nbsp;&nbsp;&nbsp; %s</p>',
                //                    $linkDelete, $linkCancel);
                $content .= sprintf('<form action="%s">',
                                    $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id, array()));
                $content .= sprintf('<p>'.
                                    '<input type="button" name="deleteAction" value="Abmelden" '.
                                    'onClick="self.location.href=\'%s\'"> &nbsp;&nbsp;&nbsp;'.
                                    '<input type="button" name="CancelAction" value="Nicht Abmelden" '.
                                    'onClick="self.location.href=\'%s\'"></p>',
                                    $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,$deleteParams),
                                    $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,$cancelParams));
                $content .= '</form>';
                 
            } else {

                $event = $this->checkSubscription($uid, $this->user['uid']);
                $regValues = array('deleted' => 1);
                
                $result = $GLOBALS['TYPO3_DB']->exec_UPDATEquery($this->registrationTable,
                                                                       sprintf('uid=%s',$event),$regValues);
                if($GLOBALS['TYPO3_DB']->sql_affected_rows() == 1) {
                    $content = sprintf('<p>Sie wurden von der Veranstaltung <strong>%s</strong> abgemeldet.</p>',
                                       $this->getEventTitle($uid));
                } else {
                    $content = sprintf('<p>Die Abmeldung von der Veranstaltung <strong>%s</strong>'.
                                       'ist fehlgeschlagen',
                                       $this->getEventTitle($uid));
                }
            }
            
            $linkBack = $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,array());
            $content .= sprintf('<p><a href="%s">Zur&uuml;ck</a></p>',$linkBack);

            return $content;
        }
    }




    /**
     *
     */
    function listEvents() {
        
        if($this->user != 'UNAUTHENTICATED') {
            $userID = $this->user['uid'];
            $userName = $this->user['name'];
        }
        
        /* get a list of active events */
        $result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid,event,subscribers_min,subscribers_max,pagelink',
                                                         'tx_skcalendareventmanagement_events',
                                                         'NOT deleted AND NOT hidden','','');
        while(list($id,$event,$sub_min,$sub_max,$pagelink) = $GLOBALS['TYPO3_DB']->sql_fetch_row($result)) {
            
            /* get more detailed informations from sk_calendar */
            $e_result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title,date,description,start_time,end_time',
                                                               'tx_skcalendar_events',
                                                               'NOT deleted AND NOT hidden','','');
            list($e_title,$e_date,$e_descr,$e_start,$e_end) = $GLOBALS['TYPO3_DB']->sql_fetch_row($e_result);
            $GLOBALS['TYPO3_DB']->sql_free_result($e_result);


            $count = $this->countSubscription($id);

            /* check if user is registered to event */
            if(!empty($userID)) { 
                $s_subscribed = $this->checkSubscription($id,$userID);
                $subscribed = ($s_subscribed > 0) ? true : false;
            }
            else { $s_subscribed = false; }
            
            $checked = true;
            if($subscribed) {
                $listSub = $this->checkPublicSubscription($id,$userID);
                $checked = ($listSub == true) ? 'checked' : '';
            }
            $this->events[$id] = array(
                                       'id' => $id,
                                       'title' => $e_title,
                                       'date' => $e_date,
                                       'start' => $e_start,
                                       'end' => $e_end,
                                       'sub_min' => $sub_min,
                                       'sub_max' => $sub_max,
                                       'sub_count' => $count,
                                       'pagelink' => $pagelink,
                                       'description' => $e_descr,
                                       'subscribed' => $s_subscribed,
                                       'listSubscription' => $checked
                                       );
        }
        $GLOBALS['TYPO3_DB']->sql_free_result($result);
        
        
        $labels = array('Titel','Datum','Teiln. [min/max/aktuell]'); //,'Teilnahme anzeigen');
        $content = '<h3>Zur Zeit angebotene Veranstaltungen</h3>';
        $content .= '<table><thead>';
        foreach($labels as $l) { $content .= sprintf('<th>%s</th>',$l); }
        $content .= '</thead>';
        foreach($this->events as $e) {
            $content .= '<tr>';
            
            //$link['tx_skcalendareventmanagement_pi1[uid]'] = $e['pagelink'];
            //            $link['tx_skcalendareventmanagement_pi1[registration]'] = 'new';
            //$link['no_cache'] = 1;
            //            $GLOBALS["TSFE"]->id;
            $linkDetails = $GLOBALS["TSFE"]->cObj->getTypoLink_URL($e['pagelink'],array());
            $content .= sprintf('<form action="%s" method="POST">',
                                $GLOBALS["TSFE"]->cObj->getTypoLink_URL($GLOBALS["TSFE"]->id,array()));
            $content .= sprintf('<td><a href="%s">%s</a></td>',$linkDetails,$e['title']);
            $content .= sprintf('<td>%s<br />%s - %s</td>',
                                strftime("%d.%m.%Y",$e['date']),
                                strftime("%R",$e['start']-3600),
                                strftime("%R",$e['end']-3600));
            $min_subs = (empty($e['sub_min'])) ? '-' : $e['sub_min'];
            $max_subs = (empty($e['sub_max'])) ? '-' : $e['sub_max'];
            $content .= sprintf('<td style="text-align:center;">%s / %s / %s</td>',$min_subs,$max_subs,$e['sub_count']);
            
            
            //$content .= sprintf('<td>'.
            //                    '<input type="checkbox" name="tx_skcalendareventmanagement_pi1[uid_listsubscription]" %s/>'.
            //                    '</td>',
            //                    $e['id'],$e['listSubscription']);
            $regAction = ($e['subscribed'] == true) ? 'delete' : 'new';
            $regLabel = ($e['subscribed'] == true) ? 'Abmelden' : 'Anmelden';
            $content .= sprintf('<td>'.
                                '<input type="hidden" name="tx_skcalendareventmanagement_pi1[uid]" value="%s" />'.
                                '<input type="hidden" name="tx_skcalendareventmanagement_pi1[registration]" value="%s" />'.
                                '<input type="submit" value="%s" />'.
                                '</td>',
                                $e['id'],$regAction,$regLabel);
            $content .= '</form>';
            $content .= '</tr>';
        }
        $content .= '</table>';
        return($content);
    }
    


    /**
     *
     */
    function checkSubscription($eventID, $userID) {
        $s_result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('uid',
                                                          'tx_skcalendareventmanagement_registration',
                                                          sprintf('event=%s AND subscriber=%s AND '.
                                                                  'NOT deleted AND NOT hidden',
                                                                  $eventID,$userID),
                                                          '','','');
        $s_countRows = $GLOBALS['TYPO3_DB']->sql_num_rows($s_result);
        if($s_countRows > 0) {
            list($uid) = $GLOBALS['TYPO3_DB']->sql_fetch_row($s_result);
        } else { $uid = 0; }
        $GLOBALS['TYPO3_DB']->sql_free_result($s_result);
        return $uid;
    }



    /**
     *
     */
    function checkPublicSubscription($eventID, $userID) {
        $s_result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('listsubscription',
                                                          'tx_skcalendareventmanagement_registration',
                                                          sprintf('event=%s AND subscriber=%s AND '.
                                                                  'NOT deleted AND NOT hidden',
                                                                  $eventID,$userID),
                                                          '','','');
        $s_countRows = $GLOBALS['TYPO3_DB']->sql_num_rows($s_result);
        if($s_countRows > 0) {
            list($listSubscription) = $GLOBALS['TYPO3_DB']->sql_fetch_row($s_result);
            $list = ($listSubscription == 1) ? true : false;
        } else { $list = false; }
        $GLOBALS['TYPO3_DB']->sql_free_result($s_result);
        return $list;
    }


    /**
     *
     */
    function countSubscription($eventID) {
        $query = sprintf('SELECT count(*) FROM %s WHERE NOT deleted AND NOT hidden AND event=%s',
                         $this->registrationTable, $eventID);
        $result = $GLOBALS['TYPO3_DB']->sql_query($query);
        $countRows = $GLOBALS['TYPO3_DB']->sql_num_rows($result);
        if($countRows > 0) {
            list($count) = $GLOBALS['TYPO3_DB']->sql_fetch_row($result);
        } else { $count = 0; }
        $GLOBALS['TYPO3_DB']->sql_free_result($result);
        return $count;
    }



    /**
     *
     */
    function loginMessage($subscribe = true) {
        if($this->user == 'UNAUTHENTICATED') {
            $action = ($subscribe == true) ? 'anzumelden' : 'abzumelden';
            $linkLogin = $GLOBALS["TSFE"]->cObj->getTypoLink_URL(47,array());
            $linkNewReg = $GLOBALS["TSFE"]->cObj->getTypoLink_URL(45,array());
            $content = sprintf('<p>Sie m&uuml;ssen eingeloggt sein um sich %s.</p>'.
                               '<p><a href="%s">Einloggen</a>&nbsp;&nbsp;<a href="%s">Neu Registrieren</a></p>',
                               $action, $linkLogin,$linkNewReg);
            return $content;
        }
    }



    /**
     *
     */
    function getEventTitle($uid) {
        $e_result = $GLOBALS['TYPO3_DB']->exec_SELECTquery('title',
                                                           'tx_skcalendar_events',
                                                           'NOT deleted AND NOT hidden','','');
        list($e_title) = $GLOBALS['TYPO3_DB']->sql_fetch_row($e_result);
        $GLOBALS['TYPO3_DB']->sql_free_result($e_result);
        
        return $e_title;
    }
        


        
        
}
    
                                
if (defined("TYPO3_MODE") && $TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sk_calendar_event_management/pi1/class.tx_skcalendareventmanagement_pi1.php"])	{
	include_once($TYPO3_CONF_VARS[TYPO3_MODE]["XCLASS"]["ext/sk_calendar_event_management/pi1/class.tx_skcalendareventmanagement_pi1.php"]);
}

?>