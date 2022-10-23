<? 

	session_start();

    require __DIR__.'/vendor/autoload.php';
    require 'controllers/UserController.php';
    require 'controllers/RecipeController.php';
    require 'controllers/IngredientController.php';
    require 'Middleware.php';
    use MiladRahimi\PhpRouter\Router;
    use MiladRahimi\PhpRouter\View\View;
    use Laminas\Diactoros\Response\HtmlResponse;
    use Laminas\Diactoros\Response\RedirectResponse;

    $router = Router::create();
    $router->setupView(__DIR__.'/views');

    $router->group(['middleware' => [NotAuthMiddleware::class]], function(Router $router) {
        $router->get('/login', [UserController::class, 'login']);        
        $router->get('/register', [UserController::class, 'register']);
        $router->post('/login', [UserController::class, 'loginPost']);
        $router->post('/register', [UserController::class, 'registerPost']);
    });

    $router->group(['middleware' => [AuthMiddleware::class]], function(Router $router) {
        $router->any('/', function (View $view) {
            return $view->make('index');
        });        
        $router->get('/index', [RecipeController::class, 'index']);   
        $router->get('/index/page/{id}', [RecipeController::class, 'index']);   
        $router->group(['prefix' => '/recipe'], function(Router $router) {
            $router->get('/view/{id}', [RecipeController::class, 'view']);   
            $router->get('/add', [RecipeController::class, 'add']);   
            $router->post('/storage', [RecipeController::class, 'storage']);   
            $router->get('/edit/{id}', [RecipeController::class, 'edit']);   
            $router->post('/update/{id}', [RecipeController::class, 'update']);   
            $router->get('/delete/{id}', [RecipeController::class, 'delete']);   
        });    
        $router->group(['prefix' => '/ingredient'], function(Router $router) {
            $router->get('/index', [IngredientController::class, 'index']);   
            $router->get('/index/page/{id}', [IngredientController::class, 'index']);   
            $router->get('/view/{id}', [IngredientController::class, 'view']);   
            $router->get('/add', [IngredientController::class, 'add']);   
            $router->post('/storage', [IngredientController::class, 'storage']);   
            $router->get('/edit/{id}', [IngredientController::class, 'edit']);   
            $router->post('/update/{id}', [IngredientController::class, 'update']);   
            $router->get('/delete/{id}', [IngredientController::class, 'delete']);   
        });   
        $router->any('/logout', function (View $view) {
            session_destroy();
            return $view->make('login');
        });
    });

    
    
    $router->group(['prefix' => '/api'], function(Router $router) {
        $router->group(['prefix' => '/ingredient'], function(Router $router) {
            $router->get('/index', [IngredientAPIController::class, 'index']);   
            $router->get('/index/page/{id}', [IngredientAPIController::class, 'index']);   
            $router->get('/view/{id}', [IngredientAPIController::class, 'view']);   
            $router->post('/storage', [IngredientAPIController::class, 'storage']);   
            $router->post('/update/{id}', [IngredientAPIController::class, 'update']);   
            $router->get('/delete/{id}', [IngredientAPIController::class, 'delete']);   
        });   
    }); 
    
    $router->group(['prefix' => '/api'], function(Router $router) {
        $router->group(['prefix' => '/recipe'], function(Router $router) {
            $router->get('/index', [RecipeAPIController::class, 'index']);   
            $router->get('/index/page/{id}', [RecipeAPIController::class, 'index']);  
            $router->get('/view/{id}', [RecipeAPIController::class, 'view']);   
            $router->post('/storage', [RecipeAPIController::class, 'storage']);   
            $router->post('/update/{id}', [RecipeAPIController::class, 'update']);   
            $router->get('/delete/{id}', [RecipeAPIController::class, 'delete']);   
        });    
    }); 
    $router->get('/404', function (View $view) {
        return $view->make('404');
    });
    try {
        $router->dispatch();
    } catch (MiladRahimi\PhpRouter\Exceptions\RouteNotFoundException $e) {
        // It's 404!
        
        return  new RedirectResponse('/404');
    }
	
?>

