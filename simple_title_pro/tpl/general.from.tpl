<form method="post" action="">
<tr>
	<td colspan=2>
		<fieldset class="admGroup">
		<legend class="title">Настройки админки</legend>
		<table width="100%" border="0" class="content">
			<tr>
				<td class="contentEntry1" valign=top>Количество записей в категории<br /><small></small></td>
				<td class="contentEntry2" valign=top>{{num_cat.error}}<input name="num_cat" type="text" title="Количество записей в категории" size=40 value="{{num_cat.print}}" /></td>
			</tr>
			<tr>
				<td class="contentEntry1" valign=top>Количество записей в новостях<br /><small></small></td>
				<td class="contentEntry2" valign=top>{{num_news.error}}<input name="num_news" type="text" title="Количество записей в новостях" size=40 value="{{num_news.print}}" /></td>
			</tr>
			<tr>
				<td class="contentEntry1" valign=top>Количество записей в статике<br /><small></small></td>
				<td class="contentEntry2" valign=top>{{num_static.error}}<input name="num_static" type="text" title="Количество записей в статике" size=40 value="{{num_static.print}}" /></td>
			</tr>
		</table>
		</fieldset>
	</td>
</tr>
<tr>
	<td colspan=2>
		<fieldset class="admGroup">
		<legend class="title">Настройки &lt;title&gt;&lt;/title&gt;</legend>
			<table width="100%" border="0" class="content">
				<tr>
					<td class="contentEntry1" valign=top>Заголовок в категории <br /><small>Текст поля &lt;title&gt;&lt;/title&gt; для категории (разрешено %cat%, %num% и %home%)</small></td>
					<td class="contentEntry2" valign=top>{{c_title.error}}<input name="c_title" type="text" title="Заголовок в категории" size=40 value="{{c_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок в полной новости<br /><small>Текст поля &lt;title&gt;&lt;/title&gt; в полной новости (разрешено %cat%, %title%, %home%, %num%)</small></td>
					<td class="contentEntry2" valign=top>{{n_title.error}}<input name="n_title" type="text" title="Заголовок в полной новости" size=40 value="{{n_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок главной страницы<br /><small>Текст поля &lt;title&gt;&lt;/title&gt; главной страницы (разрешено %home% %num%)</small></td>
					<td class="contentEntry2" valign=top>{{m_title.error}}<input name="m_title" type="text" title="Заголовок главной страницы" size=40 value="{{m_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок статической страницы<br /><small>Текст поля &lt;title&gt;&lt;/title&gt; статической страницы (разрешено %home% и %static%)</small></td>
					<td class="contentEntry2" valign=top>{{static_title.error}}<input name="static_title" type="text" title="Заголовок статической страницы" size=40 value="{{static_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок остальных страницы<br /><small>Текст поля &lt;title>&lt;/title> других страниц (профиль пользователя, личный профиль) (разрешено %home%, %other%, %html% и %num%)</small></td>
					<td class="contentEntry2" valign=top>{{o_title.error}}<input name="o_title" type="text" title="Заголовок остальных страницы" size=40 value="{{o_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Дополнительная информация для страницы<br /><small>Вывод дополнительной информацию о странице (прим. имя тега)  - данных передадутся в переменную %html%</small></td>
					<td class="contentEntry2" valign=top>{{html_secure.error}}<input name="html_secure" type="text" title="Дополнительная информация для страницы" size=40 value="{{html_secure.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Страница ошибки 404<br /><small>Вывод дополнительной информацию о странице (прим. имя тега)  - данных передадутся в переменную %html%</small></td>
					<td class="contentEntry2" valign=top>{{e_title.error}}<input name="e_title" type="text" title="Дополнительная информация для страницы" size=40 value="{{e_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Плагины исключения<br /><small>Список плагинов на которых работа плагина не распространяется</small></td>
					<td class="contentEntry2" valign=top>{{p_title.error}}<input name="p_title" type="text" title="Список плагинов на которых работа плагина не распространяется" size=40 value="{{p_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Номер страницы<br /><small>Форматирование номера страницы (например, Страница 4 [Страница %count%] - где %count% номер страницы) - данных передадутся в переменную %num%</small></td>
					<td class="contentEntry2" valign=top>{{num_title.error}}<input name="num_title" type="text" title="Номер страницы" size=40 value="{{num_title.print}}" /></td>
				</tr>
				<tr>
					<td class="contentEntry1" valign=top><br /><small>Ключи:<br /><b>%cat%</b> - имя категории<br /><b>%title%</b> - имя новости<br><b>%home%</b> - заголовок сайта<br /><b>%static%</b> - заголовок статической страницы<br /><b>%other%</b> - заголовок любой другой страницы<br></small></td>
					<td class="contentEntry2" valign=top></td>
				</tr>
			</table>
		</fieldset>
	</td>
</tr>
<tr>
	<td colspan=2>
		<fieldset class="admGroup">
		<legend class="title">Настройка кэша</legend>
		<table width="100%" border="0" class="content">
			<tr>
				<td class="contentEntry1" valign=top>Время жизни кэша<br /><small>Указывать в днях</small></td>
				<td class="contentEntry2" valign=top>{{cache.error}}<input name="cache" type="text" title="Время жизни кэша" size=40 value="{{cache.print}}" /></td>
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