<html>
<body>
<div style="font: normal 11px verdana, sans-serif;">
<h3>����� ������!</h3>
��� ��������� ����� ������ �� ����� �������.<br/>
<br/>

<table cellspacing="1" cellpadding="1" width="600">
<thead>
<tr><td width="150" style="background: #E0E0E0; font-weight: bold;">���������</td><td style="background: #E0E0E0; font-weight: bold;">����������</td></tr>
</thead>
<tbody>
{% for entry in entries %}
<tr style="background: #FFFFFF;" valign="top">
 <td style="background: #EFEFEF;" width="200">{{ entry.title }}</td>
 <td style="background: #EFEFEF;">{{ entry.value }}</td>
</tr>
{% endfor %}
</tbody>
</table>

</div>

</body>
</html>

