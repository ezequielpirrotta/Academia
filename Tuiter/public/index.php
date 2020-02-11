<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

session_start();

if (PHP_SAPI == 'cli-server') {
    $url  = parse_url($_SERVER['REQUEST_URI']);
    $file = __DIR__ . $url['path'];
    if (is_file($file)) return false;
}

$loader = new \Twig\Loader\FilesystemLoader(__DIR__ . '/../templates');
$twig = new \Twig\Environment($loader, [
    'cache' => false,
]);

$mongoconn = new \MongoDB\Client("mongodb://localhost");
$userService = new \Tuiter\Services\UserService($mongoconn->tuiter->users);
$postService = new \Tuiter\Services\PostService($mongoconn->tuiter->posts);
$likeService = new \Tuiter\Services\LikeService($mongoconn->tuiter->likes);
$followService = new \Tuiter\Services\FollowService($mongoconn->tuiter->follows, $userService);
$loginService = new \Tuiter\Services\LoginService($userService);


$app = AppFactory::create();

$app->addRoutingMiddleware();

$middlewareCheckLogin = function (Request $request, RequestHandler $next) {
    
    if(empty($_SESSION["login"]) || !$_SESSION["logeado"]){
        $response = new \Slim\Psr7\Response();
        $response = $response->withStatus(302);
        $response = $response->withHeader("Location","/logIn");
        return $response;
        
    }else{
        $response = new \Slim\Psr7\Response();
        $response = $next->handle($request);
        return $response;
    }
    
};
$app->get('/', function (Request $request, Response $response, array $args) use ($twig) {
    
    $template = $twig->load('index.html');

    $response->getBody()->write(
        $template->render(['name' => 'Dario'])
    );
    return $response;
});
$app->get('/logIn', function (Request $request, Response $response, array $args) use ($twig) {
    $template = $twig->load('logIn.html');

    $response->getBody()->write(
        $template->render(["url"=>"/logIn","reg"=>"/register"])
    );
    return $response;
});
$app->post('/logIn', function (Request $request, Response $response, array $args) use ($twig,$loginService) {
    if(null==$loginService->login($_POST["user"],$_POST["password"])){
        $response = $response->withStatus(302);
        $response = $response->withHeader("Location","/registro");
    }else{
        $response = $response->withStatus(302);
        $response = $response->withHeader("Location","/feed");
    }
    return $response;
});
$app->get('/registro', function (Request $request, Response $response, array $args) use ($twig) {
    $template = $twig->load('registro.html');

    $response->getBody()->write(
        $template->render(["url"=>"/register","reg"=>"/logIn"])
    );
    return $response;

});
$app->post('/register', function (Request $request, Response $response, array $args) use ($twig,$userService) {
    $userService->register($_POST["userId"],$_POST["user"],$_POST["password"]);
    $response = $response->withStatus(302);
    $response = $response->withHeader("Location","/feed");
    return $response;
});
$app->get('/contacto', function (Request $request, Response $response, array $args) use ($twig) {
    
    $template = $twig->load('contacto.html');

    $response->getBody()->write(
        $template->render(['name' => 'Dario'])
    );
    return $response;
});
$app->get('/feed', function (Request $request, Response $response, array $args) use ($twig,$postService,$userService) {
    
    $template = $twig->load('feed.html');
    $posts=$postService->getAllPosts($userService->getUser($_SESSION["user"]));
    $po=array();
    foreach($posts as $post){
        $po["content"]=$post->getContent();
        $po["owner"]=$post->getUserId();
    }
    $response->getBody()->write(
        $template->render(['usuario' => $_SESSION["user"]])
    );
    return $response;
})->add($middlewareCheckLogin);
$app->post('/newPost', function (Request $request, Response $response, array $args) use ($postService,$userService) {
    
    $result=$postService->create($_POST["contenido"],$userService->getUser($_POST["user"]));
    if($result->getPostId()===null){
        $response = $response->withStatus(302);
        $response = $response->withHeader("Location","/feed");
    }else{
        $response = $response->withStatus(302);
        $response = $response->withHeader("Location","/feed");
    }
    return $response;
});
$app->get('/logOut', function (Request $request, Response $response, array $args) use ($loginService) {
    $loginService->logout();
    $response = $response->withStatus(302);
    $response = $response->withHeader("Location","/logIn");
    return $response;
});
$app->run();