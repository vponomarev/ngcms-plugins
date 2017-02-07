<form method="post" action="">
	<tr>
		<td colspan=2>
			<fieldset class="admGroup">
				<legend class="title"><b>Настройки форума</b></legend>
				<table width="100%" border="0" class="content">
					<tr>
						<td class="contentEntry1" valign=top>Выберите каталог из которого плагин будет брать шаблоны для
							отображения<br/></td>
						<td class="contentEntry2" valign=top>{{ localsource }}</td>

					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Выберите вид отображения форума<br/></td>
						<td class="contentEntry2" valign=top>{{ display_main }}</td>

					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Время для редиректа<br/>
							<small>Устанавливать в секундах...</small>
						</td>
						<td class="contentEntry2" valign=top>
							<input name="redirect_time" type="text" title="Время для редиректа" size="4" value="{{ redirect_time }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Время в течении которого пользователь считается в сети<br/>
							<small>Указывать в секундах</small>
						</td>
						<td class="contentEntry2" valign=top>
							<input name="online_time" type="text" title="Время в течении которого пользователь считается в сети" size="10" value="{{ online_time }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Включить кто оналайн<br/></td>

						<td class="contentEntry2" valign=top>{{ online }}</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Титл форума<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="forum_title" type="text" title="Титл форума" value="{{ forum_title }}"/></td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Описание форума<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="forum_description" type="text" title="Описание форума" value="{{ forum_description }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Ключевые слова<br/></td>

						<td class="contentEntry2" valign=top>
							<input name="forum_keywords" type="text" title="Ключевые слова" value="{{ forum_keywords }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Выберите активный шаблон<br/>
							<small>Выбранный скин будет использоваться при установке <b>Плагин</b> в предыдущем поле
							</small>
							<br/></td>
						<td class="contentEntry2" valign=top>{{ localskin }}</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Укажите время в течении которого можно редактировать и
							удалять сообщения<br/>
							<small></small>
							<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="edit_del_time" type="text" title="Укажите время в течении которого можно редактировать и удалять сообщения" value="{{ edit_del_time }}"/>
						</td>
					</tr>
				</table>
			</fieldset>
		</td>
	</tr>
	<tr>
		<td colspan=2>
			<fieldset class="admGroup">
				<legend class="title"><b>Постраничная навигация</b></legend>
				<table width="100%" border="0" class="content">
					<tr>
						<td class="contentEntry1" valign=top>Количество сообщений в теме<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="topic_per_page" type="text" title="Количество сообщений в теме" size="4" value="{{ topic_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество найденых тем на одной странице<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="search_per_page" type="text" title="Количество найденых тем на одной странице" size="4" value="{{ search_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество пользователей на странице<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="user_per_page" type="text" title="Количество пользователей на странице" size="4" value="{{ user_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество тем в разделе форума<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="forum_per_page" type="text" title="Количество тем в разделе форума" size="4" value="{{ forum_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество сообщений в разделе репутации<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="reput_per_page" type="text" title="Количество тем в разделе форума" size="4" value="{{ reput_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество сообщений для разных страниц форума<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="act_per_page" type="text" title="Количество тем в разделе форума" size="4" value="{{ act_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество спасибо на странице<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="thank_per_page" type="text" title="Количество тем в разделе форума" size="4" value="{{ thank_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество сообщений на страницк ответа<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="newpost_per_page" type="text" title="Количество тем в разделе форума" size="4" value="{{ newpost_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество новостей<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="news_per_page" type="text" title="Количество новостей" size="4" value="{{ news_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество rss<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="rss_per_page" type="text" title="Количество новостей" size="4" value="{{ rss_per_page }}"/>
						</td>
					</tr>
					<tr>
						<td class="contentEntry1" valign=top>Количество сообщений<br/></td>
						<td class="contentEntry2" valign=top>
							<input name="list_pm_per_page" type="text" title="Количество новостей" size="4" value="{{ list_pm_per_page }}"/>
						</td>
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