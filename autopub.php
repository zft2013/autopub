<?php
/*
Plugin Name: 自动发布
Version: 1.9
Plugin URL: http://xiaosong.org/share/auto-pub-plugin-update
Description: 自动发布文章，修正时区问题，增加相关挂载点，自动删除缓存文件。
Author: 小松
Author Email: sahala_2007@126.com
Author URL: http://xiaosong.org
*/
!defined('EMLOG_ROOT') && exit('access deined!');
!defined('AUTOPUB_CACHE_DIR') && define('AUTOPUB_CACHE_DIR', EMLOG_ROOT.'/content/plugins/autopub/cache/');

function dir_is_empty($dir){ 
  if($handle = opendir($dir)){
    while($item = readdir($handle)){
      if ($item != "." && $item != "..") {
        return false;
      }
    }
  }
  return true;
}

function autopub(){
  emLoadJQuery();
  echo '<script type="text/javascript">$(function(){$.get("'.DYNAMIC_BLOGURL.'?plugin=autopub");})</script>'."\n";
}

if (!dir_is_empty(AUTOPUB_CACHE_DIR)) {
  addAction('index_footer', 'autopub');
  addAction('adm_footer', 'autopub');
}

function autopub_menu() {
	echo '<div class="sidebarsubmenu" id="autopub"><a href="./plugin.php?plugin=autopub">自动发布</a></div>';
}

addAction('adm_sidebar_ext', 'autopub_menu');
?>