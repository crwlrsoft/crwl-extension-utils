# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [1.2.0] - 2024-02-07
### Changed
* Require `illuminate/support` and register `ExtensionPackageManager` as a singleton via a new `ServiceProvider`.

## [1.1.0] - 2024-02-07
### Added
* New method `StepBuilder::setFileStoragePath()`. The app will call this method with the path where files can be stored, before the `StepBuilder` builds any step. So inside the builder, when building a step, you can rely on this path.

## [1.0.0] - 2024-01-31
Nothing added. Just more tests, static analysis, code style fixing, CI pipeline on github and some documentation in the readme file, so it can be published (the package was still private until here).

## [0.1.1] - 2023-05-02
### Added
* `ExtensionPackageManager::getPackages()` to get all registered packages.
