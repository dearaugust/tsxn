<?php
$z[0]=Array("channelartlist","\r\n					<li class=\"has-dropdown\">\r\n						<a href=\"{dede:field.typeurl/}\">{dede:field.typename/}</a>\r\n\r\n						<ul class='{dede:field name=typeid runphp=yes}global \$dsql;\$sql = \"SELECT id From `#@__arctype` WHERE reid=' @me\r\n						    ' And ishidden<>1 order by sortrank asc limit 0,1\";\$row = \$dsql->GetOne(\$sql);@me=is_array(\$row)?'dropdown ':'none ';{/dede:field}\r\n							'>\r\n							{dede:channel type=son noself=yes}\r\n							<li>\r\n								<a href=\"[field:typelink/]\">[field:typename/]</a>\r\n							</li>\r\n							{/dede:channel}\r\n						</ul>\r\n\r\n					</li>\r\n					",372,1018);
$z[0][4]=array();
$z[0][4]['row']="7";
$z[0][4]['typeid']="top";
$z[0][4]['currentstyle']="has-dropdown";

?>