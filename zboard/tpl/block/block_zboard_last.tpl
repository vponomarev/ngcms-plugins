{% for entry in entries %}
<li><em><a target="_blank" href="{{entry.catlink}}">{{entry.cat_name}}</a> - </em><a target="_blank" href="{{entry.fulllink}}">{{entry.announce_name}}</a></li>
{% endfor %}