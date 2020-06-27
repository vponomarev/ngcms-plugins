{% if (entries|length > 0) %}
	<br/>
	<b>Р”СЂСѓРіРёРµ РЅРѕРІРѕСЃС‚Рё РѕС‚ РїРѕР»СЊР·РѕРІР°С‚РµР»СЏ <a href="{{ author_link }}" target="_blank">{{ author }}</a>:</b>
	<table width="100%">
		<tr>
			<td align="center"><b>Р—Р°РіРѕР»РѕРІРѕРє</b></td>
			<td align="center"><b>РќРѕРІРѕСЃС‚СЊ</b></td>
			<td align="center"><b>Р”Р°С‚Р° РїСѓР±Р»РёРєР°С†РёРё</b></td>
		</tr>
		{% for entry in entries %}
			<tr>
				<td align="center"><a href="{{ entry.news_link }}" target="_blank">{{ entry.title }}</a></td>
				<td align="center">{{ entry.short_news }}</td>
				<td align="center">{{ entry.postdate|date("d-m-Y h:i") }}</td>
			</tr>
		{% endfor %}
	</table>
{% endif %}
