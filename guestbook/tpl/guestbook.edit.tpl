<div class="container">
  {% if (error) %}
    <div class="msge alert alert-info">{{ error }}</div>
  {% endif %}
  <form method='post' action='{{ php_self }}?action=edit'>
    <div class="row">
      <label for="author">Автор</label>
      <input type='text' name='author' id='author' value='{{ author }}' required>
    </div>
    {% for field in fields %}
    <div class="row">
      <label for="{{ field.id }}">{{ field.name }} {% if field.required %}<b style="color:red">*</b>{% endif %}</label>
      <input type="text" id="{{ field.id }}" name="{{ field.id }}" value="{{ field.value }}" {% if field.required %}required{% endif %} />
    </div>
    {% endfor %}
    <div class="row">
      <label for="content">Сообщение</label><br>
      <textarea name='content' id="content" style='width: 95%;' rows='8' required>{{ message }}</textarea>
    </div>
    <div class="row">
      <label for="answer">Ответ</label><br>
      <textarea name='answer' id="answer" style='width: 95%;' rows='8'>{{ answer }}</textarea>
    </div>
    <input type='hidden' name='id' value='{{id}}'>
    <input type='submit' value='Отредактировать'>
  </form>
</div>
