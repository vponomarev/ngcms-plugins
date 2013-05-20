{% if (submit) %}
<div id="searchform" class="blockform"> 
	<h2><span>�����</span></h2> 
	<div class="box"> 
		<form id="search" method="post" action=""> 
			<div class="inform"> 
				<fieldset> 
					<legend>������� �������� ��� ������</legend> 
					<div class="infldset"> 
						<label class="conl">�������� �����<br /><input type="text" name="keywords" size="40" maxlength="100" /><br /></label> 
					</div> 
				</fieldset> 
			</div> 
			<div class="inform"> 
				<fieldset> 
					<legend>�������� ��� ������</legend> 
					<div class="infldset"> 
						<label class="conl">�����<br />
						<select id="forum" name="forum_id"> 
							<option value='0'>��� ���������</option>
							{% for entry in entries %}
							<option value='{{ entry.forum_id }}'>{{ entry.forum_name }}</option>
							{% endfor %}
						</select> 
						<br /></label> 
						<label class="conl">����� �<br />
						<select name="search_in"> 
							<option value='all' selected>������� ��������� � ���������� ���</option>
							<option value='post'>������ � ������� ���������</option>
							<option value='topic'>������ � ���������� ���</option>
						</select> 
						<br /></label> 
						<p class="clearb">�������� � ����� ������ �� ������� ������ � ����� ������.</p> 
					</div> 
				</fieldset> 
			</div> 
			<p><input type="submit" name="submit" value="���������" accesskey="s" /></p> 
		</form> 
	</div> 
</div>
{% else %}
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<div class="clearer"></div> 
	</div> 
</div>
<div id="vf" class="blocktable"> 
	<h2><span>���������� ������</span></h2> 
	<div class="box"> 
		<div class="inbox"> 
			<table cellspacing="0"> 
			<thead> 
				<tr> 
					<th class="tcl" scope="col">����</th> 
					<th class="tcl" scope="col">�������� � ����������</th> 
				</tr> 
			</thead> 
			<tbody> 
			{% for entry in entries %}
				<tr> 
					<td class="tcl"> 
						<div class="intd"> 
							<div class="icon"><div class="nosize"><!-- --></div></div> 
							<div class="tclcon"> 
								<a href='{{ entry.topic_link }}'>{{ entry.subject }}</a> <span class='byuser'>�������&nbsp;{{ entry.user }}</span>
							</div> 
						</div> 
					</td>
					<td class="tc2">{{ entry.message }}</td> 
				</tr>
			{% else %}
				<tr> 
					<td class="tcl"> 
						<div class="intd"> 
							<div class="icon"><div class="nosize"><!-- --></div></div> 
							<div class="tclcon"> 
								�� ������ ������� <b>'.$get_url.'</b> ������ �� �������
							</div> 
						</div> 
					</td>
					<td class="tc2"></td> 
				</tr>
			{% endfor %}
			</tbody> 
			</table> 
		</div> 
	</div> 
</div>
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<div class="clearer"></div> 
	</div> 
</div>
{% endif %}