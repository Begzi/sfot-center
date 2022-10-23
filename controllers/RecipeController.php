<? 
use MiladRahimi\PhpRouter\View\View;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\JsonResponse;

function queryToGetIngredientsAll($link){  
    
    $query_ingredient_list = "SELECT * FROM `ingredient`";
    $query_ingredient_list = mysqli_query($link, $query_ingredient_list)
        or die("Ошибка выбора списка ингредиентов.<br>" . mysqli_error($link)); 
    $ingredient_list = mysqli_fetch_all($query_ingredient_list, MYSQLI_ASSOC);    
    
    return $ingredient_list;
}
function queryToGetSelectedIngredients($id, $link, $delete = FALSE){  
    $query_recipe_ingredient_list = "SELECT * FROM `recipe_ingredient` 
            WHERE recipe_id = " . $id;
    $query_recipe_ingredient_list = mysqli_query($link, $query_recipe_ingredient_list)
        or die("Ошибка выбора списка recipe_ingredient.<br>" . mysqli_error($link)); 
    $recipe_ingredient_list = mysqli_fetch_all($query_recipe_ingredient_list, MYSQLI_ASSOC); 
    if ($delete){  //Если нужно достать таблицу выбранных ингредиентов. Оно нужна для удаления
        return  $recipe_ingredient_list;
    }
    $selected_ingredients = '';
    for ($i = 0; $i < count($recipe_ingredient_list); $i++){
        if ($i != 0){
            $selected_ingredients = $selected_ingredients . ", ";
        } 
        $selected_ingredients = $selected_ingredients . $recipe_ingredient_list[$i]['ingredient_id'];
    }

    return  $selected_ingredients;
}
function queryToGetRecipes($id, $link){    

    $query_recipe_list = "SELECT * FROM `recipe`";
    $query_recipe_list = mysqli_query($link, $query_recipe_list)
        or die("Ошибка выбора списка рецепта.<br>" . mysqli_error($link)); 
    $recipe_list_count = mysqli_fetch_all($query_recipe_list, MYSQLI_ASSOC);  

    $query_recipe_list = "SELECT * FROM `recipe` 
                LIMIT ". strval(5) ." OFFSET ". strval(intval($id - 1) * 5);  
    $query_recipe_list = mysqli_query($link, $query_recipe_list)
        or die("Ошибка выбора списка рецепта.<br>" . mysqli_error($link)); 
    $recipe_list = mysqli_fetch_all($query_recipe_list, MYSQLI_ASSOC);  
    $rows['recipe_list_count'] = count($recipe_list_count);
    $rows['recipe_list'] = $recipe_list;
    return $rows;
}
function queryGetRecipe($id, $link){

    $query_recipe_list = "SELECT * FROM `recipe` WHERE id = " . $id;
    $query_recipe_list = mysqli_query($link, $query_recipe_list)
        or die("Ошибка выбора списка рецепта.<br>" . mysqli_error($link)); 
    $recipe_list = mysqli_fetch_all($query_recipe_list, MYSQLI_ASSOC); 
    return  $recipe_list[0];
}
function queryToRecipeIngredientTableStorage($recipe_id, $ingredients, $link){
    $ingredients = explode(',', $ingredients);
    foreach ($ingredients as $ingredient){
        $query_recipe_ingredient ="INSERT INTO `recipe_ingredient` ( `recipe_id`, `ingredient_id`) 
        VALUES ('".$recipe_id."', '".intval($ingredient)."');";
        $result_recipe = mysqli_query($link, $query_recipe_ingredient) 
        or die("Ошибка добавления записи в БД. recipe_ingredient<br>" . mysqli_error($link)); 
    }
}
function queryToRecipeStorage($name, $photo, $description, $ingredients, $link){    
        
    $name = mysqli_real_escape_string($link, $name);
    $photo = mysqli_real_escape_string($link, $photo);
    $description = mysqli_real_escape_string($link, $description);    

    $query ="SELECT * FROM recipe WHERE name='$name'";
    $result = mysqli_query($link, $query) or die("Ошибка выбора списка рецепта" . mysqli_error($link)); 
    $row = mysqli_fetch_all($result);

    if (count($row) != 0 ){		
        return FALSE;
    }
    $query_recipe ="INSERT INTO `recipe` ( `name`, `photo`, `description`) 
                        VALUES ('".$name."', '".$photo."', '".$description."');";
    $result_recipe = mysqli_query($link, $query_recipe) or die("Ошибка добавления записи в БД.<br>" . mysqli_error($link)); 
    
    $query_recipe = 'SELECT * FROM `recipe` WHERE `name` = "' . $name . '"';   
    $query_recipe = mysqli_query($link, $query_recipe)
        or die("Ошибка выбора списка рецепта.<br>" . mysqli_error($link)); 
    $result_recipe = mysqli_fetch_all($query_recipe, MYSQLI_ASSOC); 

    queryToRecipeIngredientTableStorage($result_recipe[0]['id'], $ingredients, $link);
    return TRUE;
}
///////////Update and Delete
function queryToRecipeIngredientTableDelete($recipe_id, $link){
    $ingredients = queryToGetSelectedIngredients($recipe_id, $link, TRUE);
    if (count ($ingredients) > 0){
        $query_recipe_ingredient = "DELETE FROM `recipe_ingredient` WHERE recipe_id = " . $recipe_id;
        $result = mysqli_query($link, $query_recipe_ingredient) 
            or die("Ошибка удаления записи в БД. recipe_ingredient<br>" . mysqli_error($link)); 
    }
}
function queryToRecipeUpdate($name, $photo, $description, $ingredients, $id, $link){    
    $recipe = queryGetRecipe($id, $link);      
        
    $name = mysqli_real_escape_string($link, $name);
    $photo = mysqli_real_escape_string($link, $photo);
    $description = mysqli_real_escape_string($link, $description);
    if ($name != $recipe['name']){
        $query ="SELECT * FROM recipe WHERE `name`='$name'";
        $result = mysqli_query($link, $query) or die("Ошибка выбора списка рецепта" . mysqli_error($link)); 
        $recipe_list = mysqli_fetch_all($result);

        if (count($recipe_list) != 0 ){		
            return FALSE;
        }
    }
    $query_recipe = "UPDATE `recipe` 
    SET `name`='". $name ."',`photo`='". $photo ."',`description`='". $description ."' 
    WHERE`id`='". $recipe['id'] ."'";
    $result_recipe = mysqli_query($link, $query_recipe) or die("Ошибка добавления записи в БД. Рецепт<br>" . mysqli_error($link)); 

    queryToRecipeIngredientTableDelete($id, $link);
    queryToRecipeIngredientTableStorage($id, $ingredients, $link);
    return TRUE;
}
function queryForDeleteRecipe($id, $link){

    $recipe = queryGetRecipe($id, $link); 
    if (($recipe) === FALSE){
        mysqli_close($link);
        return FALSE;
    }
    $query_recipe = "DELETE FROM `recipe` WHERE id = " . $id;
    $result_recipe = mysqli_query($link, $query_recipe) 
        or die("Ошибка удаления записи в БД. Рецепт<br>" . mysqli_error($link)); 

    queryToRecipeIngredientTableDelete($id, $link);

    return  TRUE;
}


class RecipeController
{
    function index(View $view, $id = 1)
    {
        $link = connectBD();
        $rows = queryToGetRecipes($id, $link);    
        mysqli_close($link);    
        return $view->make('index', 
        ['recipe_list' => $rows['recipe_list'], 'recipe_list_count' => $rows['recipe_list_count']]);
    }
    function view(View $view, $id){
        $link = connectBD();
        $recipe = queryGetRecipe($id, $link);
        $recipe_ingredient_list = queryToGetSelectedIngredients($id, $link, TRUE);
        $ingredients = [];
        foreach ($recipe_ingredient_list as $recipe_ingredient){
            array_push($ingredients, queryIngredient($recipe_ingredient['ingredient_id'], $link));
        }
        mysqli_close($link);    
        return $view->make('recipe.view', ['recipe' => $recipe, 'ingredients' => $ingredients]);
    }
    function add(View $view){        
        $link = connectBD();
        $ingredients = queryToGetIngredientsAll($link);
        mysqli_close($link);    
        return $view->make('recipe.add', ['ingredients' => $ingredients]);
    }
    function storage(View $view){
        if (!(isset($_POST['name'])) || !(isset($_POST['description'])) || !(isset($_POST['ingredients']))
        || ($_POST['name'] == '' )  || ($_POST['description'] == '' )  || ($_POST['ingredients'] == '' ))
	    {	
            $link = connectBD();
            $ingredients = queryToGetIngredientsAll($link);
            mysqli_close($link);    
            return $view->make('recipe.add', 
            ['error' => 'Заполните обязательные поля (Название блюда, Способ приготовления, Ингредиенты)', 'ingredients' => $ingredients]);
        }
        
        $link = connectBD();
        $row = queryToRecipeStorage($_POST['name'], $_POST['photo'], $_POST['description'], $_POST['ingredients'], $link );
        mysqli_close($link);  

		if ($row == FALSE){			
            return $view->make('recipe.add', ['error' => 'Рецепт с таким названием есть']);
		}    
        return  new RedirectResponse('/index');
    }
    function edit(View $view, $id){
        $link = connectBD();
        $recipe = queryGetRecipe($id, $link);   
        $ingredients = queryToGetIngredientsAll($link); 
        $selected_ingredients = queryToGetSelectedIngredients($id, $link);
        mysqli_close($link);
        return $view->make('recipe.edit', 
        ['recipe' => $recipe, 'ingredients' => $ingredients, 'selected_ingredients' => $selected_ingredients]);
    }
    function update(View $view, $id){  
        if (!(isset($_POST['name'])) || !(isset($_POST['description'])) || !(isset($_POST['recipe_ingredient_id']))
        || ($_POST['name']) == '' || ($_POST['description']) == '' || ($_POST['ingredients']) == '' )
	    {	
            $link = connectBD();
            $recipe = queryGetRecipe($id, $link);   
            $ingredients = queryToGetIngredientsAll($link); 
            $selected_ingredients = queryToGetSelectedIngredients($id, $link);
            mysqli_close($link);
            return $view->make('recipe.edit', 
            [ 'error' => 'Введите все обязательные поля (Название рецепта, способ приготовления, ингредиенты', 'recipe' => $recipe, 
            'ingredients' => $ingredients, 'selected_ingredients' => $selected_ingredients]);
            
        }

        $link = connectBD();
        $row = queryToRecipeUpdate($_POST['name'], $_POST['photo'], $_POST['description'], $_POST['ingredients'], $id, $link);
        mysqli_close($link);
		if ($row == FALSE){			
            return $view->make('recipe.add', ['error' => 'Рецепт с таким названием есть']);
		}    
        return  new RedirectResponse('/recipe/view/' . $id);
    }
    function delete(View $view, $id){
        $link = connectBD();
        $row = queryForDeleteRecipe($id, $link);
        mysqli_close($link);
        if ($row == FALSE){
            return $view->make('recipe.view', 
            ['recipe' => $recipe, 'error' => 'Не найден рецепт по полученному ID']);
        }
        return  new RedirectResponse('/index');
    }
}


class RecipeAPIController
{
    function index($id = 1)
    {
        $link = connectBD();
        $rows = queryToGetRecipes($id, $link);    
        mysqli_close($link);    
        return new JsonResponse( 
        ['recipe_list' => $rows['recipe_list'], 'recipe_list_count' => $rows['recipe_list_count']]);
    }
    function view(View $view, $id){
        $link = connectBD();
        $recipe = queryGetRecipe($id, $link);
        $recipe_ingredient_list = queryToGetSelectedIngredients($id, $link, TRUE);
        $ingredients = [];
        foreach ($recipe_ingredient_list as $recipe_ingredient){
            array_push($ingredients, queryIngredient($recipe_ingredient['ingredient_id'], $link));
        }
        mysqli_close($link);    
        return new JsonResponse(  ['recipe' => $recipe, 'ingredients' => $ingredients]);
    }
    function storage(View $view){
        if (!(isset($_POST['name'])) || !(isset($_POST['description'])) || !(isset($_POST['ingredients']))
        || ($_POST['name'] == '' )  || ($_POST['description'] == '' )  || ($_POST['ingredients'] == '' ))
	    {	
            $link = connectBD();
            $ingredients = queryToGetIngredientsAll($link);
            mysqli_close($link);    
            return new JsonResponse( 
            ['error' => 'Заполните обязательные поля (Название блюда, Способ приготовления, Ингредиенты(В виде строки, индексы через запятую))', 'ingredients' => $ingredients]);
        }
        
        $link = connectBD();
        $row = queryToRecipeStorage($_POST['name'], $_POST['photo'], $_POST['description'], $_POST['ingredients'], $link );
        mysqli_close($link);  

		if ($row == FALSE){			
            return new JsonResponse(  ['error' => 'Рецепт с таким названием есть']);
		}    
        return new JsonResponse( ['error' => 'Успешно добавлено']);
    }
    function update(View $view, $id){  
        if (!(isset($_POST['name'])) || !(isset($_POST['description'])) || !(isset($_POST['ingredients']))
        || ($_POST['name']) == '' || ($_POST['description']) == '' || ($_POST['ingredients']) == '' )
	    {	
            $link = connectBD();
            $recipe = queryGetRecipe($id, $link);   
            $ingredients = queryToGetIngredientsAll($link); 
            $selected_ingredients = queryToGetSelectedIngredients($id, $link);
            mysqli_close($link);
            return new JsonResponse( 
            [ 'error' => 'Введите все обязательные поля (Название рецепта, способ приготовления, ингредиентыИнгредиенты(В виде строки, индексы через запятую))', 
            'ingredients' => $ingredients, 'selected_ingredients' => $selected_ingredients]);
            
        }

        $link = connectBD();
        $row = queryToRecipeUpdate($_POST['name'], $_POST['photo'], $_POST['description'], $_POST['ingredients'], $id, $link);
        mysqli_close($link);
		if ($row == FALSE){			
            return new JsonResponse(  ['error' => 'Рецепт с таким названием есть']);
		}    
        return new JsonResponse( ['error' => 'Успешно добавлено']);
    }
    function delete(View $view, $id){
        $link = connectBD();
        $row = queryForDeleteRecipe($id, $link);
        mysqli_close($link);
        if ($row == FALSE){
            return new JsonResponse( 
            ['error' => 'Не найден рецепт по полученному ID']);
        }
        return new JsonResponse( ['error' => 'Успешно удалено']);
    }
}