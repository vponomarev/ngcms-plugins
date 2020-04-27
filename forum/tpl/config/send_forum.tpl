<form method="post" action="">
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		{% for entry in list_error %}
			{{ entry }}
		{% endfor %}
		<tr>
			<td width="50%" class="contentEntry1">Отображение<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<select size=1 disabled>
					<option value="{{ forum_id }}">{{ forum_title }}</option>
				</select>
			</td>

		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Название форума:<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<input type="text" size="80" name="forum_name" value="{{ forum_name }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Описание форума<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<textarea name="forum_description" cols="77" rows="4"/>{{ forum_description }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Ключевые слова<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<textarea name="forum_keywords" cols="77" rows="4"/>{{ forum_keywords }}</textarea></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Закрыть на пароль<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<input type="password" size="80" name="forum_lock_passwd" value="{{ forum_lock_passwd }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Редирект<br/>
				<small></small>
			</td>
			<td width="50%" class="contentEntry2">
				<input type="password" size="80" name="forum_redirect_url" value="{{ forum_redirect_url }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Модераторы<br/>
				<small>Укажите логины пользователей через запятую</small>
			</td>
			<td width="50%" class="contentEntry2">
				<input type="text" size="80" name="forum_moderators" value="{{ forum_moderators }}"/></td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Создавать темы</td>
			<td width="50%" class="contentEntry2">{{ m_topic_send }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Редактировать тему</td>
			<td width="50%" class="contentEntry2">{{ m_topic_modify }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Закрывать тему</td>
			<td width="50%" class="contentEntry2">{{ m_topic_closed }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Удалять тему</td>
			<td width="50%" class="contentEntry2">{{ m_topic_remove }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Отвечать в темах</td>
			<td width="50%" class="contentEntry2">{{ m_post_send }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Редактировать сообщения</td>
			<td width="50%" class="contentEntry2">{{ m_post_modify }}</td>
		</tr>
		<tr>
			<td width="50%" class="contentEntry1">Удалять сообщения</td>
			<td width="50%" class="contentEntry2">{{ m_post_remove }}</td>
		</tr>
	</table>
	<div id="userTabs">
		<ul>
			{% for entry in list_group %}
				<li><a href="#userTabs-{{ entry.group_id }}">{{ entry.group_name }}</a></li>
			{% endfor %}
		</ul>
		{% for entry in list_group %}
			<div id="userTabs-{{ entry.group_id }}">
				<div><i>Управление правами группы пользователей: <b>{{ entry.group_name }}</b></i></div>
				<br/>
				<div class="pconf">
					<h1></h1>

					<h2>Настройка прав</h2>

					<table width="100%" class="content">
						<thead>
						<tr class="contHead">
							<td><b>Действие</b></td>
							<td><b>Описание</b></td>
							<td width="90"><b>Доступ</b></td>
							</td>
						</thead>
						<tr class="contentEntry1">
							<td><strong>Просматривать форум</strong></td>
							<td>-</td>
							<td>
								{{ entry.forum_read }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Просматривать темы</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_read }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Добавлять темы</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_send }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Редактирова темы</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_modify }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Редактировать свои темы</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_modify_your }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Закрывать тему</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_closed }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Закрывать свою тему</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_closed_your }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Удалять темы</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_remove }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Удалять свои темы</strong></td>
							<td>-</td>
							<td>
								{{ entry.topic_remove_your }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Добавлять посты</strong></td>
							<td>-</td>
							<td>
								{{ entry.post_send }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Редактировать посты</strong></td>
							<td>-</td>
							<td>
								{{ entry.post_modify }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Редактировать свои посты</strong></td>
							<td>-</td>
							<td>
								{{ entry.post_modify_your }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Удалять посты</strong></td>
							<td>-</td>
							<td>
								{{ entry.post_remove }}
							</td>
						</tr>
						<tr class="contentEntry1">
							<td><strong>Удалять свои посты</strong></td>
							<td>-</td>
							<td>
								{{ entry.post_remove_your }}
							</td>
						</tr>
					</table>
					<br/>
				</div>
			</div>
		{% endfor %}
	</div>
	<script type="text/javascript">
		$(function () {
			$("#userTabs").tabs();
		});
	</script>
	<table border="0" width="100%" cellspacing="0" cellpadding="0">
		<tr>
			<td width="100%" colspan="2">&nbsp;</td>
		</tr>
		<tr>
			<td width="100%" colspan="2" class="contentEdit" align="center">
				<input type="submit" name="submit" value="Сохранить форум" class="button"/></td>
		</tr>
	</table>
</form>