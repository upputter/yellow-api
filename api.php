<?php
/*
    use https://data-star.dev/
*/

class YellowApi
{
    const VERSION = "0.1.1";
    public $yellow;
    protected $controller;
    const PRIORITY = 5;

    public function onLoad($yellow)
    {
        $this->yellow = $yellow;
        $this->yellow->system->setDefault("apiLocation", "/api/");
    }

    public function getController(): string
    {
        return (string)$this->controller;
    }

    protected function setController(string $controller): void
    {
        $this->controller = $controller;
    }

    // possible actions
    protected function getAction($data) {}
    protected function updateAction($data) {}
    protected function postAction($data) {}
    protected function putAction($data) {}
    protected function pushAction($data) {}
    protected function patchAction($data) {}

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

    protected function isApiLocation(string $location): bool
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
                    $value["object"]->onApiRequest($location);
                }
            }
            return 1; // no matter what - allways end the output!
        }
    }

    public function onApiRequest(string $location)
    {
        list($api, $controller, $action, $data) = explode('/', trim($location, '/'));
        $methodName = $action . 'Action';

        // check controller and available methods
        if ($controller == $this->controller and method_exists($this, $methodName)) {
            $this->{$methodName}($data);
        }
    }

    protected function sendSSEHeader(): void
    {
        header("X-Accel-Buffering: no");
        header("Content-Type: text/event-stream");
        header("Cache-Control: no-cache");
    }

    protected function sendSSEBody(array $data = []): void
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                foreach ($value as $singleValue) {
                    echo $key . ': ' . $singleValue . "\n";
                }
            } else {
                echo $key . ': ' . $value . "\n";
            }
        }
        echo "\n";
    }
}
