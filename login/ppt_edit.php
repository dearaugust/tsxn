<?php
require(dirname(__FILE__)."/config.php");
CheckPurview('plus_幻灯管理');
require_once(DEDEINC.'/typelink.class.php');
require_once(DEDEINC.'/customfields.func.php');
require_once(DEDEADMIN.'/inc/inc_archives_functions.php');
if(empty($dopost))
{
	$dopost = '';
}

$aid = ereg_replace('[^0-9]','',$aid);
$ENV_GOBACK_URL = empty($_COOKIE['ENV_GOBACK_URL']) ? "ppt_main.php" : $_COOKIE['ENV_GOBACK_URL'];

if($dopost=='delete')
{
	$dsql->ExecuteNoneQuery("Delete From `#@__myppt` where aid='$aid' ");
	ShowMsg("成功删除幻灯片！",$ENV_GOBACK_URL);
	exit();
}
else if($dopost=='saveedit')
{
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
	$query = "Update `#@__myppt`
	 set
	 orderid='$orderid',
	 title='$title',
	 typeid='$typeid',
	 pic='$pic',
	 pic2='$pic2',
	 content='$content',
	 url='$url'
	 where aid='$aid'
	 ";
	$dsql->ExecuteNoneQuery($query);
	ShowMsg("成功更改幻灯片！",$ENV_GOBACK_URL);
	exit();
}

$row = $dsql->GetOne("Select * From `#@__myppt` where aid='$aid'");
$dsql->Execute('dd','SELECT * FROM `#@__myppttype` ORDER BY id DESC');
$option = '';
while($arr = $dsql->GetArray('dd'))
{
    if ($arr['id'] == $row['typeid'])
    {
        $option .= "<option value='{$arr['id']}' selected='selected'>{$arr['typename']}</option>\n\r";
    } else {
        $option .= "<option value='{$arr['id']}'>{$arr['typename']}</option>\n\r";
    }
}
include DedeInclude('templets/ppt_edit.htm');

?>