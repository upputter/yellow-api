<?php
/*
    use https://data-star.dev/ to add a voting button
*/

class YellowApi
{
    const VERSION = "0.1.0";
    public $yellow;
    protected $controller;
    const PRIORITY = 5;

    public function onLoad($yellow)
    {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("apiLocation", "/api/");        
    }

    public function initApi(string $controller)
    {
        $this->setController($controller);
    }

    public function getController(): string
    {
        return (string)$this->controller;
    }

    protected function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    public function getApiPath(string $action = 'get'): string
    {
        return $this->yellow->system->get("coreServerBase") . implode(
            '/',
            [
                rtrim($this->yellow->system->get("apiLocation"), '/'),
                $this->controller,
                $action
            ]
        );
    }

    protected function isApiLocation(string $location) : bool
    {
        $locationLength = strlenu($this->yellow->system->get("apiLocation"));
        return substru($location, 0, $locationLength) == $this->yellow->system->get("apiLocation");
    }

    public function onRequest($scheme, $address, $base, $location, $fileName)
    {
        $apiStatuscode = 0;
        if ($this->isApiLocation($location)) {
            foreach ($this->yellow->extension->data as $key => $value) {
                if (method_exists($value["object"], "onApiRequest")) {
                    $this->yellow->lookup->requestHandler = $key;
                    $apiStatuscode = $value["object"]->onApiRequest($location);
                    if ($apiStatuscode != 0) break;
                }
            }            
            return 1; // no matter what - allways end the output!
        }
    }
}
