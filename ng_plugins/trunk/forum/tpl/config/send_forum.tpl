<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">�����������<br /><small></small></td>
			<td width="50%" class="contentEntry2">
				<select size=1  disabled><option value="{{ forum_id }}" >{{ forum_name }}</option></select>
			</td>
			
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� ������:<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ Sname }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� ������<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="desc" value="{{ Sdesc }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� �����<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="keyw" value="{{ Skeyw }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">����������<br /><small>������� ������ ������������� ����� �������</small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="moder" value="{{ Smoder }}" /></td>
		</tr>
	</table>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center"><input type="submit" name="submit" value="��������� �����" class="button" /></td>
		</tr>
	</table>
</form>