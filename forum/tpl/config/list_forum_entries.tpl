<tr align="center" class="contRow1">
	<td>{{ forum_id }}</td>
	<td><input type="text" name="position[{{ forum_id }}]" value="{{ pos }}" maxlength="5" size="5" /></td>
	<td><div style="float: left; margin-right: 5px;"><img alt="-" height="18" width="18" src="../engine/skins/default/images/catmenu/line.gif" /><img alt="-" height="18" width="18" src="../engine/skins/default/images/catmenu/joinbottom.gif" /></div> <div style="float: left;"><a href="/engine/admin.php?mod=extra-config&plugin=forum&action=send_forum&id={{ forum_id }}">{{ forum_name }}</a><br/><small><a href="{{ forum_link }}">{{ home_url }}{{ forum_link }}</a></small></div></td>
	<td>{{ num_topic }}</td>
	<td>{{ num_post }}</td>
	<td><a href="/engine/admin.php?mod=extra-config&plugin=forum&action=del_forum&id={{ forum_id }}"><img title="Удалить" alt="Удалить" src="../engine/skins/default/images/delete.gif" /></a></td>
</tr>