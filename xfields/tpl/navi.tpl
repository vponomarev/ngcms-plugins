<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tbody>
	<tr>
		<td colspan="8" class="contNav" width="100%">
			<div id="btnMenu">
				<span class="{{ bclass['news'] }}" onclick='document.location="?mod=extra-config&plugin=xfields&section=news";'>Новости: поля</span><span class="btnSeparator">&nbsp;</span>
				<span class="{{ bclass['grp.news'] }}" onclick='document.location="?mod=extra-config&plugin=xfields&section=grp.news";'>Новости: группы</span><span class="btnDelimiter">&nbsp;</span>
				<span class="{{ bclass['tdata'] }}" onclick='document.location="?mod=extra-config&plugin=xfields&section=tdata";'>Новости: таблицы</span><span class="btnSeparator">&nbsp;</span>
				{% if (pluginIsActive('uprofile')) %}
					<span class="{{ bclass['users'] }}" onclick='document.location="?mod=extra-config&plugin=xfields&section=users";'>Пользователи: поля</span>
					<span class="btnDelimiter">&nbsp;</span>
				{% endif %}

			</div>
			&nbsp;
		</td>
	</tr>
	</tbody>
</table>
