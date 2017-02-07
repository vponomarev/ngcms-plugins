<h2>Сортировка пользователей</h2>
<form id="userlist" method="post" action="">
	<table class="table">
		<tr>
			<th colspan="2">Укажите необходимые параметры сортировки</th>
		</tr>
		<tr>
			<td>Имя:</td>
			<td><input type="text" name="username" value="{{ username }}" size="25"/></td>
		</tr>
		<tr>
			<td>Группа:</td>
			<td>
				<select name="show_group">
					<option value="-1" {% if (show_group_) %}selected{% endif %}>
						Все пользователи
					</option>
					<option value="1" {% if (show_group_1) %}selected{% endif %}>
						Администраторы
					</option>
					<option value="2" {% if (show_group_2) %}selected{% endif %}>
						Редакторы
					</option>
					<option value="3" {% if (show_group_3) %}selected{% endif %}>
						Журналисты
					</option>
					<option value="4" {% if (show_group_4) %}selected{% endif %}>
						Комментаторы
					</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>Сортировать по:</td>
			<td>
				<select name="sort_by">
					<option value="username" {% if (sort_by_username) %}selected{% endif %}>
						Имя
					</option>
					<option value="registered" {% if (sort_by_registered) %}selected{% endif %}>
						Зарегистрирован
					</option>
					<option value="num_posts" {% if (sort_by_num_posts) %}selected{% endif %}>
						Кол-во новостей
					</option>
					<option value="num_comments" {% if (sort_by_num_comments) %}selected{% endif %}>
						Кол-во комментариев
					</option>

					{% if pluginIsActive('xfields') %}
						<!-- список доп. полей -->
						{% for xf in xflist %}
							<option value="{{ xf.id }}" {% if (sort == xf.id) %}selected{% endif %}>
								{{ xf.title }}
							</option>
						{% endfor %}
					{% endif %}
				</select>
			</td>
		</tr>
		<tr>
			<td>Упорядочить по:</td>
			<td>
				<select name="sort_dir">
					<option value="ASC" {% if (sort_dir_ASC) %}selected{% endif %}>
						Возрастанию
					</option>
					<option value="DESC" {% if (sort_dir_DESC) %}selected{% endif %}>
						Убыванию
					</option>
				</select>
			</td>
		</tr>
	</table>
	<input class="btn" type="submit" name="submit" value="Отправить"/>
	<input class="btn" type="submit" name="reset" value="Сброс"/>
</form>

<div id="users1">
	<h2>Пользователи</h2>
	<table class="table">
		<tr>
			<th>Имя</th>

			{% if pluginIsActive('xfields') %}
				<!-- заголовки доп. полей -->
				{% for xf in xflist %}
					<th>{{ xf.title }}</th>
				{% endfor %}
			{% endif %}

			<th>Откуда</th>
			<th>Новостей</th>
			<th>Комментариев</th>
			<th>Зарегистрирован</th>
			<th>Последний вход</th>
		</tr>
		{% for entry in entries %}
			<tr>
				<td><a href='{{ entry.profile_link }}'>{{ entry.profile }}</a></td>

				{% if pluginIsActive('xfields') %}
					<!-- содержимое доп. полей -->
					{% for xf in entry.xfields %}
						<td>{{ xf }}</td>
					{% endfor %}
				{% endif %}

				<td>{{ entry.from }}</td>
				<td>{{ entry.news }}</td>
				<td>{{ entry.com }}</td>
				<td>{{ entry.reg|date("d-m-Y h:i") }}</td>
				<td>
					{% if (entry.last != 0) %}
						{{ entry.last|date("d-m-Y h:i") }}
					{% else %}
						не был ни разу
					{% endif %}
				</td>
			</tr>
		{% else %}
			<tr>
				<td colspan="4">По вашему запросу ничего не найдено.</td>
			</tr>
		{% endfor %}
	</table>
</div>

{% if (pages.true) %}
	<p>
		{% if (prevlink.true) %} {{ prevlink.link }} {% endif %}
		{{ pages.print }}
		{% if (nextlink.true) %} {{ nextlink.link }} {% endif %}
	</p>
{% endif %}
