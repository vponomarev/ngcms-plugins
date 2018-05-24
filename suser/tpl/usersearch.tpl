<h2>Поиск пользователей</h2>
<form id="usersearch" method="post" action="">
	Откуда: {{ boxlist['from'] }}

	{% if pluginIsActive('xfields') %}
		<!-- выпадающие списки доп. полей -->
		{% for xf in xflist %}
			{% if boxlist[xf.id] %}
				{{ xf.title }}: {{ boxlist[xf.id] }}
			{% endif %}
		{% endfor %}
	{% endif %}

	<input class="btn" type="submit" name="search" value="Найти"/>
	<input class="btn" type="submit" name="reset" value="Сброс"/>
</form>

{% if searched and entries %}
	<!-- если поиск успешно осуществлен -->
	<table class="table">
		<thead>
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
		</thead>
		<tbody>
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
		{% endfor %}
		</tbody>
	</table>
{% else %}
	{% if searched %}
		<!-- если результат поиска пуст -->
		Пользователи, соответствующие критериям, не найдены!
	{% endif %}
{% endif %}