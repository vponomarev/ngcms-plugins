<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		{% if (global.user) %}<p class="postlink conr"><a href='{{ addtopic }}'>�������� ����</a></p>{% endif %}
		<ul><li><a href='{{ home_link }}'>������</a>&nbsp;</li><li>&raquo;&nbsp;{{ Ftitle }}</li></ul>
		<div class="clearer"></div>
	</div>
</div>
<div id="vf" class="blocktable">
	<h2><span>{{ Ftitle }}</span> <a href="{{ link_rss }}"><img src="{{ tpl }}/img/rss.gif"  alt="rss" /></a></h2> 
	<div class="box"> 
		<div class="inbox">
			{% if (entries_imp) %}<table cellspacing="0"> 
				<thead> 
					<tr> 
						<th class="tcl" scope="col">������ ����</th> 
						<th class="tc2" scope="col">�������</th> 
						<th class="tc3" scope="col">����������</th> 
						<th class="tcr" scope="col">��������� ���������</th> 
					</tr> 
				</thead> 
				<tbody>
				
				{% for entry in entries_imp %}
				<tr{% if (entry.state == 'closed') %} class="iclosed"{% endif %}> 
					<td class="tcl">
						<div class="intd">
							<div class="{% if (entry.status) %}icon inew{% else %}icon{% endif %}"><div class="nosize"><!-- --></div></div>
							<div class="tclcon">
								<a href='{{ entry.topic_link }}'>{{ entry.Ttitle }}</a><span class='byuser'> �������&nbsp;{{ entry.author }}</span> {% if (entry.pag_topic) %}( {{ entry.pag_topic }} ){% endif %}
							</div>
						</div>
					</td>
					<td class="tc2">{{ entry.int_post }}</td>
					<td class="tc3">{{ entry.int_views }}</td>
					<td class='tcr'>
						{% if (entry.last_post_forum.topic_name) %}
						<div class="last_post_img">����: 
							<span style="padding: 0px 3px 0px 0px;">
								<a href="{{ entry.last_post_forum.profile_link }}" title="������� {{ entry.last_post_forum.profile }}"><img src="{% if (entry.last_post_forum.profile_avatar.true) %}{{ entry.last_post_forum.profile_avatar.print }}{% else %}{{ entry.last_post_forum.profile_avatar.print }}/noavatar.gif{% endif %}" width="100" height="100" alt="" /></a>
							</span>
							<a href="{{ entry.last_post_forum.topic_link }}">{{ entry.last_post_forum.topic_name }}</a> <span class="byuser">�����: <a href="{{ entry.last_post_forum.profile_link }}" title="������� {{ entry.last_post_forum.profile }}">{{ entry.last_post_forum.profile }}</a> 
							(<i style="font-size: 11px;">{{ entry.last_post_forum.date }}</i>)</span>
						</div>
						{% else %}��� ���������{% endif %}
					</td>
					
				</tr>
				{% else %}
				<tr> 
					<td class="tcl">
						<div class="intd">
							<div class="icon"></div>
							<div class="tclcon">
								���� ���
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
						<th class="tcl" scope="col">����</th> 
						<th class="tc2" scope="col">�������</th> 
						<th class="tc3" scope="col">����������</th> 
						<th class="tcr" scope="col">��������� ���������</th> 
					</tr> 
				</thead> 
				<tbody>
				
				{% for entry in entries %}
				<tr{% if (entry.state == 'closed') %} class="iclosed"{% endif %}> 
					<td class="tcl">
						<div class="intd">
							<div class="{% if (entry.status) %}icon inew{% else %}icon{% endif %}"><div class="nosize"><!-- --></div></div>
							<div class="tclcon">
								<a href='{{ entry.topic_link }}'>{{ entry.Ttitle }}</a><span class='byuser'> �������&nbsp;{{ entry.author }}</span> {% if (entry.pag_topic) %}( {{ entry.pag_topic }} ){% endif %}
							</div>
						</div>
					</td>
					<td class="tc2">{{ entry.int_post }}</td>
					<td class="tc3">{{ entry.int_views }}</td>
					<td class='tcr'>
						{% if (entry.last_post_forum.topic_name) %}
						<div class="last_post_img">����: 
							<span style="padding: 0px 3px 0px 0px;">
								<a href="{{ entry.last_post_forum.profile_link }}" title="������� {{ entry.last_post_forum.profile }}"><img src="{% if (entry.last_post_forum.profile_avatar.true) %}{{ entry.last_post_forum.profile_avatar.print }}{% else %}{{ entry.last_post_forum.profile_avatar.print }}/noavatar.gif{% endif %}" width="100" height="100" alt="" /></a>
							</span>
							<a href="{{ entry.last_post_forum.topic_link }}">{{ entry.last_post_forum.topic_name }}</a> <span class="byuser">�����: <a href="{{ entry.last_post_forum.profile_link }}" title="������� {{ entry.last_post_forum.profile }}">{{ entry.last_post_forum.profile }}</a> 
							(<i style="font-size: 11px;">{{ entry.last_post_forum.date }}</i>)</span>
						</div>
						{% else %}��� ���������{% endif %}
					</td>
				</tr>
				{% else %}
				<tr> 
					<td class="tcl">
						<div class="intd">
							<div class="icon"></div>
							<div class="tclcon">
								���� ���
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
		{% if (global.user) %}<p class="postlink conr"><a href='{{ addtopic }}'>�������� ����</a></p>{% endif %}
		<ul><li><a href='{{ home_link }}'>������</a>&nbsp;</li><li>&raquo;&nbsp;{{ Ftitle }}</li></ul>
		<div class="clearer"></div>
	</div>
</div>
<div class="blockform">
	<div class="box">
		<div style="padding-left: 4px">
				<dl>
					<dt>{{ local.num_user_loc + local.num_guest_loc }} ���. ������������� ��� ���� (������: {{ local.num_guest_loc }})</dt>
					<dt>�������������: {{ local.num_user_loc }} {{ local.list_loc_user }}</dt>
					<dt>�����: {{ local.num_bot_loc }} {{ local.list_loc_bot }}</dt>
				</dl>
		</div> 
	</div> 
</div>
