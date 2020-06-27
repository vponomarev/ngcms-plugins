<!-- List of news start here -->
<form action="/engine/admin.php?mod=extra-config&plugin=zboard&action=modify" method="post" name="zboard">
<table border="0" cellspacing="0" cellpadding="0" class="content" align="center" width="100%">
<tr align="left" class="contHead">
<td width="5%" nowrap>ID</td>
<td width="10%" nowrap>Дата</td>
<td width="10%" nowrap>Категория</td>
<td>Заголовок</td>
<td width="10%">Автор</td>
<td width="20%">Период</td>
<td width="5%">Активен?</td>
<td width="5%"><input class="check" type="checkbox" name="master_box" title="Выбрать все" onclick="javascript:check_uncheck_all(zboard)" /></td>
</tr>
{entries}
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>

<tr align="center">
<td colspan="8" class="contentEdit" align="right" valign="top">
<div style="text-align: left;">
Действие: <select name="subaction" style="font: 12px Verdana, Courier, Arial; width: 230px;">
<option value="">-- Действие --</option>
<option value="mass_approve">Активировать</option>
<option value="mass_forbidden">Деактивировать</option>
<option value="" style="background-color: #E0E0E0;" disabled="disabled">===================</option><option value="mass_delete">Удалить объявление</option>
</select>
<input type="submit" value="Выполнить.." class="button" />
<br/>
</div>
</td>
</tr>
<tr>
<td width="100%" colspan="8">&nbsp;</td>
</tr>
<tr>
<td align="center" colspan="8" class="contentHead">{pagesss}</td>
</tr>
</table>
</form>