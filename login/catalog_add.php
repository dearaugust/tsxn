<?php
/**
 * 栏目添加
 *
 * @version        $Id: catalog_add.php 1 14:31 2010年7月12日Z tianya $
 * @package        DedeCMS.Administrator
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */
require_once(dirname(__FILE__)."/config.php");
require_once(DEDEINC."/typelink.class.php");
require_once(DEDEINC.'/customfields.func.php');
require_once(DEDEADMIN.'/inc/inc_archives_functions.php');
if(empty($listtype)) $listtype='';
if(empty($dopost)) $dopost = '';
if(empty($upinyin)) $upinyin = 0;
if(empty($channelid)) $channelid = 1;
if(isset($channeltype)) $channelid = $channeltype;

$id = empty($id) ? 0 :intval($id);
$reid = empty($reid) ? 0 :intval($reid);
$nid = 'article';

if($id==0 && $reid==0)
{
    CheckPurview('t_New');
}
else
{
    $checkID = empty($id) ? $reid : $id;
    CheckPurview('t_AccNew');
    CheckCatalog($checkID, '你无权在本栏目下创建子类！');
}

if(empty($myrow)) $myrow = array();

$dsql->SetQuery("SELECT id,typename,nid FROM `#@__channeltype` WHERE id<>-1 AND isshow=1 ORDER BY id");
$dsql->Execute();
while($row=$dsql->GetObject())
{
    $channelArray[$row->id]['typename'] = $row->typename;
    $channelArray[$row->id]['nid'] = $row->nid;
    if($row->id==$channelid)
    {
        $nid = $row->nid;
    }
}
if($dopost=='quick')
{
    $tl = new TypeLink(0);
    $typeOptions = $tl->GetOptionArray(0,0,0);
    include DedeInclude('templets/catalog_add_quick.htm');
    exit();
}
else if($dopost=='fast')
{
    $tl = new TypeLink(0);
    $typeOptions = $tl->GetOptionArray(0,0,0);
    include DedeInclude('templets/catalog_add_fast.htm');
    exit();
}
/*---------------------
function action_savequick(){ }
---------------------*/
else if($dopost=='savequick')
{
    if(!isset($savetype)) $savetype = '';
    $isdefault = isset($isdefault)? $isdefault : 0;
    $tempindex = "{style}/index_{$nid}.htm";
    $templist = "{style}/list_{$nid}.htm";
    $temparticle = "{style}/article_{$nid}.htm";
    $queryTemplate = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typenamedir,typelitpic,typesmallpic,typedir,isdefault,defaultname,issend,channeltype,
    tempindex,templist,temparticle,modname,namerule,namerule2,ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`)
    VALUES('~reid~','~topid~','~rank~','~typename~','~typenamedir~','~typelitpic~','~typesmallpic~','~typedir~','$isdefault','$defaultname','$issend','$channeltype',
    '$tempindex','$templist','$temparticle','default','$namerule','$namerule2','0','0','','','~typename~','0','','','0','0','0','','')";//英文栏目名和图片字段 by dedediy.com
    
    if (empty($savetype))//处理顶级分类
    {
        foreach($_POST as $k=>$v)
        {
            if(preg_match("#^posttype#", $k))
            {
                $k = str_replace('posttype', '', $k);
            }
            else
            {
                continue;
            }

            $rank = ${'rank'.$k};
            $toptypename = trim(${'toptype'.$k});
            $toptypenamedir = trim(${'toptypedir'.$k});
            $toptypelitpic = trim(${'toptypelitpic'.$k});
            $toptypesmallpic = trim(${'toptypesmallpic'.$k});
            $sontype = trim(${'sontype'.$k});
            $toptypedir = GetPinyin(stripslashes($toptypename));
            $toptypedir = $referpath=='parent' ? $nextdir.'/'.$toptypedir : '/'.$toptypedir;
            if(empty($toptypename))
            {
                continue;
            }
            $sql = str_replace('~reid~','0',$queryTemplate);
            $sql = str_replace('~topid~','0',$sql);
            $sql = str_replace('~rank~',$rank,$sql);
            $sql = str_replace('~typename~',$toptypename,$sql);
            $sql = str_replace('~typenamedir~',$toptypenamedir,$sql);
            $sql = str_replace('~typelitpic~',$toptypelitpic,$sql);
            $sql = str_replace('~typesmallpic~',$toptypesmallpic,$sql);
            $sql = str_replace('~typedir~',$toptypedir,$sql);
            $dsql->ExecuteNoneQuery($sql);
            $tid = $dsql->GetLastID();
            if($tid>0 && $sontype!='')
            {
                $sontypes = explode(',',$sontype);
                foreach($sontypes as $k=>$v)
                {
                    $v = trim($v);
                    if($v=='')
                    {
                        continue;
                    }
                    $typedir = $toptypedir.'/'.GetPinyin(stripslashes($v));
                    $sql = str_replace('~reid~',$tid,$queryTemplate);
                    $sql = str_replace('~topid~',$tid,$sql);
                    $sql = str_replace('~rank~',$k,$sql);
                    $sql = str_replace('~typename~',$v,$sql);
                    $sql = str_replace('~typenamedir~','',$sql);
                    $sql = str_replace('~typelitpic~','',$sql);
                    $sql = str_replace('~typesmallpic~','',$sql);
                    $sql = str_replace('~typedir~',$typedir,$sql);
                    $dsql->ExecuteNoneQuery($sql);
                }
            }
        }
    } else {//处理二级以下分类
    

        $row = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE `id`={$reid}");
        foreach($_POST as $k=>$v)
        {
            if(preg_match("#^posttype#", $k))
            {
                $k = str_replace('posttype', '', $k);
            }
            else
            {
                continue;
            }
            $rank = ${'relrank'.$k};
            $toptypename = trim(${'reltype'.$k});
            $toptypenamedir = trim(${'reltypedir'.$k});
            $toptypelitpic = trim(${'reltypelitpic'.$k});
            $toptypesmallpic = trim(${'reltypesmallpic'.$k});
            $toptypedir = GetPinyin(stripslashes($toptypename));
            switch ($referpath) {
                case 'parent':
                    $toptypedir = $nextdir.'/'.$toptypedir;
                    break;
                case 'typepath':
                    $toptypedir = isset($row['typedir'])? $row['typedir'].'/'.$toptypedir : '/'.$toptypedir;
                    break; 
                default:
                    $toptypedir = '/'.$toptypedir;
                    break;
            }
            
            if(empty($toptypename))
            {
                continue;
            }
            $sql = str_replace('~reid~', $reid, $queryTemplate);
            $sql = str_replace('~topid~', $reid, $sql);
            $sql = str_replace('~rank~', $rank, $sql);
            $sql = str_replace('~typename~', $toptypename, $sql);
            $sql = str_replace('~typenamedir~', $toptypenamedir, $sql);
            $sql = str_replace('~typelitpic~', $toptypelitpic, $sql);
            $sql = str_replace('~typesmallpic~', $toptypesmallpic, $sql);
            $sql = str_replace('~typedir~', $toptypedir, $sql);
            $dsql->ExecuteNoneQuery($sql);
        }
    }
    UpDateCatCache();
    ShowMsg('成功增加指定栏目！','catalog_main.php');
    exit();
}
/*---------------------
function action_savefast(){ }
---------------------*/
else if($dopost=='savefast')
{
    if(!isset($savetype)) $savetype = '';
    $isdefault = isset($isdefault)? $isdefault : 0;
    $tempindex = "{style}/index_{$nid}.htm";
    $templist = "{style}/list_{$nid}.htm";
    $temparticle = "{style}/article_{$nid}.htm";
	//中文逗号转换英文逗号
	$sortrank=str_replace("，",",",$_POST['sortrank']);
	$typename=str_replace("，",",",$_POST['typename']);
	$typenamedir=str_replace("，",",",$_POST['typenamedir']);
	$typelitpic=str_replace("，",",",$_POST['typelitpic']);
	$typesmallpic=str_replace("，",",",$_POST['typesmallpic']);
	//组装数组
	$sortrank = explode("," ,$sortrank);
	$typename = explode("," ,$typename);
	$typenamedir = explode("," ,$typenamedir);
	$typelitpic = explode("," ,$typelitpic);
	$typesmallpic = explode("," ,$typesmallpic);
    if (empty($savetype))//处理分类
    {
		foreach($typename as $k=>$v)
		{
			$v = trim($v);
			if($v=='')
			{
				continue;
			}
			$vtypedir = $nextdir.'/'.GetPinyin(stripslashes($v));
			$vsortrank = empty($sortrank[$k]) ? $k : $sortrank[$k];
			$vtypelitpic = $typelitpic[$k];
			$vtypesmallpic = $typesmallpic[$k];
			$vtypenamedir = $typenamedir[$k];
			$sql = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typenamedir,typelitpic,typesmallpic,typedir,isdefault,defaultname,issend,channeltype,tempindex,templist,temparticle,modname,namerule,namerule2,ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`) VALUES ('0','0','$vsortrank','$v','$vtypenamedir','$vtypelitpic','$vtypesmallpic','$vtypedir','$isdefault','$defaultname','$issend','$channeltype','$tempindex','$templist','$temparticle','default','$namerule','$namerule2','0','0','','','$v','0','','','0','0','0','','')";
			$dsql->ExecuteNoneQuery($sql);
		}
    } 
	else 
	{//处理二级以下分类

        $row = $dsql->GetOne("SELECT `typedir` FROM `#@__arctype` WHERE `id`={$reid}");
		foreach($typename as $k=>$v)
		{
			$v = trim($v);
			if($v=='')
			{
				continue;
			}
			$vtypedir = GetPinyin(stripslashes($v));
            switch ($referpath) {
                case 'parent':
                    $vtypedir = $nextdir.'/'.$vtypedir;
                    break;
                case 'typepath':
                    $vtypedir = isset($row['typedir'])? $row['typedir'].'/'.$vtypedir : '/'.$vtypedir;
                    break; 
                default:
                    $vtypedir = '/'.$vtypedir;
                    break;
            }
			
			$vtopid = gettoptype($reid,'id');
			$vsortrank = empty($sortrank[$k]) ? $k : $sortrank[$k];
			$vtypelitpic = $typelitpic[$k];
			$vtypesmallpic = $typesmallpic[$k];
			$vtypenamedir = $typenamedir[$k];
			$sql = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typenamedir,typelitpic,typesmallpic,typedir,isdefault,defaultname,issend,channeltype,tempindex,templist,temparticle,modname,namerule,namerule2,ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`) VALUES ('$reid','$vtopid','$vsortrank','$v','$vtypenamedir','$vtypelitpic','$vtypesmallpic','$vtypedir','$isdefault','$defaultname','$issend','$channeltype','$tempindex','$templist','$temparticle','default','$namerule','$namerule2','0','0','','','$v','0','','','0','0','0','','')";
			$dsql->ExecuteNoneQuery($sql);
		}
    }
    UpDateCatCache();
    ShowMsg('成功增加指定栏目！','catalog_main.php');
    exit();
}
/*---------------------
function action_save(){ }
---------------------*/
else if($dopost=='save')
{
    $smalltypes = '';
    if(empty($smalltype)) $smalltype = '';
    if(is_array($smalltype)) $smalltypes = join(',',$smalltype);
    
    if(!isset($sitepath)) $sitepath = '';
    if($topid==0 && $reid>0) $topid = $reid;
    if($ispart!=0) $cross = 0;
    
    $description = Html2Text($description,1);
    $keywords = Html2Text($keywords,1);
	if(empty($seotitle)) $seotitle = $typename;//没填SEO标题时用栏目名称
	//栏目内容本地化 by dedediy.com
	$content = stripslashes($content);
	$content = GetCurContent($content);
	$content = GetFieldValueA($content,'htmltext');
	$content = addslashes($content);
	//处理远程图片
    if(empty($ddisremote))
    {
        $ddisremote = 0;
    }
    $typelitpic = GetDDImage('none', $typelitpic, $ddisremote);
	if(empty($ddisremote2))
    {
        $ddisremote2 = 0;
    }
    $typesmallpic = GetDDImage('none', $typesmallpic, $ddisremote2);
    if($ispart != 2 )
    {
        //栏目的参照目录
        if($referpath=='cmspath') $nextdir = '{cmspath}';
        if($referpath=='basepath') $nextdir = '';
        //用拼音命名
        if($upinyin==1 || $typedir=='')
        {
            $typedir = GetPinyin(stripslashes($typename));
        }
        $typedir = $nextdir.'/'.$typedir;
        $typedir = preg_replace("#\/{1,}#", "/", $typedir);
    }

    //开启多站点时的设置(仅针对顶级栏目)
    if($reid==0 && $moresite==1)
    {
        $sitepath = $typedir;

        //检测二级网址
        if($siteurl!='')
        {
            $siteurl = preg_replace("#\/$#", "", $siteurl);
            if(!preg_match("#http:\/\/#i", $siteurl))
            {
                ShowMsg("你绑定的二级域名无效，请用(http://host)的形式！","-1");
                exit();
            }
            if(preg_match("#".$cfg_basehost."#i", $siteurl))
            {
                ShowMsg("你绑定的二级域名与当前站点是同一个域，不需要绑定！","-1");
                exit();
            }
        }
    }

    //创建目录
    if($ispart != 2)
    {
        $true_typedir = str_replace("{cmspath}", $cfg_cmspath, $typedir);
        $true_typedir = preg_replace("#\/{1,}#", "/", $true_typedir);
        if(!CreateDir($true_typedir))
        {
            ShowMsg("创建目录 {$true_typedir} 失败，请检查你的路径是否存在问题！","-1");
            exit();
        }
    }
    
    $in_query = "INSERT INTO `#@__arctype`(reid,topid,sortrank,typename,typenamedir,typelitpic,typesmallpic,typedir,isdefault,defaultname,issend,channeltype,
    tempindex,templist,temparticle,modname,namerule,namerule2,
    ispart,corank,description,keywords,seotitle,moresite,siteurl,sitepath,ishidden,`cross`,`crossid`,`content`,`smalltypes`)
    VALUES('$reid','$topid','$sortrank','$typename','$typenamedir','$typelitpic','$typesmallpic','$typedir','$isdefault','$defaultname','$issend','$channeltype',
    '$tempindex','$templist','$temparticle','default','$namerule','$namerule2',
    '$ispart','$corank','$description','$keywords','$seotitle','$moresite','$siteurl','$sitepath','$ishidden','$cross','$crossid','$content','$smalltypes')";//英文栏目名和图片字段 by dedediy.com

    if(!$dsql->ExecuteNoneQuery($in_query))
    {
        ShowMsg("保存目录数据时失败，请检查你的输入资料是否存在问题！","-1");
        exit();
    }
    UpDateCatCache();
    if($reid>0)
    {
        PutCookie('lastCid',GetTopid($reid),3600*24,'/');
    }
    ShowMsg("成功创建一个分类！","catalog_main.php");
    exit();

}//End dopost==save

//获取从父目录继承的默认参数
if($dopost=='')
{
    $channelid = 1;
    $issend = 1;
    $corank = 0;
    $reid = 0;
    $topid = 0;
    $typedir = '';
    $moresite = 0;
    if($id>0)
    {
        $myrow = $dsql->GetOne(" SELECT tp.*,ch.typename AS ctypename FROM `#@__arctype` tp LEFT JOIN `#@__channeltype` ch ON ch.id=tp.channeltype WHERE tp.id=$id ");
        $channelid = $myrow['channeltype'];
        $issennd = $myrow['issend'];
        $corank = $myrow['corank'];
        $topid = $myrow['topid'];
        $typedir = $myrow['typedir'];
    }

    //父栏目是否为二级站点
    $moresite = empty($myrow['moresite']) ? 0 : $myrow['moresite'];
}

include DedeInclude('templets/catalog_add.htm');