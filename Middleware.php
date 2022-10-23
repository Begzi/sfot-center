<? 
// use MiladRahimi\PhpRouter\Router;
use Psr\Http\Message\ServerRequestInterface;
use Laminas\Diactoros\Response\JsonResponse;
use Laminas\Diactoros\Response\RedirectResponse;

class AuthMiddleware
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        if (isset($_SESSION['login'])) {            
            // Call the next middleware/controller
            return $next($request);
        }
        
        return  new RedirectResponse('/404');
    }
};
class NotAuthMiddleware
{
    public function handle(ServerRequestInterface $request, Closure $next)
    {
        if (isset($_SESSION['login'])) {            
            // Call the next middleware/controller
            return  new RedirectResponse('/404');
        }
        else{        
            return $next($request);
        }
    }
};

?>