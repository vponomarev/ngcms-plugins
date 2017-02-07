<form method="post" action="">
	<tr>
		<td colspan=2>
			<fieldset class="admGroup">
				<legend class="title">Настройки</legend>
				<table width="100%" border="0" class="content">
					<tr>
						<td class="contentEntry1" valign=top>Стили для админов<br/>
							<small></small>
						</td>
						<td class="contentEntry2" valign=top>
							{{ style_admin_start.error }}
							<input name="style_admin_start" type="text" size=40 value="{{ style_admin_start.print }}"/>

						</td>
						<td class="contentEntry1" valign=top>Стили для админов<br/>
							<small></small>
						</td>
						<td class="contentEntry2" valign=top>
							{{ style_admin_end.error }}
							<input name="style_admin_end" type="text" size=40 value="{{ style_admin_end.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Заголовок в полной новости<br/>
							<small>Текст поля &lt;title&gt;&lt;/title&gt; в полной новости (разрешено %cat%, %title%,
								%home%, %num%)
							</small>
						</td>
						<td class="contentEntry2" valign=top>{{ n_title.error }}
							<input name="n_title" type="text" title="Заголовок в полной новости" size=40 value="{{ n_title.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Заголовок главной страницы<br/>
							<small>Текст поля &lt;title&gt;&lt;/title&gt; главной страницы (разрешено %home% %num%)
							</small>
						</td>
						<td class="contentEntry2" valign=top>{{ m_title.error }}
							<input name="m_title" type="text" title="Заголовок главной страницы" size=40 value="{{ m_title.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Заголовок статической страницы<br/>
							<small>Текст поля &lt;title&gt;&lt;/title&gt; статической страницы (разрешено %home% и
								%static%)
							</small>
						</td>
						<td class="contentEntry2" valign=top>{{ static_title.error }}
							<input name="static_title" type="text" title="Заголовок статической страницы" size=40 value="{{ static_title.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Заголовок остальных страницы<br/>
							<small>Текст поля &lt;title>&lt;/title> других страниц (профиль пользователя, личный
								профиль) (разрешено %home%, %other%, %html% и %num%)
							</small>
						</td>
						<td class="contentEntry2" valign=top>{{ o_title.error }}
							<input name="o_title" type="text" title="Заголовок остальных страницы" size=40 value="{{ o_title.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Дополнительная информация для страницы<br/>
							<small>Вывод дополнительной информацию о странице (прим. имя тега) - данных передадутся в
								переменную %html%
							</small>
						</td>
						<td class="contentEntry2" valign=top>{{ html_secure.error }}
							<input name="html_secure" type="text" title="Дополнительная информация для страницы" size=40 value="{{ html_secure.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Плагины исключения<br/>
							<small>Список плагинов на которых работа плагина не распространяется</small>
						</td>
						<td class="contentEntry2" valign=top>{{ p_title.error }}
							<input name="p_title" type="text" title="Список плагинов на которых работа плагина не распространяется" size=40 value="{{ p_title.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Номер страницы<br/>
							<small>Форматирование номера страницы (например, Страница 4 [Страница %count%] - где %count%
								номер страницы) - данных передадутся в переменную %num%
							</small>
						</td>
						<td class="contentEntry2" valign=top>{{ num_title.error }}
							<input name="num_title" type="text" title="Номер страницы" size=40 value="{{ num_title.print }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top><br/>
							<small>Ключи:<br/><b>%cat%</b> - имя категории<br/><b>%title%</b> - имя
								новости<br><b>%home%</b> - заголовок сайта<br/><b>%static%</b> - заголовок статической
								страницы<br/><b>%other%</b> - заголовок любой другой страницы<br></small>
						</td>
						<td class="contentEntry2" valign=top></td>
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input name="submit" type="submit" value="Сохранить" class="button"/>
			</td>
		</tr>
	</table>

</form>