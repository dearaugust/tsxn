<li class="has-dropdown">
						<a href="{dede:field.typeurl/}">{dede:field.typename/}</a>

						<ul class='{dede:field name=typeid runphp=yes}global $dsql;$sql = "SELECT id From `#@__arctype` WHERE reid=' @me
						    ' And ishidden<>1 order by sortrank asc limit 0,1";$row = $dsql->GetOne($sql);@me=is_array($row)?'dropdown ':'none ';{/dede:field}
							'>
							{dede:channel type=son noself=yes}
							<li>
								<a href="[field:typelink/]">[field:typename/]</a>
							</li>
							{/dede:channel}
						</ul>

					</li>