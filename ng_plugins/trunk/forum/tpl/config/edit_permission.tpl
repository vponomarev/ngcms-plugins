<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">������������ ������:<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="GROUP_PERM[{{ id }}][name]" value="{{ name }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">����</td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="GROUP_PERM[{{ id }}][color]" value="{{ color }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">������ ������<br /><small></small></td>
			<td width="50%" class="contentEntry2">{{ read }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">������ ��������<br /><small></small></td>
			<td width="50%" class="contentEntry2">{{ news }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�����</td>
			<td width="50%" class="contentEntry2">{{ search }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">���������</td>
			<td width="50%" class="contentEntry2">{{ pm }}</td>
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