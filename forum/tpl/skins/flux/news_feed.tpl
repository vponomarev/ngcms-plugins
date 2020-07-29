<div class="linkst">
	<div class="inbox">
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
		<div class="clearer"></div>
	</div>
</div>
<div class="block">
	<h2><span>Вся лента</span></h2>
	<div class="box">
		<div class="inbox">
			{% for entry in entries %}
				<p><a href='{{ entry.link_news }}'>{{ entry.title }}</a></p>
			{% endfor %}
		</div>
	</div>
</div>
<div class="linkst">
	<div class="inbox">
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p>
		<div class="clearer"></div>
	</div>
</div>
<div class="blockform">
	<div class="box">
		<div style="padding-left: 4px">
			<dl>
				<dt>{{ local.num_user_loc + local.num_guest_loc }} чел. просматривают эту тему
					(гостей: {{ local.num_guest_loc }})
				</dt>
				<dt>Пользователей: {{ local.num_user_loc }} {{ local.list_loc_user }}</dt>
				<dt>Ботов: {{ local.num_bot_loc }} {{ local.list_loc_bot }}</dt>
			</dl>
		</div>
	</div>
</div>
