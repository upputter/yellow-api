# Api 0.1.0

Use this extension, if you want to add some API-functionality to the frontend of Yellow CMS, e.g. view- or click-counters, a search or other kinds of data-interaction.

## What does it do?

This extensions adds some PHP-Methods, you can use to handle API-requests within your own/ an other extension.

## How does it work?

By using the YellowExtension "PRIORITY" constants, it makes sure to be loaded very early in the whole initialation proccess.
Extensions, using this API-extension MUST have a higher PRIORITY constant (`>= 6`), to work correct.

### API methods
  * `initApi(controller: 'extensionControlerName')`: MUST be used within your extensions `onLoad()`-method, to initialize the API for a given extension controller
  * `isApiLocation($location)`: checks, if the requested `$location` is handled by the API
  * `onRequest( ... )`: appends the standard `onRequest()`method by calling the new API-extension method `onApiReques($location)`
  * `getApiPath(action: 'actionName')`: returns a API-URL for a given action: e.g. `getApiPath('update')` may return `/api/mycontroler/update


### extension methods
Within your extension, you can use a construct like the following, to handle API-requests:

```php
class YellowMyextension
{
    
    const PRIORITY = 20;
    public $api;
    public string $controller = 'mycontroler';

    public function onLoad($yellow)
    {
        $this->yellow = $yellow;
        $this->initApi($this->controller); // start API shenanigans 
    }

    protected function initApi()
    {
        if ($this->yellow->extension->isExisting('api')) {
            $this->api = clone $this->yellow->extension->get('api');
            $this->api->initApi(
                controller: $this->controller
            );
        }
    }

    public function onApiRequest($location)
    {
        list($api, $controller, $action, $someData) = explode('/', trim($location, '/'));
        if ($controller == $this->controller) {
            switch ($action) {
                case 'get': // will be fired when the URL /api/mycontroller/get/fooBar is requested
                default:
                   //...
                    break;
                case 'update': // will be fired when the URL /api/mycontroller/update/fooBar is requested
                   // ...
                    break;
            }
        }
    }
```

To get an action API-URL, you can use `$this->api->getApiPath('get') . '/' . $fooBarVarialbe;` within your extension.

