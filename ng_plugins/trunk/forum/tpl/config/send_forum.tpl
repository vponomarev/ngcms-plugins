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
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="description" value="{{ description }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Ключевые слова<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="keywords" value="{{ keywords }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Модераторы<br /><small>Укажите логины пользователей через запятую</small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="moderators" value="{{ moderators }}" /></td>
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