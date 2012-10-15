<?php
/**
 * autopub_setting.php
 * design by 小松
 */

!defined('EMLOG_ROOT') && exit('access deined!');
!defined('AUTOPUB_CACHE_DIR') && define('AUTOPUB_CACHE_DIR', EMLOG_ROOT.'/content/plugins/autopub/cache/');
function plugin_setting_view(){
}
function adminview(){
    $DB = MySql::getInstance();
    $sql = "SELECT gid,title FROM ".DB_PREFIX."blog where hide ='y' and type = 'blog' order by date DESC";
	$result = $DB->query($sql);
	$i = 1;
	while($row = $DB->fetch_array($result)){
    if(file_exists(AUTOPUB_CACHE_DIR.$row['gid'].'.php')){
      include_once(AUTOPUB_CACHE_DIR.$row['gid'].'.php');
		}else{
	    $year = $month = $day = $hour = $minute = $second = '';
		}
		$output .= '<form action="plugin.php?plugin=autopub&action=setting&pub='.$row['gid'].'" method="post"><p>';
    $output .= $i.'、<a href="'.BLOG_URL.'admin/write_log.php?action=edit&gid='.$row['gid'].'">'.$row['title'].'</a>&nbsp;&nbsp;';
		$output .= '预发布时间：<input name="year" type="text" value="'.$year.'" size="3" />年<input name="month" type="text" value="'.$month.'" size="3" />月<input name="day" type="text" value="'.$day.'" size="3" />日<input name="hour" type="text" value="'.$hour.'" size="3" />时<input name="minute" type="text" value="'.$minute.'" size="3" />分<input name="second" type="text" value="'.$second.'" size="3" />秒&nbsp;&nbsp;';
		$output .= '<input type="submit" value="保 存" class="submit" /></p></form>';
		$i++;
	}
	if(empty($output)){
	    $output = '<p>暂无待发布日志！请将待发布日志保存为草稿！</p>';
	}
	echo $output;
}
?>
<script type="text/javascript">
$(function(){
$("#autopub").addClass('sidebarsubmenu1');
})
</script>
<div class="containertitle"><b>自动发布</b>
<?php if(isset($_GET['setting'])):?><span class="actived">插件设置完成</span><?php endif;?>
<?php if(isset($_GET['error'])):?><span class="error">插件设置失败，请填写合理日期！年月日必填</span><?php endif;?>
</div>
<div class="line"></div>
<div>
<?php adminview(); ?>
</div>
<?php 
function plugin_setting(){
if(isset($_GET['pub']))
{
  $gid = isset($_GET['pub']) ? intval($_GET['pub']) : -1;
	$year = isset($_POST['year']) ? intval($_POST['year']) : 0;
	$month = isset($_POST['month']) ? intval($_POST['month']) : 0;
	$day = isset($_POST['day']) ? intval($_POST['day']) : 0;
	$hour = isset($_POST['hour']) ? intval($_POST['hour']) : 0;
	$minute = isset($_POST['minute']) ? intval($_POST['minute']) : 0;
	$second = isset($_POST['second']) ? intval($_POST['second']) : 0;
	$data = "<?php
	\$year = ".$year.";
	\$month = ".$month.";
	\$day = ".$day.";
	\$hour = ".$hour.";
	\$minute = ".$minute.";
	\$second = ".$second.";
?>";
    $file = AUTOPUB_CACHE_DIR.$gid.'.php';
	if($year != 0 && $month != 0 && $day != 0){
	    @ $fp = fopen($file, 'wb') OR emMsg('读取文件失败，如果您使用的是Unix/Linux主机，请修改/content/plugins/autopub/cache/目录的权限为777。如果您使用的是Windows主机，请联系管理员，将该文件设为everyone可写');
	    @ $fw =	fwrite($fp,$data) OR emMsg('写入文件失败，如果您使用的是Unix/Linux主机，请修改/content/plugins/autopub/cache/目录的权限为777。如果您使用的是Windows主机，请联系管理员，将该文件设为everyone可写');
	    fclose($fp);
	}else{
	    return false;
	}
}
}