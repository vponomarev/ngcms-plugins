{% for entry in entries %}
			<li style="display:{% if (loop.index == 1) %}block{% else %}none{% endif %};">
				<div id="common-rnd">
				<div id="img-rnd">{% if (entry.pid) %}<a href='{{entry.fulllink}}' target='_blank'><img src='{{home}}/uploads/zboard/thumb/{{entry.filepath}}' width='60' height='60'></a>{% else %}<a href='{{entry.fulllink}}' target='_blank'><img src='{{tpl_url}}/img/noimage.png' width='60' height='60'></a>{% endif %}</div>
				<div id="text-rnd">
					<p>{{entry.date|date("m-d-Y H:i")}}</p>
					<p><a target="_blank" href="{{entry.catlink}}">{{entry.cat_name}}</a></p>
					<h3><a target="_blank" href="{{entry.fulllink}}">{{entry.announce_name}}</a></h3>	
				</div>
				</div>

			</li>
{% endfor %}