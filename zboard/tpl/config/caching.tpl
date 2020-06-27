<form method="post" action="" name="form">
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">Настройки кэша</legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">Кэш включен?<br /><small></small></td>
<td width="50%" class="contentEntry2"><select name="cache">
{cache}
</select>
</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Время жизни кэша<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="10" name="time" value="{time}"  /></td>
</tr>
</table>
</fieldset>
</td>
</tr>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="submit" value="Отредактировать" class="button" />
</td>
</tr>
</table>
</form>

<form method="post" action="" name="form">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="clear_cache" value="Очистить кэш" class="button" />
</td>
</tr>
</table>
</form>
