<?xml version="1.0" encoding="windows-1251"?>
 <rss version="2.0" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:wfw="http://wellformedweb.org/CommentAPI/">
 <channel>
  <title><![CDATA[{{ home_title }}]]></title>
  <link><![CDATA[{{ home_url }}]]></link>
  <language>ru</language>
  <description><![CDATA[{{ description }}]]></description>
  <generator><![CDATA[{{ generator }}]]></generator>
{% for entry in entries %}
  <item>
   <title><![CDATA[ {{ entry.title }} ]]></title>
   <link><![CDATA[ {{ entry.link }} ]]></link>
   <description><![CDATA[ {{ entry.text }} &copy; {{ entry.author }}]]></description>
   <category>{{ entry.category_link}}</category>
   <guid isPermaLink="false">{{ entry.rsslink }}</guid>
   <pubDate>{{ entry.rssdate }}</pubDate>
  </item>
{% endfor %}
 </channel>
</rss>