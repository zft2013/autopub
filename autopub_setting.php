<?php
/**
 * 自动发布插件
 * @copyright (c) xiaosong.org All Rights Reserved
 */

!defined('EMLOG_ROOT') && exit('access deined!');
function plugin_setting_view(){
	$autopubArray = unserialize(Option::get('autopub'));
}
function adminview(){
	$autopubArray = unserialize(Option::get('autopub'));
	$timezone = Option::get('timezone');
  $DB = MySql::getInstance();
  $sql = "SELECT gid, title FROM ".DB_PREFIX."blog where hide ='y' and type = 'blog' order by date DESC";
	$result = $DB->query($sql);
	$i = 1;
	while ($row = $DB->fetch_array($result)) {
		$key = 'post-'.$row['gid'];
    if (isset($autopubArray[$key])) {
    	$pub_time = gmdate('Y-m-d H:i:s', $autopubArray[$key] + $timezone * 3600);
		} else {
	    $pub_time = '';
		}
		$output .= '<form action="plugin.php?plugin=autopub&action=setting&pub='.$row['gid'].'" method="post"><p>';
    $output .= $i.'、<a href="'.BLOG_URL.'admin/write_log.php?action=edit&gid='.$row['gid'].'">'.$row['title'].'</a>&nbsp;&nbsp;';
		$output .= '预发布时间：<input name="pubTime" type="text" value="'.$pub_time.'" class="calendar" />&nbsp;&nbsp;';
		$output .= '<input type="submit" value="保 存" class="submit" /></p></form>';
		$i++;
	}
	if (empty($output)) {
    $output = '<p>暂无待发布日志！请将待发布日志保存为草稿！</p>';
	}
	echo $output;
}
?>
<style type="text/css">
	.calendar { background: url(<?php echo BLOG_URL; ?>content/plugins/autopub/skins/default/calendar.gif) no-repeat right 1px; cursor: pointer; padding-right: 20px; border: 1px solid #ABADB3; outline: 0 none; height: 18px; width: 160px; }
</style>
<script type="text/javascript" src="<?php echo BLOG_URL; ?>content/plugins/autopub/lhgcalendar.min.js"></script>
<script type="text/javascript">
$(function(){
$('#autopub').addClass('sidebarsubmenu1');
$('input.calendar').calendar({format: 'yyyy-MM-dd HH:mm:ss'});
})
</script>
<div class="containertitle"><b>自动发布</b>
<?php if(isset($_GET['setting'])):?><span class="actived">插件设置完成</span><?php endif;?>
<?php if(isset($_GET['error'])):?><span class="error">插件设置失败</span><?php endif;?>
</div>
<div class="line"></div>
<p><b>插件设置：</b></p>
<form action="plugin.php?plugin=autopub&action=setting" method="post">
	<p><label for="trigger_ajax"><input type="radio" name="trigger" value="ajax" id="trigger_ajax"<?php if (isset($autopubArray['trigger']) && $autopubArray['trigger'] == "ajax"): ?> checked<?php endif; ?>> ajax请求的方式触发自动发布（有访客就能自动发布）</label> <label for="trigger_php"><input type="radio" name="trigger" value="php" id="trigger_php"<?php if (isset($autopubArray['trigger']) && $autopubArray['trigger'] == "php"): ?> checked<?php endif; ?>> php定时触发自动发布（无访客也能自动发布，但会增加系统开销）</label></p>
	<p><input type="submit" value="保 存" class="submit" /></p>
</form>
<div class="line"></div>
<p><b>自动发布列表：</b></p>
<?php adminview(); ?>
<?php 
function plugin_setting(){
	$autopubArray = unserialize(Option::get('autopub'));
	if (!isset($autopubArray['trigger'])) {
		$autopubArray['trigger'] = 'ajax';
	}
	$trigger = isset($_POST['trigger']) ? trim($_POST['trigger']) : $autopubArray['trigger'];
	$autopubArray['trigger'] = $trigger;
	$run = EMLOG_ROOT.'/content/plugins/autopub/autopub_cron_pid.php';
	clearstatcache();
	if ($trigger == 'php') {
		if (!file_exists($run)) {
			$fp = fopen($run, 'wb') OR emMsg('读取文件失败，如果您使用的是Unix/Linux主机，请修改文件/content/plugins/autopub/的权限为755或777。如果您使用的是Windows主机，请联系管理员，将该文件设为everyone可写');
    	fclose($fp);
    }
	} else {
		unlink($run);
	}
	if (isset($_GET['pub'])) {
	  $gid = isset($_GET['pub']) ? intval($_GET['pub']) : -1;
		$pubTime = isset($_POST['pubTime']) ? trim($_POST['pubTime']) : '';
		if ($gid > 0 && !empty($pubTime)) {
			$key = 'post-'.$gid;
		  $autopubArray[$key] = emStrtotime($pubTime);
		} else {
	    return false;
		}
	}
	Option::updateOption('autopub', serialize($autopubArray));
  $CACHE = Cache::getInstance();
  $CACHE->updateCache('options');
}