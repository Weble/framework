# Zoolanders Framework

## Basic Usage

```php
require_once JPATH_LIBRARIES . '/zoolanders/include.php';

$container = \Zoolanders\Framework\Container\Container::getInstance();
```

## Services

This is a list of services that the container exposes by default.

### zoo

This service is the main entry point to any zoo related stuff.
By default it proxies any function call and any property access to the main ZOO App class (App::getInstance('zoo'));

```php
$container->zoo->table->item->save($data);
```

Also, it exposes several methods:

- **getApp**: get the zoo's app instance (App::getInstance('zoo'))
- **isLoaded**: check if zoo is loaded
- **load**: actually load zoo (if it's not already loaded)

### system

Deals with system-related stuff (mostly related to the platform, ie: joomla).
It just exposes subservices.

#### language
Deals with language stuff

```php
$container->system->language->getTag();
```

#### application
Deals with application-level stuff

```php
$container->system->application->isAdmin();
```

#### document
An interface to JDocument

```php
$container->system->document->addScript(...);
```

Also, it exposes several methods:

- **addStilesheet($path, $version)**: Add a stylesheet to the document using also path parsable variables (media:system/file.css)
- **addScript($path, $version)**: Add a script to the document using also path parsable variables (media:system/file.js)

### db

The usual database service

```php
$container->db->execute();
```

### zoo
```php
$container->language->load(...);
```

### filesystem

It deals with anything filesystem related. For now it's just a Flysystem integration
plus some old zlfw functions.

In time, it will allow for different mounting points, like s3, etc.
By default it access the local filesystem.

### joomla

Clone of zoo's joomla helper, removing the deprecated methods

- **getVersion**: Get the joomla's current short version
- **isVersion($version)**: Check if we're on a specific joomla version
- **getDefaultAccess**: Get the default access level of joomla

### path

Extended version of the zoo's path helper
It still uses the zoo's path helper to register the paths, since most of them
are registered by zoo itself, but it deals with the file search and listing
using the zoolanders framework service.

Same api as the zoo's path helper

### environment

The old zlfw environment helper

- **get**: get the current environment (site.com_zoo.item)
- **is($environments)**: Checks if the passed environment is the current environment

### installation

The old methods in the zlfw plugin dealing with the installation checks

- **checkInstallation**: Check if the installation was performed correctly
- **checkDependencies**: Check if the dependecies are correctly resolved
- **checkPluginsOrder**: Check if the plugin order is correct

### dependecies

The old zlfw dependecies helper

- **check($file)** Check the dependecies listed in a given json file
- **warn($extensions, $extension)**: Displays a warning about a list of not resolved extensions dependecies

## Workarounds

To rerun DB migrations add the following in some executed PHP and remove it after.

```
$manager = new \Zoolanders\Framework\Migration\Manager();
$manager->run();
```

## Devflow

Dependencies must be installed with composer which will check if the PHP version satisfy the requirements during the update. As such make sure you run this command from the virtual machine.

```
composer update
```

If for some reason the autoloader needs to be re-generated:

```
composer dump-autoload
```

## Controllers

Controllers need to extend the base class `Zoolanders\Framework\Controller\Controller`.
Any public method can be used as a task, triggered in the url by the `task` variable
Any method of the controller (as well as the constructor itself) supports Dependecy Injection.
Therefore you can "ask" for any class / service known to the container and expect it to be given to you.

```php
public function index(Request $request, Response $response, Filesystem $filesystem) {
   ....
}
```

Before and after each task several events are fired:
- `onBeforeExecute`
- `onAfterExecute`
- `onBefore{Task}`
- `onAfter{Task}`

The easiest way to use these events is to implement a ***public*** method with the same name of the event

```php
public function onBeforeSave(BeforeExecute $event)
{
    // For example, deal with acl stuff
    if ($cannotSave) {
        throw new \Zoolanders\Framework\Dispatcher\Exception\AccessForbidden();
    }

    // change the task name maybe?
    $event->setTask('similarTask');
}

public function onAfterSave(AftereExecute $event)
{
    // change the response?
    $response = $event->getResponse();

    ....


    $event->setResponse($response);
}
```

Another way is to use the core event system. You would need to create an event class for your event
```php
class \Zoolanders\ExtensionName\ViewName\BeforeTaskName extends BeforeExecute {

}
```

and listen to it with a listener that you will have to attach to it at some point

```php
class DoSomethingBeforeWhatever extends Listener {

    public function handle(BeforeTaskName $event) {

    }
}

....

$container->event->connect('\Zoolanders\ExtensionName\ViewName\BeforeTaskName', 'DoSomethingBeforeWhatever@handle');
```

## Build

The build requires [Docker](https://www.docker.com/) to be set in the host mashine.

```sh
npm run build
```
