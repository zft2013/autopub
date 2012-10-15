<?php
!defined('EMLOG_ROOT') && exit('access deined!');
define('AUTOPUB_CACHE_DIR', EMLOG_ROOT.'/content/plugins/autopub/cache/');
set_time_limit(0);
$DB = MySql::getInstance();
$CACHE = Cache::getInstance();
$sql = "SELECT gid FROM ".DB_PREFIX."blog where hide ='y' order by date DESC";
$result = $DB->query($sql);
while($row = $DB->fetch_array($result)){
  if(file_exists(AUTOPUB_CACHE_DIR.$row['gid'].'.php')){
    include_once(AUTOPUB_CACHE_DIR.$row['gid'].'.php');
    $pub_time = emStrtotime($year.'-'.$month.'-'.$day.' '.$hour.':'.$minute.':'.$second);
    if(time() > $pub_time){
      $sql_do = "UPDATE ".DB_PREFIX."blog SET hide = 'n',date = '".$pub_time."' WHERE gid='".$row['gid']."'";
      $DB->query($sql_do);
      $CACHE->updateCache();
      doAction('save_log', $row['gid']);
      unlink(AUTOPUB_CACHE_DIR.$row['gid'].'.php');
    }
  }
}
?>