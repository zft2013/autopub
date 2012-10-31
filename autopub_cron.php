<?php
/**
 * 自动发布插件
 * @copyright (c) xiaosong.org All Rights Reserved
 */
ignore_user_abort(true);
set_time_limit(0);

if (!defined('EMLOG_ROOT')) {
  require_once('../../../init.php');
}

do {
  clearstatcache();
  $run = EMLOG_ROOT.'/content/plugins/autopub/autopub_cron_pid.php';
  if(!file_exists($run)) die('do nothing');
  doPub();
  sleep(30);
} while (true);

?>