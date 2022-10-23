<? 
use MiladRahimi\PhpRouter\View\View;
use Laminas\Diactoros\Response\RedirectResponse;

function connectBD(){    
    require_once 'Connection.php'; // подключаем скрипт 
    $link = mysqli_connect($host, $user, $password, $database) 
    or die("Ошибка соединения с БД." . mysqli_error($link));
    return $link;

}
function queryToLoginAndCheck($post_login, $post_password, $link){    

    $login = mysqli_real_escape_string($link, $post_login);
    $password = mysqli_real_escape_string($link, $post_password);
    $query ="SELECT * FROM users WHERE login='$login'";
    $result = mysqli_query($link, $query) or die("Ошибка выбора списка пользователей" . mysqli_error($link)); 
    $row = mysqli_fetch_array($result);
    // закрываем подключение
    if ( ($login == $row['login']) && ($password == $row['password']) )
    {
        return $login;
    }
    else{
        return FALSE;
    }
}

function queryToRegister($post_login, $post_password, $post_password_repeat, $link){
    

    $login = mysqli_real_escape_string($link, $post_login);
    $password = mysqli_real_escape_string($link, $post_password);
    $password_repeat = mysqli_real_escape_string($link, $post_password_repeat);

    $query ="SELECT * FROM users WHERE login='$login'";
    $result = mysqli_query($link, $query) or die("Ошибка выбора списка пользователей" . mysqli_error($link)); 
    $row = mysqli_fetch_all($result, MYSQLI_ASSOC);
    if ( count($row) != 0 ){
        return FALSE;
    }
    $query_user ="INSERT INTO `users` ( `login`, `password`)  VALUES ('".$login."', '".$password."');";
    $query_user = mysqli_query($link, $query_user) or die("Ошибка добавления записи в БД.<br>" . mysqli_error($link)); 

    return TRUE;
}
class UserController
{
    function login(View $view)
    {
        return $view->make('login');
    }

    function register(View $view)
    {
        return $view->make('register');
    }
    function loginPost(View $view)
    {
        if (!(isset($_POST['login'])) || !(isset($_POST['password'])))
	    {	
            return $view->make('login', ['error' => 'Заполните все поля']);
        }
		
		//проверка пароля и логина
        $link = connectBD();
        $login = queryToLoginAndCheck($_POST['login'], $_POST['password'], $link);
        mysqli_close($link);
		if ($login !== FALSE){			
			$_SESSION['login']=$login;
		}
		else
		{	
            return $view->make('login', ['error' => 'Неверный логин или пароль.']);
		}

        return  new RedirectResponse('/index');
    }
    function registerPost(View $view)
    {
        if (!(isset($_POST['login'])) || !(isset($_POST['password'])) || !(isset($_POST['password_repeat'])))
        {
            return $view->make('register', ['error' => 'Заполните все поля']);            	
        }

        if ($_POST['password'] != $_POST['password_repeat']){
            return $view->make('register', ['error' => 'Пароли не совпали']);    
        }
                
        $link = connectBD();
        $row = queryToRegister($_POST['login'], $_POST['password'], $_POST['password_repeat'], $link);
        mysqli_close($link);
        if ($row == FALSE){	
            return $view->make('register', ['error' => 'Пользователь с таким логином уже есть, придумайте новое']);    
        }
        
        return  new RedirectResponse('/index');
    }
}
?> 