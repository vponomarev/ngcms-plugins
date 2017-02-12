<div class="linkst">
	<div class="inbox">
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
		<div class="clearer"></div>
	</div>
</div>
<div id="vf" class="blocktable">
	<h2><span>Результаты поиска</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
				<thead>
				<tr>
					<th class="tcl" scope="col">Тема</th>
					<th class="tc2" scope="col">Форум</th>
					<th class="tc3" scope="col">Ответов</th>
					<th class="tcr" scope="col">Последнее сообщение</th>
				</tr>
				</thead>
				<tbody>
				{% for entry in entries %}
					<tr{% if (entry.state == 'closed') %} class="iclosed"{% endif %}>
						<td class="tcl">
							<div class="intd">
								<div class="{% if (entry.status) %}icon inew{% else %}icon{% endif %}">
									<div class="nosize"><!-- --></div>
								</div>
								<div class="tclcon">
									<a href='{{ entry.topic_link }}'>{{ entry.subject }}</a><span class='byuser'> оставил&nbsp;{{ entry.user }}</span>
								</div>
							</div>
						</td>
						<td class="tc2"><a href='{{ entry.forum_link }}'>{{ entry.forum_name }}</a></td>
						<td class="tc3">{{ entry.num_replies }}</td>
						<td class='tcr'>
							<a href='{{ entry.last_post_forum.topic_link }}'>{% if entry.last_post_forum.date|date('d-m-Y') == "now"|date('d-m-Y') %}
									Сегодня {{ entry.last_post_forum.date|date('H:i') }}
								{% elseif entry.last_post_forum.date|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
									Вчера {{ entry.last_post_forum.date|date('H:i') }}
								{% else %}
									{{ entry.last_post_forum.date|date('d-m-Y H:i') }}
								{% endif %}</a>
							оставил&nbsp;<a href='{{ entry.last_post_forum.profile_link }}'>{{ entry.last_post_forum.profile }}</a>
						</td>
					</tr>
				{% else %}
					<tr>
						<td class="tcl">
							<div class="intd">
								<div class="icon"></div>
								<div class="tclcon">
									Нету тем
								</div>
							</div>
						</td>
						<td class="tc2"></td>
						<td class="tc3"></td>
						<td class='tcr'></td>
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