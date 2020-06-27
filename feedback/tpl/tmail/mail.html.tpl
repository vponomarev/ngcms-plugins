<html>
<body>
<div style="font: normal 11px verdana, sans-serif;">
<h3>РќРѕРІР°СЏ Р·Р°СЏРІРєР°!</h3>
Р’Р°Рј РїРѕСЃС‚СѓРїРёР»Р° РЅРѕРІР°СЏ Р·Р°СЏРІРєР° РЅР° РІС‹Р·РѕРІ РєСѓСЂСЊРµСЂР°.<br/>
<br/>

<table cellspacing="1" cellpadding="1" width="600">
<thead>
<tr><td width="150" style="background: #E0E0E0; font-weight: bold;">Р—Р°РіРѕР»РѕРІРѕРє</td><td style="background: #E0E0E0; font-weight: bold;">РЎРѕРґРµСЂР¶РёРјРѕРµ</td></tr>
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

