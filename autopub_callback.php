<?php
/**
 * 自动发布插件
 * @copyright (c) xiaosong.org All Rights Reserved
 */
if(!defined('EMLOG_ROOT')) {exit('error!');}

function callback_init(){
	$DB = MySql::getInstance();
	$is_exist_option = $DB->query("SELECT 1 FROM ".DB_PREFIX."options WHERE option_name='autopub'");
	if (!$DB->num_rows($is_exist_option)) {
		$DB->query("INSERT INTO ".DB_PREFIX."options (option_name, option_value) VALUES('autopub', '".serialize(array())."')");
	}
	$cacheDir = EMLOG_ROOT.'/content/plugins/autopub/cache/';
	$sql = "SELECT gid FROM ".DB_PREFIX."blog where hide ='y' order by date DESC";
	$result = $DB->query($sql);
	$autopub = array();
	while ($row = $DB->fetch_array($result)) {
	  if (file_exists($cacheDir.$row['gid'].'.php')) {
	    include_once($cacheDir.$row['gid'].'.php');
	    $pub_time = emStrtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second);
	    $key = 'post-'.$row['gid'];
	    $autopub[$key] = $pub_time;
      unlink($cacheDir.$row['gid'].'.php');
	  }
	}
	emDeleteFile($cacheDir);
	Option::updateOption('autopub', serialize($autopub));
	$CACHE = Cache::getInstance();
	$CACHE->updateCache('options');
}

?>