<?php
class App {
    protected $controller = 'DashboardController';
    protected $method = 'index';
    protected $params = [];

    public function __construct() {
        // Debug mode
        error_log("=== App::__construct() called ===");
        
        // Lấy URL từ request
        $url = $this->parseUrl();
        error_log("Parsed URL: " . ($url ? implode('/', $url) : 'Empty URL'));
        
        // Khởi tạo Router
        $router = new Router();
        require_once APPROOT . DS . 'config' . DS . 'routes.php';
        
        // Nếu URL là rỗng, chuyển hướng đến trang dashboard
        if (empty($url)) {
            $this->controller = 'DashboardController';
            $this->method = 'index';
            $this->params = [];
            
            error_log("Empty URL, using default controller: {$this->controller}::{$this->method}()");
            $this->executeController();
            return;
        }
        
        // Kiểm tra controller có tồn tại không
        $controllerName = ucfirst($url[0]) . 'Controller';
        $controllerFile = APPROOT . DS . 'controllers' . DS . $controllerName . '.php';
        
        error_log("Looking for controller file: {$controllerFile}");
        
        if (file_exists($controllerFile)) {
            $this->controller = $controllerName;
            unset($url[0]);
            
            require_once $controllerFile;
            error_log("Controller file found and loaded: {$controllerName}");
        } else {
            // Kiểm tra xem có phải route đặc biệt không
            $specialURL = implode('/', $url);
            error_log("Checking special route for: {$specialURL}");
            
            // Xử lý URL đặc biệt cho chi tiết chi tiêu
            if (isset($url[0]) && $url[0] == 'expenses' && 
                isset($url[1]) && ($url[1] == 'detail' || $url[1] == 'view') && 
                isset($url[2]) && is_numeric($url[2])) {
                
                error_log("Special route detected: expenses/detail/{$url[2]}");
                
                require_once APPROOT . DS . 'controllers' . DS . 'ExpensesController.php';
                $controller = new ExpensesController();
                $controller->viewExpense($url[2]);
                return;
            }
            
            // Thử tìm route qua Router
            $routeMatch = $router->match($specialURL);
            
            if ($routeMatch) {
                error_log("Route matched: {$routeMatch['controller']}::{$routeMatch['method']}()");
                
                $controllerFile = APPROOT . DS . 'controllers' . DS . $routeMatch['controller'] . '.php';
                
                if (file_exists($controllerFile)) {
                    require_once $controllerFile;
                    
                    $controllerInstance = new $routeMatch['controller']();
                    call_user_func_array([$controllerInstance, $routeMatch['method']], $routeMatch['params']);
                    return;
                }
            }
            
            // Nếu không tìm thấy controller, sử dụng controller mặc định
            error_log("Controller not found, using default: {$this->controller}");
            require_once APPROOT . DS . 'controllers' . DS . $this->controller . '.php';
        }
        
        // Khởi tạo controller
        $this->controller = new $this->controller;
        
        // Kiểm tra method có tồn tại không
        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
                
                error_log("Method found: {$this->method}()");
            } else {
                error_log("Method not found: {$url[1]}()");
                $this->handle404();
                return;
            }
        }
        
        // Lấy parameters
        $this->params = $url ? array_values($url) : [];
        error_log("Parameters: " . print_r($this->params, true));
        
        // Gọi method của controller với parameters
        try {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } catch (Exception $e) {
            error_log("Error calling {$this->method}(): " . $e->getMessage());
            $this->handle500($e);
        }
    }
    
    protected function executeController() {
        require_once APPROOT . DS . 'controllers' . DS . $this->controller . '.php';
        $this->controller = new $this->controller;
        
        try {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } catch (Exception $e) {
            error_log("Error executing controller: " . $e->getMessage());
            $this->handle500($e);
        }
    }
    
    protected function parseUrl() {
        if(isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            
            error_log("[App] Parsed URL: " . $url);
            
            return explode('/', $url);
        }
        
        error_log("[App] No URL parameter found");
        return [];
    }
    
    protected function handle404() {
        header("HTTP/1.0 404 Not Found");
        require_once APPROOT . DS . 'views' . DS . 'errors' . DS . '404.php';
        exit;
    }
    
    protected function handle500($exception) {
        header("HTTP/1.0 500 Internal Server Error");
        require_once APPROOT . DS . 'views' . DS . 'errors' . DS . '500.php';
        exit;
    }
}