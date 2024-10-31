<?php
	require_once ('../../../wp-config.php');
	require_once ('includes/class.typography.php');
	$theme = 'default';
	
	
	$conn = mysql_connect (DB_HOST,DB_USER,DB_PASSWORD);
	mysql_select_db (DB_NAME, $conn);
	$table   = $table_prefix.'posts';
	$id      = $_GET['post_ID'];
	
	$result  = mysql_query ("SELECT `post_content` FROM `".$table_prefix."posts` WHERE ID = '".$id."'", $conn);
	$content = mysql_result($result,0,'post_content');
	
	$typography = new CI_Typography();
	$content    = $typography->auto_typography($content);
	
	$result  = mysql_query ("SELECT `option_value` FROM `".$table_prefix."options` WHERE `option_name` = 's9-egoi' ORDER BY `option_id` DESC LIMIT 1",$conn);
	$options = unserialize(mysql_result($result,0,'option_value'));
	
	$result  = mysql_query ("SELECT `option_value` FROM `".$table_prefix."options` WHERE `option_name` = 'siteurl' ORDER BY `option_id` DESC LIMIT 1",$conn);
	$siteurl = mysql_result($result,0,'option_value');
	
	$output = '';
	ob_start();
		include_once ('themes/'.$theme.'/header.php');
		include_once ('themes/'.$theme.'/entry.php');
		include_once ('themes/'.$theme.'/footer.php');
		$output = ob_get_contents();
	ob_end_clean();
	echo $output;
?>