{% if (info) %}
<div class="feed-me">
{{info}}
</div>
{% endif %}

		<ul class="section clearfix">
			{% for entry in entries %}
					<li class="post clearfix">
						<div class="review">
							<a href="#" title="">{% if (entry.pid) %}<a href='{{entry.fulllink}}'><img src='{{home}}/uploads/zboard/thumb/{{entry.filepath}}' width='60' height='60'></a>{% else %}<a href='{{entry.fulllink}}'><img src='{{tpl_url}}/img/noimage.png' width='60' height='60'></a>{% endif %}</a>
						</div>
						<div class="entry">
							<h2><a href="{{ entry.fulllink }}">{{ entry.announce_name }}</a></h2>
							{{entry.announce_description|truncateHTML(30,'...')}}<br/><br/>
							<div class="tag">
								<a href="{{entry.catlink}}" class="tag-{{entry.cid}}">{{entry.cat_name}}</a>
							</div>
							<div class="desc" style="padding-top:5px;">{{entry.date|date("m-d-Y H:i")}} / {{entry.announce_author}}</div>
							<div class="view">
								<a title="{{entry.views}}">{{entry.views}}</a>
							</div>
						</div>
					</li>
			{% endfor %}
		</ul>				

{% if (pages.true) %}
<div class="pagenavi clearfix">

{% if (prevlink.true) %}
{{ prevlink.link }}
{% endif %}

{{ pages.print }}

{% if (nextlink.true) %}
{{ nextlink.link }}
{% endif %}

</div>
{% endif %}