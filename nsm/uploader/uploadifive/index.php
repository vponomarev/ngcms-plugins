<!DOCTYPE HTML>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>UploadiFive Test</title>
<script src="jquery.js" type="text/javascript"></script>
<script src="jquery.uploadifive.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="uploadifive.css">
<style type="text/css">
#queue {
	border: 1px solid #E5E5E5;
	height: 177px;
	overflow: auto;
	margin-bottom: 10px;
	padding: 0 3px 3px;
	width: 300px;
}
</style>
</head>

<body>
	<h1>UploadiFive Demo</h1>

		<div id="queue">

		</div>

	<input id="file_upload" name="file_upload" type="file" multiple="true">

	<a style="position: relative; top: 8px;" href="javascript:$('#file_upload').uploadifive('upload')">Upload Files</a>

	<script type="text/javascript">
		<?php 
			$template = '<div class="uploadifive-queue-item"><div id="foto"></div><span class="filename"></span> <br><span class="fileinfo"></span><div class="close"></div></div>';
		 ?>
		<?php $timestamp = time();?>

		$(function() {
			var count = 0;
			$('#file_upload').uploadifive({
				'auto'             : false,
				//'itemTemplate' 	   : '<?php echo $template; ?>',
				'checkScript'      : 'check-exists.php',
				'formData'         : {
									   'timestamp' : '<?php echo $timestamp;?>',
									   'token'     : '<?php echo md5('unique_salt' . $timestamp);?>'
				                     },
				'queueID'          : 'queue',
				'uploadScript'     : 'uploadifive.php',
				'onUpload' : function(filesToUpload) {
				        count = 0;
				    },			
				'onUploadComplete' : function(file, data) { 
						//$('.uploadifive-queue-item').appendChild('<img src="../../../uploads/galerias/'+data+'" width=100 >');
						//alert('../../../uploads/galerias/'+data);
						//$('#uploadifive-file_upload-file-'+count).html('<img src="../../../uploads/galerias/'+data+'" width=100 >');
						count++;
					}
			});
		});
	</script>
</body>
</html>
