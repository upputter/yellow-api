# Api 0.1.0

Use this extension, if you want to add some API-functionality to the frontend of Yellow CMS, e.g. view- or click-counters, a search, chat or other kinds of data-interaction.

## What does it do?

This extensions adds some PHP-Methods, you can use to handle API-requests within your own/an other extension.

## How does it work?

By using the YellowExtension constant `PRIORITY = 5`, it makes sure to be loaded very early in the whole initialation proccess.
Extensions, using this API-extension MUST have a higher PRIORITY constant (`>= 6`), to work correct.

Your extension class MUST extend the `YellowApi` class and have a value within the`$controller`.

If a HTTP-request is within the given API-path (system variable `apiLocation`), the requested path is segmented by slashes (`/`) in:
  * API-location
  * Controller-Name
  * Action-Name
  * Data  

The example URL `https://example.com/api/mycontroller/update/fooBar` will handle the API-call for `mycontroller` and call its `update`-Action with `fooBar` as `$data`

Your extension may overwrite the `Action`-methods:
  * getAction();
  * postAction();
  * updateAction();
  * patchAction();
  * pushAction();
  * putAction();

You can use the methods `sendSSEHeader()` and `sendSSEBody()` to send [SSE](https://developer.mozilla.org/en-US/docs/Web/API/Server-sent_events/Using_server-sent_events) outputs.

### API methods
  
  * `getApiPath(action: 'actionName')`: returns a API-URL for a given action: e.g. `getApiPath('update')` may return `/api/mycontroler/update
  * the differen `Actions` may be defined in your extension
    
### extension methods

To get an action API-URL, you can use `$this->getApiPath('get') . '/' . $fooBarVarialbe;` within your extension.


## Example

```php
class YellowMycontroller extends YellowApi
{
    const PRIORITY = 23;
    public $yellow;
    protected $controller = 'mycontroller';

    public function onLoad($yellow)
    {
        $this->yellow = $yellow;        
    }

    protected function getAction($data) {/* ... */}

    protected function updateAction($data) { /* ... */ }
```
        

