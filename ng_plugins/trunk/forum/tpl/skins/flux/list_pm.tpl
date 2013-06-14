	<div class="blockmenu" style="padding: 0px 10px 0px 0px; margin-top: 10px;">
		<h2><span>������ ���������</span></h2>
		<div class="box">
			<div class="inbox">
				<ul>
					<li {% if (case_io == 'inbox') %}class="isactive"{% endif %}><a href="{{ inbox_link }}">��������</a></li>
					<li {% if (case_io == 'outbox') %}class="isactive"{% endif %}><a href="{{ outbox_link }}">������������</a></li>
				</ul>
			</div>
		</div>
	</div>
	
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		{% if (global.user) %}<p class="postlink conr"><a href='{{ send_pm }}'>����� ���������</a></p>{% endif %}
		<ul><li><a href='{{ home_link }}'>������</a>&nbsp;</li><li>&raquo;&nbsp;������ ���������</li></ul>
		<div class="clearer"></div>
	</div>
</div>
{% if (pm.pm_true) %}
<div id="p579" class="blockpost row_odd firstpost">
		<h2><span>{% if pm.pmdate|date('d-m-Y') == "now"|date('d-m-Y') %}
	������� {{ pm.pmdate|date('H:i') }}
{% elseif pm.pmdate|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	����� {{ pm.pmdate|date('H:i') }}
{% else %}
	{{ pm.pmdate|date('d-m-Y H:i') }}
{% endif %}</span></h2>
		<div class="box">
			<div class="inbox">
				<div class="postleft">
					<dl>
						<dt><strong><a href="{{ pm.profile_link }}">{{ pm.profile }}</a></strong></dt>
						<dd class="usertitle"><strong>{{ pm.userstatus }}</strong></dd>
						<dd class="postavatar"><img src="{% if (pm.avatar.true) %}{{ pm.avatar.print }}{% else %}{{ pm.avatar.print }}/noavatar.gif{% endif %}" /></dd>

					<dd>���������������: {{ pm.pmdate2|date("Y-m-d") }}</dd>
					<dd>���������: {{ pm.num_post }}</dd>
					<dd class="usercontacts">
						<a href="{{ pm.send_pm }}">��</a>&nbsp;&nbsp;
						{% if (pm.site.true) %}<a href='{{ pm.site.print }}'>��� ����</a>{% endif %}</dd>
					</dl>

				</div>
				<div class="postright">
					<div class="postmsg">
						<p>{{ pm.content }}</p>
					</div>
					<div class="postsignature"><hr />{{ pm.signature }}</div>
				</div>
				<div class="clearer"></div>

				<div class="postfootleft">{% if (pm.active) %}<p><strong>�������</strong></p>{% else %}<p>���������</p>{% endif %}</div>
				<div class="postfootright"><ul><li><a href="{{ pm.link_pm_reply }}">��������</a> | </li><li><a href="{{ pm.link_del_pm }}">�������</a> | </li><li><a href="{{ pm.link_pm_quote }}">����������</a></li></ul></div>
			</div>
		</div>
	</div>
{% endif %}
<form action="" method="post">
{% if (case_io == 'inbox') %}
<div class="blocktable" style="margin-left: 152px;">
	<h2><span>��������</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" style="width:65%">����</th>
					<th style="width:10%">�����������</th>
					<th class="tcr" style="width:15%">����</th>
					<th class="tcr" style="width:10%">������</th>
					<th class="tcr" style="width:10%">��������</th>
					</tr>
			</thead>
			<tbody>
	{% for entry in entries_inbox %}
	<tr>
		<td class="tcl">
			<div class="intd">
				<div class="icon"><div class="nosize"><!-- --></div></div>
				<div class="tclcon"><a href="{{ entry.link_pm }}">{{ entry.title }}</a>
				</div>
			</div>
		</td>
		<td class="tc2" style="white-space: nowrap; OVERFLOW: hidden"><a href="{{ entry.profile_link }}">{{ entry.profile }}</a></td>
		<td class="tcr" style="white-space: nowrap">{% if entry.pmdate|date('d-m-Y') == "now"|date('d-m-Y') %}
	������� {{ entry.pmdate|date('H:i') }}
{% elseif entry.pmdate|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	����� {{ entry.pmdate|date('H:i') }}
{% else %}
	{{ entry.pmdate|date('d-m-Y H:i') }}
{% endif %}</td>
		<td class="tcr" style="white-space: nowrap">{% if (entry.viewed == 0) %}<font color=green><b>�������������</b></font>{% else %}�����������{% endif %}</td>
		<td><input name="sel_pm[{{ entry.pmid }}]" value="1" class="check" type="checkbox" /></td>
	</tr>
	{% else %}
	<tr>
		<td class="tcl">
		��� ���������
		</td>
		<td class="tc2" style="white-space: nowrap; OVERFLOW: hidden"></td>
		<td class="tcr" style="white-space: nowrap"></td>
		<td class="tcr" style="white-space: nowrap"></td>
		<td class="tcr" style="white-space: nowrap"></td>
	</tr>
	{% endfor %}
			</tbody>
			</table>
		</div>
	</div>
</div>

{% endif %}
{% if (case_io == 'outbox') %}
<div class="blocktable" style="margin-left: 152px;">
	<h2><span>���������</span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" style="width:65%">����</th>
					<th style="width:10%">����������</th>
					<th class="tcr" style="width:15%">����</th>
					<th class="tcr" style="width:10%">��������</th>
					</tr>
			</thead>
			<tbody>
	{% for entry in entries_outbox %}
	<tr>
		<td class="tcl">
			<div class="intd">
				<div class="icon"><div class="nosize"><!-- --></div></div>
				<div class="tclcon"><a href="{{ entry.link_pm }}">{{ entry.title }}</a>
				</div>
			</div>
		</td>
		<td class="tc2" style="white-space: nowrap; OVERFLOW: hidden"><a href="{{ entry.profile_link }}">{{ entry.profile }}</a></td>
		<td class="tcr" style="white-space: nowrap">{% if entry.pmdate|date('d-m-Y') == "now"|date('d-m-Y') %}
	������� {{ entry.pmdate|date('H:i') }}
{% elseif entry.pmdate|date('d-m-Y') == "now-1 day"|date('d-m-Y') %}
	����� {{ entry.pmdate|date('H:i') }}
{% else %}
	{{ entry.pmdate|date('d-m-Y H:i') }}
{% endif %}</td>
		<td><input name="sel_pm[{{ entry.pmid }}]" value="1" class="check" type="checkbox" /></td>
	</tr>
	{% else %}
	<tr>
		<td class="tcl">
		��� ���������
		</td>
		<td class="tc2" style="white-space: nowrap; OVERFLOW: hidden"></td>
		<td class="tcr" style="white-space: nowrap"></td>
		<td class="tcr" style="white-space: nowrap"></td>
	</tr>
	{% endfor %}
			</tbody>
			</table>
		</div>
	</div>
</div>
{% endif %}
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<p class="postlink conr"><input type="submit" name="submit" value="Delete"></p>
		{% if (global.user) %}<p class="postlink conr"><a href='{{ send_pm }}'>����� ���������</a></p>{% endif %}
		<ul><li><a href='{{ home_link }}'>������</a>&nbsp;</li><li>&raquo;&nbsp;������ ���������</li></ul>
		<div class="clearer"></div>
	</div>
</div>
</form>
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