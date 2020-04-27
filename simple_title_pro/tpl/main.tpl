<div style="text-align : left;">
	<table class="content" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
			<td width="100%" colspan="2" class="contentHead">
				<img src="{{ skins_url }}/images/nav.gif" hspace="8" alt=""/>Настройка плагина: Simple Title Pro
				=> {{ global }} </td>
		</tr>
		<tr>
			<td>&nbsp;</td>
		</tr>
	</table>

	<table border="0" cellspacing="0" cellpadding="0" width="100%">
		<tr align="center">
			<td width="100%" class="contentNav" align="center" style="background-repeat: no-repeat; background-position: left;">
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro'" value="Общие" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro&action=list_static'" value="Список статиков" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro&action=list_cat'" value="Список категорий" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro&action=list_news'" value="Список новостей" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro&action=clear_cache'" value="Очистить кэш" class="navbutton"/>
				<input type="button" onmousedown="javascript:window.location.href='{{ admin_url }}/admin.php?mod=extra-config&plugin=simple_title_pro&action=about'" value="О плагине" class="navbutton"/>
			</td>
		</tr>
	</table>
	<br/>
	{% if (info.true) %}
		<tr>
		<td class="contentNav">
			<font color="red">
				{{ info.print }}
			</font>
		</td>
		</tr>{% endif %}
	{% if (reklama.true) %}
		<tr>
		<td class="contentNav">
			<font color="blue">
				{{ reklama.print }}
			</font>
		</td>
		</tr>{% endif %}
	{{ entries }}

</div>