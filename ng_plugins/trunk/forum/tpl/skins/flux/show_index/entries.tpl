<tr>
	<td class="tcl">
		<div class="intd">
			<div class="{% if (status) %}icon inew{% else %}icon{% endif %}"><div class="nosize"><!-- --></div></div>
			<div class="tclcon">
				<h3><a href='{{ forum_link }}'>{{ forum_name }}</a></h3>{{ forum_desc }}
				<p><em>(����������:</em> {{ moder_print }})</p>
			</div>
		</div>
	</td>
	<td class='tc2'>{{ num_topic }}</td>
	<td class='tc3'>{{ num_post }}</td>
	<td class='tcr'>
		{% if (last_post_forum.topic_name) %}
		<div class="last_post_img">����: 
			<span style="padding: 0px 3px 0px 0px;">
				<a href="{{ last_post_forum.profile_link }}" title="������� {{ last_post_forum.profile }}"><img src="{% if (last_post_forum.profile_avatar.true) %}{{ last_post_forum.profile_avatar.print }}{% else %}{{ last_post_forum.profile_avatar.print }}/noavatar.gif{% endif %}" width="100" height="100" alt="" /></a>
			</span>
			<a href="{{ last_post_forum.topic_link }}">{{ last_post_forum.topic_name }}</a> <span class="byuser">�����: <a href="{{ last_post_forum.profile_link }}" title="������� {{ last_post_forum.profile }}">{{ last_post_forum.profile }}</a> 
			(<i style="font-size: 11px;">{{ last_post_forum.date }}</i>)</span>
		</div>
		{% else %}��� ���������{% endif %}
	</td> 
</tr>