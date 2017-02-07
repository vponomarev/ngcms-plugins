<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">Название форума:<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ name }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Описание форума<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<textarea name="description" cols="77" rows="4"/>{{ description }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Ключевые слова<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<textarea name="keywords" cols="77" rows="4"/>{{ keywords }}</textarea></td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" name="submit" value="Сохранить форум" class="button"/></td>
		</tr>
	</table>
</form>