<form method="post" action="">
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">Общие настройки</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>Кол-во пользователей для отображения на одной странице<br /></td>
<td class="contentEntry2" valign=top><input name="user_per_page" type="text" title="Кол-во пользователей для отображения на одной странице" size="5" value="{user_per_page}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Title для страницы плагина<br /><small></small></td>
<td class="contentEntry2" valign=top><input name="title_plg" type="text" title="Описание" size="50" value="{title_plg}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Описание для страницы плагина<br /><small></small></td>
<td class="contentEntry2" valign=top><input name="description" type="text" title="Описание" size="50" value="{description}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Ключевые слова для страницы плагина<br /><small></small></td>
<td class="contentEntry2" valign=top><input name="keywords" type="text" title="Ключевые слова" size="50" value="{keywords}" /></td>
</tr>
</table>
</fieldset>
<fieldset class="admGroup">
<legend class="title">Настройки отображения</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>Выберите каталог из которого плагин будет брать шаблоны для отображения<br /><small><b>Шаблон сайта</b> - плагин будет пытаться взять шаблоны из общего шаблона сайта; в случае недоступности - шаблоны будут взяты из собственного каталога плагина<br /><b>Плагин</b> - шаблоны будут браться из собственного каталога плагина</small><br /></td>
<td class="contentEntry2" valign=top><select name="localsource" >{localsource}</select></td>
</tr>

</table>
</fieldset>
</td>
</tr>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input name="submit" type="submit"  value="Сохранить" class="button" />
</td>
</tr>
</table>
</form>