<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");
$TCA["tx_skcalendareventmanagement_events"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_events",		
		"label" => "event",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",	
			"starttime" => "starttime",	
			"endtime" => "endtime",	
			"fe_group" => "fe_group",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_skcalendareventmanagement_events.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, starttime, endtime, fe_group, event, subscribers_min, subscribers_max, pagelink",
	)
);

$TCA["tx_skcalendareventmanagement_registration"] = Array (
	"ctrl" => Array (
		"title" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_registration",		
		"label" => "event",	
		"tstamp" => "tstamp",
		"crdate" => "crdate",
		"cruser_id" => "cruser_id",
		"default_sortby" => "ORDER BY crdate",	
		"delete" => "deleted",	
		"enablecolumns" => Array (		
			"disabled" => "hidden",
		),
		"dynamicConfigFile" => t3lib_extMgm::extPath($_EXTKEY)."tca.php",
		"iconfile" => t3lib_extMgm::extRelPath($_EXTKEY)."icon_tx_skcalendareventmanagement_registration.gif",
	),
	"feInterface" => Array (
		"fe_admin_fieldList" => "hidden, event, subscriber, registrationcode, listsubscription",
	)
);


t3lib_div::loadTCA("tt_content");
$TCA["tt_content"]["types"]["list"]["subtypes_excludelist"][$_EXTKEY."_pi1"]="layout,select_key";


t3lib_extMgm::addPlugin(Array("LLL:EXT:sk_calendar_event_management/locallang_db.php:tt_content.list_type_pi1", $_EXTKEY."_pi1"),"list_type");


t3lib_extMgm::addStaticFile($_EXTKEY,"pi1/static/","VCE Event Registration");						


if (TYPO3_MODE=="BE")	$TBE_MODULES_EXT["xMOD_db_new_content_el"]["addElClasses"]["tx_skcalendareventmanagement_pi1_wizicon"] = t3lib_extMgm::extPath($_EXTKEY)."pi1/class.tx_skcalendareventmanagement_pi1_wizicon.php";
?>