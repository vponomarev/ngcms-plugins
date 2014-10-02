<html>
<body>
<div style="font: normal 11px verdana, sans-serif;">
<h3>Новая заявка!</h3>
Вам только что поступила новая на вызов курьера.<br/>
<br/>

<h4>Параметры заявки:</h4>
<table width="100%" cellspacing="1" cellpadding="1">
<thead>
<tr><td width="15%" style="background: #E0E0E0; font-weight: bold;">ID</td><td width="35%" style="background: #E0E0E0; font-weight: bold;">Заголовок</td><td style="background: #E0E0E0; font-weight: bold;">Содержимое</td></tr>
</thead>
<tbody>
{% for entry in entries %}
<tr style="background: #FFFFFF;" valign="top">
 <td style="background: #EFEFEF;" width="100">{{ entry.id }}</td>
 <td style="background: #EFEFEF;" width="200">{{ entry.title }}</td>
 <td style="background: #EFEFEF;">{{ entry.value }}</td>
</tr>
{% endfor %}
</tbody>
</table>

<br/>
<br/>
---<br/>
С уважением,<br/>
почтовый робот Вашего сайта
</div>

</body>
</html>

