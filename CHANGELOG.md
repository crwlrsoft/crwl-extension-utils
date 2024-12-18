# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.4.2] - 2024-12-08
### Fixed
* Support `crwlr/crawler` v3.0.

## [2.4.1] - 2024-11-06
### Fixed
* Support `illuminate/support` 11.x.

## [2.4.0] - 2024-10-15
### Added
* Support `crwlr/crawler` v2.0.
* Method `StepBuilder::outputType()`, returning a `Crwlr\Crawler\Steps\StepOutputType` enum instance, informing about the possible output type of the underlying step. Currently, there is a default implementation in the abstract `StepBuilder` class, returning `StepOutputType::Mixed`. But this implementation will be removed in v3.0, so child classes should always explicitly define the possible output type. More info in the readme file. __Attention__: As `Crwlr\Crawler\Steps\StepOutputType` was introduced in `crwlr/crawler` v1.8.0, this is now the minimum required version of the crawler package.
* Method `StepBuilder::isLoadingStep()`, so the crwl.io app knows if it's dealing with a loading step. Default implementation just returns false, so in loading steps you need to provide an implementation of this method, returning true.

## [2.3.1] - 2024-06-18
### Fixed
* It tries to cast step config values based on their configured type when using `StepBuilder::getValueFromConfigArray()`.

## [2.3.0] - 2024-03-18
### Added
* New config param type multi line string (`ConfigParam::multiLineString()` / `ConfigParamTypes::MultiLineString`).

## [2.2.0] - 2024-02-22
### Added
* New config param type float (`ConfigParam::float()` / `ConfigParamTypes::Float`).

## [2.1.0] - 2024-02-14
### Added
* New classes `RequestTracker` and `TrackingGuzzleClientFactory`. When steps need to execute HTTP requests without the `HttpLoader` from the crawler package (for example when using some REST API SDK), developers are encouraged to utilize either a Guzzle Client instance generated by the `TrackingGuzzleClientFactory` or invoke the `trackHttpResponse()` or `trackHeadlessBrowserResponse()` methods of the `RequestTracker` manually after each request. This enables seamless tracking of requests within the crwl.io app.

## [2.0.0] - 2024-02-07
### Changed
* Require `illuminate/support`, register `ExtensionPackageManager` as a singleton via a new `ServiceProvider` and remove `ExtensionPackageManager::singleton()` and `ExtensionPackageManager::new()` methods.

## [1.1.0] - 2024-02-07
### Added
* New method `StepBuilder::setFileStoragePath()`. The app will call this method with the path where files can be stored, before the `StepBuilder` builds any step. So inside the builder, when building a step, you can rely on this path.

## [1.0.0] - 2024-01-31
Nothing added. Just more tests, static analysis, code style fixing, CI pipeline on github and some documentation in the readme file, so it can be published (the package was still private until here).

## [0.1.1] - 2023-05-02
### Added
* `ExtensionPackageManager::getPackages()` to get all registered packages.
