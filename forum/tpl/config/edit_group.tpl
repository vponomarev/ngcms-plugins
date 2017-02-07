<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">Наименование группы:<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<input type="text" size="80" name="group_name" value="{{ group_name }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Цвет</td>
			<td width="50%" class="contentEntry2">
				<input type="text" size="80" name="group_color" value="{{ group_color }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Чтение форума<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">{{ group_read }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Чтение новостей<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">{{ group_news }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Поиск</td>
			<td width="50%" class="contentEntry2">{{ group_search }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Сообщения</td>
			<td width="50%" class="contentEntry2">{{ group_pm }}</td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" name="submit" value="Сохранить группу" class="button"/></td>
		</tr>
	</table>
</form>