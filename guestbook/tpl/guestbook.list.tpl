<section class="content container">
  <div class="row">

    <article class="content-text col-xs-12">
      <h1>Отзывы</h1>
      <h2>Мы дорожим мнением наших клиентов</h2>
      <p>Гарантия от 100 дней на установленные комплектующие и исправленный дефект. В случае повторного поступления iPhone с дефектом, который устранялся ранее нашим сервисом, проводится диагностика, по результатам которой устанавливается причина дефекта/неисправности.</p>
    </article><!-- /.content-text -->

  </div>
</section><!-- /.content -->

<section class="reviews-page container">

{% if (errors|length > 0) %}
    {% for error in errors %}
<div class="msge alert alert-error">{{error}}<br/></div>
    {% endfor %}
{% endif %}

{% if (success|length > 0) %}
    {% for succ in success %}
<div class="msgi alert alert-success">{{succ}}<br/></div>
    {% endfor %}
{% endif %}

  <div class="reviews-page-list row">

{% if (total_count > 0) %}

    {% for entry in entries %}
    <div class="review col-xs-12 col-sm-6">
      <div class="review-inner">
        <div class="review-header">
          {% if entry.social %}
            {% if entry.social.Vkontakte.photo %}
              {% set avatar = entry.social.Vkontakte.photo %}
            {% elseif entry.social.Facebook.photo %}
              {% set avatar = entry.social.Facebook.photo %}
            {% elseif entry.social.Instagram.photo %}
              {% set avatar = entry.social.Instagram.photo %}
            {% elseif entry.social.Google.photo %}
              {% set avatar = entry.social.Google.photo %}
            {% endif %}
          {% else %}
            {% set avatar = '/uploads/avatars/noavatar.gif' %}
          {% endif %}
          <div class="person-photo"><img src="{{ avatar }}" width="60" height="60"></div>
          <div class="person-name">{% if entry.author == 'guest' %} {{ entry.fields.firstname.value }} {{ entry.fields.lastname.value }}{% else %}{{ entry.author }}{% endif %}</div>
          <div class="review-date">{{ entry.date }}</div>
          <div class="review-subject">Ремонтировали - {{ entry.fields.item.value }}</div>
        </div>
        <div class="review-caption"><p>{{entry.message}}</p></div>
        {% if(global.user.id) and (global.user.status == '1') %}
        <div class="review-caption">
          <p>
            {{ entry.ip }} /
            <a href="{{ home }}/engine/admin.php?mod=extra-config&plugin=guestbook&action=edit_message&id={{ entry.id }}">Редактировать</a> /
            <a href="{{ entry.del }}">Удалить</a>
          </p>
        </div>
        {% endif %}
        <div class="review-social">
          <ul class="social-links social-links-default list-inline">
            {% if entry.social.Vkontakte %}
              <li class="active"><a href="{{ entry.social.Vkontakte.link }}"><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></a></li>
            {% else %}
              <li><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></li>
            {% endif %}
            {% if entry.social.Google %}
              <li class="active"><a href="{{ entry.social.Google.link }}"><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></a></li>
            {% else %}
              <li><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></li>
            {% endif %}
            {% if entry.social.Facebook %}
              <li class="active"><a href="{{ entry.social.Facebook.link }}"><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></a></li>
            {% else %}
              <li><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></li>
            {% endif %}
            {% if entry.social.Instagram %}
              <li class="active"><a href="{{ entry.social.Instagram.link }}"><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></a></li>
            {% else %}
              <li><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></li>
            {% endif %}
          </ul>
        </div>
      </div>
    </div><!-- /.review -->
    {% endfor %}
{% endif %}

  </div><!-- /.reviews-feed-list -->

{% if (total_count > perpage) %}
  <ul class="pagination">
    {{ pages }}
  </ul>
{% endif %}

</section><!-- /.reviews-page -->

{% if(use_guests) %}
<div class="container">
  <div class="msgi alert alert-success">Гостям нельзя оставлять отзывы. Зарегистрируйтесь.</siv>
</div>
{% else %}
<form name="form" method="post" action="{{ php_self }}?action=add" class="review-form verifiable-form container">
  <fieldset class="row">
    {% if(global.user.name) %}
      Ваш комментарий будет опубликован от имени <strong>{{global.user.name}}</strong>
      <input type="hidden" name="author" value="{{global.user.name}}"/>
    {% else %}
    <div class="col-xs-12 col-sm-4 col-md-3">
      <div class="form-group">
        <label>{{ fields.firstname.name }}</label>
        <input type="text" class="form-control required" placeholder="{{ fields.firstname.placeholder }}" name="{{ fields.firstname.id }}" value="{{ fields.firstname.default_value }}">
      </div>
    </div>

    <div class="col-xs-12 col-sm-4 col-md-3">
      <div class="form-group">
        <label>{{ fields.lastname.name }}</label>
        <input type="text" class="form-control required" placeholder="{{ fields.lastname.placeholder }}" name="{{ fields.lastname.id }}" value="{{ fields.lastname.default_value }}">
      </div>
    </div>

    <input type="hidden" name="author" value="guest"/>

  {% endif %}

    {% if(global.user.name) %}
      <div class="col-xs-12 col-sm-12 col-md-12">
    {% else %}
      <div class="col-xs-12 col-sm-4 col-md-3 col-md-offset-3">
    {% endif %}
      <div class="form-group">
        <label>{{ fields.item.name }}</label>
        <input type="text" class="form-control required" placeholder="{{ fields.item.placeholder }}" name="{{ fields.item.id }}" value="{{ fields.item.default_value }}">
      </div>
    </div>

    <div class="col-xs-12 col-md-12">
      <div class="form-group">
        <label>Ваш отзыв</label>
        <textarea name="content" id="content" class="form-control required" placeholder="{{placeholder.message}}">{{field.message}}</textarea>
      </div>
    </div>

    <div class="col-xs-12 col-sm-3 col-md-2">
      <div class="form-group">
        <button name="submit" type="submit" class="btn btn-danger">Отправить отзыв</button>
        <input type="hidden" name="ip" value="{{ip}}"/>
      </div>
    </div>

    <div class="social-links-wrap col-xs-12 col-sm-3 col-md-3 col-lg-2">
      <ul class="social-links social-links-default list-inline">
        <li id="Vkontakte_li"><a id="vk" href="#"><svg class="icon icon-vk"><use xlink:href="#icon-vk"></use></svg></a></li>
        <li id="Google_li"><a id="gg" href="#"><svg class="icon icon-google"><use xlink:href="#icon-google"></use></svg></a></li>
        <li id="Facebook_li"><a id="fb" href="#"><svg class="icon icon-facebook"><use xlink:href="#icon-facebook"></use></svg></a></li>
        <li id="Instagram_li"><a id="ig" href="#"><svg class="icon icon-instagram"><use xlink:href="#icon-instagram"></use></svg></a></li>
      </ul>
    </div>

    <div class="form-caption col-xs-12 col-md-7">
      <p>Прикрепите свой профиль в социальной сети, сделайте отзыв более убедительным!<br>
      <span class="text-muted">Просто нажмите на иконку соцсети которую хотите прикрепить</span></p>
    </div>

    {% if(use_captcha) %}{{captcha}}{% endif %}

    <input type="hidden" name="Vkontakte_id" id="Vkontakte_id" value="" />
    <input type="hidden" name="Facebook_id" id="Facebook_id" value="" />
    <input type="hidden" name="Google_id" id="Google_id" value="" />
    <input type="hidden" name="Instagram_id" id="Instagram_id" value="" />
  </fieldset>
</form>
<script>
  (function() {

    function hasClass(element, cls) {
      return (' ' + element.className + ' ').indexOf(' ' + cls + ' ') > -1;
    }

    var fb = document.getElementById('fb'),
        vk = document.getElementById('vk'),
        gg = document.getElementById('gg'),
        ig = document.getElementById('ig');

    fb.onclick = function(ev) {
      ev.preventDefault();
      var li = document.getElementById('Facebook_li');
      if (hasClass(li, 'active')) {
        li.className = '';
        document.getElementById('Facebook_id').value = '';
        return;
      } else {
        var n = window.open('{{ home }}/plugin/guestbook/social/?provider=Facebook', 'FB', 'width=420,height=400');
        n.focus();
      }
    }
    vk.onclick = function(ev) {
      ev.preventDefault();
      var li = document.getElementById('Vkontakte_li');
      if (hasClass(li, 'active')) {
        li.className = '';
        document.getElementById('Vkontakte_id').value = '';
        return;
      } else {
        var n = window.open('{{ home }}/plugin/guestbook/social/?provider=Vkontakte', 'VK', 'width=420,height=400');
        n.focus();
      }
    }
    gg.onclick = function(ev) {
      ev.preventDefault();
      var li = document.getElementById('Google_li');
      if (hasClass(li, 'active')) {
        li.className = '';
        document.getElementById('Google_id').value = '';
        return;
      } else {
        var n = window.open('{{ home }}/plugin/guestbook/social/?provider=Google', 'Google', 'width=420,height=400');
        n.focus();
      }
    }
    ig.onclick = function(ev) {
      ev.preventDefault();
      var li = document.getElementById('Instagram_li');
      if (hasClass(li, 'active')) {
        li.className = '';
        document.getElementById('Instagram_id').value = '';
        return;
      } else {
        var n = window.open('{{ home }}/plugin/guestbook/social/?provider=Instagram', 'Instagram', 'width=420,height=400');
        n.focus();
      }
    }
  })();
</script>
{% endif %}
