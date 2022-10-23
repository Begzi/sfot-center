# sfot-center
php 7.4<br>
Использовал при разработке сайта Windows 10, OpenServer, MySql 3306 порт. <br>
<br>
Не авторизованным пользователям доступны 2 url, http:localhost/login и http:localhost/register. Обрабатывает запросы class UserController<br>
Имеет 4 метода, login и register показывают страницы для ввода данных. loginPost и registerPost для обработки POST запроса<br>
У авторизованных же доступны 10 страниц. <br>
Просмотр главной странцы, по факту все рецепты. (http:localhost/index, http:localhost/index/page/{$id} для пагинации)<br>
http:localhost/recipe/* для просмотра, добавление, редактирования и удаления рецептов. Обрабатывает запросы class RecipeController<br>
Имеет 7 метода: index (просмотр всех), view (просмотр одного), add (показывает страницу для ввода данных), storage (для обработки POST запроса),<br>
edit (показывает страницу для релактирования данных), update (для обработки POST запроса), delete (get запрос для удаления рецепта)<br>
<br>
http:localhost/ingredient/* для просмотра, добавление, редактирования и удаления рецептов. Обрабатывает запросы class IngredientController<br>
Имеет 7 метода, функции те же как у RecipeController<br>
<br>
Для реализации требования "Один и тот же ингредиент может присутствовать в нескольких рецептах" нужна связь многое ко многих<br>
Есть таблица users, recipe, ingredient и recipe_ingredient (таблица хранения связей между recipe и ingredient)<br>
<br>
Сначала добавляются ингредиенты, без ингредиентов рецепты не добавяться<br>
<br>
Для API взаимодействия используется http:localhost/api/*<br>
http:localhost/api/recipe/* для просмотра, добавление, редактирования и удаления рецептов. Обрабатывает запросы class RecipeAPIController<br>
Имеет 5 метода: index (просмотр всех), view (просмотр одного), storage (для обработки POST запроса),<br>
update (для обработки POST запроса), delete (get запрос для удаления рецепта)<br>
http:localhost/api/ingredient/* для просмотра, добавление, редактирования и удаления рецептов. Обрабатывает запросы class ingredientAPIController<br>
Имеет 5 метода: index (просмотр всех), view (просмотр одного), storage (для обработки POST запроса),<br>
update (для обработки POST запроса), delete (get запрос для удаления рецепта)<br>
<br>
<br>
Весь список url:<br>
http:localhost/login<br>
http:localhost/register<br>
http:localhost/logout<br>
http:localhost/index<br>
http:localhost/index/page/{$id}<br>
http:localhost/recipe/view/{$id}<br>
http:localhost/recipe/add<br>
http:localhost/recipe/storage<br>
http:localhost/recipe/edit/{$id}<br>
http:localhost/recipe/update/{$id}<br>
http:localhost/recipe/delete/{$id}<br>
http:localhost/ingredient/index<br>
http:localhost/ingredient/index/page/{$id}<br>
http:localhost/ingredient/view/{$id}<br>
http:localhost/ingredient/add<br>
http:localhost/ingredient/storage<br>
http:localhost/ingredient/edit/{$id}<br>
http:localhost/ingredient/update/{$id}<br>
http:localhost/ingredient/delete/{$id}<br>
API:<br>
http:localhost/api/recipe/index<br>
http:localhost/api/recipe/index/page/{$id}<br>
http:localhost/api/recipe/view/{$id}<br>
http:localhost/api/recipe/storage<br>
http:localhost/api/recipe/update/{$id}<br>
http:localhost/api/recipe/delete/{$id}<br>
http:localhost/api/ingredient/index<br>
http:localhost/api/ingredient/index/page/{$id}<br>
http:localhost/api/ingredient/view/{$id}<br>
http:localhost/api/ingredient/storage<br>
http:localhost/api/ingredient/update/{$id}<br>
http:localhost/api/ingredient/delete/{$id}<br>
<br>
P.S. Проверки на корректность введённых данных есть, но его мало. Для проверок введённых данных для обычного пользователя. <br>
На безопасность мало упора, главное чтобы работало, с таким мыслями выполнял задание.
