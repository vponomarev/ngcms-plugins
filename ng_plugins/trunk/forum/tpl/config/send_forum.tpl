<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">Отображение<br /><small></small></td>
			<td width="50%" class="contentEntry2">
				<select size=1  disabled><option value="{{ forum_id }}" >{{ forum_name }}</option></select>
			</td>
			
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Название форума:<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ name }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Описание форума<br /><small></small></td>
			<td width="50%" class="contentEntry2"><textarea name="description" cols="77" rows="4" />{{ description }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Ключевые слова<br /><small></small></td>
			<td width="50%" class="contentEntry2"><textarea name="keywords" cols="77" rows="4" />{{ keywords }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Модераторы<br /><small>Укажите логины пользователей через запятую</small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="moderators" value="{{ moderators }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Создавать темы</td>
			<td width="50%" class="contentEntry2">{{ m_topic_send }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Редактировать тему</td>
			<td width="50%" class="contentEntry2">{{ m_topic_modify }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Закрывать тему</td>
			<td width="50%" class="contentEntry2">{{ m_topic_closed }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Удалять тему</td>
			<td width="50%" class="contentEntry2">{{ m_topic_remove }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Отвечать в темах</td>
			<td width="50%" class="contentEntry2">{{ m_post_send }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Редактировать сообщения</td>
			<td width="50%" class="contentEntry2">{{ m_post_modify }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Удалять сообщения</td>
			<td width="50%" class="contentEntry2">{{ m_post_remove }}</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center"><input type="submit" name="submit" value="Сохранить форум" class="button" /></td>
		</tr>
	</table>
</form>