# Resource Watcher

A resource watcher allows you to watch a resource for any changes. This means you can watch a directory and then listen for any changes to files within that directory or to the directory itself.

[![Build Status](https://travis-ci.org/jasonlewis/resource-watcher.png?branch=master)](https://travis-ci.org/jasonlewis/resource-watcher)

## Installation

To install Resource Watcher add it to the `requires` key of your `composer.json` file.

```
"jasonlewis/resource-watcher": "1.1.*"
```

Then update your project with `composer update`.

## Usage

The Resource Watcher is best used from a console. An example of a console command can be found in the `watcher` file. This file is commented to give you
an idea of how to configure and use a resource watcher. Once you've customized the command to your liking you can run it from your console.

```
$ php watcher
```

Any changes you make to the resource will be outputted to the console.

## Quick Overview

To watch resources you first need an instance of `JasonLewis\ResourceWatcher\Watcher`. This class has a few dependencies (`JasonLewis\ResourceWatcher\Tracker` and `Illuminate\Filesystem\Filesystem`) that must also be instantiated.

```php
$files = new Illuminate\Filesystem\Filesystem;
$tracker = new JasonLewis\ResourceWatcher\Tracker;

$watcher = new JasonLewis\ResourceWatcher\Watcher($tracker, $files);
```

Now that we have our watcher we can create a listener for a given resource.

```php
$listener = $watcher->watch('path/to/resource');
```

When you watch a resource an instance of `JasonLewis\ResourceWatcher\Listener` is returned. With this we can now listen for certain events on a resource.

There are three events we can listen for: `modify`, `create`, and `delete`. The callback you give to the listener receives two parameters, the first being an implementation of `JasonLewis\ResourceWatcher\Resource\ResourceInterface` and the second being the absolute path to the resource.

```php
$listener->modify(function($resource, $path)
{
    echo "{$path} has been modified.".PHP_EOL;
});
```

You can use the alias methods as well.

```php
$listener->onModify(function($resource, $path)
{
    echo "{$path} has been modified.".PHP_EOL;
});
```

You can also listen for any of these events. This time the callback receives a different set of parameters, the first being an instance of `JasonLewis\ResourceWatcher\Event` and the remaining two being the same as before.

```php
$listener->anything(function($event, $resource, $path)
{

});
```

> Remember that each call to `$watcher->watch()` will return an instance of `JasonLewis\ResourceWatcher\Listener`, so be sure you attach listeners to the right one!

Once you're watching some resources and have your listeners set up you can start the watching process.

```php
$watcher->start();
```

By default the watcher will poll for changes every second. You can adjust this by passing in an optional first parameter to the `start` method. The polling interval is given in microseconds, so 1,000,000 microseconds is 1 second. The watch will continue until such time that it's aborted from the console. To set a timeout pass in the number of microseconds before the watch will abort as the second parameter.

The `start` method can also be given a callback as an optional third parameter. This callback will be fired before checking for any changes to resources.

```php
$watcher->start(1000000, null, function($watcher)
{
	// Perhaps perform some other check and then stop the watch.
	$watcher->stop();
});
```

## Framework Integration

### Laravel 4

Included is a service provider for the Laravel 4 framework. This service provider will bind an instance of `JasonLewis\ResourceWatcher\Watcher` to the application container under the `watcher` key.

```php
$listener = $app['watcher']->watch('path/to/resource');

// Or if you don't have access to an instance of the application container.
$listener = App::make('watcher')->watch('path/to/resource');
```

Register `JasonLewis\ResourceWatcher\Integration\LaravelServiceProvider` in the array of providers in `app/config/app.php`.

## License

Resource Watcher is released under the 2-clause BSD license. See the `LICENSE` for more details.