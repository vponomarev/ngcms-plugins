<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
	<tr align="left" class="contHead">
		<td width="10%" nowrap>#</td>
		<td width="40%">Название</td>
		<td width="60%">Заголовок</td>
		<td width="80%">Действие</td>
	</tr>
	{{ entries }}
	<tr>
		<td width="100%" colspan="8">&nbsp;</td>
	</tr>

	<tfoot>
	<tr>
		<td colspan="8" class="contentEdit" align="right">
			<input class="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro&action=send_title&do=news'" value="Добавить новость"/>
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