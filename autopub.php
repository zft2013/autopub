<?php
/*
Plugin Name: 自动发布
Version: 2.0.0
Plugin URL: http://xiaosong.org/share/new-version-automatically-publish-plugin-released
Description: 自动发布文章，修正时区问题，增加相关挂载点，自动删除缓存文件。
Author: 小松
Author Email: sahala_2007@126.com
Author URL: http://xiaosong.org
*/
!defined('EMLOG_ROOT') && exit('access deined!');

function autopubAjax(){
  echo '<script type="text/javascript">$(function(){$.get("'.BLOG_URL.'?plugin=autopub");})</script>'."\n";
}
function autopubPhp(){
  echo '<script type="text/javascript">$(function(){$.ajax({url: "'.BLOG_URL.'content/plugins/autopub/autopub_cron.php",timeout: 1000,cache: false});})</script>'."\n";
}

function doPub(){
  $DB = MySql::getInstance();
  $CACHE = Cache::getInstance();
  $autopub = unserialize(Option::get('autopub'));
  $sql = "SELECT gid, content FROM ".DB_PREFIX."blog where hide ='y' order by date DESC";
  $result = $DB->query($sql);
  while($row = $DB->fetch_array($result)){
    $key = 'post-'.$row['gid'];
    if (isset($autopub[$key])) {
      $pub_time = $autopub[$key];
      if (time() >= $pub_time) {
        $autopubCopyRight = '<!-- (此文通过<a href="http://xiaosong.org/share/auto-pub-plugin-update">emlog自动发布插件</a>发布) -->';
        $condition = strpos($row['content'], $autopubCopyRight) === false ? ",content = '".addslashes($row['content'].$autopubCopyRight)."'" : "";
        $sql_do = "UPDATE ".DB_PREFIX."blog SET hide = 'n',date = '".$pub_time."'".$condition." WHERE gid='".$row['gid']."'";
        $DB->query($sql_do);
        doAction('save_log', $row['gid']);
        unset($autopub[$key]);
      }
    }
  }
  Option::updateOption('autopub', serialize($autopub));
  $CACHE->updateCache();
}

$autopubArray = unserialize(Option::get('autopub'));
if (count($autopubArray) > 1) {
  if (isset($autopubArray['trigger']) && $autopubArray['trigger'] == 'ajax') {
    emLoadJQuery();
    addAction('index_footer', 'autopubAjax');
    addAction('adm_footer', 'autopubAjax');
  } else if (isset($autopubArray['trigger']) && $autopubArray['trigger'] == 'php') {
    addAction('adm_footer', 'autopubPhp');
  }
}

function autopub_menu() {
	echo '<div class="sidebarsubmenu" id="autopub"><a href="./plugin.php?plugin=autopub">自动发布</a></div>';
}

addAction('adm_sidebar_ext', 'autopub_menu');
?>