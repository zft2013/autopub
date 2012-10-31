<?php
/**
 * 自动发布插件
 * @copyright (c) xiaosong.org All Rights Reserved
 */
!defined('EMLOG_ROOT') && exit('access deined!');
$isAjax = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strstr($_SERVER['HTTP_X_REQUESTED_WITH'], 'XMLHttpRequest')) ? 1 : 0;
if ($isAjax) {
  doPub();
  exit();
}
emDirect(BLOG_URL);
?>