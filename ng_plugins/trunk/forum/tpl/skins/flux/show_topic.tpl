<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<p class="postlink conr">{% if ((group_perm.replies and state == 'open') or (group_perm.modunit)) %}<a href='{{ addpost }}'>�������� ���������</a>{% endif %}</p>
		<ul><li><a href='{{ home_link }}'>������</a>&nbsp;</li><li>&raquo;&nbsp;<a href='{{ forum_link }}'>{{ forum_name }}</a>&nbsp;</li><li>&raquo;&nbsp;{{ subject }}</li></ul>
		<div class="clearer"></div> 
	</div> 
</div> 
<div id="msg" class="block"> 
	<form id="search" method="get" action="{{ link_topic_s }}">
	<h2><span>����� �� ����</span></h2> 
	<div class="box"> 
		<div class="inbox"> 
			<input type="hidden" name="id" value="{{ tid }}">
			{% if (not num_page == 1) %}<input type="hidden" name="page" value="{{ num_page }}">{% endif %}
			<label><input type="text" name="s" size="40" value="{{ search }}" maxlength="100" /><input type="submit" value="������" accesskey="s" /><br /></label> 
		</div> 
	</div> 
	</form>
</div> 

<div id="result">
{% for entry in entries %}
	<div id="{{ entry.post_id }}" class="blockpost rowodd">
		<h2><span><span class="conr">#{{ entry.i }}&nbsp;</span><a href='{{ entry.topic_link }}#{{ entry.post_id }}'>{% if entry.date|date('d-m-Y') == "now"|date('d-m-Y') %}
	������� {{ entry.date|date('H:i') }}
{% elseif entry.date|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	����� {{ entry.date|date('H:i') }}
{% else %}
	{{ entry.date|date('d-m-Y H:i') }}
{% endif %}</a></span></h2>
		<div class="box">
			<div class="inbox">
				<div class="postleft">
					<dl> 
						<dt><strong><a href="{{ entry.profile_link }}">{{ entry.author }}</a>{% if (entry.tc) %} ��{% endif %}</strong></dt>
						<dd class="usertitle"><strong>{{ entry.userstatus }}</strong></dd>
						<dd class="postavatar"><img src="{% if (entry.avatar.true) %}{{ entry.avatar.print }}{% else %}{{ entry.avatar.print }}/noavatar.gif{% endif %}" /></dd>
						<dd>���������������: {{ entry.data_reg|date("Y-m-d") }}</dd>
						<dd>���������: {{ entry.num_post }}</dd>
						{% if (entry.ip.true) %}
						<dd>IP: {{ entry.ip.print }}</dd>
						{% endif %}{% if (entry.uid) %} 
						<dd>
							<a href='{{ entry.reputation_link }}'>�������</a> : 
							<a href='{{ entry.plus }}'>+</a>
							&nbsp;&nbsp;<strong>{{ entry.sum }}&nbsp;&nbsp;</strong>
							<a href='{{ entry.minus }}'>-</a>
						</dd>
						<dd>������� �������: <a href='{{ entry.thank_link }}'>{{ entry.int_thank }} ���(�)</a></dd>
						<dd class="usercontacts"><a href='{{ entry.profile_link }}'>�������</a>&nbsp;&nbsp;
						<a href="{{ entry.send_pm }}">��</a>&nbsp;&nbsp;
						{% if (entry.site.true) %}<a href='{{ entry.site.print }}'>��� ����</a>{% endif %}
						</dd>
						{% if (entry.add_thank_link) %}<dd><a href='{{ entry.add_thank_link }}'>������� �������</a></dd>{% endif %}
						{% endif %}
					</dl>
				</div>
				<div class="postright"> 
					<h3></h3>
						<div class="postmsg"> 
							<p>{{ entry.message }}</p>
							{% if (entry.editdate.true) %}<p class="postedit"><em>���������������� {{ entry.editdate.edited_by }} ({% if entry.editdate.time|date('d-m-Y') == "now"|date('d-m-Y') %}
	������� {{ entry.editdate.time|date('H:i') }}
{% elseif entry.editdate.time|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	����� {{ entry.editdate.time|date('H:i') }}
{% else %}
	{{ entry.editdate.time|date('d-m-Y H:i') }}
{% endif %})</em></p>{% endif %}
							{% if (entry.list_attach) %}
							<div class="postsignature"><hr />������������� �����: <br />
							{% for entry in entry.list_attach %}
								<a href='{{ entry.file_link }}'>{{ entry.file }}</a>, ������: {{ entry.size }} �����, �������: {{ entry.int_file }}<br />
							{% endfor %}
							</div>{% endif %}
						</div> 
					<div class="postsignature"><hr />{{ entry.signature }}</div>
					{% if (entry.list_thank) %}<dl>
						<dd>������� �������: {{ entry.list_thank }}</dd>
					</dl>{% endif %}
				</div> 
				<div class="clearer"></div> 
				<div class="postfootleft">{% if (entry.active) %}<p><strong>�������</strong></p>{% else %}<p>���������</p>{% endif %}</div> 
				<div class="postfootright">
					<ul>
						{% if (global.user) %}<li class="postreport"><a href="{{ entry.complaints_link }}">�������� ����������</a></li>{% endif %}
						{% if (group_perm.remove or group_perm.remove_your) %}<li class="postdelete"><a href='{{ entry.del_link }}'>�������</a></li>{% endif %}
						{% if (group_perm.modify or group_perm.modify_your) %}<li class="postedit"><a href='{{ entry.edit_link }}'>�������������</a></li>{% endif %}
						{% if (group_perm..replies) %}<li class="postquote"><a href='{{ entry.quote.print }}'>��������</a></li>{% endif %}
						{% if ((group_perm.replies and state == 'open') or (group_perm.modunit)) %}
						<script>
							$(document).ready(function() {
							  $("#IncertText_{{ entry.post_id }}").click(function() {
								 $('#content').execCommand('quotes',{author: '{{ entry.author }}',seltext:'{{ entry.quote.quote }}'});
							  });
							})
						</script>
						<li><a href="#" id="IncertText_{{ entry.post_id }}">����������</a></li>
						{% endif %}
					</ul>
				</div>
			</div>
		</div>
	</div>
{% endfor %}
</div>
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<p class="postlink conr">{% if ((group_perm.replies and state == 'open') or (group_perm.modunit)) %}<a href='{{ addpost }}'>�������� ���������</a>{% endif %}</p>
		<ul><li><a href='{{ home_link }}'>������</a>&nbsp;</li><li>&raquo;&nbsp;<a href='{{ forum_link }}'>{{ forum_name }}</a>&nbsp;</li><li>&raquo;&nbsp;{{ subject }}</li></ul>
		{% if (subscript.true) %}�� ��������� �� ��� ���� - <a href='{{ subscript.uns }}'>����������</a>{% else %}<a href='{{ subscript.sus }}'>����������� � ����������� ����</a>{% endif %}
		<div class="clearer"></div> 
	</div> 
</div> 
<div class="blockform">
	<div class="box">
		<div style="padding-left: 4px">
				<dl>
					<dt>{{ local.num_user_loc + local.num_guest_loc }} ���. ������������� ��� ���� (������: {{ local.num_guest_loc }})</dt>
					<dt>�������������: {{ local.num_user_loc }} {{ local.list_loc_user }}</dt>
					<dt>�����: {{ local.num_bot_loc }} {{ local.list_loc_bot }}</dt>
				</dl>
		</div> 
	</div> 
</div>
<div style="float:right"><script src="http://pip.qip.ru/js/pip_button.js?type=3" type="text/javascript" charset="UTF-8"></script></div>
<div class="clearer"></div><br />
{% if ((group_perm.replies and state == 'open') or (group_perm.modunit)) %}

<div class="blockform">
	<h2><span>������� �����</span></h2>
	<div class="box">
		<form id="post" method="post" action="{{ addpost }}">
			<div class="inform">
				<fieldset>
					<legend>�������� ���� ��������� � ������� ���������</legend>
					<div class="infldset txtarea">
						<div style="padding-top: 4px">
						</div>
						<label><textarea name="message" id="content" rows="7" cols="75" tabindex="1"></textarea></label>
					</div>
				</fieldset>
			</div>
			<p><input type="submit" name="submit" tabindex="2" value="���������" accesskey="s" /></p>
		</form>
	</div>
</div>
<script>
	var myOpt = {
		buttons: 'bold,italic,underline,strike|,-|,link,myimg,|,smilebox,|,bullist,numlist,|,codephp, |,quotes', 
			allButtons: {
				myimg: {
					title: '�����������',
					buttonHTML: '<span class="ve-tlb-img"></span>',
					modal:{
						title: '�������� �����������',
						width: '600px',
						tabs: [
							{
								input: [
									{param: "SRC",title:"������� ����� �����������",validation: '^http(s)?://.*?\.(jpg|png|gif|jpeg)$'},
									{param: "TITLE",title:"������� ��������� �����������"}
								]
							}
						],
						onLoad: function(){},
						onSubmit: function(){}
					}, transform:{
						'<img src="{SRC}" title="{TITLE}" />':'[img title={TITLE}]{SRC}[/img]'
					}
				},
				codephp: {
					title: CURLANG.code,
					buttonText:"[code]",
					transform:{ 
						'<div class="codewrap"><div class="codetop">���: PHP</div><div class="codemain">{SELTEXT}</div></div>':"[code=PHP]{SELTEXT}[/code]"
					}
				},quotes:{
					title:CURLANG.quote,
					buttonHTML:'<span class="ve-tlb-quote"></span>',
					transform:{
						'<div class="quote">{SELTEXT}</div>':'[quote]{SELTEXT}[/quote]',
						'<div class="quote"><cite>{AUTHOR} �������:</cite>{SELTEXT}</div>':'[quote={AUTHOR}]{SELTEXT}[/quote]'
					}
				}
			},
			smileList: [
					{title:CURLANG.sm1, img: '<img src="{themePrefix}{themeName}/smiles/1.gif" class="sm">', bbcode:":)"},
					{title:CURLANG.sm8 ,img: '<img src="{themePrefix}{themeName}/smiles/2.gif" class="sm">', bbcode:":("},
					{title:CURLANG.sm1 ,img: '<img src="{themePrefix}{themeName}/smiles/3.gif" class="sm">', bbcode:":D"},
					{title:CURLANG.sm3 ,img: '<img src="{themePrefix}{themeName}/smiles/11.gif" class="sm">', bbcode:";)"},
					{title:CURLANG.sm4, img: '<img src="{themePrefix}{themeName}/smiles/4.gif" class="sm">', bbcode:":up:"},
					{title:CURLANG.sm5, img: '<img src="{themePrefix}{themeName}/smiles/9.gif" class="sm">', bbcode:":down:"},
					{title:CURLANG.sm6, img: '<img src="{themePrefix}{themeName}/smiles/6.gif" class="sm">', bbcode:":shock:"},
					{title:CURLANG.sm7, img: '<img src="{themePrefix}{themeName}/smiles/9.gif" class="sm">', bbcode:":angry:"},
					{title:CURLANG.sm9, img: '<img src="{themePrefix}{themeName}/smiles/14.gif" class="sm">', bbcode:":sick:"}
			]
	};
	$('#content').wysibb(myOpt);
</script>
{% endif %}