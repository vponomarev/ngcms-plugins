<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
	<tr align="left" class="contHead">
		<td width="3%" nowrap>#</td>
		<td width="15%">Стукач</td>
		<td width="40%">Жалоба</td>
		<td width="15%">Действие</td>
		<td width="15%">Кто закрыл</td>
	</tr>
	{% for entry in entries %}
	<tr align="left" >
		<td class="contentEntry1">{{ entry.id }}</td>
		<td class="contentEntry1">{{ entry.author }}<br/><small><a href="{{ entry.author_link }}">{{ entry.home_url }}{{ entry.author_link }}</a></small></td>
		<td class="contentEntry1">{{ entry.message }}<br/><small><a href="{{ entry.post_link }}">{{ entry.home_url }}{{ entry.post_link }}#{{ entry.post_id }}</a></small></td>
		<td class="contentEntry1">{% if (entry.viewed == 0) %}<input class="button" onmousedown="javascript:window.location.href='{{admin_url}}/admin.php?mod=extra-config&plugin=forum&action=closed_complaints&id={{ entry.id }}'" value="Закрыть" />{% else %}<input class="button" value="Закрыто" />{% endif %}</td>
		<td class="contentEntry1">{% if (entry.viewed == 0) %}Пока открыа{% else %}{{ entry.who_author }}<br/><small><a href="{{ entry.who_author_link }}">{{ entry.home_url }}{{ entry.who_author_link }}</a></small>{% endif %}</td>
	</tr>
	{% else %}
	<tr align="left" >
		<td width="10%" class="contentEntry1">Пусто</td>
		<td width="60%" class="contentEntry1"></a></td>
		<td width="30%" class="contentEntry1"></td>
		<td width="30%" class="contentEntry1"></td>
		<td width="30%" class="contentEntry1"></td>
	</tr>
	{% endfor %}
	<tr>
		<td width="100%" colspan="8">&nbsp;</td>
	</tr>
	<tr>
		<td width="100%" colspan="8">&nbsp;</td>
	</tr>
	<tr>
		<td align="center" colspan="8" class="contentHead">{{ pagesss }}</td>
	</tr>
</table>