{% if (headr) %}<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr">
<head>
<title>{{ titles }}</title>
<meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
<meta name='keywords' content='{{ keywords }}' />
<meta name='description' content='{{ description }}' />
<link rel="stylesheet" href="{{ forum_tpl }}/style.css" type="text/css" />
<link rel="stylesheet" href="{{ forum_tpl }}/wbbtheme.css" type="text/css" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.0/jquery.min.js"></script>
<script src="{{ forum_tpl }}/jquery.wysibb.min.js"></script>
</head>{% endif %}
<body>
<div id="punwrap">
	<div id="punindex" class="pun">
		<div id="brdheader" class="block">
			<div class="box">
				<div id="brdtitle" class="inbox">
					<table style="border: 0px;" width="99%" cellspacing="0" cellpadding="0">
						<tr>
							<td style="padding: 0px; margin:0px; border: 0px;">
								<h1><span>{{ title }}</span></h1>
								<p><span></span></p>
							</td>
								<td style="padding: 0px; margin:0px; border: 0px;" align="right"><a href="{{ rss_feed }}"><img src="{{ css }}/img/rss.png" border="0" height="32" width="32" alt="rss" /></a></td>
						</tr>
					</table>
				</div>

				<div id="brdmenu" class="inbox">
					<ul>
						<li><a href='http://rozard.ngdemo.ru'>На сайт</a></li>
						<li><a href='{{ home }}'>Главная страница</a></li>
						<li><a href='{{ news_feed }}'>Новости</a></li>
						<li><a href='{{ userslist }}'>Пользователи</a></li>
						{% if (rules.true) %}<li><a href='{{ rules.print }}'>Правила</a></li>{% endif %}
						<li><a href='{{ search }}'>Поиск</a></li>
						{% if (global.user) %}<li><a href='{{ profile }}'>Профиль</a></li>{% else %}<li><a href='{{ register }}'>Регистрация</a></li>{% endif %}
						{% if (global.user['status'] == 1) %}<li><a href='{{ administration }}'>Администрирование</a></li>{% endif %}
						{% if (global.user) %}<li><a href='{{ pm }}'>Сообщения{% if (num_pm) %}[<span style="color:red">{{ num_pm }}</span>]{% endif %}</a></li>{% endif %}
						{% if (global.user) %}<li><a href='{{ out }}'>Выйти</a></li>{% else %}<li><a href='{{ login }}'>Зайти</a></li>{% endif %}
					</ul>
				</div>

				<div id="brdwelcome" class="inbox">
					<ul class="conl">
						{% if (global.user) %}<li>Вы вошли как: <strong>{{ global.user['name'] }}</strong></li>{% else %}<li>Вы вошли как: <strong>Гость</strong></li>{% endif %}
						{% if (global.user) %}<li>Ваш последний визит: {% if (last_visit_u) %}
{% if last_visit_u|date('d-m-Y') == "now"|date('d-m-Y') %}
	Сегодня {{ last_visit_u|date('H:i') }}
{% elseif last_visit_u|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	Вчера {{ last_post_forum.date|date('H:i') }}
{% else %}
	{{ last_post_forum.date|date('d-m-Y H:i') }}
{% endif %}
{% else %}
0
{% endif %}</li>{% else %}<li>Ваш последний визит: {% if (last_visit_g) %}
{% if last_visit_g|date('d-m-Y') == "now"|date('d-m-Y') %}
	Сегодня {{ last_visit_g|date('H:i') }}
{% elseif last_visit_g|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	Вчера {{ last_post_forum.date|date('H:i') }}
{% else %}
	{{ last_post_forum.date|date('d-m-Y H:i') }}
{% endif %}
{% else %}
Только вошли
{% endif %}</li>{% endif %}
					</ul>
					
					<div class="clearer"></div>
				</div>
			</div>
		</div>
		{% if (announc_on_off) %}
		<div id="announce" class="block"> 
			<h2><span>Объявление</span></h2> 
			<div class="box"> 
				<div class="inbox"> 
					<div>{{ announce }}</div> 
				</div> 
			</div> 
		</div>
		{% endif %}
		{% if (dis_wel) %}{% if (global.user) %}
		<div class="blocktable" style="MARGIN-BOTTOM: 12px">
			<h2><span>Добро пожаловать <strong>{{ global.user['name'] }}</strong></span></h2>
			<div class="box">
				<div class="inbox">
					<table cellspacing="0">
						<tbody>
							<tr>
								<td style="width: 100px" class="addcc">
									<img width= "100" height="100"  src="{% if (avatar.true) %}{{ avatar.print }}{% else %}{{ avatar.print }}/noavatar.gif{% endif %}" />
								</td>
								<td class="addcc" valign="top">
									<table cellpadding="0" cellspacing="0" width="100%" align="left" >
										<tr>
											<td width="50%" class="desc" style="padding: 3px;  border:0px"><strong>Новости:</strong></td>
										</tr>
										{% for entry in entries_list_news %}
										<tr>
											<td class="desc" style="padding: 3px;  border:0px "><a href="{{ entry.link_news }}">{% if (entry.create_data > global.user['last']) %}<span style="color:red;">{% endif %}{{ entry.title }}{% if (entry.create_data > global.user['last']) %}</span>{% endif %}</a></td>
										<tr>
										{% else %}
										<tr>
											<td class="desc" style="padding: 3px;  border:0px ">НЕТУ</td>
										<tr>
										{% endfor %}
									</table>
								</td>
								<td class="addc" valign="top">
									<table cellpadding="0" cellspacing="0" width="100%" align="left" >
										<tr>
											{% if (global.user) %}<td class="desc" style="padding: 3px;  border:0px "><a href='{{ show_new }}'>Показать новые сообщения, с момента Вашего последнего визита</a></td>{% endif %}
										</tr>
										<tr>
											{% if (global.user) %}<td class="desc" style="padding: 3px;  border:0px "><a href='{{ show_24 }}'>Показать последние сообщения</a></td>{% endif %}
										</tr>
										<tr>
											{% if (global.user) %}<td class="desc" style="padding: 3px;  border:0px "><a href='{{ markread }}'>Пометить все форумы как прочитанные</a></td>{% endif %}
										</tr>
									</table>
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		{% else %}
		<script language="javascript">
			function forum_change(){
				if (document.getElementById('forum_captcha').className == "yellow"){
					document.getElementById('forum_captcha').className = "red";
					document.getElementById('forum_captcha_sess').value = 0;
				} else {
					document.getElementById('forum_captcha').className = "yellow";
					document.getElementById('forum_captcha_sess').value = 1;
				}
			}
		</script>
		<div class="blocktable" style="MARGIN-BOTTOM: 12px">
			<h2><span> Добро пожаловать на наш форум</span></h2>
			<div class="box">
				<div class="inbox">
					<table cellspacing="0">
						<tbody>
							<tr>
								<td style="width: 100px" class="addcc">
									<img width= "100"  height="100" src="{% if (avatar.true) %}{{ avatar.print }}{% else %}{{ avatar.print }}/noavatar.gif{% endif %}" />
								</td>
								<td class="addcc" valign="top">
									Здравствуйте, уважаемый посетитель! К сожалению вы не были распознаны форумом как зарегистрированный пользователь. Для полноценного использования возможностей нашего форума вам необходимо <a href='{{ register }}'>зарегистрироваться</a>. Если вы уже зарегистрированы на форуме, то вам необходимо пройти авторизацию, используя Ваш логин и пароль. Зарегистрированные пользователи получают возможность просматривать закрытые разделы форума, а также возможность общения на нашем форуме.
								</td>
								<td width="25%" class="addc">
									<form id="login" method="post" action="{{ login }}">
										<legend>Введите ваше имя и пароль ниже</legend> 
										<div class="infldset"> 
											<label class="conl"><strong>Имя</strong><br /><input type="text" name="username" value="{{ username }}" size="25" maxlength="25" tabindex="1" /><br /></label> 
											<label class="conl"><strong>Пароль</strong><br /><input type="text" name="password" size="16" maxlength="16" tabindex="2" /><br /></label> 
											<p class="clearb">Поставь галочку если человек: <input type="checkbox" id="forum_captcha" onclick="forum_change();" value="1"></p>
											<input type="hidden" name="forum_captcha_sess" id="forum_captcha_sess" value="0">
										</div>
										<p><input type="submit" name="submit" value="Зайти" tabindex="3" /></p>
									</form> 
								</td>
							</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>
		{% endif %}{% endif %}
		{% if (dis_event) %}
		<div class="blocktable" style="MARGIN-BOTTOM: 12px">
			<h2><span>Последние события</span></h2>
			<div class="box">
				<table class='ipbtable' cellspacing="0" >
					<tr>
						<th width="22%" valign="middle" align="left">Активные пользователи:</th>
						<th width="18%" valign="middle" align="left">Новоприбывшие:</th>
						<th width="60%" valign="middle" align="left">Последние ответы:</th>
					</tr>
					<tr>
						<td class="row1" valign="top" >
							<table cellpadding="0" cellspacing="0" width="100%" align="left" >
								<tr>
									<td width="50%" class="desc" style="padding: 3px;  border:0px"><strong>Юзер:</strong></td>
									<td width="50%" class="desc" style="padding: 3px;  border:0px"><strong>Сообщений:</strong></td>
								</tr>
								{% for entry in entries_active_user %}
									<tr>
										<td class="desc" style="padding: 3px;  border:0px "><a href="{{ entry.profile_link }}">{{ entry.color_start }}{{ entry.profile }}{{ entry.color_end }}</a></td>
										<td class="desc" style="padding: 3px;  border:0px">{{ entry.num_post }}</td>
									</tr>
								{% endfor %}
							</table>
						</td>
						<td class="row2" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" align="left">
								<tr>
									<td width="50%" class="desc" style="padding: 3px;  border:0px"><strong>Юзер:</strong></td>
									<td width="50%" class="desc" style="padding: 3px;  border:0px"><strong>Сообщений:</strong></td>
								</tr>
								{% for entry in entries_new_user %}
									<tr>
										<td class="desc" style="padding: 3px;  border:0px "><a href="{{ entry.profile_link }}">{{ entry.color_start }}{{ entry.profile }}{{ entry.color_end }}</a></td>
										<td class="desc" style="padding: 3px;  border:0px">{{ entry.num_post }}</td>
									</tr>
								{% endfor %}
							</table>
						</td>
						<td class="row1" valign="top">
							<table cellpadding="0" cellspacing="0" width="100%" align="left">
								<tr>
									<td width="60%" class="desc" style="padding: 3px; border:0px"><strong>Тема:</strong></td>
									<td width="15%" class="desc" style="padding: 3px; border:0px"><strong>Последний:</strong></td>
									<td width="15%" class="desc" style="padding: 3px; border:0px"><strong>Просмотров:</strong></td>
									<td width="10%" class="desc" style="padding: 3px; border:0px"><strong>Ответов:</strong></td>
								</tr>
								{% for entry in entries_last_topic %}
									<tr>
										<td width="60%" class="desc" style="padding: 3px; border:0px"><a href="{{ entry.topic_link }}">{{ entry.subject }}</a></td>
										<td width="15%" class="desc" style="padding: 3px; border:0px"><a href="{{ entry.profile_link }}">{{ entry.profile }}</a></td>
										<td width="15%" class="desc" style="padding: 3px; border:0px">{{ entry.num_views }}</td>
										<td width="10%" class="desc" style="padding: 3px; border:0px">{{ entry.num_replies }}</td>
									</tr>
								{% endfor %}
							</table>
						</td>
					</tr>
				</table>
			</div>
		</div>
		{% endif %}
		{{ content }}
		{% if (stat) %}
		<div id="brdstats" class="block">
			<h2><span>Информация о форуме</span></h2>
			<div class="box">
				<div class="inbox">
					<dl class="conr">
						<dt><strong>Статистика форума</strong></dt>
						<dd>Всего зарегистрированных пользователей: <strong>{{ total_users }}</strong></dd>
						<dd>Всего тем: <strong>{{ total_topics }}</strong></dd>
						<dd>Всего сообщений: <strong>{{ total_posts }}</strong></dd>
					</dl>
					<dl class="conl">
						<dt><strong>Информация о пользователе</strong></dt>
						<dd>Последний зарегистрированный пользователь: <a href='{{ last_user.url }}'>{{ last_user.name }}</a></dd>
						<dd>Сейчас посетителей: <strong>{{ num_users }}</strong></dd>
						<dd>Сейчас Гостей: <strong>{{ num_guest }}</strong></dd>
					</dl>{% if (online.true) %}
					<dl id="onlinelist" class= "clearb">
						<b>Активны:</b> {{ online.print }}
					</dl>{% endif %}
					<dl id="todaylist" class= "clearb">
						<dt><strong>Сегодня нас посетили ({% if (num_today.true) %}Пользователей: {{ num_today.print }}{% endif %}{% if (num_guest_today.true) %} Гостей: {{ num_guest_today.print }}{% endif %}):&nbsp;</strong>
						{% if (users_today.true) %}{{ users_today.print }}{% endif %}
						</dt>
					</dl>
					{% if (list_bans) %}
					<dl id="todaylist" class= "clearb">
						<dt><strong>Cписок забаненых IP:</strong>
						{{ list_bans }}
						</dt>
					</dl>{% endif %}
				</div>
			</div>
		</div>
		{% endif %}
		<div id="brdfooter" class="block">
			<h2><span>Board footer</span></h2>
			<div class="box">
				<div class="inbox">
					<p class="conr">Работает на  <a href='http://rozard.net' target='_blank'>NG форум</a> <span style="color:red;">{{ version }}</span></p>
					<p class="conr">[ Время генирациии {{ exectime }} сек (только форума: {{ exectime_forum }} сек), {{ queries }} SQL запросов, Потребление памяти: {{ memory }} ]</p>
					<div class="clearer"></div>
				</div>
			</div>
		</div>
	</div>
</div>
</body>
</html>
{% if (global.user['status'] == 1) %}
{{ debug_queries }}
{{ debug_profiler }}
{% endif %}