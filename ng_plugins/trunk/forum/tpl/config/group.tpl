	<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center" class="contHead">
			<td>ID</td>
			<td>������������</td>
			<td>����</td>
			<td>������ ������</td>
			<td>������ ��������</td>
			<td>�����</td>
			<td>���������</td>
			<td>��������</td>
		</tr>
		{% for entry in entries %}
			<tr align="center" class="contRow1">
				<td>{{ entry.group_id }}</td>
				<td>{{ entry.group_name }}</td>
				<td>{% if (entry.group_color) %}<span style="color:{{ entry.group_color }}">{{ entry.group_color }}</span>{% else %}�����{% endif %}</td>
				<td>{% if (entry.group_read) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="��" alt="��" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="���" alt="���" border="0" /></a>{% endif %}</td>
				<td>{% if (entry.group_news) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="��" alt="��" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="���" alt="���" border="0" /></a>{% endif %}</td>
				<td>{% if (entry.group_search) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="��" alt="��" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="���" alt="���" border="0" /></a>{% endif %}</td>
				<td>{% if (entry.group_pm) %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/yes.png" title="��" alt="��" border="0" /></a>{% else %}<img src="{{ admin_url }}/plugins/forum/tpl/config/images/no.png" title="���" alt="���" border="0" /></a>{% endif %}</td>
				<td><a href="admin.php?mod=extra-config&plugin=forum&action=edit_group&id={{ entry.id }}"><img src="{{ admin_url }}/plugins/forum/tpl/config/images/edit.png" title="�������������" alt="�������������" border="0" /></a></td>
			</tr>
		{% endfor %}
	</table>