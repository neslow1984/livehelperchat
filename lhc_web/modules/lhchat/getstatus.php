<?php

header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');
header('Content-type: text/javascript');
header('Expires: Sat, 26 Jul 1997 05:00:00 GMT' );
header('Last-Modified: ' . gmdate( 'D, d M Y H:i:s',time()+60*60*8 ) . ' GMT' );
header('Cache-Control: no-store, no-cache, must-revalidate' );
header('Cache-Control: post-check=0, pre-check=0', false );
header('Pragma: no-cache' );


if ((int)$Params['user_parameters_unordered']['department'] > 0 && erLhcoreClassModelChatConfig::fetch('hide_disabled_department')->current_value == 1){
	try {
		$department = erLhcoreClassModelDepartament::fetch((int)$Params['user_parameters_unordered']['department']);
		if ($department->disabled == 1) {
			// Hide disabled department
			exit;
		}		
	} catch (Exception $e) {
		exit;
	}
}


$tpl = erLhcoreClassTemplate::getInstance('lhchat/getstatus.tpl.php');

if ( erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1 ) {

	$allchar = "abcdefghijklmnopqrstuvwxyz1234567890";
	$str = "" ;
	mt_srand (( double) microtime() * 1000000 );
	for ( $i = 0; $i < 20 ; $i++ ) {
		$str .= substr( $allchar, mt_rand (0,36), 1 );
	}

	$tpl->set('vid', $str);
}

$validUnits = array('pixels' => 'px','percents' => '%');

$theme = false;
if (isset($Params['user_parameters_unordered']['theme']) && (int)$Params['user_parameters_unordered']['theme'] > 0){
	try {
		$theme = erLhAbstractModelWidgetTheme::fetch($Params['user_parameters_unordered']['theme']);	
	} catch (Exception $e) {
		$theme = false;
	}
} else {
	$defaultTheme = erLhcoreClassModelChatConfig::fetch('default_theme_id')->current_value;
	if ($defaultTheme > 0) {
		try {
			$theme = erLhAbstractModelWidgetTheme::fetch($defaultTheme);
		} catch (Exception $e) {
			$theme = false;
		}
	}
}

$tpl->set('referrer',isset($_GET['r']) ? rawurldecode($_GET['r']) : '');
$tpl->set('track_online_users',erLhcoreClassModelChatConfig::fetch('track_online_visitors')->current_value == 1);
$tpl->set('click',$Params['user_parameters_unordered']['click']);
$tpl->set('position',$Params['user_parameters_unordered']['position']);
$tpl->set('identifier',(!is_null($Params['user_parameters_unordered']['identifier']) && !empty($Params['user_parameters_unordered']['identifier'])) ? (string)$Params['user_parameters_unordered']['identifier'] : false);
$tpl->set('leaveamessage',(string)$Params['user_parameters_unordered']['leaveamessage'] == 'true');
$tpl->set('noresponse',(string)$Params['user_parameters_unordered']['noresponse'] == 'true');
$tpl->set('hide_offline',$Params['user_parameters_unordered']['hide_offline']);
$tpl->set('department',(int)$Params['user_parameters_unordered']['department'] > 0 ? (int)$Params['user_parameters_unordered']['department'] : false);
$tpl->set('check_operator_messages',$Params['user_parameters_unordered']['check_operator_messages']);
$tpl->set('top_pos',(!is_null($Params['user_parameters_unordered']['top']) && (int)$Params['user_parameters_unordered']['top'] >= 0) ? (int)$Params['user_parameters_unordered']['top'] : 350);
$tpl->set('units',key_exists((string)$Params['user_parameters_unordered']['units'], $validUnits) ? $validUnits[(string)$Params['user_parameters_unordered']['units']] : 'px');
$tpl->set('disable_pro_active',(string)$Params['user_parameters_unordered']['disable_pro_active'] == 'true');
$tpl->set('priority',is_numeric($Params['user_parameters_unordered']['priority']) ? (int)$Params['user_parameters_unordered']['priority'] : false);
$tpl->set('theme',$theme);

erLhcoreClassChatEventDispatcher::getInstance()->dispatch('chat.getstatus',array('tpl' => & $tpl));

echo $tpl->fetch();
exit;