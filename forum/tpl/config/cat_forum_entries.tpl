<tr>
	<td class="tcl">
		<div class="intd">
			<div class="{{ status }}"><div class="nosize"><!-- --></div></div>
			<div class="tclcon">
				<h3><a href='{{ forum_link }}'>{{ forum_name }}</a></h3>{{ forum_desc }}
			</div>
		</div>
	</td>
	<td class='tc2'>{{ num_topic }}</td>
	<td class='tc3'>{{ num_post }}</td>
	<td class='tcr'>{% if (last_post_forum.topic_name) %}{{ last_post_forum.topic_name }}<br /><a href='{{ last_post_forum.topic_link }}'>{{ last_post_forum.date }}</a> оставил&nbsp;<a href='{{ last_post_forum.profile_link }}'>{{ last_post_forum.profile }}</a>{% else %}нет сообщений{% endif %}</td> 
</tr>