<tr><td align=left>
 <input type=button value="��������" style="width:170px;" onclick="document.getElementById('vtr_{voteid}').style.display='inline';" /> 
 <input type=button value="��������" style="width:170px;" onclick="document.getElementById('vtr_{voteid}').style.display='none';" /> &nbsp; &nbsp; <b>[</b> <u>����� �������������:</u> <b>{allcnt}</b> ] {fregonly}</b>
</td>
<td align=right><input type=button value="������� �����" style="width:170px;" onclick="if(confirm('�� �������?�������� �������� ����������!')){ document.location='{php_self}?mod=extra-config&plugin=voting&action=delvote&id={voteid}';}" />
</td>
</tr>

<tr><td colspan=2>
<div id="vtr_{voteid}" style="display: none;">
<table border="1" style="width:600px;">
 <tr valign="top">
  <td>��������:</td>
  <td><input size="60" style="width:370px;" value="{name}" name="vname_{voteid}" /></td>
  <td width="15" rowspan="2">&nbsp;</td>
  <td rowspan=2><label><input type=checkbox name="vactive_{voteid}" value=1 {vactive} /> �������</label><br />
   <label><input type=checkbox name="vclosed_{voteid}" value=1 {vclosed} /> ������</label><br />
   <label><input type=checkbox name="vregonly_{voteid}" value=1 {vregonly} /> ������ ��� ������������������</label>
  </td>
 </tr>
 <tr>
  <td>��������:</td>
  <td><textarea cols=56 rows=3 style="width:365px;" name="vdescr_{voteid}">{descr}</textarea></td>
 </tr>
</table>
<br />

<table border="1" style="width:600px;" id="vlist_{voteid}">
<tr nowrap><td><b>������� ������</b></td><td><b>�������</b></td><td><b>�������</b></td><td><b>�������</b></td></tr>
{entries}
</table>
<br />

<input type=button style="width:600px;" value="�������� ������� ������" onclick="createVLine({voteid});" />
</div>
</td></tr>
