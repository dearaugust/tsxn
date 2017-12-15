<?php
function litimgurls($imgid=0)
{
    global $lit_imglist,$dsql;
    $row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c ON a.channel=c.id where a.id='$imgid'");
    $addtable = trim($row['addtable']);

    $row = $dsql->GetOne("Select imgurls From `$addtable` where aid='$imgid'");

    $ChannelUnit = new ChannelUnit(2,$imgid);
    
    $lit_imglist = $ChannelUnit->GetlitImgLinks($row['imgurls']);

    return $lit_imglist;
}

/**
 *  获取顶级栏目相关信息
 *
 * @access    public
 * @param     string  $tid  栏目id
 * @param     string  $action  字段
 * @return    string
 */
if (!function_exists('gettoptype'))
{
	function gettoptype($tid,$action)
	{
		global $dsql,$cfg_Cs;
		if(!is_array($cfg_Cs))
		{
			require_once(DEDEDATA."/cache/inc_catalog_base.inc");
		}
		if(!isset($cfg_Cs[$tid][0]) || $cfg_Cs[$tid][0]==0)
		{
			$topid = $tid;
		}
		else
		{
			$topid = GetTopid($cfg_Cs[$tid][0]);
		}
		$row = $dsql->GetOne("SELECT * FROM `#@__arctype` WHERE id=$topid");
		$toptypename = $row['typename'];
		$toptypeurl = $topid;
		if($action=='id') return $topid;
		if($action=='typename') return $toptypename;
		if($action=='typeurl') return GetOneTypeUrlA($row);
		if($action=='typelitpic') return $row['typelitpic'] ? $row['typelitpic'] : '/images/nopic.jpg';
		if($action=='typesmallpic') return $row['typesmallpic'] ? $row['typesmallpic'] : '/images/nopic.jpg';
		if($action=='typenamedir') return $row['typenamedir'];
		if($action=='seotitle') return $row['seotitle'];
		if($action=='description') return $row['description'];
	}
}

/**
 *  获取当前栏目相关信息
 *
 * @access    public
 * @param     string  $tid  栏目id
 * @param     string  $action  字段
 * @return    string
 */
if (!function_exists('mygettype'))
{
	function mygettype($tid,$action)
	{
		global $dsql;
		$row = $dsql->GetOne("SELECT * FROM `#@__arctype` WHERE id=$tid");
		$typename = $row['typename'];
		$typeurl = $tid;
		if($action=='id') return $tid;
		if($action=='typename') return $typename;
		if($action=='typeurl') return GetOneTypeUrlA($row);
		if($action=='typelitpic') return $row['typelitpic'] ? $row['typelitpic'] : gettoptype($tid,'typelitpic');
		if($action=='typesmallpic') return $row['typesmallpic'] ? $row['typesmallpic'] : gettoptype($tid,'typesmallpic');
		if($action=='typenamedir') return $row['typenamedir'];
		if($action=='seotitle') return $row['seotitle'];
		if($action=='description') return $row['description'];
	}
}

/**
 *  获取上一级栏目相关信息
 *
 * @access    public
 * @param     string  $tid  栏目id
 * @param     string  $action  字段
 * @return    string
 */
if(!function_exists('mygetretype'))
{
	function mygetretype($tid,$action)
	{
		global $dsql;
		$typeid = $tid;
		$query = "SELECT reid FROM `#@__arctype` where id = $typeid";
		$rs = $dsql->GetOne($query);
		$reid = $rs['reid']; 
		$query2 = "SELECT * FROM `#@__arctype` where id = $reid";
		$row = $dsql->GetOne($query2);
		$typename = $row['typename'];
		if($action=='id') return $reid;
		if($action=='typename') return $typename;
		if($action=='typeurl') return GetOneTypeUrlA($row);
		if($action=='typelitpic') return $row['typelitpic'] ? $row['typelitpic'] : gettoptype($tid,'typelitpic');
		if($action=='typenamedir') return $row['typenamedir'];
		if($action=='seotitle') return $row['seotitle'];
		if($action=='description') return $row['description'];
	}
}

/**
 *  获取软件模型本地下载地址
 *
 * @access    public
 * @param     string  $aid  文档id
 * @return    string
 */
if (!function_exists('GetDownLink'))
{
    function GetDownLink($aid)
    {
       global $dsql;
       $row= $dsql->GetOne("SELECT softlinks FROM `#@__addonsoft` WHERE aid = {$aid}");
           if(!is_array($row)){
            return '';
            }
           else{
            $dtp = new DedeTagParse();
            $dtp->LoadSource($row['softlinks']);
                foreach($dtp->CTags as $ctag)
                {
                    if($ctag->GetName()=='link')
                {
                    $link = trim($ctag->GetInnerText());
                }
            }
       }
       return  $link;
    }
}

/**
 *  自定义图片字段调用图片地址
 *
 * @access    public
 * @param     string  $fieldname  	字段名
 * @param     string  $ftype	  	输出样式
 * @return    string
 */
if(!function_exists('GetImgUrl'))
{
	function GetImgUrl($fieldname, $ftype = 1)
	{
		if($fieldname != '')
		{
			$dtp = new DedeTagParse();
			$dtp->LoadSource($fieldname);
			if(is_array($dtp->CTags))
			{
				foreach($dtp->CTags as $ctag)
				{
					if($ctag->GetName() == 'img')
					{
						$width = $ctag->GetAtt('width');
						$height = $ctag->GetAtt('height');
						$imgurl = trim($ctag->GetInnerText());
						$img = '';
						if($imgurl != '')
						{
							if($ftype == 1)
							{
								$img .= $imgurl;
							}
							else
							{
								$img .= '<img src="' . $imgurl . '" width="' . $width . '" height="' . $height . '" />';
							}
						}
					}
				}
			}
			$dtp->Clear();
			return $img;
		}
	}
}

/**
 *  其他页面调用模板的头部尾部模板
 *
 * @access    public
 * @param     string  $path  	模板路径
 * @return    string
 */
if(!function_exists('pasterTempletDiy'))
{
	function pasterTempletDiy($path)
	{
		require_once(DEDEINC."/arc.partview.class.php");
		global $cfg_basedir,$cfg_templets_dir,$cfg_df_style;
		$tmpfile = $cfg_basedir.$cfg_templets_dir.'/'.$cfg_df_style.'/'.$path;
		$dtp = new PartView();
		$dtp->SetTemplet($tmpfile);
		$dtp->Display();
	}
}

/**
 *  获取图集图片数量
 *
 * @access    public
 * @param     string  $aid  	文章id
 * @return    string
 */
if(!function_exists('GetImgCount'))
{
	function GetImgCount($aid)     
	{
		global $dsql;
		$imgurls = '';
		$row =$dsql->getone( "Select imgurls From `#@__addonimages` where aid='$aid' ");
		$imgurls= $row['imgurls'];
		preg_match_all("/{dede:img (.*){\/dede:img/isU",$imgurls,$wordcount);
		$count=count($wordcount[1]);
		return $count;
	}
}

/**
 *  获取栏目文档数量
 *
 * @access    public
 * @param     string  $tid  	栏目id
 * @return    string
 */
if(!function_exists('GetTotalArc'))
{
	function GetTotalArc($tid){
		global $dsql;
		$sql = GetSonIds($tid);
		$row = $dsql->GetOne("Select count(id) as total From `#@__archives` where typeid in({$sql})");
		if(is_array($row)){
			return " ".$row[total]." ";
		}else{
			return "0" ;
		}
	}
}

/**
 *  自定义图片大小输出
 *
 * @access    public
 * @param     string  $imgurl  	图片地址
 * @param     string  $width  	自定义宽
 * @param     string  $height  	自定义高
 * @param     string  $bg	  	背景色
 * @return    string
 */
if(!function_exists('thumb'))
{
	function thumb($imgurl, $width, $height, $bg = true) 
	{ 
		global $cfg_mainsite,$cfg_multi_site;
		$thumb = eregi("http://",$imgurl)?str_replace($cfg_mainsite,'',$imgurl):$imgurl; 
		list($thumbname,$extname) = explode('.',$thumb); 
		$newthumb = $thumbname.'_'.$width.'_'.$height.'.'.$extname;
		if(!$thumbname || !$extname || !file_exists(DEDEROOT.$thumb)) return $imgurl;
		if(!file_exists(DEDEROOT.$newthumb))
		{ 
			include_once DEDEINC.'/image.func.php';
			if($bg==true)
			{
				ImageResizeNew(DEDEROOT.$thumb, $width, $height, DEDEROOT.$newthumb);
			}
			else
			{
				ImageResize(DEDEROOT.$thumb, $width, $height, DEDEROOT.$newthumb);
			} 
		}
		return $cfg_multi_site=='Y' ? $cfg_mainsite.$newthumb : $newthumb;
	}
}

/**
 *  图集在首页列表页调用并且自定义输出和指定几张
 *
 * @access    public
 * @param     string  $aid  	文档id
 * @param     string  $num  	输出几张
 * @return    string
 */
if(!function_exists('Getimgurls'))
{
	function Getimgurls($aid, $num=4)    
	{
		global $dsql;
		$imgurls = $result = '';
		$imgrow = $dsql->GetOne( "Select imgurls From `#@__addonimages` where aid='$aid' ");
		$imgurls = $imgrow['imgurls'];
		if($imgurls != '')
		{
			$dtp = new DedeTagParse();
			$dtp->LoadSource($imgurls);
			$images = array();
			if(is_array($dtp->CTags))
			{
				foreach($dtp->CTags as $ctag)
				{
					if($ctag->GetName() == 'img')
					{
						$row = array();
						$row['width'] = $ctag->GetAtt('width');
						$row['height'] = $ctag->GetAtt('height');
						$row['imgsrc'] = trim($ctag->GetInnerText());
						$row['text'] = $ctag->GetAtt('text');
						$images[] = $row;
					}
				}
			}
			$dtp->Clear();
			$i = 0;
			foreach($images as $row)
			{
				if($i == $num) break;
				if($row['imgsrc'] != '')
				{
					$result .= "<li><div class='pic'><a title='{$row['text']}' href='{$row['imgsrc']}'><img src='{$row['imgsrc']}' mid='{$row['imgsrc']}' big='{$row['imgsrc']}' width='70' height='70'></a></div></li>";
				}
				$i++;
			}
			return $result;
		}  
	}
}

/**
 *  文章内容提取图片(多张)自定义输出
 *
 * @access    public
 * @param     string  $string  	文档内容
 * @param     string  $num  	输出几张
 * @return    string
 */
if(!function_exists('getBodypics'))
{
	function getBodypics($string, $num)
	{
		preg_match_all("/<img([^>]*)\s*src=('|\")([^'\"]+)('|\")/",$string,$matches);
		$imgsrc_arr = array_unique($matches[3]);
		$count = count($imgsrc_arr);
		$i = 0;
		foreach($imgsrc_arr as $imgsrc)
		{
			if($i == $num) break;
			$result .= "<img src=\"$imgsrc\"/>";
			$i++;
		}
		return $result;
	}
}

/**
 *  在首页列表页调用TAG标签
 *
 * @access    public
 * @param     string  $aid  	文档id
 * @param     string  $num  	输出几个
 * @return    string
 */
if(!function_exists('MyGetTags'))
{
	function MyGetTags($aid, $num=3) 
	{ 
		 global $dsql; 
		 $tags = ''; 
		 $query = "Select tag From `#@__taglist` Where aid='$aid' limit $num "; 
		 $dsql->Execute('tag',$query); 
		 while($row = $dsql->GetArray('tag')) 
		 { 
			 $tags.= ($tags=='' ? "<a href=/tags.php?/{$row['tag']} rel='tag'>{$row['tag']}</a>" : ','."<a href=/tags.php?/{$row['tag']} rel='tag'>{$row['tag']}</a>"); 
		 } 
		 return $tags; 
	}
}


/**
 *  字符过滤函数(用于安全)
 *
 * @access    public
 * @param     string  $str  字符
 * @param     string  $stype  类型
 * @return    string
 */
if(!function_exists('string_filter'))
{
	function string_filter($str,$stype="inject")
	{
		if ($stype=="inject")  {
			$str = str_replace(
				   array( "select", "insert", "update", "delete", "alter", "cas", "union", "into", "load_file", "outfile", "create", "join", "where", "like", "drop", "modify", "rename", "'", "/*", "*", "../", "./"),
				   array("","","","","","","","","","","","","","","","","","","","","",""),
				   $str);
		} else if ($stype=="xss") {
			$farr = array("/\s+/" ,
						  "/<(\/?)(script|META|STYLE|HTML|HEAD|BODY|STYLE |i?frame|b|strong|style|html|img|P|o:p|iframe|u|em|strike|BR|div|a|TABLE|TBODY|object|tr|td|st1:chsdate|FONT|span|MARQUEE|body|title|\r\n|link|meta|\?|\%)([^>]*?)>/isU", 
						  "/(<[^>]*)on[a-zA-Z]+\s*=([^>]*>)/isU",
						  );
			$tarr = array(" ",
						  "",
						  "\\1\\2",
						  ); 
			$str = preg_replace($farr, $tarr, $str);
			$str = str_replace(
				   array( "<", ">", "'", "\"", ";", "/*", "*", "../", "./"),
				   array("&lt;","&gt;","","","","","","",""),
				   $str);
		}
		return $str;
	}
}

/**
 *  输出筛选按钮
 *
 * @access    public
 * @param     string  $fieldset  字段列表
 * @param     string  $loadtype  载入类型
 * @return    string
 */
if(!function_exists('AddFilter'))
{
	function AddFilter($channelid, $type=1, $fieldsnamef, $defaulttid, $toptid=0, $loadtype='autofield')
	{
		global $tid,$dsql,$id,$aid;
		$tid = $defaulttid ? $defaulttid : $tid;
		if ($id!="" || $aid!="")
		{
			$arcid = $id!="" ? $id : $aid;
			$tidsq = $dsql->GetOne(" Select * From `#@__archives` where id='$arcid' ");
			$tid = $toptid==0 ? $tidsq["typeid"] : $tidsq["topid"];
		}
		$nofilter = (isset($_REQUEST['TotalResult']) ? "&TotalResult=".$_REQUEST['TotalResult'] : '').(isset($_REQUEST['PageNo']) ? "&PageNo=".$_REQUEST['PageNo'] : '');
		$filterarr = string_filter(stripos($_SERVER['REQUEST_URI'], "list.php?tid=") ? str_replace($nofilter, '', $_SERVER['REQUEST_URI']) : $GLOBALS['cfg_cmsurl']."/plus/list.php?tid=".$tid);
		$cInfos = $dsql->GetOne(" Select * From  `#@__channeltype` where id='$channelid' ");
		$fieldset=$cInfos['fieldset'];
		$dtp = new DedeTagParse();
		$dtp->SetNameSpace('field','<','>');
		$dtp->LoadSource($fieldset);
		$dede_addonfields = '';
		if(is_array($dtp->CTags))
		{
			foreach($dtp->CTags as $tida=>$ctag)
			{
				$fieldsname = $fieldsnamef ? explode(",", $fieldsnamef) : explode(",", $ctag->GetName());
				if(($loadtype!='autofield' || ($loadtype=='autofield' && $ctag->GetAtt('autofield')==1)) && in_array($ctag->GetName(), $fieldsname) )
				{
					$href1 = explode($ctag->GetName().'=', $filterarr);
					$href2 = explode('&', $href1[1]);
					$fields_value = $href2[0];
					$dede_addonfields .= '<b>'.$ctag->GetAtt('itemname').'：</b>';
					switch ($type) {
						case 1:
							$dede_addonfields .= (preg_match("/&".$ctag->GetName()."=/is",$filterarr,$regm) ? '<a title="全部" href="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'">全部</a>' : '<span>全部</span>').'&nbsp;';
						
							$addonfields_items = explode(",",$ctag->GetAtt('default'));
							for ($i=0; $i<count($addonfields_items); $i++)
							{
								$href = stripos($filterarr,$ctag->GetName().'=') ? str_replace("=".$fields_value,"=".urlencode($addonfields_items[$i]),$filterarr) : $filterarr.'&'.$ctag->GetName().'='.urlencode($addonfields_items[$i]);
								$dede_addonfields .= ($fields_value!=urlencode($addonfields_items[$i]) ? '<a title="'.$addonfields_items[$i].'" href="'.$href.'">'.$addonfields_items[$i].'</a>' : '<span>'.$addonfields_items[$i].'</span>')."&nbsp;";
							}
						break;
						
						case 2:
							$dede_addonfields .= '<select name="filter'.$ctag->GetName().'" onchange="window.location=this.options[this.selectedIndex].value">
								'.'<option value="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'">全部</option>';
							$addonfields_items = explode(",",$ctag->GetAtt('default'));
							for ($i=0; $i<count($addonfields_items); $i++)
							{
								$href = stripos($filterarr,$ctag->GetName().'=') ? str_replace("=".$fields_value,"=".urlencode($addonfields_items[$i]),$filterarr) : $filterarr.'&'.$ctag->GetName().'='.urlencode($addonfields_items[$i]);
								$dede_addonfields .= '<option value="'.$href.'"'.($fields_value==urlencode($addonfields_items[$i]) ? ' selected="selected"' : '').'>'.$addonfields_items[$i].'</option>
								';
							}
							$dede_addonfields .= '</select>';
						break;
						
						case 3:
							$dede_addonfields .= (preg_match("/&".$ctag->GetName()."=/is",$filterarr,$regm) ? '<a title="全部" href="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'"><input type="radio" name="filter'.$ctag->GetName().'" value="'.str_replace("&".$ctag->GetName()."=".$fields_value,"",$filterarr).'" onclick="window.location=this.value">全部</a>' : '<span><input type="radio" name="filter'.$ctag->GetName().'" checked="checked">全部</span>').'&nbsp;';
						
							$addonfields_items = explode(",",$ctag->GetAtt('default'));
							for ($i=0; $i<count($addonfields_items); $i++)
							{
								$href = stripos($filterarr,$ctag->GetName().'=') ? str_replace("=".$fields_value,"=".urlencode($addonfields_items[$i]),$filterarr) : $filterarr.'&'.$ctag->GetName().'='.urlencode($addonfields_items[$i]);
								$dede_addonfields .= ($fields_value!=urlencode($addonfields_items[$i]) ? '<a title="'.$addonfields_items[$i].'" href="'.$href.'"><input type="radio" name="filter'.$ctag->GetName().'" value="'.$href.'" onclick="window.location=this.value"/>'.$addonfields_items[$i].'</a>' : '<span><input type="radio" name="filter'.$ctag->GetName().'" checked="checked"/>'.$addonfields_items[$i].'</span>')."&nbsp;";
							}
						break;
					}
				}
			}
		}
		echo $dede_addonfields;
	}
}

/**
 *  时间美化
 *
 * @access    public
 * @param     string  $time  	时间戳
 * @return    string
 */
if(!function_exists('tranTime'))
{
	function tranTime($time)
	{
		$rtime = date("m-d H:i",$time);
		$htime = date("H:i",$time);
		$etime = time() - $time;
		if ($etime < 1) return '刚刚';
		$interval = array (
			12 * 30 * 24 * 60 * 60  =>  ' 年 前',
			30 * 24 * 60 * 60       =>  ' 个 月 前',
			7 * 24 * 60 * 60        =>  ' 周 前',
			24 * 60 * 60            =>  ' 天 前',
			60 * 60                 =>  ' 小 时 前',
			60                      =>  ' 分 钟 前',
			1                       =>  ' 秒 前'
		);
		foreach($interval as $secs => $str)
		{
			$d = $etime / $secs;
			if($d >= 1)
			{
				$r = round($d);
				return $r . $str;
			}
		};
	}
}

/**
 *  获取自定义字段的值
 *
 * @access    public
 * @param     string  $aid  	文档id
 * @param     string  $addField	自定义字段名
 * @return    string
 */
if(!function_exists('GetAddField'))
{
	function GetAddField($aid,$addField)
	{
		global $dsql;
		$row = $dsql->GetOne("SELECT c.addtable FROM #@__archives AS a LEFT JOIN #@__channeltype AS c ON a.channel=c.id where a.id='$aid'");
		$addtable = trim($row['addtable']);

		$row = $dsql->GetOne("SELECT $addField FROM `$addtable` WHERE aid=$aid");
		return $row["$addField"];
	}
}
//获取图集内容  
function getbody($id)    
{    
    global $dsql;    
    $row = $dsql->GetOne("SELECT body FROM #@__addonimages WHERE aid= '$id'");    
    $res = $row['body'];    
    return $res;    
}  

/**
 *  自定义图片字段调用图片地址
 *
 * @access    public
 * @param     string  $fieldname  	字段名
 * @param     string  $ftype	  	输出样式
 * @return    string
 */
if(!function_exists('GetImgUrl'))
{
	function GetImgUrl($fieldname, $ftype = 1)
	{
		if($fieldname != '')
		{
			$dtp = new DedeTagParse();
			$dtp->LoadSource($fieldname);
			if(is_array($dtp->CTags))
			{
				foreach($dtp->CTags as $ctag)
				{
					if($ctag->GetName() == 'img')
					{
						$width = $ctag->GetAtt('width');
						$height = $ctag->GetAtt('height');
						$imgurl = trim($ctag->GetInnerText());
						$img = '';
						if($imgurl != '')
						{
							if($ftype == 1)
							{
								$img .= $imgurl;
							}
							else
							{
								$img .= '<img src="' . $imgurl . '" width="' . $width . '" height="' . $height . '" />';
							}
						}
					}
				}
			}
			$dtp->Clear();
			return $img;
		}
	}
}