# Palantir Behat Drupal Extension

The Palantir Behat Drupal Extension provides additional step definitions for testing Drupal sites using [Behat](http://behat.org),
[Mink Extension](https://github.com/Behat/MinkExtension).

## Drupal 8

Drupal 8 is supported using the `drupal8` branch, but all of the available steps and methods currently throw a `NotUpdatedException`. Please update them as you need them for your projects!

## Drupal 7

Drupal 7 is supported using the `master` branch.

## What can I do with this?

### Extra step syntax

* `NodeContext`: test viewing and editing nodes by title
* `DrupalCommentContext`: test commenting functionality
* `DrupalFileContext`: add files in your tests (see [PR #3](https://github.com/palantirnet/palantir-behat-extension/pull/3) for usage details)
* `DrupalOrganicGroupsContext`: test access to Organic Groups
* `DrupalSetupContext`: test for enabled modules and overridden features
* `EntityDataContext`: test field data and properties on nodes, terms, and users directly, without relying on output (or write a simpletest...)

### Disable module functionality during tests

* `DrupalAutoNodetitleContext`: tag scenarios with `@disableAutoNodetitle` to bypass automatic title generation; sometimes this is required in order to have predictable test content
* `DrupalWorkbenchModerationContext`: tag scenarios with `@disableWorkbenchModeration` to bypass moderation

### Extend for your project's needs

* `MarkupContext`: extend this class to use the `assertRegionElementText` method, and encapsulate complex markup in a custom context rather than in the Gherkin acceptance criteria

## How do I add this to my project?

If you are already using the `Drupal\DrupalExtension` Behat extension, it is very easy to add these contexts--and if not, go set that up before attempting to use these custom contexts.

First, add this package to your `composer.json` file:

```
    "repositories": [
        {
            "type": "vcs",
            "url": "git@github.com:palantirnet/palantir-behat-extension.git"
        }
    ],
    "require-dev": {
        "palantirnet/palantir-behat-extension": "dev-drupal8"
    },
```

Then, add the specific contexts you want to use to your project's `behat.yml` file:

```
default:
  suites:
    default:
      contexts:
        - Palantirnet\PalantirBehatExtension\Context\DrupalAutoNodetitleContext
        - Palantirnet\PalantirBehatExtension\Context\DrupalCommentContext
        - Palantirnet\PalantirBehatExtension\Context\DrupalOrganicGroupsContext
        - Palantirnet\PalantirBehatExtension\Context\DrupalSetupContext
        - Palantirnet\PalantirBehatExtension\Context\DrupalWorkbenchModerationContext
        - Palantirnet\PalantirBehatExtension\Context\EntityDataContext
        - Palantirnet\PalantirBehatExtension\Context\MarkupContext
        - Palantirnet\PalantirBehatExtension\Context\NodeContext
        - Palantirnet\PalantirBehatExtension\Context\SharedDrupalContext
```

----
@copyright (c) Copyright 2015 Palantir.net, Inc.
