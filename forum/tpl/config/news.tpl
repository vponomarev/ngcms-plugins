<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
	<tr align="left" class="contHead">
		<td width="10%" nowrap>#</td>
		<td width="60%">Заголовок</td>
		<td width="30%">Действие</td>
	</tr>
	{% for entry in entries %}
		<tr align="left">
			<td width="10%" class="contentEntry1">{{ entry.news_id }}</td>
			<td width="60%" class="contentEntry1">{{ entry.title }}</td>
			<td width="30%" class="contentEntry1">{{ entry.edit }} {{ entry.del }}</td>
		</tr>
	{% else %}
		<tr align="left">
			<td width="10%" class="contentEntry1">Пусто</td>
			<td width="60%" class="contentEntry1"></a></td>
			<td width="30%" class="contentEntry1"></td>
		</tr>
	{% endfor %}
	<tr>
		<td width="100%" colspan="8">&nbsp;</td>
	</tr>
	<tfoot>
	<tr>
		<td colspan="8" class="contentEdit" align="right">
			<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=new_news'" value="Добавить новость"/>
		</td>
	</tr>
	</tfoot>
	<tr>
		<td width="100%" colspan="8">&nbsp;</td>
	</tr>
	<tr>
		<td align="center" colspan="8" class="contentHead">{{ pagesss }}</td>
	</tr>
</table>