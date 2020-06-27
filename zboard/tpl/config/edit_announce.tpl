<script src="{tpl_home}/plugins/zboard/upload/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{tpl_home}/plugins/zboard/upload/uploadifive/uploadifive.css">
<link rel="stylesheet" href="{tpl_home}/plugins/zboard/tpl/config/capty/jquery.capty.css" type="text/css" />
<script src="{tpl_home}/plugins/zboard/upload/uploadifive/jquery.uploadifive.min.js" type="text/javascript"></script>
<script type="text/javascript" src="{tpl_home}/plugins/zboard/tpl/config/capty/jquery.capty.min.js"></script>

{error}
<form method="post" action="" name="form" enctype="multipart/form-data">
<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
<td width="50%" class="contentEntry1">Заголовок объявления<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="40" name="announce_name" value="{announce_name}"  /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Автор<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="text" size="40"  name="author" value="{author}"  /></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Период объявления<br /><small></small></td>
<td width="50%" class="contentEntry2">
<select name="announce_period">
{list_period}
</select></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Категория<br /><small></small></td>
<td width="50%" class="contentEntry2"><select name="cat_id">
{options}
</select>
</td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Текст объявления<br /><small></small></td>
<td width="50%" class="contentEntry2"><textarea type="text" name="announce_description" cols="100" rows="10">{announce_description}</textarea></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Контакты<br /><small></small></td>
<td width="50%" class="contentEntry2"><textarea type="text" name="announce_contacts" cols="100" rows="10">{announce_contacts}</textarea></td>
</tr>
<tr>
<td width="50%" class="contentEntry1">Прикрепить изображения<br /><small></small></td>
<td width="50%" class="contentEntry2">

<script type="text/javascript">
$(document).ready(function() {

    var count = 0;
    $('#file_upload').uploadifive({
        'auto'             : false,
        'formData'         : {
                               'id' : $("#txtdes").val()
                             },
        'queueID'          : 'queue',
        'uploadScript'     : '/engine/plugins/zboard/upload/libs/subirarchivo.php?id={id}',
        'onUpload' : function(filesToUpload) {
                count = 0;
            },
        'onUploadComplete' : function(file, data) {
                //$('.uploadifive-queue-item').appendChild('<img src="../../../uploads/galerias/'+data+'" width=100 >');
                //alert('../../../uploads/galerias/'+data);
                //$('#uploadifive-file_upload-file-'+count).html('<img src="../../../uploads/galerias/'+data+'" width=100 >');
                count++;
            },
        'onQueueComplete' : function(uploads) {
            //$("#txtdes").val();
            //location.reload();
        }
    });


$('.fix').capty({
   cWrapper:  'capty-tile',
   height:   36,
   opacity:  .6
 });


});

</script>

<input type="hidden" id="txtdes" name="txtdes" value="{id}" />
<div id="queue">
</div>
<input id="file_upload" name="file_upload" type="file" multiple="true">

</td>
</tr>

<tr>
<td width="50%" class="contentEntry1">Прикрепленные изображения<br /><small></small></td>
<td width="50%" class="contentEntry2">
<table>
<tr>
{entriesImg}
</tr>
</table>
</td>
</tr>

<tr>
<td width="50%" class="contentEntry1">Активировать объявление?<br /><small></small></td>
<td width="50%" class="contentEntry2"><input type="checkbox" name="announce_activeme" {announce_activeme} value="1" > </td>
</tr>

</table>




<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr><td width="100%" colspan="2">&nbsp;</td></tr>
<tr>
<td width="100%" colspan="2" class="contentEdit" align="center">
<input type="submit" name="submit" value="Отредактировать" onclick="javascript:$('#file_upload').uploadifive('upload')" class="button" />
<input type="submit" name="delme" value="Удалить" class="button" />
</td>
</tr>
</table>
</form>
