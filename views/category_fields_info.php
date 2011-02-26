<div id="mm_fields_info">
<h3>Правила описания полей категории</h3>

<p><strong>Обязательные поля:</strong></p>

<p>1) Имя - указывается в квадратных скобках (в имени можно использовать только латинские буквы без пробелов и спец. символов).</p>

<p>2) Тип поля в формате.<br />
<code>type=значение</code><br />
Допустимые значения text, checkbox, select, textarea.</p>

<p>3) Заголовок. Задается в виде:<br />
<code>title=значение</code><br />
В качестве значения можно ввести любой текст.</p>

<p>4) Список опций. Указывается для поля типа select.<br />
<code>values=значение 1|значение 2</code><br />
Значения необходимо разделять символом «|»</p>

<p><strong>Не обязательные поля.</strong></p>

<p>1) Тип поля в для формы поиска.<br />
<code>utype=значение</code><br />
Допустимые значения text, checkbox, select, textarea. По-умолчанию совпадает с полем type.<br />
Используется чтобы упростить ввод значений в форму поиска. Например, если указан utype=select, то плагин автоматически сформирует список из всех введённых значений для данного поля.</p>

<p><strong>Пример</strong>. Ниже показан список полей, которые можно использовать для описания параметров компьютерной мыши.</p>

<p><code>[color]<br />
type=text<br />
title=Цвет<br />
utype=select</code></p>

<p><code>[scroll]<br />
type=checkbox<br />
title=Наличие скролла</code></p>

<p><code>[interface]<br />
type=select<br />
title=Интерфейс<br />
values=USB|PS/2</code></p>

<p><code>[additional_info]<br />
type=textarea<br />
title=Дополнительная информация</code></p>
</div>