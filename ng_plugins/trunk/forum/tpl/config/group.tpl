	<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center" class="contHead">
			<td>ID</td>
			<td>Наименование</td>
			<td>Цвет</td>
			<td>Чтение форума</td>
			<td>Чтение новостей</td>
			<td>Поиск</td>
			<td>Сообщения</td>
			<td>Действие</td>
		</tr>
		{% for entry in entries %}
			<tr align="center" class="contRow1">
				<td>{{ entry.id }}</td>
				<td>{{ entry.name }}</td>
				<td>{% if (entry.color) %}<span style="color:{{ entry.color }}">{{ entry.color }}</span>{% else %}Пусто{% endif %}</td>
				<td>{% if (entry.read) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="Да" alt="Да" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="Нет" alt="Нет" border="0" /></a>{% endif %}</td>
				<td>{% if (entry.news) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="Да" alt="Да" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="Нет" alt="Нет" border="0" /></a>{% endif %}</td>
				<td>{% if (entry.search) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="Да" alt="Да" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="Нет" alt="Нет" border="0" /></a>{% endif %}</td>
				<td>{% if (entry.pm) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="Да" alt="Да" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="Нет" alt="Нет" border="0" /></a>{% endif %}</td>
				<td><a href="admin.php?mod=extra-config&plugin=forum&action=edit_group&id={{ entry.id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/edit.png" title="Редактировать" alt="Редактировать" border="0" /></a></td>
			</tr>
		{% endfor %}
	</table>