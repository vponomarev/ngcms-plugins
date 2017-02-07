<optgroup label="{{ cat_name }}">
	{% for entry in entries %}
		<option value="{{ entry.forum_id }}">{{ entry.forum_name }}</option>
	{% endfor %}
</optgroup>