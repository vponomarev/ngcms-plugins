<?xml version="1.0" encoding="UTF-8" ?>
<rss version="2.0">
	<channel>
		<title>{{ title }}</title>
		<link>{{ home }}</link>
		<description>{{ title }}</description>
		<language>ru</language>
		<copyright>{{ title }}</copyright>
		<lastBuildDate>{{ date|date("r") }}</lastBuildDate>
		{% for entry in entries %}
			<item>
				<title><![CDATA[{{ entry.Ttitle }}]]></title>
				<guid isPermaLink="true"><![CDATA[{{ entry.topic_link }}#{{ entry.pid }}]]></guid>
				<pubDate>{{ entry.c_data|date("r") }}</pubDate>
				<link>
				<![CDATA[{{ entry.topic_link }}#{{ entry.pid }}]]></link>
				<description><![CDATA[<p>{{ entry.content }}</p><br/>&copy; <b>{{ entry.profile }}</b>]]></description>
			</item>
		{% endfor %}
	</channel>
</rss>