<?php
if(isset($_GET['date'])){
	$date = $_GET['date'];
	$date = rtrim($date, '/');
	$parts = explode('/',$date);
	
	if(isset($parts[2])){
		\app::$request->setParam('date1', gmdate('Y-m-d', mktime(0, 0, 0, $parts[1], $parts[2], $parts[0])).' 00:00:00');
		\app::$request->setParam('date2', gmdate('Y-m-d', mktime(0, 0, 0, $parts[1], $parts[2]+1, $parts[0])).' 00:00:00');
	}elseif(isset($parts[1])){
		\app::$request->setParam('date1', gmdate('Y-m-d', mktime(0, 0, 0, $parts[1], 0, $parts[0])).' 00:00:00');
		\app::$request->setParam('date2', gmdate('Y-m-d', mktime(0, 0, 0, $parts[1]+1, 0, $parts[0])).' 00:00:00');
	}else{
		\app::$request->setParam('date1', gmdate('Y-m-d', mktime(0, 0, 0, 0, 0, $parts[0])).' 00:00:00');
		\app::$request->setParam('date2', gmdate('Y-m-d', mktime(0, 0, 0, 0, 0, $parts[0]+1)).' 00:00:00');
	}
	/*print_r(\app::$request->getParam('date1'));
	print_r(\app::$request->getParam('date2'));*/
}
?>