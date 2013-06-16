<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">�����������<br /><small></small></td>
			<td width="50%" class="contentEntry2">
				<select size=1 name="parent">
						{% for entry in list_forum %}
							<option value="{{ entry.id }}" {% if (entry.id_set == entry.id) %}selected="selected"{% endif %}>{{ entry.title }}</option>
						{% endfor %}
				</select>
			</td>
			
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� ������:<br /><small></small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="name" value="{{ name }}" /></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� ������<br /><small></small></td>
			<td width="50%" class="contentEntry2"><textarea name="description" cols="77" rows="4" />{{ description }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">�������� �����<br /><small></small></td>
			<td width="50%" class="contentEntry2"><textarea name="keywords" cols="77" rows="4" />{{ keywords }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">����������<br /><small>������� ������ ������������� ����� �������</small></td>
			<td width="50%" class="contentEntry2"><input type="text" size="80" name="moderators" value="{{ moderators }}" /></td>
		</tr>
	</table>
	<div id="userTabs">
		<ul>
			{% for entry in list_group %}
				<li><a href="#userTabs-{{ entry.id }}">{{ entry.name }}</a></li>
			{% endfor %}
		</ul>
		{% for entry in list_group %}
		<div id="userTabs-{{ entry.id }}">
			<div><i>���������� ������� ������ �������������: <b>{{ entry.name }}</b></i></div>
			<br/>
			<div class="pconf">
				<h1></h1>
				
				<h2>��������� ����</h2>
				
				<table width="100%" class="content">
					<thead><tr class="contHead"><td><b>��������</b></td><td><b>��������</b></td><td width="90"><b>������</b></td></td></thead>
					<tr class="contentEntry1">
						<td><strong>������������� ������</strong></td><td>-</td>
						<td>
							{{ entry.read_forum }}
						</td>
					</tr>
					<tr class="contentEntry1">
						<td><strong>������������� ����</strong></td><td>-</td>
						<td>
							{{ entry.read_topic }}
						 </td>
					</tr>
					<tr class="contentEntry1">
						<td><strong>��������� ����</strong></td><td>-</td>
						<td>
							{{ entry.send_topic }}
						</td>
					</tr>
					<tr class="contentEntry1">
						<td><strong>����������� ����</strong></td><td>-</td>
						<td>
							{{ entry.remove_topic }}
						</td>
					</tr>
					<tr class="contentEntry1">
						<td><strong>������������� ���� ����</strong></td><td>-</td>
						<td>
							{{ entry.remove_your_topic }}
						</td>
					</tr>
				</table>
				<br/>
			</div>
		</div>
		{% endfor %}
	</div>
	<script type="text/javascript">
	$(function(){
		$("#userTabs").tabs();
	});
	</script>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center"><input type="submit" name="submit" value="��������� �����" class="button" /></td>
		</tr>
	</table>
</form>