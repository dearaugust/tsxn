<?php   if(!defined('DEDEINC')) exit('Request Error!');
/**
 * 获取当前频道的下级栏目的内容列表标签
 *
 * @version        $Id: channelartlist.lib.php 1 9:29 2010年7月6日Z tianya $
 * @package        DedeCMS.Taglib
 * @copyright      Copyright (c) 2007 - 2010, DesDev, Inc.
 * @license        http://help.dedecms.com/usersguide/license.html
 * @link           http://www.dedecms.com
 */

/*>>dede>>
<name>频道文档</name>
<type>全局标记</type>
<for>V55,V56,V57</for>
<description>获取当前频道的下级栏目的内容列表标签</description>
<demo>
{dede:channelartlist row=6}
<dl>
 <dt><a href='{dede:field name='typeurl'/}'>{dede:field name='typename'/}</a></dt>
 <dd>
 {dede:arclist titlelen='42' row='10'}    <ul class='autod'> 
     <li><a href="[field:arcurl /]">[field:title /]</a></li>
      <li>([field:pubdate function="MyDate('m-d',@me)"/])</li>
    </ul>
{/dede:arclist}
</dl>
{/dede:channelartlist}
</demo>
<attributes>
    <iterm>typeid:频道ID</iterm> 
    <iterm>row:获取的栏目返回值</iterm>
</attributes> 
>>dede>>*/
 
require_once(DEDEINC.'/arc.partview.class.php');

function lib_channelartlist(&$ctag,&$refObj)
{
    global $dsql,$envs,$_sys_globals;

    //处理标记属性、innertext
    $attlist = 'typeid|0,row|20,cacheid|,limit|';
    FillAttsDefault($ctag->CAttribute->Items,$attlist);
    extract($ctag->CAttribute->Items, EXTR_SKIP);
    $innertext = trim($ctag->GetInnerText());
    $artlist = '';
    //读取固定的缓存块
    $cacheid = trim($cacheid);
    if($cacheid !='') {
        $artlist = GetCacheBlock($cacheid);
        if($artlist!='') return $artlist;
    }
    
    if(empty($typeid))
    {
        $typeid = ( !empty($refObj->TypeLink->TypeInfos['id']) ?  $refObj->TypeLink->TypeInfos['id'] : 0 );
    }
    
    if($innertext=='') $innertext = GetSysTemplets('part_channelartlist.htm');
    $totalnum = $row;
    if(empty($totalnum)) $totalnum = 20;

    //获得类别ID总数的信息
    $typeids = array();
	/* 栏目下获取2级，3级栏目，没有2,3级调用所有顶级
	if($typeid=='son')
	{
		$typeid = ( !empty($refObj->TypeLink->TypeInfos['id']) ?  $refObj->TypeLink->TypeInfos['id'] : 0 );
		$ids = explode(',',GetSonIds($typeid));
		if(count($ids)>1)
		{
			$typeid2 = gettoptype($typeid,'id');
		}
		else
		{
			$typeid2 = 0;
		}
		$tpsql = " reid='$typeid2' AND ishidden<>1 ";
		$order = " ORDER BY sortrank ASC";
	}
	*/
	if($typeid=='son')
	{
		$typeid = ( !empty($refObj->TypeLink->TypeInfos['id']) ?  $refObj->TypeLink->TypeInfos['id'] : 0 );
		$typeid2 = gettoptype($typeid,'id');
		$tpsql = " reid='$typeid2' AND ishidden<>1 ";
		$order = " ORDER BY sortrank ASC";
	}/*
	if($typeid=='ifson')
	{
		$tid = $refObj->TypeLink->TypeInfos['id'];
		$reid = gettoptype($tid,'id');
		if($reid>0)
		{
			$reid = $reid;
		}
		else
		{
			$reid = $tid;
		}
		$sql = "SELECT id From `#@__arctype` WHERE reid='$reid' And ishidden<>1 order by sortrank asc limit 0, 100 ";
		$row = $dsql->GetOne($sql);
		if(is_array($row) && $topid == 0)
		{
			$tpsql = " reid='$reid' AND ishidden<>1 ";
			$order = " ORDER BY sortrank ASC";
		}
		else
		{
            $tpsql = " id IN($tid) AND ishidden<>1 ";
			$order = " ORDER BY substring_index( '$typeid',id,1)";
		}
	}*/
    elseif($typeid==0 || $typeid=='top') {
        $tpsql = " reid=0 AND ishidden<>1 AND channeltype>0 ";
		$order = " ORDER BY sortrank ASC";
    }
    else
    {
        if(!preg_match('#,#', $typeid)) {
            $tpsql = " reid='$typeid' AND ishidden<>1 ";
			$order = " ORDER BY sortrank ASC";
        }
        else {
            $tpsql = " id IN($typeid) AND ishidden<>1 ";
			$order = " ORDER BY FIELD(id,$typeid)";
        }
    }
$limit = trim(preg_replace('#limit#is', '', $limit));
if($limit!='') $limitsql = " LIMIT $limit ";
else $limitsql = " LIMIT 0,$totalnum";

    $dsql->SetQuery("SELECT id,typename,typenamedir,typelitpic,typesmallpic,typedir,isdefault,ispart,defaultname,namerule2,moresite,siteurl,sitepath FROM `#@__arctype` WHERE $tpsql $order $limitsql");//英文栏目名和图片字段 by neo
    $dsql->Execute();
	$totalRow = $dsql->GetTotalRow();
    while($row = $dsql->GetArray()) {
        $typeids[] = $row;
    }

    if(!isset($typeids[0])) return '';

    $GLOBALS['itemindex'] = 0;
    $GLOBALS['itemparity'] = 1;
	$GLOBALS['iflast']=0;
    for($i=0;isset($typeids[$i]);$i++)
    {
        $GLOBALS['itemindex']++;
        $pv = new PartView($typeids[$i]['id']);
        $pv->Fields['typeurl'] = GetOneTypeUrlA($typeids[$i]);

//neo 子栏目当前高亮 start
if($typeids[$i]['id'] == $refObj->TypeLink->TypeInfos['id'] || $typeids[$i]['id'] == $refObj->TypeLink->TypeInfos['topid'] || $typeids[$i]['id'] == GetTopid($refObj->TypeLink->TypeInfos['id']) )
{
	$pv->Fields['currentstyle'] = $currentstyle ? $currentstyle : 'current';
}
else
{
	$pv->Fields['currentstyle'] = '';
}
//neo 子栏目当前高亮 start

        $pv->SetTemplet($innertext,'string');
        $artlist .= $pv->GetResult();
        $GLOBALS['itemparity'] = ($GLOBALS['itemparity']==1 ? 2 : 1);
		if($GLOBALS['itemindex']==$totalRow-1){$GLOBALS['iflast']=1;}
    }
    //注销环境变量，以防止后续调用中被使用
    $GLOBALS['envs']['typeid'] = $_sys_globals['typeid'];
    $GLOBALS['envs']['reid'] = '';
    if($cacheid !='') {
        WriteCacheBlock($cacheid, $artlist);
    }
    return $artlist;
}