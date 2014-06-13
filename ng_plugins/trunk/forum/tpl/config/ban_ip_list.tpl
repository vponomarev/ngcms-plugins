<table border="0" cellspacing="0" cellpadding="0" width="100%">
	<tr align="center">
		<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
			<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=ban'" value="Общие" class="navbutton" />
			<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=ban_ip_list'" value="Забаненые IP" class="navbutton" />
			<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=ban_ip_range'" value="Забаненый диапазон IP" class="navbutton" />
			<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=ban_user'" value="Забаненые пользователи" class="navbutton" />
		</td>
	</tr>
</table><br />
<form method="post" name="categories" action="">
	<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center" class="contHead">
			<td width="15%">IP</td>
			<td width="70%">Причины бана</td>
			<td width="15%">Действие</td>
		</tr>
		{% for entry in entries %}
			<tr align="center" class="contRow1">
				<td>{{ entry.ip }}</td>
				<td>{{ entry.desc_error }}</td>
				<td><a href="admin.php?mod=extra-config&plugin=forum&action=ban_ip_list_del&ip={{ entry.ip }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/dell.png" title="Удалить" alt="Удалить" border="0" /></a></td>
			</tr>
		{% endfor %}
	</table>
</form>