{error}
<form method="post" action="">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">Имя<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="cat_name" value="{cat_name}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Описание<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="description" value="{description}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Ключевые слова<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="keywords" value="{keywords}" /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Родительская категория<br /><small></small></td>
<td width="50%" class="contentEntry2">
<select name="parent">
    <option value="0">Выберите категорию</option>
	{catz}
</select>

</tr>
<!--
<tr>
<td width="50%" class="contentEntry1">Позиция<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="80" name="position" value="{position}" /></td>
</tr>
-->
</table>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="submit" value="Добавить категорию" class="button" />
</td>
</tr>
</table>
</form>
