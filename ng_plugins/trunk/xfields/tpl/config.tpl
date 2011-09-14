<style type="text/css">
.xListEntry TD {
	padding: 5px 0 1px 5px;
	background-color: #ffffff;
	border-bottom: 1px solid #f0f0f0;
	font: normal 11px verdana, tahoma, sans-serif;
	color: #555;
	text-align: left;
}
.xListEntryDisabled TD {
	padding: 5px 0 1px 5px;
	background-color: #DDDDDD;
	border-bottom: 1px solid #f0f0f0;
	font: normal 11px verdana, tahoma, sans-serif;
	color: #555555;
	text-align: left;
}

.contNav {
    padding: 10px 0 10px 10px;
    background: #eaf0f7 url({{ skins_url }}/images/1px.png) repeat-x;
    color: #152F59; font-family:"Trebuchet MS", Arial, Helvetica, sans-serif; font-size:13px;
    border-top: 1px solid #dfe5ec;
    border-bottom: 3px solid #dfe5ec; margin-top: 10px;
}
.btnMenu {
	font: 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal;
	float: left;
	color: #555;
}
.btnInactive {
	width: 170px;
	float: left;
	margin-top: 5px;
	margin-bottom: 5px;
	padding: 7px;
	padding-left: 35px;
	border: 1px solid #dbe4ed;
	cursor: pointer;
	background: #f6f8fb url("{{ skins_url }}/images/no_plug.png") no-repeat;
	background-position: 8px center;
}
.btnInactive A { font: normal 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal; text-decoration: none; }

.btnActive {
	width: 170px;
	float: left;
	margin-top: 5px;
	margin-bottom: 5px;
	padding: 7px;
	padding-left: 35px;
	border: 1px solid #54a1c1;
	cursor: pointer;
	background: #FFFFFF url("{{ skins_url }}/images/yes_plug.png") no-repeat;
	background-position: 8px center;
}



.btnActive A { font: normal 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal; text-decoration: none; }

.btnSeparator {float: left; width: 10px;}
.btnDelimiter {float: left; width: 50px;}
</style>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr>
<td colspan="5" class="contentHead" width="100%"><img src="{{ skins_url }}/images/nav.gif" hspace="8"><a href="?mod=extras" title="Управление плагинами">Управление плагинами</a> &#8594; <a href="?mod=extra-config&plugin=xfields">xfields</a></td>
</tr>
</tbody>
</table>
{% include 'plugins/xfields/tpl/navi.tpl' %}

<table width="100%">
<tr>
<td colspan="8" width="100%" class="contentHead"><img src="{{ skins_url }}/images/nav.gif" hspace="8">{{ lang.xfconfig['list'] }}: {{ section_name }}</td>
</tr>
<tr align="left">
<td class="contentHead"><b>ID поля</b></td>
<td class="contentHead"><b>Название поля</b></td>
<td class="contentHead"><b>Тип поля</b></td>
<td class="contentHead"><b>Возможные значения</b></td>
<td class="contentHead"><b>По умолчанию</b></td>
<td class="contentHead"><b>Обязательно</b></td>
{% if (sectionID != 'tdata') %}<td class="contentHead"><b>Блок</b></td>{% endif %}
<td class="contentHead">&nbsp;</td>
</tr>
{% for entry in entries %}
<tr align="left" class="xListEntry{% if (entry.flags.disabled) %}Disabled{% endif %}">
	<td style="padding:2px;"><a href="{{ entry.linkup }}"><img src="{{ skins_url }}/images/up.gif" width="16" height="16" alt="UP" /></a><a href="{{ entry.linkdown }}"><img src="{{ skins_url }}/images/down.gif" width="16" height="16" alt="DOWN" /></a> <a href="{{ entry.link }}">{{ entry.name }}</a></td>
	<td>{{ entry.title }}</td>
	<td>{{ entry.type }}</td>
	<td>{{ entry.options }}</td>
	<td>{% if (entry.flags.default) %}{{ entry.default }}{% else %}<font color="red">не задано</font>{% endif %}</td>
	<td>{% if (entry.flags.required) %}<font color="red"><b>Да</b></font>{% else %}Нет{% endif %}</td>
	{% if (sectionID != 'tdata') %}<td>{{ entry.area }}</td>{% endif %}
	<td nowrap><a href="{{ entry.linkdel }}" onclick="return confirm('{{ lang.xfconfig['suretest'] }}');"><img src="{{ skins_url }}/images/delete.gif" alt="DEL" width="12" height="12" /></a></td>
</tr>
{% endfor %}
</table>
<table width="100%">
<tr>&nbsp;</tr>
<tr align="center">
<td class="contentEdit" valign="top" width="100%">
<input value="{{ lang.xfconfig['add'] }}" class="button" type="submit" onclick='document.location="?mod=extra-config&plugin=xfields&action=add&section={{ sectionID }}";'>
</td>
</tr>
</table>
</form>