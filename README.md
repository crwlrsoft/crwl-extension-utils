# crwl.io Extension Utils

These utilities enable you to develop extensions for the crwl.io web crawling and scraping app.

## How to Package Your Custom Steps for the crwl.io App

The [crwlr.software documentation](https://www.crwlr.software/packages/crawler/steps-and-data-flow/custom-steps) provides a detailed explanation of how to build your own steps for the crwlr/crawler library.

Once you've successfully created a working step, the most challenging part of your task is already complete. To integrate your custom steps into the crwl.io app, follow these simple steps:
* Package it as a Composer package in a GitHub repository.
* Create a `StepBuilder`
* Create a laravel `ServiceProvider` and register your extension steps using the `ExtensionPackageManager` included in this package.

### Composer Package Setup

Ensure your package repository's `composer.json` file resembles the following template:

```json
{
  "name": "my-vendor/my-crwl-extension",
  "description": "Extension package with custom steps for the crwl.io app",
  "type": "library",
  "autoload": {
    "psr-4": {
      "MyVendor\\MyCrwlExtension\\": "src/"
    }
  },
  "require": {
    "crwlr/crawler": "^1.5",
    "illuminate/support": "^9.27|^10.0",
    "crwlr/crwl-extension-utils": "^1.0"
  }
}
```

Feel free to customize it according to your preferences, but ensure that it includes dependencies on the following three packages: `crwlr/crawler`, `crwlr/crwl-extension-utils` and `illuminate/support`.

For a well-organized project structure, we recommend the following folder arrangement:

```
/
├─ src/
│  ├─ StepBuilders/
│  ├─ Steps/
│  ├─ ServiceProvider.php
├─ .gitignore
├─ composer.json
├─ README.md
```

In your `.gitignore`, include at least the following entries:

```gitignore
/vendor/
composer.lock
```

### Step Builders

To create a `StepBuilder` for your step, follow the example below:

```php
namespace MyVendor\MyCrwlExtension\StepBuilders;

use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\Crawler\Steps\StepOutputType;
use Crwlr\CrwlExtensionUtils\ConfigParam;
use Crwlr\CrwlExtensionUtils\StepBuilder;
use MyVendor\MyCrwlExtension\Steps\MyStep;

class MyStepBuilder extends StepBuilder
{
    public function stepId(): string
    {
        return 'my-extension.my-step';
    }

    public function label(): string
    {
        return 'This step does X.';
    }

    public function configToStep(array $stepConfig): StepInterface
    {
        $fooConfigValue = $this->getValueFromConfigArray('foo', $stepConfig);

        $barConfigValue = $this->getValueFromConfigArray('bar', $stepConfig);
        
        $bazConfigValue = $this->getValueFromConfigArray('baz', $stepConfig);

        return new MyStep($fooConfigValue, $barConfigValue);
    }

    public function configParams(): array
    {
        return [
            ConfigParam::string('foo')
                ->inputLabel('Your foo'),
            ConfigParam::int('bar')
                ->default(5)
                ->inputLabel('Number of bar')
                ->description('Provide the number of bar, so the step can do X.'),
            ConfigParam::bool('baz')
                ->inputLabel('Baz?'),
        ];
    }

    /**
     * Define the possible output type. More info at
     * https://www.crwlr.software/packages/crawler/v1.10/steps-and-data-flow/custom-steps#step-output-types
     * If the underlying step can yield different output types based on the configuration,
     * just return StepOutputType::Mixed. 
     */
    public function outputType(): StepOutputType
    {
        return StepOutputType::AssociativeArrayOrObject;
    }
}
```

If your step requires configuration, define the necessary parameters in the `configParams()` method. The crwl.io app's crawler creation/editing form will display corresponding inputs for these config options. When the crawler runs, the `StepBuilder::configToStep()` method is invoked with the user-saved configuration data. In this method, construct your custom step with the configured values and return it.

Currently, the available config param types are `string`, `int`, and `bool`. Optionally, you can specify a default value (`default()`), an input label (`inputLabel()`), and a description text (`description()`) for each configuration parameter.

If your step doesn't require any settings, the `StepBuilder` looks rather minimalistic:

```php
namespace MyVendor\MyCrwlExtension\StepBuilders;

use Crwlr\Crawler\Steps\StepInterface;
use Crwlr\Crawler\Steps\StepOutputType;use Crwlr\CrwlExtensionUtils\StepBuilder;
use MyVendor\MyCrwlExtension\Steps\MyStep;

class MyStepBuilder extends StepBuilder
{
    public function stepId(): string
    {
        return 'my-extension.my-step';
    }

    public function label(): string
    {
        return 'This step does X.';
    }

    public function configToStep(array $stepConfig): StepInterface
    {
        return new MyStep();
    }

    public function outputType(): StepOutputType
    {
        return StepOutputType::Scalar;
    }
}
```

If your step needs a filesystem path, where it can store files, you can use `$this->fileStoragePath` inside the builder. The crwl.io app sets this path for all step builders before building any steps.

```php
public function configToStep(array $stepConfig): StepInterface
{
    return new MyStep($this->fileStoragePath);
}
```

When creating a step builder for an HTTP loading step, please implement the following method:

```php
public function isLoadingStep(): bool
{
    return true;
}
```

### ServiceProvider and Registering Package and Steps

To make your steps accessible in the crwl.io app, the final step is to register an extension package and all your steps using the `ExtensionPackageManager` included in this package. Since crwl.io is a [Laravel](https://laravel.com/) application, this is accomplished through a `ServiceProvider` class:

```php
namespace MyVendor\MyCrwlExtension;

use Crwlr\CrwlExtensionUtils\ExtensionPackageManager;
use MyCrwlExtension\StepBuilders\FooStepBuilder;
use MyCrwlExtension\StepBuilders\BarStepBuilder;
use MyCrwlExtension\StepBuilders\BazStepBuilder;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function register()
    {
        $this->app->make(ExtensionPackageManager::class)
            ->registerPackage('my-vendor-name/my-crwl-extension')
            ->registerStep(FooStepBuilder::class)
            ->registerStep(BarStepBuilder::class)
            ->registerStep(BazStepBuilder::class);
    }
}
```

To complete the setup, add the `ServiceProvider` to the `extra` section in the `composer.json` file:

```json
{
  "name": "my-vendor/my-crwl-extension",
  "description": "Extension package with custom steps for the crwl.io app",
  "type": "library",
  "autoload": {
    "psr-4": {
      "MyVendor\\MyCrwlExtension\\": "src/"
    }
  },
  "require": {
    "crwlr/crawler": "^1.4",
    "illuminate/support": "^9.27|^10.0",
    "crwlr/crwl-extension-utils": "^1.0"
  },
  "extra": {
    "laravel": {
      "providers": [
        "MyVendor\\MyCrwlExtension\\ServiceProvider"
      ]
    }
  }
}
```

With these configurations in place, your extension package is ready for use. If your extension is private, ensure you grant access to the [crwlrsoft GitHub organization](https://github.com/crwlrsoft). As a super-user on your crwl.io instance, you can then install your extension via the extensions page in the app.

## Custom Steps Performing HTTP Requests Without the crwlr/crawler HttpLoader

In scenarios where your custom steps need to execute HTTP requests that cannot leverage the `HttpLoader` from the `crwlr/crawler` package—such as when utilizing a REST API SDK to retrieve data from an API—you'll need to ensure that every HTTP request is tracked when executing your custom steps within the crwl.io app.

To accomplish this, you have two options:
* Use a Guzzle Client instance generated by the `TrackingGuzzleClientFactory`.
* Alternatively, manually invoke the `trackHttpResponse()` or `trackHeadlessBrowserResponse()` methods of the `RequestTracker` following each request.

### Using a guzzle Client instance

If you want to use a Guzzle `Client` instance for the requests (it's common practice for PHP API SDKs to let you provide your own Guzzle instance), use the `TrackingGuzzleClientFactory` from this package:

```php
use Crwlr\CrwlExtensionUtils\TrackingGuzzleClientFactory;

// Let the factory be resolved by the laravel service container.
$factory = app()->make(TrackingGuzzleClientFactory::class);

$client = $factory->getClient();
```

You can also pass your custom Guzzle configuration as an argument:

```php
$client = $factory->getClient(['allow_redirects' => false]);
```

### Using the RequestTracker

In scenarios where utilizing a Guzzle `Client` instance for requests is not feasible, you need to call either `RequestTracker::trackHttpResponse()` or if your request was executed using a headless browser `RequestTracker::trackHeadlessBrowserResponse()`.

```php
use Crwlr\CrwlExtensionUtils\RequestTracker;

// Let the tracker be resolved by the laravel service container.
$tracker = app()->make(RequestTracker::class);

// Execute your request however you want...

$tracker->trackHttpResponse();

// or

$tracker->trackHeadlessBrowserResponse();
```

If you can provide request/response instances implementing the PSR-7 `RequestInterface` and/or `ResponseInterface`, please do so:

```php
$tracker->trackHttpResponse($request, $response);

// or

$tracker->trackHeadlessBrowserResponse($request, $response);
```
