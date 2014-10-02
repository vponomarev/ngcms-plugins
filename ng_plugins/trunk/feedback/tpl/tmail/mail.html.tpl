<html>
<body>
<div style="font: normal 11px verdana, sans-serif;">
<h3>Новая заявка!</h3>
Вам поступила новая заявка на вызов курьера.<br/>
<br/>

<table cellspacing="1" cellpadding="1" width="600">
<thead>
<tr><td width="150" style="background: #E0E0E0; font-weight: bold;">Заголовок</td><td style="background: #E0E0E0; font-weight: bold;">Содержимое</td></tr>
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

