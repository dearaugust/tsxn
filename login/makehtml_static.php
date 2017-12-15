<?php
/**
 * 一键设置全站动静态
 *
 * @version        $Id: makehtml_static.php 2 9:30 2019-9-4 dedediy $
 * @package        DedeDIY.Administrator
 * @copyright      Copyright (c) 1987 - 2016, DeDeDev, Inc.
 * @license        http://www.dedediy.com
 * @link           http://www.dedediy.com
 */
require_once(dirname(__FILE__)."/config.php");
CheckPurview('sys_MakeHtml');
require_once(DEDEINC."/arc.partview.class.php");
$action = (empty($action) ? '' : $action);

if($action=='')
{
    require_once(DEDEADMIN."/templets/makehtml_static.htm");
    exit();
}
else if($action=='make')
{
	if($uptype=='dynamic')
	{
		$row = $dsql->ExecuteNoneQuery("update `#@__arctype` set isdefault=-1");//栏目动态
		$row = $dsql->ExecuteNoneQuery("update `#@__archives` set ismake=-1");//文章动态
		$row = $dsql->ExecuteNoneQuery("UPDATE `#@__homepageset` SET showmod=0");//首页动态
		
		$homeFile = DEDEADMIN."/../index.html";
		$homeFile = str_replace("\\","/",$homeFile);
		$homeFile = str_replace("//","/",$homeFile);
		// 动态浏览
        if (file_exists($homeFile)) @unlink($homeFile);
        $goto = '../index.php';
		
		$article_add = file_get_contents(DEDEADMIN.'/templets/article_add.htm');
		$article_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" />', $article_add);
		$article_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" checked />', $article_add1);
		@$fp = fopen(DEDEADMIN.'/templets/article_add.htm','w'); 
		fwrite($fp,$article_add2);
		fclose($fp);

		$archives_add = file_get_contents(DEDEADMIN.'/templets/archives_add.htm');
		$archives_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" />', $archives_add);
		$archives_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" checked />', $archives_add1);
		@$fp = fopen(DEDEADMIN.'/templets/archives_add.htm','w'); 
		fwrite($fp,$archives_add2);
		fclose($fp);

		$album_add = file_get_contents(DEDEADMIN.'/templets/album_add.htm');
		$album_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" />', $album_add);
		$album_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" checked />', $album_add1);
		@$fp = fopen(DEDEADMIN.'/templets/album_add.htm','w'); 
		fwrite($fp,$album_add2);
		fclose($fp);
		
		$soft_add = file_get_contents(DEDEADMIN.'/templets/soft_add.htm');
		$soft_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" />', $soft_add);
		$soft_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" checked />', $soft_add1);
		@$fp = fopen(DEDEADMIN.'/templets/soft_add.htm','w'); 
		fwrite($fp,$soft_add2);
		fclose($fp);
		
		$spec_add = file_get_contents(DEDEADMIN.'/templets/spec_add.htm');
		$spec_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" />', $spec_add);
		$spec_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" checked />', $spec_add1);
		@$fp = fopen(DEDEADMIN.'/templets/spec_add.htm','w'); 
		fwrite($fp,$spec_add2);
		fclose($fp);
		ShowMsg("全站动态设置成功，4秒钟后返回上一页！<a href='../index.php?upcache=1' target='_blank'>预览首页</a><br />",-1,0,4000);
	}
	else
	{
		$row = $dsql->ExecuteNoneQuery("update `#@__arctype` set isdefault=1");//栏目静态
		$row = $dsql->ExecuteNoneQuery("update `#@__archives` set ismake=1");//文章静态
		$row = $dsql->ExecuteNoneQuery("UPDATE `#@__homepageset` SET showmod=1");//首页静态
		
		$article_add = file_get_contents(DEDEADMIN.'/templets/article_add.htm');
		$article_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" checked />', $article_add);
		$article_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" />', $article_add1);
		@$fp = fopen(DEDEADMIN.'/templets/article_add.htm','w'); 
		fwrite($fp,$article_add2);
		fclose($fp);

		$archives_add = file_get_contents(DEDEADMIN.'/templets/archives_add.htm');
		$archives_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" checked />', $archives_add);
		$archives_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" />', $archives_add1);
		@$fp = fopen(DEDEADMIN.'/templets/archives_add.htm','w'); 
		fwrite($fp,$archives_add2);
		fclose($fp);

		$album_add = file_get_contents(DEDEADMIN.'/templets/album_add.htm');
		$album_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" checked />', $album_add);
		$album_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" />', $album_add1);
		@$fp = fopen(DEDEADMIN.'/templets/album_add.htm','w'); 
		fwrite($fp,$album_add2);
		fclose($fp);
		
		$soft_add = file_get_contents(DEDEADMIN.'/templets/soft_add.htm');
		$soft_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" checked />', $soft_add);
		$soft_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" />', $soft_add1);
		@$fp = fopen(DEDEADMIN.'/templets/soft_add.htm','w'); 
		fwrite($fp,$soft_add2);
		fclose($fp);
		
		$spec_add = file_get_contents(DEDEADMIN.'/templets/spec_add.htm');
		$spec_add1 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="1"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="1" checked />', $spec_add);
		$spec_add2 = preg_replace('/<input(.*?)name="ishtml"(.*?)value="0"(.*?)>/i', '<input type="radio" name="ishtml" class="np" value="0" />', $spec_add1);
		@$fp = fopen(DEDEADMIN.'/templets/spec_add.htm','w'); 
		fwrite($fp,$spec_add2);
		fclose($fp);
		ShowMsg("全站静态设置成功，4秒钟跳转到生成页面！<a target='main' href='makehtml_all.php'>现在去生成全站</a><br />","makehtml_all.php",0,4000);
	}
}