<!-- List of news start here -->
<form action="/engine/admin.php?mod=extra-config&plugin=subscribe_comments&action=modify" method="post" name="subscribe_comments">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center">
<tr align="left" class="contHead">
<td width="5%" nowrap>ID</td>
<td width="15%" nowrap>��������</td>
<td width="45%" >�����������</td>
<td width="10%">Email</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" title="������� ���" onclick="javascript:check_uncheck_all(subscribe_comments)" /></td>
</tr>
{entries}
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>

<tr align="center">
<td colspan="8" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">
��������: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
<option value="">-- �������� --</option>
<option value="mass_delete_post">������� ������</option>
</select>
<input type="submit" value="���������.." class="button" />
<br/>
</div>
</td>
</tr>
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>
<tr>
<td align="center" colspan="8" class="contentHead">{pagesss}</td>
</tr>
</table>
</form>