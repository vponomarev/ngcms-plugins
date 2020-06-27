{% if (error) %}
<div class="feed-me">
{{error}}
</div>
{% endif %}

<div class="comment">
<h3><span>Оплата VIP объявления</span></h3>
<form method="post" action="" class="comment-form" name="form" enctype="multipart/form-data">
<ul class="comment-author">

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
</form>
</div>
