<tr>
	<td>
		<table border="0" width="100%" cellspacing="0" cellpadding="0">
			<tr>
				<td>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<img border="0" src="{tpl_url}/images/2z_35.gif" width="7" height="36"/></td>
							<td style="background-image:url('{tpl_url}/images/2z_36.gif');" width="100%">
								&nbsp;<font color="#FFFFFF"><b>Кто онлайн</b></font></td>
							<td>
								<img border="0" src="{tpl_url}/images/2z_38.gif" width="7" height="36"/></td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td style="background-image:url('{tpl_url}/images/2z_56.gif');" width="7">&nbsp;</td>
							<td bgcolor="#FFFFFF">
								<b>Всего на сайте: {{ all }} </b><br/>
								- Анонимов: {{ num_guest }}<br/>
								- Авторизированных: {{ num_auth }}<br/>
								<i>-- Команда сайта:</i> {{ num_team }}<br/>
								<i>-- Пользователи:</i> {{ num_users }}<br/>
								- Поисковых роботов: {{ num_bot }}
								{% if (entries_team.true) %}
									<br/><br/>
									{{ entries_team.print }}
								{% endif %}
								{% if (entries_user.true) %}
									<br/><br/>
									{{ entries_user.print }}
								{% endif %}
								{% if (today.true) %}
									<br/><br/>
									{{ today.print }}
								{% endif %}
								{% if (entries_bot.true) %}
									<br/><br/>
									{{ entries_bot.print }}
								{% endif %}
							</td>
							<td style="background-image:url('{tpl_url}/images/2z_58.gif');" width="7">&nbsp;</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td>
					<table border="0" width="100%" cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<img border="0" src="{tpl_url}/images/2z_60.gif" width="7" height="11"/></td>
							<td style="background-image:url('{tpl_url}/images/2z_61.gif');" width="100%"></td>
							<td>
								<img border="0" src="{tpl_url}/images/2z_62.gif" width="7" height="11"/></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
	</td>
</tr>