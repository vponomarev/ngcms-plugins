<div class="block">
	<h2><span>{{ title }}</span></h2>
	<div class="box">
		<div class="inbox">
			<p>{{ content }}</p>
		</div>
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