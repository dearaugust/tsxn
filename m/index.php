<?php
if(!file_exists(dirname(__FILE__).'/../data/common.inc.php'))
{
    header('Location:install/index.php');
    exit();
}
if ( preg_match("#PHP (.*) Development Server#",$_SERVER['SERVER_SOFTWARE']) )
{
    if ( $_SERVER['REQUEST_URI'] == dirname($_SERVER['SCRIPT_NAME']) )
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location:'.$_SERVER['REQUEST_URI'].'/');
    }
}
require_once (dirname(__FILE__) . "/../include/common.inc.php");
require_once DEDEINC."/arc.partview.class.php";
$GLOBALS['_arclistEnv'] = 'index';
$row = $dsql->GetOne("Select * From `#@__homepageset`");
$row['templet'] = MfTemplet($row['templet']);

$pv = new PartView();
$row['templet'] =str_replace('.htm','_m.htm',$row['templet']);
if ( !file_exists($cfg_basedir . $cfg_templets_dir . "/" . $row['templet']) )
{
	echo "模板文件不存在，无法解析文档！";
	exit();
}
$pv->SetTemplet($cfg_basedir . $cfg_templets_dir . "/" . $row['templet']);
$pv->Display();
exit();
?>