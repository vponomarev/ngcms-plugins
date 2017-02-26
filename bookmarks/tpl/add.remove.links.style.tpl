<span id="bookmarks_{{ news }}"><a href="{{ link }}" title="{{ link_title }}">{% if (found) %}
			<img src="/engine/plugins/bookmarks/img/delete.gif"/>{% else %}
			<img src="/engine/plugins/bookmarks/img/add.gif"/>{% endif %}</a> {{ counter }}</span>
<script type="text/javascript">
	var el = document.getElementById('bookmarks_{{ news }}').getElementsByTagName('a')[0];
	el.setAttribute('href', '#');
	el.setAttribute('onclick', 'bookmarks("{{ url }}","{{ news }}","{{ action }}"); return false;');
</script>