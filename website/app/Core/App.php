<?php

declare(strict_types=1);
namespace Core;

/**
 * Description of App
 *  Parse the URL and feed the Router with the information from the request
 * @author mohamed
 */
class App {
    private array $url;
    private string $method;
    private array $params;
    
    public function __construct(private Router $router): void
    {
        ;
    }
    
    private function ParseURL(): void
    {
        if (!isset($_GET['url'])){
            $this->url = [];
            return;
        }
        $url = filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL);
        $url = preg_replace('/[^a-zA-Z0-9\/\.\-\_]/', '', $url);
        $this->url = explode('/', $url);
    }
    
    private function SetMethod(): void
    {
        if($this->url[0] === ''){
            $this->method = "home";
        }
        
        if (!method_exists($this->router, $this->url[0])){
            ; // err 404
        }
        
        $this->method = $this->url[0];
        unset($this->url[0]);
    }
    
    private function SetParams(): void
    {
        $this->params = isset($this->url[1]) ? array_values($this->url) : [];
    }
}
