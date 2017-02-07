{% if (entriesCount > 0) %}
	<div class="xfImagesList">
		<u>{{ fieldTitle }}</u> ({{ entriesCount }})<br/>
		{% for entry in entries %}
			{% if entry.flags.hasPreview %}
				<a target="_blank" href="{{ entry.url }}" title="{{ entry.description }}"><img alt="{{ entry.description }}" src="{{ entry.purl }}" width="{{ entry.pwidth }}" height="{{ entry.pheight }}"/></a>
			{% else %}
				<a target="_blank" href="{{ entry.url }}">{{ entry.origName }} ({{ entry.description }})</a>
			{% endif %}
			<br/>
		{% endfor %}
	</div>
{% endif %}
