<?php
use MiladRahimi\PhpRouter\View\View;
use Laminas\Diactoros\Response\RedirectResponse;
use Laminas\Diactoros\Response\JsonResponse;

function queryToIngredients($id, $link){    

    $query_ingredient_list = "SELECT * FROM `ingredient`";
    $query_ingredient_list = mysqli_query($link, $query_ingredient_list)
        or die("Ошибка выбора списка сертификатов.<br>" . mysqli_error($link)); 
    $ingredient_list_count = mysqli_fetch_all($query_ingredient_list, MYSQLI_ASSOC);  

    $query_ingredient_list = "SELECT * FROM `ingredient` 
                LIMIT ". strval(intval($id) * 5) ." OFFSET ". strval(intval($id - 1) * 5);        
    $query_ingredient_list = mysqli_query($link, $query_ingredient_list)
        or die("Ошибка выбора списка сертификатов.<br>" . mysqli_error($link)); 
    $ingredient_list = mysqli_fetch_all($query_ingredient_list, MYSQLI_ASSOC);  
    $rows['ingredient_list_count'] = count($ingredient_list_count);
    $rows['ingredient_list'] = $ingredient_list;
    return $rows;
}

function queryIngredient($id, $link){

    $query_ingredient_list = "SELECT * FROM `ingredient` WHERE id = " . $id;
    $query_ingredient_list = mysqli_query($link, $query_ingredient_list)
        or die("Ошибка выбора списка сертификатов.<br>" . mysqli_error($link)); 
    $ingredient_list = mysqli_fetch_all($query_ingredient_list, MYSQLI_ASSOC); 
    return  $ingredient_list[0];
}
function queryToIngredientStorage($name, $cost, $link){    
        
    $name = mysqli_real_escape_string($link, $name);
    $cost = mysqli_real_escape_string($link, $cost);
    
    $query ="SELECT * FROM ingredient WHERE name='$name'";
    $result = mysqli_query($link, $query) or die("Ошибка выбора списка пользователей" . mysqli_error($link)); 
    $row = mysqli_fetch_all($result);

    if (count($row) != 0 ){		
        return FALSE;
    }
    $query_ingredient ="INSERT INTO `ingredient` ( `name`, `cost`) 
                        VALUES ('".$name."', '".$cost."');";
    $result_ingredient = mysqli_query($link, $query_ingredient) or die("Ошибка добавления записи в БД.<br>" . mysqli_error($link)); 

    return TRUE;
}
function queryToIngredientUpdate($name, $cost, $id, $link){    

    $ingredient = queryIngredient($id, $link);      
        
    $name = mysqli_real_escape_string($link, $name);
    $cost = mysqli_real_escape_string($link, $cost);
    if ($name != $ingredient['name']){
        $query ="SELECT * FROM ingredient WHERE name='$name'";
        $result = mysqli_query($link, $query) or die("Ошибка выбора списка пользователей" . mysqli_error($link)); 
        $row = mysqli_fetch_all($result);

        if (count($row) != 0 ){		
            return FALSE;
        }
    }
    $query_ingredient="UPDATE `ingredient` 
    SET `name`='". $name ."',`cost`='". $cost ."'WHERE`id`='". $ingredient['id'] ."';";
    $result_ingredient = mysqli_query($link, $query_ingredient) or die("Ошибка добавления записи в БД.<br>" . mysqli_error($link)); 

    return TRUE;
}
function queryToIngredientDeleteRecipeIngredient($ingredient_id, $link){
    $query_recipe_ingredient_list = "SELECT * FROM `recipe_ingredient` 
            WHERE ingredient_id = " . $ingredient_id;
    $query_recipe_ingredient_list = mysqli_query($link, $query_recipe_ingredient_list)
        or die("Ошибка выбора списка recipe_ingredient.<br>" . mysqli_error($link)); 
    $ingredients = mysqli_fetch_all($query_recipe_ingredient_list, MYSQLI_ASSOC); 
    if (count ($ingredients) > 0){
        $query_recipe_ingredient = "DELETE FROM `recipe_ingredient` WHERE ingredient_id = " . $ingredient_id;
        $result = mysqli_query($link, $query_recipe_ingredient) 
            or die("Ошибка удаления записи в БД. recipe_ingredient<br>" . mysqli_error($link)); 
    }

}
function queryForDeleteIngredient($id, $link){

    $ingredient = queryIngredient($id, $link);   
    if ($ingredient === FALSE){
        mysqli_close($link);
        return FALSE;
    }
    $query_ingredient = "DELETE FROM `ingredient` WHERE id = " . $id;
    $query_ingredient = mysqli_query($link, $query_ingredient) 
        or die("Ошибка добавления записи в БД.<br>" . mysqli_error($link)); 
    
    queryToIngredientDeleteRecipeIngredient($id, $link);
    return  TRUE;
}
class IngredientController
{
    function index(View $view, $id = 1)
    {
        $link = connectBD();
        $rows = queryToIngredients($id, $link);  
        mysqli_close($link);      
        return $view->make('index', 
        ['ingredient_list' => $rows['ingredient_list'], 'ingredient_list_count' => $rows['ingredient_list_count']]);
    }
    function view(View $view, $id){
        $link = connectBD();
        $ingredient = queryIngredient($id, $link);
        mysqli_close($link);      
        return $view->make('ingredient.view', ['ingredient' => $ingredient]);
    }
    function add(View $view){        
        return $view->make('ingredient.add');
    }
    function storage(View $view){
        if (!(isset($_POST['name'])) || !(isset($_POST['cost'])) 
        || ($_POST['name'] == '' )  || ($_POST['cost'] == '' ))
	    {	
            return $view->make('ingredient.add', ['error' => 'Заполните обязательные поля']);
        }
        
        $link = connectBD();
        $row = queryToIngredientStorage($_POST['name'], $_POST['cost'], $link);
        mysqli_close($link);      

		if ($row == FALSE){			
            return $view->make('ingredient.add', ['error' => 'Рецепт с таким названием есть']);
		}    
        return  new RedirectResponse('/ingredient/index');
    }
    function edit(View $view, $id){
        $link = connectBD();
        $ingredient = queryIngredient($id, $link);
        mysqli_close($link);      
        return $view->make('ingredient.edit', ['ingredient' => $ingredient]);
    }
    function update(View $view, $id){ 
        if (!(isset($_POST['name'])) || !(isset($_POST['cost']))
        || ($_POST['name']) == '' || ($_POST['cost']) == ''){	
            return $view->make('ingredient.edit',
            ['error' => 'Заполните обязательные поля', 'ingredient' => $ingredient]);
        }

        $link = connectBD();
        $row = queryToIngredientUpdate($_POST['name'], $_POST['cost'],  $id, $link);
        mysqli_close($link);      

		if ($row == FALSE){			
            return $view->make('ingredient.add', ['error' => 'Рецепт с таким названием есть']);
		}    
        return  new RedirectResponse('/ingredient/view/' . $id);
    }
    function delete(View $view, $id){
        $link = connectBD();
        $row = queryForDeleteIngredient($id, $link);
        mysqli_close($link);      
        if ($row == FALSE){
            return $view->make('ingredient.view', 
            ['ingredient' => $ingredient, 'error' => 'Не найден рецепт по полученному ID']);
        }
        return  new RedirectResponse('/ingredient/index');
    }
}
class IngredientAPIController
{
    function index($id = 1)
    {
        $link = connectBD();
        $rows = queryToIngredients($id, $link);  
        mysqli_close($link);      
        return  new JsonResponse(['ingredient_list' => $rows['ingredient_list'], 
        'ingredient_list_count' => $rows['ingredient_list_count']]);
    }
    function view($id){
        $link = connectBD();
        $ingredient = queryIngredient($id, $link);
        mysqli_close($link);      
        return new JsonResponse(['ingredient' => $ingredient]);
    }
    function storage(View $view){
        if (!(isset($_POST['name'])) || !(isset($_POST['cost'])) 
        || ($_POST['name'] == '' )  || ($_POST['cost'] == '' ))
	    {	
            return new JsonResponse(['error' => 'Заполните обязательные поля (name, cost)']);
        }
        
        $link = connectBD();
        $row = queryToIngredientStorage($_POST['name'], $_POST['cost'], $link);
        mysqli_close($link);      

		if ($row == FALSE){			
            return new JsonResponse(['error' => 'Ингредиент с таким названием есть']);
		}    
        return new JsonResponse(['error' => 'Успешно добавлено']);
    }

    function update($id){ 
        $link = connectBD();
        $row = queryIngredient($id, $link);
        if ($row == FALSE){
            mysqli_close($link);      
            return new JsonResponse(['error' => 'Не найден Ингредиент по полученному ID']);
        }
        if (!(isset($_POST['name'])) || !(isset($_POST['cost']))
        || ($_POST['name']) == '' || ($_POST['cost']) == ''){	
            return new JsonResponse(['error' => 'Заполните обязательные поля (name, cost)']);
        }
        
        $row = queryToIngredientUpdate($_POST['name'], $_POST['cost'],  $id, $link);
        mysqli_close($link);      

		if ($row == FALSE){			
            return new JsonResponse(['error' => 'Ингредиент с таким именем есть, переименуйте на другое имя']);
		}    
        return new JsonResponse(['error' => 'Успешно обновлено']);
    }
    function delete(View $view, $id){
        $link = connectBD();
        $row = queryForDeleteIngredient($id, $link);
        mysqli_close($link);      
        if ($row == FALSE){
            return new JsonResponse(['error' => 'Не найден Ингредиент по полученному ID']);
        }
        return new JsonResponse(['error' => 'Успешно удалено']);
    }
}


?>