<?php
if (!defined ("TYPO3_MODE")) 	die ("Access denied.");

$TCA["tx_skcalendareventmanagement_events"] = Array (
	"ctrl" => $TCA["tx_skcalendareventmanagement_events"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,starttime,endtime,fe_group,event,subscribers_min,subscribers_max,pagelink"
	),
	"feInterface" => $TCA["tx_skcalendareventmanagement_events"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"starttime" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.starttime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"default" => "0",
				"checkbox" => "0"
			)
		),
		"endtime" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.endtime",
			"config" => Array (
				"type" => "input",
				"size" => "8",
				"max" => "20",
				"eval" => "date",
				"checkbox" => "0",
				"default" => "0",
				"range" => Array (
					"upper" => mktime(0,0,0,12,31,2020),
					"lower" => mktime(0,0,0,date("m")-1,date("d"),date("Y"))
				)
			)
		),
		"fe_group" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.fe_group",
			"config" => Array (
				"type" => "select",	
				"items" => Array (
					Array("", 0),
					Array("LLL:EXT:lang/locallang_general.php:LGL.hide_at_login", -1),
					Array("LLL:EXT:lang/locallang_general.php:LGL.any_login", -2),
					Array("LLL:EXT:lang/locallang_general.php:LGL.usergroups", "--div--")
				),
				"foreign_table" => "fe_groups"
			)
		),
		"event" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_events.event",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_skcalendar_events",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"subscribers_min" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_events.subscribers_min",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"range" => Array ("lower"=>0,"upper"=>1000),	
				"checkbox" => "0",	
				"eval" => "int,nospace",
			)
		),
		"subscribers_max" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_events.subscribers_max",		
			"config" => Array (
				"type" => "input",	
				"size" => "30",	
				"range" => Array ("lower"=>0,"upper"=>1000),	
				"checkbox" => "0",	
				"eval" => "required,int,nospace",
			)
		),
		"pagelink" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_events.pagelink",		
			"config" => Array (
				"type" => "input",		
				"size" => "15",
				"max" => "255",
				"checkbox" => "",
				"eval" => "trim",
				"wizards" => Array(
					"_PADDING" => 2,
					"link" => Array(
						"type" => "popup",
						"title" => "Link",
						"icon" => "link_popup.gif",
						"script" => "browse_links.php?mode=wizard",
						"JSopenParams" => "height=300,width=500,status=0,menubar=0,scrollbars=1"
					)
				)
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, event, subscribers_min, subscribers_max, pagelink")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "starttime, endtime, fe_group")
	)
);



$TCA["tx_skcalendareventmanagement_registration"] = Array (
	"ctrl" => $TCA["tx_skcalendareventmanagement_registration"]["ctrl"],
	"interface" => Array (
		"showRecordFieldList" => "hidden,event,subscriber,registrationcode,listsubscription"
	),
	"feInterface" => $TCA["tx_skcalendareventmanagement_registration"]["feInterface"],
	"columns" => Array (
		"hidden" => Array (		
			"exclude" => 1,	
			"label" => "LLL:EXT:lang/locallang_general.php:LGL.hidden",
			"config" => Array (
				"type" => "check",
				"default" => "0"
			)
		),
		"event" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_registration.event",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "tx_skcalendareventmanagement_events",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"subscriber" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_registration.subscriber",		
			"config" => Array (
				"type" => "group",	
				"internal_type" => "db",	
				"allowed" => "fe_users",	
				"size" => 1,	
				"minitems" => 0,
				"maxitems" => 1,
			)
		),
		"registrationcode" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_registration.registrationcode",		
			"config" => Array (
				"type" => "none",
			)
		),
		"listsubscription" => Array (		
			"exclude" => 1,		
			"label" => "LLL:EXT:sk_calendar_event_management/locallang_db.php:tx_skcalendareventmanagement_registration.listsubscription",		
			"config" => Array (
				"type" => "check",
			)
		),
	),
	"types" => Array (
		"0" => Array("showitem" => "hidden;;1;;1-1-1, event, subscriber, registrationcode, listsubscription")
	),
	"palettes" => Array (
		"1" => Array("showitem" => "")
	)
);
?>