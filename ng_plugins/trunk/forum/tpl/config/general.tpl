<form method="post" action="">
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title"><b>��������� ������</b></legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>�������� ������� �� �������� ������ ����� ����� ������� ��� �����������<br /></td>
<td class="contentEntry2" valign=top>{{ localsource }}</td>

</tr><tr>
<td class="contentEntry1" valign=top>�������� ��� ����������� ������<br /></td>
<td class="contentEntry2" valign=top>{{ display_main }}</td>

</tr><tr>
<td class="contentEntry1" valign=top>����� ��� ���������<br /><small>������������� � ��������...</small></td>
<td class="contentEntry2" valign=top><input name="redirect_time" type="text" title="����� ��� ���������" size="4" value="{{ redirect_time }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>����� � ������� �������� ������������ ��������� � ����<br /><small>��������� � ��������</small></td>
<td class="contentEntry2" valign=top><input name="online_time" type="text" title="����� � ������� �������� ������������ ��������� � ����" size="10" value="{{ online_time }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>�������� ��� �������<br /></td>

<td class="contentEntry2" valign=top>{{ online }}</td>
</tr><tr>
<td class="contentEntry1" valign=top>���� ������<br /></td>
<td class="contentEntry2" valign=top><input name="forum_title" type="text" title="���� ������"  value="{{ forum_title }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>�������� ������<br /></td>
<td class="contentEntry2" valign=top><input name="forum_description" type="text" title="�������� ������"  value="{{ forum_description }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>�������� �����<br /></td>

<td class="contentEntry2" valign=top><input name="forum_keywords" type="text" title="�������� �����"  value="{{ forum_keywords }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>�������� �������� ������<br /><small>��������� ���� ����� �������������� ��� ��������� <b>������</b> � ���������� ����</small><br /></td>
<td class="contentEntry2" valign=top>{{ localskin }}</td>
</tr>
<tr>
<td class="contentEntry1" valign=top>������� ����� � ������� �������� ����� ������������� � ������� ���������<br /><small></small><br /></td>
<td class="contentEntry2" valign=top><input name="edit_del_time" type="text" title="������� ����� � ������� �������� ����� ������������� � ������� ���������"  value="{{ edit_del_time }}" /></td>
</tr>
</table>
</fieldset>
</td>
</tr>
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title"><b>������������ ���������</b></legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>���������� ��������� � ����<br /></td>
<td class="contentEntry2" valign=top><input name="topic_per_page" type="text" title="���������� ��������� � ����" size="4" value="{{ topic_per_page }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>���������� �������� ��� �� ����� ��������<br /></td>
<td class="contentEntry2" valign=top><input name="search_per_page" type="text" title="���������� �������� ��� �� ����� ��������" size="4" value="{{ search_per_page }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>���������� ������������� �� ��������<br /></td>
<td class="contentEntry2" valign=top><input name="user_per_page" type="text" title="���������� ������������� �� ��������" size="4" value="{{ user_per_page }}" /></td>
</tr><tr>
<td class="contentEntry1" valign=top>���������� ��� � ������� ������<br /></td>
<td class="contentEntry2" valign=top><input name="forum_per_page" type="text" title="���������� ��� � ������� ������" size="4" value="{{ forum_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ��������� � ������� ���������<br /></td>
<td class="contentEntry2" valign=top><input name="reput_per_page" type="text" title="���������� ��� � ������� ������" size="4" value="{{ reput_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ��������� ��� ������ ������� ������<br /></td>
<td class="contentEntry2" valign=top><input name="act_per_page" type="text" title="���������� ��� � ������� ������" size="4" value="{{ act_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ������� �� ��������<br /></td>
<td class="contentEntry2" valign=top><input name="thank_per_page" type="text" title="���������� ��� � ������� ������" size="4" value="{{ thank_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ��������� �� �������� ������<br /></td>
<td class="contentEntry2" valign=top><input name="newpost_per_page" type="text" title="���������� ��� � ������� ������" size="4" value="{{ newpost_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ��������<br /></td>
<td class="contentEntry2" valign=top><input name="news_per_page" type="text" title="���������� ��������" size="4" value="{{ news_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� rss<br /></td>
<td class="contentEntry2" valign=top><input name="rss_per_page" type="text" title="���������� ��������" size="4" value="{{ rss_per_page }}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>���������� ���������<br /></td>
<td class="contentEntry2" valign=top><input name="list_pm_per_page" type="text" title="���������� ��������" size="4" value="{{ list_pm_per_page }}" /></td>
</tr>
</table>
</fieldset>
</td>
</tr>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input name="submit" type="submit"  value="���������" class="button" />
</td>
</tr>
</table>
</form>