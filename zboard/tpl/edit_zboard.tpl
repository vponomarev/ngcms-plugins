<script src="{{tpl_home}}/plugins/zboard/upload/js/jquery-1.7.2.min.js" type="text/javascript"></script>
<link rel="stylesheet" type="text/css" href="{{tpl_home}}/plugins/zboard/upload/uploadifive/uploadifive.css">
<link rel="stylesheet" href="{{tpl_home}}/plugins/zboard/tpl/config/capty/jquery.capty.css" type="text/css" />
<script src="{{tpl_home}}/plugins/zboard/upload/uploadifive/jquery.uploadifive.min.js" type="text/javascript"></script>
<script type="text/javascript" src="{{tpl_home}}/plugins/zboard/tpl/config/capty/jquery.capty.min.js"></script>

{% if (error) %}
<div class="feed-me">
{{error}}
</div>
{% endif %}

<script language="javascript" type="text/javascript">
var currentInputAreaID = 'content_description';
</script>
<div class="comment">
<h3><span>Редактирование объявления</span></h3>
<form method="post" action="" class="comment-form" name="form" enctype="multipart/form-data">
<ul class="comment-author">
<li class="item clearfix">
<input type="text" name="announce_name" value="{{announce_name}}" tabindex="1">
<label>Заголовок объявления <i>(*)</i></label>
</li>
<li class="item clearfix">
<input type="text" name="author" value="{{author}}" tabindex="1">
<label>Автор <i>(*)</i></label>
</li>
<li class="item clearfix">
<select name="announce_period">
{{list_period}}
</select>
<label>Период объявления <i>(*)</i></label>
</li>
<li class="item clearfix">
<select name="cat_id">
{{options}}
</select>
<label>Категория <i>(*)</i></label>
</li>
</ul>
<span class="textarea">
<label>Описание объявления <i>(*)</i></label><br/><br/>
{{bb_tags}}
<textarea type="text" id="content_description" name="announce_description" tabindex="4">{{announce_description}}</textarea>

</span>
<span class="textarea">
Контакты <i>(*)</i>
<textarea type="text" name="announce_contacts" tabindex="4">{{announce_contacts}}</textarea>
</span>

<ul class="comment-author">
<li class="item clearfix">
<script type="text/javascript">
$(document).ready(function() {

    var count = 0;
    $('#file_upload').uploadifive({
        'auto'             : false,
        'formData'         : {
                               'id' : $("#txtdes").val()
                             },
        'queueID'          : 'queue',
        'uploadScript'     : '/engine/plugins/zboard/upload/libs/subirarchivo.php?id={{id}}',
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
<label>Прикрепить изображения</label><br/><br/>
<input type="hidden" id="txtdes" name="txtdes" value="{{id}}" />
<div id="queue">
</div>
<input id="file_upload" name="file_upload" type="file" multiple="true">
</li>

<li class="item clearfix">
<label>Прикрепленные изображения</label><br/><br/>
<table>
<tr>
{% for entry in entriesImg %}
<td style="padding-left:5px;">
<a href='{{entry.home}}/uploads/zboard/{{entry.filepath}}' target='_blank'><img class="fix" name="#content-target-{{entry.pid}}" src='{{entry.home}}/uploads/zboard/thumb/{{entry.filepath}}' width='150' height='120'></a>
<div id="content-target-{{entry.pid}}">
<a href="{{entry.del}}">[x]</a>&nbsp;&nbsp;&nbsp;
</div>
</td>
{% endfor %}
</tr>
</table>
</li>

</ul>
<span class="submit"><button name="submit" type="submit"  tabindex="5" onclick="javascript:$('#file_upload').uploadifive('upload')" >Отправить</button></span>
<span class="submit"><button tabindex="5" type="reset" >Сброс</button></span>
</form>
</div>
