<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr>
<td colspan="2" width=100% class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8"><a href="?mod=extras" title="{l_extras}">{l_extras}</a> <b>=></b> <a href="?mod=extra-config&plugin=feedback">��������� ������� feedback</a></td>
</tr>
</table>

[enabled]
<form method="post" action="">
<input type="hidden" name="mod" value="extra-config"/>
<input type="hidden" name="plugin" value="feedback"/>
<input type="hidden" name="action" value="saveform"/>

<table width="100%" border="0">
<tr>
<td colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8">�������������� ����� "{name}"</td>
</tr>
<tr align="left"><td class="contentRow" width="170"><b>��� �����:</b></td><td><input style="width: 30px; background: white;" type="text" name="id" value="{id}" disabled="disabled"/></td></tr>
<tr align="left"><td class="contentRow" width="170"><b>ID �����:</b></td><td><input style="width: 200px;" type="text" name="name" value="{name}"/></td></tr>
<tr align="left"><td class="contentRow" width="170"><b>�������� �����:</b></td><td><input style="width: 300px;" type="text" name="title" value="{title}"/></td></tr>
<tr align="left"><td class="contentRow" width="170"><b>�������� �����:</b><br/><small>��������� ������������ ����� ������</small></td><td><textarea style="margin-left: 0px;" cols="60" rows="2" name="description">{description}</textarea></td></tr>
<tr align="left"><td class="contentRow" width="170"><b>�������:</b></td><td><input type="checkbox" name="active" value="1" {active_checked} /></td></tr>
<tr align="left"><td class="contentRow" width="170"><b>������������ ������:</b></td><td><select name="template">{template_options}</select></td></tr>
<tr align="left"><td class="contentRow" width="170"><b>Email ������ ��������:</b><br/><small>������ email ������� (�� ������ � ������) �� ������� ����� ������������ ������ �� �����.</small></td><td><textarea style="margin-left: 0px;" cols="60" rows="2" name="emails">{emails}</textarea></td></tr>
<tr><td colspan="2"><input type="submit" value="���������"/></td></tr>
</table>
<hr/>
[/enabled]

<table width="100%">
<tr><td class="contentHead">ID ����</td><td class="contentHead">������������ ����</td><td class="contentHead">��� ����</td><td class="contentHead">�������</td></tr>
{entries}
<tr>
<td colspan="5" style="text-align: left; padding: 10px 10px 0 0;">
<a href="?mod=extra-config&plugin=feedback&action=row&form_id={id}">�������� ����� ����</a>
</td>
</tr>
</table>
</form>