<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<div class="clearer"></div>
	</div>
</div>
<div class="blockform">
	<h2><span>Рейтинг пользователя {{ to_author }}&nbsp;&nbsp;<strong>[+{{ plus }} / -{{ min }}] &nbsp;</strong></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
				<th class="tc3" style="width:15%">От кого</th>
				<th class="tc3" style="width:15%">За топик</th>
				<th class="tc3"  style="width:35%">Причина</th>
				<th class="tc3" style="width:10%; text-align:center;">Оценка</th>
				<th class="tc3" style="width:15%">Дата</th>
				<!-- <th class="tc3" style="width:10%">Удалить</th> -->				</tr>
				<tbody>
					{% for entry in entries %}
					<tr>
						<td><a href='{{ entry.profile_link }}'>{{ entry.profile }}</a></td>
						<td><a href='{{ entry.topic_link }}#{{ entry.post_id }}'>{{ entry.subject }}</a></td>
						<td><p>{{ entry.message }}</p></td>
						<td style="text-align:center;">{% if (entry.rep.plus) %}+{% elseif (entry.rep.minus) %}-{% endif %}</td>
						<td>{{ entry.date|date("Y-m-d H:i:s") }}</td>
						<!-- <td style="text-align:center;"><input type="checkbox" name="delete_rep_id[]" value="641"></td> -->						
					</tr>
					{% else %}
					<tr>
						<td>Пусто</td>
						
						<!-- <td style="text-align:center;"><input type="checkbox" name="delete_rep_id[]" value="641"></td> -->						
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