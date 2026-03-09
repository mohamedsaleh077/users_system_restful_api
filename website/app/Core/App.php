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
    
    public function __construct(): void
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
    
    
}
