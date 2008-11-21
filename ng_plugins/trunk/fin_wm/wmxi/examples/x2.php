<?php
	require_once("_header.php");

	# ��������� � ��������� ������ �����
	if (count($_POST) > 0) {

		$response = $wmxi->X2(
			intval($_POST["tranid"]),    # ����� �������� � ������� ����� �����������; ����� ����� ����� ��� �����, ������ ���� ����������
			$_POST["pursesrc"],          # ����� �������� � �������� ����������� ������� (�����������)
			$_POST["pursedest"],         # ����� ��������, �� ������� ����������� ������� (����������)
			floatval($_POST["amount"]),  # ����� � ��������� ������ ��� ���������� ��������
			intval($_POST["period"]),    # ����� �� 0 �� 255 ��������; 0 - ��� ���������
			trim($_POST["pcode"]),       # ������������ ������ �� 0 �� 255 ��������; ������� � ������ ��� ����� �� �����������
			trim($_POST["desc"]),        # ������������ ������ �� 0 �� 255 ��������; ������� � ������ ��� ����� �� �����������
			intval($_POST["wminvid"])    # ����� ����� > 0; ���� 0 - ������� �� �� �����
		);

		# ��������������� ����� ������� � ���������. ������� ���������:
		# - XML-����� �������
		# - ���������, ������������ �� �����. �� ��������� ������������ UTF-8
		$structure = $parser->Parse($response, DOC_ENCODING);

		# ����������� ������� ��������� � ����� ������� ��� �������.
		# �� ������������� ��������� ����� �������������� � � �����������, ���� �� ��������
		# ��������� ���������� ����� (��������, ������ ����������)
		# ���� ���������� � ���������� XML-����� ������ ���, �� ������ �������� �����
		# ���������� � false - � ����� ������ ��������� ������ ����� ����������
		$transformed = $parser->Reindex($structure, true);

	}


?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">
<html>
<head>
	<title>X2</title>
	<meta http-equiv="Content-Type" content="text/html; charset=<?=DOC_ENCODING;?>" />
	<meta name="author" content="DKameleon" />
	<meta name="site" content="http://my-tools.net/wmxi/" />
	<link rel="stylesheet" type="text/css" href="style.css" />
</head>
<body>
	��������� �������� ����������:
	<a href="http://webmoney.ru/rus/developers/interfaces/xml/purse2purse/index.shtml">http://webmoney.ru/rus/developers/interfaces/xml/purse2purse/index.shtml</a>
	<br />

	<form action="" method="post">

		<label>����� ��������:</label>
		<input type="text" name="tranid" value="2" />
		<br/>

		<label>����� �������� � �������� ����������� ������� (�����������):</label>
		<input type="text" name="pursesrc" value="" />
		<br/>

		<label>����� ��������, �� ������� ����������� ������� (����������):</label>
		<input type="text" name="pursedest" value="" />
		<br/>

		<label>����������� �����:</label>
		<input type="text" name="amount" value="0.01" />
		<br/>

		<label>���� ��������� ������ � ����:</label>
		<input type="text" name="period" value="0" />
		<br/>

		<label>��� ��������� ������:</label>
		<input type="text" name="pcode" value="" />
		<br/>

		<label>�������� ������������� ������ ��� ������:</label>
		<input type="text" name="desc" value="������������ X2 wmxi" />
		<br/>

		<label>����� ����� (� ������� WebMoney), �� �������� ����������� �������:</label>
		<input type="text" name="wminvid" value="0" />
		<br/>

		<input type="submit" value="��������� �������" />
		<br/>

	</form>

	<pre><?=htmlspecialchars(@$response, ENT_QUOTES);?></pre>
	<!--pre><?=htmlspecialchars(print_r(@$structure, true), ENT_QUOTES);?></pre-->
	<!--pre><?=htmlspecialchars(print_r(@$transformed, true), ENT_QUOTES);?></pre-->

	<pre><!-- ������ � ���������� �������� ������������� ������� ����� ��������� ������ � ������� -->
		����� ��������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["tranid"], ENT_QUOTES); ?></b>
		�����������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["pursesrc"], ENT_QUOTES); ?></b>
		����������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["pursedest"], ENT_QUOTES); ?></b>
		�����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["amount"], ENT_QUOTES); ?></b>
		���������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["comiss"], ENT_QUOTES); ?></b>
		��� ��������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["opertype"], ENT_QUOTES); ?></b>
		���� ���������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["period"], ENT_QUOTES); ?></b>
		����� �����: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["wminvid"], ENT_QUOTES); ?></b>
		��������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["desc"], ENT_QUOTES); ?></b>
		������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["datecrt"], ENT_QUOTES); ?></b>
		������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["operation"]["dateupd"], ENT_QUOTES); ?></b>
		��� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retval"], ENT_QUOTES); ?></b>
		�������� ������: <b><?=htmlspecialchars(@$transformed["w3s.response"]["retdesc"], ENT_QUOTES); ?></b>
	</pre>

</body>
</html>
