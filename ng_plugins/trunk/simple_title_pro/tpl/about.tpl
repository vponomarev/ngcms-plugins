<script type="text/javascript">
function ChangeOption(selectedOption) {
	document.getElementById('about').style.display = (selectedOption == 'about')?"block":"none";
	document.getElementById('author').style.display = (selectedOption == 'author')?"block":"none";
	document.getElementById('acknowledgments').style.display = (selectedOption == 'acknowledgments')?"block":"none";
	document.getElementById('support').style.display = (selectedOption == 'support')?"block":"none";
}
</script>
<input type="button" onmousedown="javascript:ChangeOption('about')" value="� �������" class="button" />
<input type="button" onmousedown="javascript:ChangeOption('author')" value="������" class="button" />
<input type="button" onmousedown="javascript:ChangeOption('acknowledgments')" value="�������������" class="button" />
<input type="button" onmousedown="javascript:ChangeOption('support')" value="���������" class="button" />

<fieldset id="author" style="display: none;" class="admGroup">
<legend class="title">������</legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<dl>
	<dt><center><strong>Nail' Davydov</strong></center></dt>
	<dt><center><strong><a href="http://rozard.net" target="_blank">http://rozard.net</a></strong></center></dt>
	<dt><center><strong><a href="http://rozard.ngdemo.ru/" target="_blank">http://rozard.ngdemo.ru/</a></strong></center></dt><br />
	<dt><center>� 2011-2012 Nail' Davydov</center></dt>
</dl>
</table>
</fieldset>


<fieldset id="about" class="admGroup">
<legend class="title">� �������</legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<dl>
	<dt><center><strong>simple_title_pro, ������������ ��������� ��� ������ �����</strong></center></dt><br />
	<dt><center><a href="/engine/admin.php?mod=extra-config&plugin=simple_title_pro&action=license" target="_blank"><b>������������ ����������</b></a></center></dt><br />
	<dt><center>� 2011-2012 Nail' Davydov</center></dt>
</dl>
</table>
</fieldset>

<fieldset id="acknowledgments" style="display: none;" class="admGroup">
<legend class="title">�������������</legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<dl>
	<dt><strong>������� � �����:</strong></dt>
	<dd>��������� -(<a href="http://ngcms.ru/forum/profile.php?id=435">�����</a>)</dd>
	<dt><strong>������� � �����:</strong></dt>
	<dd>- - (<a href="http://ngcms.ru/forum/profile.php?id=65">tayzer</a>)</dd>
	<dt><center>� 2011-2012 Nail' Davydov</center></dt>
</dl>
</table>
</fieldset>
<fieldset id="support" style="display: none;" class="admGroup">
<legend class="title">���������</legend>
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<dl>
	<dt><strong>�� ���� �������� � ������������ ���������� ��: <a href="http://ngcms.ru/forum/viewtopic.php?id=2055" target="_blank"><b>simple_title_pro</b></a></strong></dt>
	<dt><center>� 2011-2012 Nail' Davydov</center></dt>
</dl>
</table>
</fieldset>