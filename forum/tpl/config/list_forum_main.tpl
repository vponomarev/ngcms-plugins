<form method="post" name="categories" action="">
	<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr align="center" class="contHead">
			<td>ID</td>
			<td>Позиция</td>
			<td>Наименование</td>
			<td>Тем</td>
			<td>Сообщений</td>
			<td width="160">Действие</td>
		</tr>

		{{ entries }}

		<tr>
			<td width="100%" colspan="6" align="right">
				<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=forum&action=send_section'" value="Добавить раздел"/>
			</td>
		</tr>
		<tr>
			<td width="100%" colspan="6" class="contentEdit" align="center">
				<input type="submit" name="submit" value="Отсортировать форум" class="navbutton"/>
			</td>
		</tr>
		<tr>
			<td align="center" colspan="6" class="contentHead">{{ pagesss }}</td>
		</tr>
	</table>
</form>