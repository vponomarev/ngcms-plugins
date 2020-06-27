<form method="post" action="admin.php?mod=extra-config&plugin=zboard">
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">Настройки</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>Разрешить гостям добавлять объявления?<br /></td>
<td class="contentEntry2" valign=top><select name="send_guest" >{send_guest}</select></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Количество объявлений на странице<br /></td>
<td class="contentEntry2" valign=top><input name="count" type="text" title="Количество объявлений на странице" size="4" value="{count}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Количество объявлений на странице пользвателя<br /></td>
<td class="contentEntry2" valign=top><input name="count_list" type="text" title="Количество объявлений на странице пользвателя" size="4" value="{count_list}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Количество объявлений на странице поиска<br /></td>
<td class="contentEntry2" valign=top><input name="count_search" type="text" title="Количество объявлений на странице поиска" size="4" value="{count_search}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Описание для главной объявлений<br /><small></small></td>
<td class="contentEntry2" valign=top><input name="description" type="text" title="Описание для объявлений" size="50" value="{description}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Ключевые слова для главной объявлений<br /><small></small></td>
<td class="contentEntry2" valign=top><input name="keywords" type="text" title="Ключевые слова для объявлений" size="50" value="{keywords}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Информация, выводимая после добавления<br /><small></small></td>
<td class="contentEntry2" valign=top><textarea name="info_send" title="Информация, выводимая после добавления" rows=8 cols=100>{info_send}</textarea></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Информация выводимая после редактирования<br /><small></small></td>
<td class="contentEntry2" valign=top><textarea name="info_edit" title="Информация выводимая после редактирования" rows=8 cols=100>{info_edit}</textarea></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Включить снятие объявления по истечении срока?<br /></td>
<td class="contentEntry2" valign=top><select name="use_expired" >{use_expired}</select></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Время жизни объявления<br /></td>
<td class="contentEntry2" valign=top><input name="list_period" type="text" title="Время жизни объявления" size="10" value="{list_period}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Учёт просмотра объявлений?<br /></td>
<td class="contentEntry2" valign=top><select name="views_count" >{views_count}</select></td>
</tr>
<!--
<tr>
<td class="contentEntry1" valign=top>Категория, которая будет активна перейдя на главную страницу объявлений<br /></td>
<td class="contentEntry2" valign=top><select name="cat_id" >{cat_id}</select></td>
</tr>
-->
<tr>
<td class="contentEntry1" valign=top>Уведомлять админа о новых объявлениях?<br /></td>
<td class="contentEntry2" valign=top><select name="notice_mail" >{notice_mail}</select></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Шаблон уведомлений по почте<br /><small>Доступные теги:<br />%announce_name% - <br />%author% - <br />%announce_description% - <br />%announce_period% - <br />%announce_contacts% - <br />%date% -</small></td>
<td class="contentEntry2" valign=top><textarea name="template_mail" title="Шаблон уведомлений по почте" rows=8 cols=100>{template_mail}</textarea></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Главный шаблон для объявлений<br /><small>Если пусто то берется основной шаблон <b>main.tpl</b><br />Пример: Создать template.tpl в ngcms/www/templates/default/ и добавить в это поле только название <b>template</b> без разшерения</small></td>
<td class="contentEntry2" valign=top><input name="main_template" type="text" title="Главный шаблон для объявлений" size="20" value="{main_template}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Ширина уменьшенной копии<br /></td>
<td class="contentEntry2" valign=top><input name="width_thumb" type="text" title="Ширина уменьшенной копии" size="20" value="{width_thumb}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Разрешенные расширения для изображений<br /><small>Формат записи <b>*.jpg;*.jpeg;*.gif;*.png</b></small></td>
<td class="contentEntry2" valign=top><input name="ext_image" type="text" title="Разрешенные разширения для изображений" size="50" value="{ext_image}" /></td>
</tr>
<!--
<tr>
<td class="contentEntry1" valign=top>Максимальный размер загружаемого изображения<br /><small>Размер в мегабайтах</small></td>
<td class="contentEntry2" valign=top><input name="max_image_size" type="text" title="Максимальный размер загружаемого изображения" size="20" value="{max_image_size}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Максимальный ширина загружаемого изображения<br /><small>Указывается в пикселях</small></td>
<td class="contentEntry2" valign=top><input name="width" type="text" title="Максимальный ширина загружаемого изображения" size="20" value="{width}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Максимальный высота загружаемого изображения<br /><small>Указывается в пикселях</small></td>
<td class="contentEntry2" valign=top><input name="height" type="text" title="Максимальный высота загружаемого изображения" size="20" value="{height}" /></td>
</tr>
-->
</table>
</fieldset>
</td>
</tr>
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">Настройки reCaptcha</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>Использовать reCaptcha?<br /></td>
<td class="contentEntry2" valign=top><select name="use_recaptcha" >{use_recaptcha}</select></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Public Key<br /></td>
<td class="contentEntry2" valign=top><input name="public_key" type="text" title="Public Key" size="50" value="{public_key}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Private Key<br /></td>
<td class="contentEntry2" valign=top><input name="private_key" type="text" title="Private Key" size="50" value="{private_key}" /></td>
</tr>
</table>
</fieldset>
</td>
</tr>
<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">Настройки админки</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>Количество объявлений на странице<br /></td>
<td class="contentEntry2" valign=top><input name="admin_count" type="text" title="Количество объявлений на странице" size="4" value="{admin_count}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Формат даты<br /></td>
<td class="contentEntry2" valign=top><input name="date" type="text" title="Формат даты" size="10" value="{date}" /></td>
</tr>
</table>
</fieldset>
</td>
</tr>

<tr>
<td colspan=2>
<fieldset class="admGroup">
<legend class="title">Настройки Pay2Pay</legend>
<table width="100%" border="0" class="content">
<tr>
<td class="contentEntry1" valign=top>Идентификатор магазина в Pay2Pay<br /></td>
<td class="contentEntry2" valign=top><input name="pay2pay_merchant_id" type="text" title="Идентификатор магазина в Pay2Pay" size="100" value="{pay2pay_merchant_id}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Секретный ключ<br /></td>
<td class="contentEntry2" valign=top><input name="pay2pay_secret_key" type="text" title="Секретный ключ" size="100" value="{pay2pay_secret_key}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Скрытый ключ<br /></td>
<td class="contentEntry2" valign=top><input name="pay2pay_hidden_key" type="text" title="Скрытый ключ" size="100" value="{pay2pay_hidden_key}" /></td>
</tr>
<tr>
<td class="contentEntry1" valign=top>Тестовый режим?<br /></td>
<td class="contentEntry2" valign=top><select name="pay2pay_test_mode" >{pay2pay_test_mode}</select></td>
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
