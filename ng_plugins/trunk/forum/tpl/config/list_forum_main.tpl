<form method="post" name="categories" action="">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
	<thead>
		<tr align="left" class="contHead">
			<td width="5%">�</td>
			<td width="5%">�������</td>
			<td>��������</td>
			<td>����</td>
			<td>���������</td>
			<td width="160">��������</td>
		</tr>
	</thead>
	<tbody id="admCatList">
	{{ entries }}
	</tbody>
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>

<tfoot>
<tr><td colspan="8" class="contentEdit" align="center"><input type="submit" name="submit" value="������������� �����" class="button" /></td></tr>
<tr>
<td colspan="8" class="contentEdit" align="right">
<input class="button" onmousedown="javascript:window.location.href='{{admin_url}}/admin.php?mod=extra-config&plugin=forum&action=send_forum'" value="�������� �����" />
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
</form>