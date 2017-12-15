<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_幻灯管理');
require_once DEDEINC."/typelink.class.php";
require_once(DEDEINC.'/customfields.func.php');
require_once(DEDEADMIN.'/inc/inc_archives_functions.php');
if(empty($dopost))
{
	$dopost = "";
}

if($dopost=="save")
{
	$orderid = trim($orderid);
	//处理远程图片
    if(empty($ddisremote))
    {
        $ddisremote = 0;
    }
    $pic = GetDDImage('none', $pic, $ddisremote);
	if(empty($ddisremote2))
    {
        $ddisremote2 = 0;
    }
    $pic2 = GetDDImage('none', $pic2, $ddisremote2);
	$query = "INSERT INTO #@__myppt(typeid,orderid,title,pic,pic2,content,url) VALUES ('$typeid','$orderid','$title','$pic','$pic2','$content','$url');";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功增加一个幻灯片！","ppt_main.php");
	exit();
}
$dsql->Execute('dd','SELECT * FROM `#@__myppttype` ORDER BY id DESC');
$option = '';
while($arr = $dsql->GetArray('dd'))
{
    $option .= "<option value='{$arr['id']}'>{$arr['typename']}</option>\n\r";
}
include DedeInclude('templets/ppt_add.htm');
?>