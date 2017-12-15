<?php
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/arc.rssview.class.php");
require_once DEDEINC."/arc.partview.class.php";

if(empty($dopost)){
	include DedeInclude('templets/map.htm');
	exit();
}

if($x!="sitemap.html" && $x!="sitemap.xml" && $x!="rss.xml" && $x!="feed.xml")
{
	ShowMsg("参数错误!","-1");
	exit();
}
if(empty($cfg_cmspath)){$cfg_cmspath='/';}
$murl = $cfg_cmspath. $x;
if(!empty($dopost) && !empty($x))
{
	$pv = new PartView();
	$pv->SetTemplet($cfg_basedir . $cfg_templets_dir . '/plus' .'/'. $x);
	$pv->SaveToHtml(dirname(__FILE__).'/../'. $x);
	echo "<a href='$murl' target='_blank'>成功更新文件: $murl 浏览...</a>";
	exit();
}
?>