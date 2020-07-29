<div style="text-align : left;">
<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" /><a href="admin.php?mod=extras">Управление плагинами</a></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />Настройка плагина: re_stat</td>
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
<form method="post" action="admin.php?mod=extra-config&amp;plugin=re_stat">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="100%" colspan="2" class="contentHead"><img src="{skins_url}/images/nav.gif" hspace="8" alt="" />[add]
Добавить[/add][edit]Редакрировать[/edit] элемент списка</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Код</td>
<td width="50%" class="contentEntry2"><input type="text" size="40" name="code" value="{code}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Статическая страница</td>
<td width="50%" class="contentEntry2">{statlist}</td>
</tr>
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" value="[add]Добавить[/add][edit]Редакрировать[/edit] элемент списка" class="button" />
<input type="hidden" name="action" value="confirm" />
<input type="hidden" name="id" value="{id}" />
</td>
</tr>
</table>
</form>
</div>