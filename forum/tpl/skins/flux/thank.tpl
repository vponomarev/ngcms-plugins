<div class="linkst">
	<div class="inbox">
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
		<div class="clearer"></div>
	</div>
</div>
<div class="blockform">
	<h2><span>История благодарностей участнику {{ to_author }}::: Спасибо сказали: {{ int_thank }} раз(а)</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
				<thead>
				<tr>
					<th class="tc3" style="width:15%">Дата поста:</th>
					<th class="tc3" style="width:15%">В теме:</th>
					<th class="tc3" style="width:35%">За сообщение:</th>
					<th class="tc3" style="width:10%">Спасибо сказали:</th>
				<tbody>
				{% for entry in entries %}
					<tr>
						<td>{{ entry.c_data|date("j-m-Y, H:i") }}</td>
						<td><a href='{{ entry.topic_link }}'>{{ entry.Ttitle }}</a></td>
						<td><p>{{ entry.message }}</p></td>
						<td><a href='{{ entry.profile_link }}'>{{ entry.profile }}</a></td>
					</tr>
				{% else %}
					<tr>
						<td>Пусто</td>
					</tr>
				{% endfor %}
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="linkst">
	<div class="inbox">
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
		<div class="clearer"></div>
	</div>
</div>