<form method="post" action="">
<tr>
	<td colspan=2>
		<fieldset class="admGroup">
		<legend class="title">Настройки &lt;title&gt;&lt;/title&gt;</legend>
			<table width="100%" border="0" class="content">
				<tr>
					<td class="contentEntry1" valign=top>Заголовок главной страницы форума<br /><small>Текст поля &lt;title&gt;&lt;/title&gt;(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{home_title.error}}<input name="home_title" type="text" title="Заголовок главной страницы форума" size=40 value="{{home_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице форума<br /><small>Текст поля &lt;title&gt;&lt;/title&gt;(разрешено %name_site%, %name_forum%, %cat_forum%, %num%)</small></td>
					<td class="contentEntry2" valign=top>{{forums_title.error}}<input name="forums_title" type="text" title="Заголовок на странице форума" size=40 value="{{forums_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице темы<br /><small>Текст поля &lt;title&gt;&lt;/title&gt;(разрешено %name_site%, %name_forum%, %cat_forum%, %name_topic%, %num%)</small></td>
					<td class="contentEntry2" valign=top>{{topic_title.error}}<input name="topic_title" type="text" title="Заголовок на странице темы" size=40 value="{{topic_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок списка пользователей<br /><small>Текст поля &lt;title&gt;&lt;/title&gt;(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{userlist_title.error}}<input name="userlist_title" type="text" title="Заголовок списка пользователей" size=40 value="{{userlist_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице поиска<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{search_title.error}}<input name="search_title" type="text" title="Заголовок на странице поиска" size=40 value="{{search_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице регистрации<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{register_title.error}}<input name="register_title" type="text" title="Заголовок на странице регистрации" size=40 value="{{register_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице входа<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{login_title.error}}<input name="login_title" type="text" title="Заголовок на странице входа" size=40 value="{{login_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице профиля<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{profile_title.error}}<input name="profile_title" type="text" title="Заголовок на странице профиля" size=40 value="{{profile_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице выхода<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%, %others%)</small></td>
					<td class="contentEntry2" valign=top>{{out_title.error}}<input name="out_title" type="text" title="Заголовок на странице поиска" size=40 value="{{out_title.print}}" /></td>
				</tr>
				<tr>
					<td class="contentEntry1" valign=top>Заголовок на странице сообщения<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{addreply_title.error}}<input name="addreply_title" type="text" title="Заголовок на странице сообщения" size=40 value="{{addreply_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице создания темы<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{newtopic_title.error}}<input name="newtopic_title" type="text" title="Заголовок на странице поиска" size=40 value="{{newtopic_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице удаления<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{delpost_title.error}}<input name="delpost_title" type="text" title="Заголовок на странице поиска" size=40 value="{{delpost_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице редактирования<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{edit_title.error}}<input name="edit_title" type="text" title="Заголовок на странице поиска" size=40 value="{{edit_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице правил форума<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{rules_title.error}}<input name="rules_title" type="text" title="Заголовок на странице поиска" size=40 value="{{rules_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице последних сообщений<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{show_new_title.error}}<input name="show_new_title" type="text" title="Заголовок на странице поиска" size=40 value="{{show_new_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице отметить все темы как прочитанные<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{markread_title.error}}<input name="markread_title" type="text" title="Заголовок на странице поиска" size=40 value="{{markread_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице репутации<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%, %others%)</small></td>
					<td class="contentEntry2" valign=top>{{rep_title.error}}<input name="rep_title" type="text" title="Заголовок на странице поиска" size=40 value="{{rep_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице добавления репутации<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{addr_title.error}}<input name="addr_title" type="text" title="Заголовок на странице поиска" size=40 value="{{addr_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице новости<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%, %name_news%)</small></td>
					<td class="contentEntry2" valign=top>{{news_title.error}}<input name="news_title" type="text" title="Заголовок на странице поиска" size=40 value="{{news_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок ленты новостей<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{news_feed_title.error}}<input name="news_feed_title" type="text" title="Заголовок на странице поиска" size=40 value="{{news_feed_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице ХЗ<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%, %others%)</small></td>
					<td class="contentEntry2" valign=top>{{act_title.error}}<input name="act_title" type="text" title="Заголовок на странице поиска" size=40 value="{{act_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице сказать спасибо<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%, %others%)</small></td>
					<td class="contentEntry2" valign=top>{{thank_title.error}}<input name="thank_title" type="text" title="Заголовок на странице поиска" size=40 value="{{thank_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице пожаловаться на сообщение<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%, %others%)</small></td>
					<td class="contentEntry2" valign=top>{{complaints_title.error}}<input name="complaints_title" type="text" title="Заголовок на странице пожаловаться на сообщение" size=40 value="{{complaints_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице добавлении личного сообщения<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{send_pm_title.error}}<input name="send_pm_title" type="text" title="Заголовок на странице поиска" size=40 value="{{send_pm_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице личных сообщений<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{list_pm_title.error}}<input name="list_pm_title" type="text" title="Заголовок на странице поиска" size=40 value="{{list_pm_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице удаления личного сообщения<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{del_pm_title.error}}<input name="del_pm_title" type="text" title="Заголовок на странице поиска" size=40 value="{{del_pm_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице закачки<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{downloads_title.error}}<input name="downloads_title" type="text" title="Заголовок на странице поиска" size=40 value="{{downloads_title.print}}" /></td>
				</tr><tr>
					<td class="contentEntry1" valign=top>Заголовок на странице инфрмации<br /><small>Текст поля &lt;title>&lt;/title>(разрешено %name_site%, %name_forum%)</small></td>
					<td class="contentEntry2" valign=top>{{erro404_title.error}}<input name="erro404_title" type="text" title="Заголовок на странице поиска" size=40 value="{{erro404_title.print}}" /></td>
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
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input name="submit" type="submit"  value="Сохранить" class="button" />
</td>
</tr>
</table>

</form>