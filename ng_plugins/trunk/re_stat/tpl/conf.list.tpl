<div style="text-align : left;">

<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td width="100%" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" /><a href="admin.php?mod=extras">Управление плагинами</a></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td width="100%" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />Настройка плагина: re_stat</td>
</tr>
<tr><td>&nbsp;</td></tr>
</table>

<table border="0" cellspacing="0" cellpadding="0" width="100%">
<tr align="center">
<td width="100%" class="contentNav" align="center" valign="top">
<form action="admin.php?mod=extra-config&amp;plugin=re_stat" method="post" name="options_bar">
<input type="hidden" name="action" value="" />
<input type="hidden" name="id" value="-1" />
<input type="submit" value="Список" class="navbutton" onClick="document.forms['options_bar'].action.value = '';" />
<input type="submit" value="Добавить" class="navbutton" onClick="document.forms['options_bar'].action.value = 'add';" />
</form>
</td>
</tr>
</table>
<br/>

<table width="97%" class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr align="center" class="contHead">
<td>№п.п.</td>
<td>Код</td>
<td>Статическая страница</td>
<td width="160">Действие</td>
</tr>
{entries}
<tr><td width="100%" colspan="4">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="4" class="contentEdit" align="center">
<form action="admin.php?mod=extra-config&amp;plugin=re_stat" method="post" name="options_bar_bottom">
<input type="hidden" name="action" value="re_map" />
<input type="submit" value="Перестроить карту ссылок" class="navbutton" />
</form>
</td>
</tr>
</table>
</div>