<form method="post" action="">
<tr>
	<td colspan=2>
		<fieldset class="admGroup">
		<legend class="title">���������</legend>
			<table width="100%" border="0" class="content">
				<tr>
					<td class="contentEntry1" valign=top>����� ��� �������<br /><small></small></td>
					<td class="contentEntry2" valign=top>
						{{style_admin_start.error}}<input name="style_admin_start" type="text" size=40 value="{{style_admin_start.print}}" />
						
					</td>
					<td class="contentEntry1" valign=top>����� ��� �������<br /><small></small></td>
					<td class="contentEntry2" valign=top>
						{{style_admin_end.error}}<input name="style_admin_end" type="text" size=40 value="{{style_admin_end.print}}" />
					</td>
				</tr><tr>
					<td class="contentEntry1" valign=top>��������� � ������ �������<br /><small>����� ���� &lt;title&gt;&lt;/title&gt; � ������ ������� (��������� %cat%, %title%, %home%, %num%)</small></td>
					<td class="contentEntry2" valign=top>{{n_title.error}}<input name="n_title" type="text" title="��������� � ������ �������" size=40 value="{{n_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>��������� ������� ��������<br /><small>����� ���� &lt;title&gt;&lt;/title&gt; ������� �������� (��������� %home% %num%)</small></td>
					<td class="contentEntry2" valign=top>{{m_title.error}}<input name="m_title" type="text" title="��������� ������� ��������" size=40 value="{{m_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>��������� ����������� ��������<br /><small>����� ���� &lt;title&gt;&lt;/title&gt; ����������� �������� (��������� %home% � %static%)</small></td>
					<td class="contentEntry2" valign=top>{{static_title.error}}<input name="static_title" type="text" title="��������� ����������� ��������" size=40 value="{{static_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>��������� ��������� ��������<br /><small>����� ���� &lt;title>&lt;/title> ������ ������� (������� ������������, ������ �������) (��������� %home%, %other%, %html% � %num%)</small></td>
					<td class="contentEntry2" valign=top>{{o_title.error}}<input name="o_title" type="text" title="��������� ��������� ��������" size=40 value="{{o_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>�������������� ���������� ��� ��������<br /><small>����� �������������� ���������� � �������� (����. ��� ����)  - ������ ����������� � ���������� %html%</small></td>
					<td class="contentEntry2" valign=top>{{html_secure.error}}<input name="html_secure" type="text" title="�������������� ���������� ��� ��������" size=40 value="{{html_secure.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>������� ����������<br /><small>������ �������� �� ������� ������ ������� �� ����������������</small></td>
					<td class="contentEntry2" valign=top>{{p_title.error}}<input name="p_title" type="text" title="������ �������� �� ������� ������ ������� �� ����������������" size=40 value="{{p_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>����� ��������<br /><small>�������������� ������ �������� (��������, �������� 4 [�������� %count%] - ��� %count% ����� ��������) - ������ ����������� � ���������� %num%</small></td>
					<td class="contentEntry2" valign=top>{{num_title.error}}<input name="num_title" type="text" title="����� ��������" size=40 value="{{num_title.print}}" /></td>
				</tr>
				<tr>
					<td class="contentEntry1" valign=top><br /><small>�����:<br /><b>%cat%</b> - ��� ���������<br /><b>%title%</b> - ��� �������<br><b>%home%</b> - ��������� �����<br /><b>%static%</b> - ��������� ����������� ��������<br /><b>%other%</b> - ��������� ����� ������ ��������<br></small></td>
					<td class="contentEntry2" valign=top></td>
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