<?php
if(!isset($GLOBALS['_vars'])) $GLOBALS['_vars'] = array(); 
$fields = array();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $cfg_soft_lang; ?>">
<title>幻灯管理</title>
<link href='css/base.css' rel='stylesheet' type='text/css'>
</head>
<body background='images/allbg.gif' leftmargin='8' topmargin='8'>
<table width="98%" border="0" align="center" cellpadding="3" cellspacing="1" bgcolor="#D6D6D6">
<tr>
	<td height="28" background="images/tbg.gif" style="padding-left:10px;" colspan="8"> 
		<b>幻灯管理</b>&nbsp;
		[<a href="ppt_add.php"><u>添加幻灯片图片</u></a>]
		[<a href="ppttype_main.php"><u>增加一组幻灯片</u></a>]
	</td>
</tr>

  <tr align="center" bgcolor="#F9FCEF" height="24"> 
      <td width="5%">幻灯排序</td>
	  <td width="10%">幻灯片组</td>
	  <td width="5%">幻灯组ID</td>
	  <td width="10%">幻灯标题</td>
      <td width="30%">图片地址</td>
      <td width="30%">图片链接</td>
      <td width="10%">管理</td>
   </tr>
   <?php
$atts = array();
$atts['tagname'] = 'datalist';
$blockValue = $this->refObj->GetArcList($atts,$this->refObj,$fields); 
if(is_array($blockValue)){
foreach( $blockValue as $key=>$fields )
{
?>
    <tr align="center" class="bgcolor<?php echo $fields['typeid']; ?>" height="24" onMouseMove="javascript:this.className='bgcolor';" onMouseOut="javascript:this.className='bgcolor<?php echo $fields['typeid']; ?>';"> 
        <td><?php echo $fields['orderid']; ?></td>
		<td><a href="?tid=<?php echo $fields['typeid']; ?>"><?php echo GetPptTypeName($fields['typeid']); ?></a></td>
		<td><a href="?tid=<?php echo $fields['typeid']; ?>"><?php echo $fields['typeid']; ?></a></td>
        <td><a href="<?php echo $fields['url']; ?>" target='_blank'><?php echo $fields['title']; ?></a></td>
        <td><a href="<?php echo $fields['url']; ?>" target='_blank'><?php echo GetPic($fields['pic']); ?></a></td>
        <td><?php echo $fields['url']; ?></td>
        <td>
            <a href='ppt_edit.php?aid=<?php echo $fields['aid']; ?>&dopost=edit'>[更改]</a>
	    <a href='ppt_edit.php?aid=<?php echo $fields['aid']; ?>&dopost=delete'>[删除]</a>
		    </td>
     </tr>
		<?php
}
}?>
     <tr align="center" bgcolor="#F9FCEF" height="24"> 
      <td colspan="8">
      	<?php
$atts = array();
$atts['tagname'] = 'pagelist';
$atts['listsize'] = '5';
 echo $this->refObj->GetPageList($atts,$this->refObj,$fields); 
?>

      </td>
     </tr>
   </table>
   </td>
</tr>
</table>
<style>
.bgcolor{background:#f4fbf4;}
.bgcolor1{background:#F0F5F9;}
.bgcolor2{background:#F5EEDC;}
.bgcolor3{background:#6A99CD;}
.bgcolor4{background:#FDF6EC;}
.bgcolor5{background:#F2F2F2;}
.bgcolor6{background:#F8EFF0;}
.bgcolor7{background:#FFF6F7;}
.bgcolor8{background:#FFEDED;}
.bgcolor9{background:#FFEDED;}
.bgcolor10{background:#FFF7F8;}
.bgcolor11{background:#FFF7F8;}
.bgcolor12{background:#F8EFDE;}
.bgcolor13{background:#F8F8F8;}
.bgcolor14{background:#FAF6D1;}
</style>
</body>
</html>