{% include localPath(0) ~ "conf.navi.tpl" %}

<form method="post" action="">
<input type="hidden" name="mod" value="extra-config"/>
<input type="hidden" name="plugin" value="feedback"/>
<input type="hidden" name="action" value="saveform"/>

<table width="100%" border="0">
<tr align="left" valign="top"><td class="contentRow" width="230"><b>��� ����� / URL ��������:</b></td><td><input style="width: 30px; background: white;" type="text" name="id" value="{{ id }}" disabled="disabled"/> <input style="width: 420px; background: white;" type="text" value="{{ url }}" readonly="readonly" /></td><td rowspan="6" width="3" style="background-image: url({{ skins_url }}/images/delim.png); background-repeat: repeat-y;"></td><td><input type="checkbox" id="id_active" name="active" value="1" {{ flags.active ? 'checked="checked"' : '' }} /></td><td><label for="id_active"><b>����� �������</b></label></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>ID / �������� �����:</b><br><small><b>ID</b> - ���������� �������������</small></td><td><input style="width: 100px;" type="text" name="name" value="{{ name }}"/> <input style="width: 350px;" type="text" name="title" value="{{ title }}"/></td><td><input type="checkbox" id="id_jcheck" name="jcheck" value="1" {{ flags.jcheck ? 'checked="checked"' : '' }} /></td><td><label for="id_jcheck"><b>��������� ���� �����</b><br/><small>�������� JavaScript ��� ��� �������� ���������� �����</small></label></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>�������� �����:</b><br/><small>��������� ������������ ����� ������</small></td><td><textarea style="margin-left: 0px;" cols="72" rows="3" name="description">{{ description }}</textarea></td><td><input type="checkbox" value="1" name="html" id="id_html" {{ flags.html ? 'checked="checked"' : ''  }} /></td><td><label for="id_html"><b>HTML ��������</b><br/><small>���������� �������������� Email ������ � HTML �������</small></label></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>����������� ���� � email:</b><br/><small>���������� ���������:<br/><b>{name}</b> - ID �����<br/><b>{title}</b> - �������� �����</small></td><td><select name="isSubj"><option value="0">���</option><option value="1" {% if (isSubj) %}selected="selected"{% endif %}>��</option></select> &nbsp; <input style="width: 350px;" type="text" name="subj" value="{{ subj }}"></td><td><input type="checkbox" id="id_captcha" name="captcha" value="1" {{ flags.captcha ? 'checked="checked"' : '' }} /></td><td><label for="id_captcha"><b>������������ <i>captcha</i></b><br/><small>��������� ���� ������������ ���� ��� �������� �������</small></label></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>�������� � ��������:</b><br/><small></small></td><td><select name="link_news">
{% for x in link_news.options %}
	<option value="{{ x }}" {% if (link_news.value == x) %}selected="selected"{% endif %}>{{ lang['feedback:link_news.' ~ x] }}</option>
{% endfor %}

</select></td></tr>
<tr align="left" valign="top"><td class="contentRow" width="230"><b>������������ ������:</b><br/><small>������� ����� � ����������� tpl/templates/</small></td><td><select name="template">{{ template_options }}</select></td><td>&nbsp;</td></tr>
<tr align="left" valign="top">
 <td class="contentRow" width="230"><b>Email ������ ��������:</b><br/><small>������ email ������� � ����� �������������, ������� ����� ������������ ��������� �� ������ �����.<br/><font color="red"><i>���� ������� ������ ���� ������, �� ���� ������ ����������� � ����� ������������ �� �����</i></font></small></td>
 <td colspan="4">
  <table>
   <thead>
    <tr><td>UID</td><td>�������� ������</td><td>������ email ������� ������ (����� �������)</td></tr>
   </thead>
   <tbody>
{% for egroup in egroups %}
<tr>
	<td><input type="text" name="elist[{{ loop.index }}][0]" value="{{ egroup.num }}" size="2" disabled="disabled" /></td>
	<td><input type="text" name="elist[{{ loop.index }}][1]" value="{{ egroup.name }}"/></td>
	<td><input style="width: 550px;" type="text" name="elist[{{ loop.index }}][2]" value="{{ egroup.value }}"/></td>
</tr>
{% endfor %}
   </tbody>
  </table>
 </td>
</tr>
<tr><td colspan="6"><input type="submit" value="���������"/></td></tr>
</table>
<hr/>

<table width="100%">
<tr>
	<td class="contentHead">ID ����</td>
	<td class="contentHead">������������ ����</td>
	<td class="contentHead">��� ����</td>
	<td class="contentHead">��������������</td>
	<td class="contentHead">����������</td>
	<td class="contentHead">�������</td>
</tr>
{% for entry in entries %}
<tr align="left" class="contRow1">
	<td style="padding:2px;">
		<a href="?mod=extra-config&plugin=feedback&action=update&subaction=up&id={{ formID }}&name={{ entry.name }}"><img src="{{ skins_url }}/images/up.gif" width="16" height="16" alt="UP" /></a>
		<a href="?mod=extra-config&plugin=feedback&action=update&subaction=down&id={{ formID }}&name={{ entry.name }}"><img src="{{ skins_url }}/images/down.gif" width="16" height="16" alt="DOWN" />
		<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}&row={{ entry.name }}">{{ entry.name }}</a></td>
	<td>{{ entry.title }}</td>
	<td>{{ lang['feedback:type.' ~ entry.type] }}</td>
	<td>{{ lang['feedback:field.auto.' ~ entry.auto] }}</td>
	<td>{{ lang['feedback:field.block.' ~ entry.block] }}</td>
	<td nowrap><a href="?mod=extra-config&plugin=feedback&action=update&subaction=del&id={{ formID }}&name={{ entry.name }}"><img src="{{ skins_url }}/images/delete.gif" alt="DEL" width="12" height="12" /></a></td>
</tr>
{% endfor %}
<tr>
<td colspan="5" style="text-align: left; padding: 10px 10px 0 0;">
<a href="?mod=extra-config&plugin=feedback&action=row&form_id={{ formID }}">�������� ����� ����</a>
</td>
</tr>
</table>
</form>