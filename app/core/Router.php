<?php
class Router {
    protected $routes = [];
    
    public function add($route, $controller, $method) {
        $this->routes[] = [
            'url' => $route,
            'controller' => $controller,
            'method' => $method
        ];
    }
    
    public function dispatch($url) {
        if(array_key_exists($url, $this->routes)) {
            $controller = $this->routes[$url]['controller'];
            $method = $this->routes[$url]['method'];
            
            require_once 'app/controllers/' . $controller . '.php';
            
            $controllerInstance = new $controller();
            $controllerInstance->$method();
        } else {
            // Xử lý 404 - Not Found
            header('HTTP/1.0 404 Not Found');
            echo '404 Page Not Found';
        }
    }

    public function match($url) {
        error_log("[Router] Matching URL: " . $url);
        
        // Xử lý đặc biệt cho trang chi tiết chi tiêu - ưu tiên cao nhất
        if (preg_match('#^expenses/detail/(\d+)$#i', $url, $matches)) {
            error_log("[Router] Special match for expense detail: " . $matches[1]);
            return [
                'controller' => 'ExpensesController',
                'method' => 'viewExpense',
                'params' => [$matches[1]]
            ];
        }
        
        // Tiếp tục với các routes khác...
        
        if (preg_match('#^expenses/view/(\d+)$#i', $url, $matches)) {
            error_log("[Router] Special match for expense view: " . $matches[1]);
            return [
                'controller' => 'ExpensesController',
                'method' => 'viewExpense',
                'params' => [$matches[1]]
            ];
        }
        
        if (preg_match('#^expenses/edit/(\d+)$#i', $url, $matches)) {
            error_log("[Router] Special match for expense edit: " . $matches[1]);
            return [
                'controller' => 'ExpensesController',
                'method' => 'edit',
                'params' => [$matches[1]]
            ];
        }
        
        // Kiểm tra các routes đã đăng ký
        foreach ($this->routes as $route) {
            if (!isset($route['url']) || $route['url'] === null) {
                continue;  // Bỏ qua nếu không có URL
            }
            
            $pattern = $this->convertToRegex($route['url']);
            error_log("[Router] Checking pattern: " . $pattern . " against URL: " . $url);
            
            if (preg_match($pattern, $url, $matches)) {
                array_shift($matches); // Loại bỏ phần tử đầu tiên
                
                error_log("[Router] Route matched: " . $route['url'] . " => " . $route['controller'] . "::" . $route['method'] . "()");
                error_log("[Router] Parameters: " . implode(", ", $matches));
                
                return [
                    'controller' => $route['controller'],
                    'method' => $route['method'],
                    'params' => $matches
                ];
            }
        }
        
        // Kiểm tra controller/method mặc định
        $parts = explode('/', $url);
        if (count($parts) >= 1) {
            $controllerName = ucfirst($parts[0]) . 'Controller';
            $method = isset($parts[1]) ? $parts[1] : 'index';
            $params = array_slice($parts, 2);
            
            $controllerFile = APPROOT . DS . 'controllers' . DS . $controllerName . '.php';
            
            if (file_exists($controllerFile)) {
                error_log("[Router] Found controller file: " . $controllerFile);
                
                require_once $controllerFile;
                $controllerInstance = new $controllerName();
                
                if (method_exists($controllerInstance, $method)) {
                    error_log("[Router] Found method: " . $method . "()");
                    
                    return [
                        'controller' => $controllerName,
                        'method' => $method,
                        'params' => $params
                    ];
                }
            }
        }
        
        // Không tìm thấy route
        error_log("[Router] No matching route found for URL: " . $url);
        return false;
    }
    
    protected function convertToRegex($route) {
        if ($route === null) {
            return '#^$#'; // Route rỗng
        }
        
        // Chuyển đổi /:id thành regex
        $pattern = preg_replace('#:([a-zA-Z0-9_]+)#', '([^/]+)', $route);
        
        // Convert các pattern khác nếu cần
        return '#^' . $pattern . '$#i';
    }
}