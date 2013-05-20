<div class="blockform">
	<h2><span>����� �������������</span></h2>
	<div class="box">
	<form id="userlist" method="get" action="">
		<div class="inform">
			<fieldset>
				<legend>����� � ���������� �������������</legend>
				<div class="infldset">
					<label class="conl">���<br /><input type="text" name="username" value="{{ username }}" size="25" maxlength="25" /><br /></label>
					<label class="conl">������
					<br /><select name="show_group">
						<option value="-1" {% if (show_group_) %}selected{% endif %}>��� ������������</option>
						<option value="1" {% if (show_group_1) %}selected{% endif %}>��������������</option>
						<option value="2" {% if (show_group_2) %}selected{% endif %}>����. ���������</option>
						<option value="3" {% if (show_group_3) %}selected{% endif %}>���������</option>
						<option value="4" {% if (show_group_4) %}selected{% endif %}>������������</option>
					</select>
					<br /></label>
					<label class="conl">����������� ��
					<br /><select name="sort_by">
						<option value="username" {% if (sort_by_username) %}selected{% endif %}>���</option>
						<option value="registered" {% if (sort_by_registered) %}selected{% endif %}>���������������</option>
						<option value="num_posts" {% if (sort_by_num_posts) %}selected{% endif %}>���-�� ���������</option>
					</select>
					<br /></label>
					<label class="conl">����������� ��
					<br /><select name="sort_dir">
						<option value="ASC" {% if (sort_dir_ASC) %}selected{% endif %}>�����������</option>
						<option value="DESC" {% if (sort_dir_DESC) %}selected{% endif %}>��������</option>
					</select>
					<br /></label>
					<p class="clearb">������� ��� ������������ ��� ������ �/��� ������� ������. ��� ������������ ����� ���� ������. ����������� * � �������� ������� ��� ���������� ����������. ������������ ������������� �� �����, ���� ����������� ��� ���������� ���������� ��������� � �����������  �� �����������/��������.</p>
				</div>
			</fieldset>
		</div>
		<p><input type="submit" name="submit" value="���������" /></p>
	</form>
	</div>
</div>
<div class="linkst"> 
	<div class="inbox"> 
		<p class="pagelink conl">{% if (pages.true) %}{% if (prevlink.true) %}{{ prevlink.link }}{% endif %}{{ pages.print }}{% if (nextlink.true) %}{{ nextlink.link }}{% endif %}{% endif %}</p> 
		<div class="clearer"></div> 
	</div> 
</div>
<div id="users1" class="blocktable"> 
	<h2><span>������������</span></h2> 
	<div class="box"> 
		<div class="inbox"> 
			<table cellspacing="0"> 
				<thead> 
					<tr> 
						<th class="tcl" scope="col">���</th> 
						<th class="tc2" scope="col">������</th> 
						<th class="tc3" scope="col">���������</th> 
						<th class="tcr" scope="col">���������������</th> 
					</tr> 
				</thead> 
				<tbody>
				{% for entry in entries %}
					<tr> 
						<td class="tcl"><a href='{{ entry.profile_link }}'>{{ entry.profile }}</a></td> 
						<td class="tc2">{{ entry.status }}</td> 
						<td class="tc3">{{ entry.num_post }}</td> 
						<td class="tcr">{{ entry.date|date("d-m-Y") }}</td> 
					</tr>
				{% else %}
					<tr> 
						<td class="tcl">�� ������ ������� ������ �� �������.</td> 
						<td class="tc2"></td> 
						<td class="tc3"></td> 
						<td class="tcr"></td> 
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