<style type="text/css">
.xListEntry TD {
	padding: 5px 0 1px 5px;
	background-color: #ffffff;
	border-bottom: 1px solid #f0f0f0;
	font: normal 11px verdana, tahoma, sans-serif;
	color: #000;
	text-align: left;
}
.btnInactive {
	width: 180px;
	float: left;
	margin-top: 10px;
	margin-bottom: 10px;
	padding: 5px;
	padding-left: 25px;
	border: 1px solid #D0D0D0;
	cursor: pointer;
	background: #F0F0F0;
	background-position: 8px center;
	font: normal 11px verdana;
}
.btnInactive A { font: normal 11px verdana; text-decoration: none; }

.btnActive {
	width: 180px;
	float: left;
	margin-top: 10px;
	margin-bottom: 10px;
	padding: 5px;
	padding-left: 25px;
	border: 1px solid red;
	cursor: pointer;
	background: #FFFFFF url("/engine/skins/default/images/yes.png") no-repeat;
	background-position: 8px center;
}
.btnActive A { font: normal 11px verdana; text-decoration: none; }

.btnSeparator {	float: left;	width: 10px;	}
.btnDelimiter {	float: left;	width: 50px;	}
</style>

<table border="0" cellspacing="1" cellpadding="1" class="content">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />{l_config_text}: xfields</td>
</tr>
</table>
<div style="float: left;">
<span class="{bclass.news}" onclick='document.location="?mod=extra-config&plugin=xfields&section=news";'>Новости: список полей</span><span class="btnSeparator">&nbsp;</span>
<span class="{bclass.grp.news}" onclick='document.location="?mod=extra-config&plugin=xfields&section=grp.news";'>Новости: группы</span><span class="btnDelimiter">&nbsp;</span>
<!--
<span class="{bclass.users}" onclick='document.location="?mod=extra-config&plugin=xfields&section=users";'>Пользователи: список полей</span><span class="btnSeparator">&nbsp;</span>
<span class="{bclass.grp.users}">Пользователи: группы</span>
-->
</div>

<table width="100%">
<tr>
<td colspan="7" width=100% class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8">{l_xfields_list}: {section_name}</td>
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
<tr>
<td></td>
<td colspan="5" style="text-align: left; padding: 10px 10px 0 0;">
<div class="btnActive"><a href="?mod=extra-config&plugin=xfields&action=add&section={sectionID}">{l_xfields_add}</a></div>
</td>
</tr>
</table>
</form>