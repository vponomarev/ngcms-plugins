<style type="text/css">
.xListEntry TD {
	padding: 5px 0 1px 5px;
	background-color: #ffffff;
	border-bottom: 1px solid #f0f0f0;
	font: normal 11px verdana, tahoma, sans-serif;
	color: #555;
	text-align: left;
}
.contNav {
    padding: 10px 0 10px 10px;
    background: #eaf0f7 url({skins_url}/images/1px.png) repeat-x;
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
	background: #f6f8fb url("{skins_url}/images/no_plug.png") no-repeat;
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
	background: #FFFFFF url("{skins_url}/images/yes_plug.png") no-repeat;
	background-position: 8px center;
}



.btnActive A { font: normal 14px "Trebuchet MS", Arial, Helvetica, sans-serif normal; text-decoration: none; }

.btnSeparator {float: left; width: 10px;}
.btnDelimiter {float: left; width: 50px;}
</style>


<table border="0" cellpadding="0" cellspacing="0" width="100%">
<tbody>
<tr>
<td colspan="5" class="contentHead" width="100%"><img src="{skins_url}/images/nav.gif" hspace="8">{l_config_text}: xfields</td>
</tr>
</tbody>
</table>

<table border="0" cellpadding="0" cellspacing="0" width="100%">

<tbody><tr>
<td colspan="8" class="contNav" width="100%">
<div id="btnMenu">
<span class="{bclass.news}" onclick='document.location="?mod=extra-config&plugin=xfields&section=news";'>Новости: список полей</span><span class="btnSeparator">&nbsp;</span>
<span class="{bclass.grp.news}" onclick='document.location="?mod=extra-config&plugin=xfields&section=grp.news";'>Новости: группы</span><span class="btnDelimiter">&nbsp;</span>
<!--
<span class="{bclass.users}" onclick='document.location="?mod=extra-config&plugin=xfields&section=users";'>Пользователи: список полей</span><span class="btnSeparator">&nbsp;</span>
<span class="{bclass.grp.users}">Пользователи: группы</span>
-->
</div>
&nbsp;
</td>
</tr>
</tbody>
</table>

<table width="100%">
<tr>
<td colspan="7" width="100%" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8">{l_xfields_list}: {section_name}</td>
</tr>
<tr align="left">
<td class="contentHead"><b>ID поля</b></td>
<td class="contentHead"><b>Название поля</b></td>
<td class="contentHead"><b>Тип поля</b></td>
<td class="contentHead"><b>Возможные значения</b></td>
<td class="contentHead"><b>По умолчанию</b></td>
<td class="contentHead"><b>Обязательно</b></td>
<td class="contentHead">&nbsp;</td>
</tr>
{entries}
</table>
<table width="100%">
<tr>&nbsp;</tr>
<tr align="center">
<td class="contentEdit" valign="top" width="100%">
<input value="{l_xfields_add}" class="button" type="submit" onclick='document.location="?mod=extra-config&plugin=xfields&action=add&section={sectionID}";'>
</td>
</tr>
</table>

</form>