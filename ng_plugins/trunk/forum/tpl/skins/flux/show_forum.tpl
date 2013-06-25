<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		{% if (topic_send) %}<p class="postlink conr"><a href='{{ send_topic }}'>Добавить тему</a></p>{% endif %}
		<ul><li><a href='{{ home_link }}'>Список</a>&nbsp;</li><li>&raquo;&nbsp;{{ forum_name }}</li></ul>
		<div class="clearer"></div>
	</div>
</div>
<div id="vf" class="blocktable">
	<h2><span>{{ forum_name }}</span> <a href="{{ link_rss }}"><img src="{{ tpl }}/img/rss.gif"  alt="rss" /></a></h2> 
	<div class="box"> 
		<div class="inbox">
			{% if (entries_imp) %}<table cellspacing="0"> 
				<thead> 
					<tr> 
						<th class="tcl" scope="col">Важные темы</th> 
						<th class="tc2" scope="col">Ответов</th> 
						<th class="tc3" scope="col">Просмотров</th> 
						<th class="tcr" scope="col">Последнее сообщение</th> 
					</tr> 
				</thead> 
				<tbody>
				
				{% for entry in entries_imp %}
				<tr{% if (entry.state == 'closed') %} class="iclosed"{% endif %}> 
					<td class="tcl">
						<div class="intd">
							<div class="{% if (entry.status) %}icon inew{% else %}icon{% endif %}"><div class="nosize"><!-- --></div></div>
							<div class="tclcon">
								<a href='{{ entry.topic_link }}'>{{ entry.topic_name }}</a><span class='byuser'> оставил&nbsp;{{ entry.topic_author }}</span> {% if (entry.pag_topic) %}( {{ entry.pag_topic }} ){% endif %}
							</div>
						</div>
					</td>
					<td class="tc2">{{ entry.int_post }}</td>
					<td class="tc3">{{ entry.int_views }}</td>
					<td class='tcr'>
						{% if (entry.last_post_forum.topic_name) %}
						<div class="last_post_img">Тема: 
							<span style="padding: 0px 3px 0px 0px;">
								<a href="{{ entry.last_post_forum.l_author_link }}" title="Профиль {{ entry.last_post_forum.l_author }}"><img src="{% if (entry.last_post_forum.l_author_avatar.true) %}{{ entry.last_post_forum.l_author_avatar.print }}{% else %}{{ entry.last_post_forum.l_author_avatar.print }}/noavatar.gif{% endif %}" width="100" height="100" alt="" /></a>
							</span>
							<a href="{{ entry.last_post_forum.topic_link }}">{{ entry.last_post_forum.topic_name }}</a> <span class="byuser">Автор: <a href="{{ entry.last_post_forum.l_author_link }}" title="Профиль {{ entry.last_post_forum.l_author }}">{{ entry.last_post_forum.l_author }}</a> 
							(<i style="font-size: 11px;">{% if entry.last_post_forum.date|date('d-m-Y') == "now"|date('d-m-Y') %}
	Сегодня {{ entry.last_post_forum.date|date('H:i') }}
{% elseif entry.last_post_forum.date|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	Вчера {{ entry.last_post_forum.date|date('H:i') }}
{% else %}
	{{ entry.last_post_forum.date|date('d-m-Y H:i') }}
{% endif %}</i>)</span>
						</div>
						{% else %}нет сообщений{% endif %}
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
			</table>{% endif %}
			<table cellspacing="0"> 
				<thead> 
					<tr> 
						<th class="tcl" scope="col">Тема</th> 
						<th class="tc2" scope="col">Ответов</th> 
						<th class="tc3" scope="col">Просмотров</th> 
						<th class="tcr" scope="col">Последнее сообщение</th> 
					</tr> 
				</thead> 
				<tbody>
				
				{% for entry in entries %}
				<tr{% if (entry.state == 'closed') %} class="iclosed"{% endif %}> 
					<td class="tcl">
						<div class="intd">
							<div class="{% if (entry.status) %}icon inew{% else %}icon{% endif %}"><div class="nosize"><!-- --></div></div>
							<div class="tclcon">
								<a href='{{ entry.topic_link }}'>{{ entry.topic_name }}</a><span class='byuser'> оставил&nbsp;{{ entry.topic_author }}</span> {% if (entry.topic_modify) %}<a href="{{ entry.topic_modify_link }}">(Редактировать)</a>{% endif %} {% if (entry.pag_topic) %}( {{ entry.pag_topic }} ){% endif %}
							</div>
						</div>
					</td>
					<td class="tc2">{{ entry.int_post }}</td>
					<td class="tc3">{{ entry.int_views }}</td>
					<td class='tcr'>
						{% if (entry.last_post_forum.topic_name) %}
						<div class="last_post_img">Тема: 
							<span style="padding: 0px 3px 0px 0px;">
								<a href="{{ entry.last_post_forum.l_author_link }}" title="Профиль {{ entry.last_post_forum.l_author }}"><img src="{% if (entry.last_post_forum.l_author_avatar.true) %}{{ entry.last_post_forum.l_author_avatar.print }}{% else %}{{ entry.last_post_forum.l_author_avatar.print }}/noavatar.gif{% endif %}" width="100" height="100" alt="" /></a>
							</span>
							<a href="{{ entry.last_post_forum.topic_link }}">{{ entry.last_post_forum.topic_name }}</a> <span class="byuser">Автор: <a href="{{ entry.last_post_forum.l_author_link }}" title="Профиль {{ entry.last_post_forum.l_author }}">{{ entry.last_post_forum.l_author }}</a> 
							(<i style="font-size: 11px;">{% if entry.last_post_forum.date|date('d-m-Y') == "now"|date('d-m-Y') %}
	Сегодня {{ entry.last_post_forum.date|date('H:i') }}
{% elseif entry.last_post_forum.date|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	Вчера {{ entry.last_post_forum.date|date('H:i') }}
{% else %}
	{{ entry.last_post_forum.date|date('d-m-Y H:i') }}
{% endif %}</i>)</span>
						</div>
						{% else %}нет сообщений{% endif %}
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
		<p class="pagelink conl">{% if (pages) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		{% if (topic_send) %}<p class="postlink conr"><a href='{{ send_topic }}'>Добавить тему</a></p>{% endif %}
		<ul><li><a href='{{ home_link }}'>Список</a>&nbsp;</li><li>&raquo;&nbsp;{{ forum_name }}</li></ul>
		<div class="clearer"></div>
	</div>
</div>
<div class="blockform">
	<div class="box">
		<div style="padding-left: 4px">
				<dl>
					<dt>{{ local.num_user_loc + local.num_guest_loc }} чел. просматривают эту тему (гостей: {{ local.num_guest_loc }})</dt>
					<dt>Пользователей: {{ local.num_user_loc }} {{ local.list_loc_user }}</dt>
					<dt>Ботов: {{ local.num_bot_loc }} {{ local.list_loc_bot }}</dt>
				</dl>
		</div> 
	</div> 
</div>
