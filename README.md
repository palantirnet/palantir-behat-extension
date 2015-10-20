# Palantir Behat Drupal Extension

The Palantir Behat Drupal Extension provides additional step definitions for testing Drupal sites using [Behat](http://behat.org),
[Mink Extension](https://github.com/Behat/MinkExtension).


## What can I do with this?

* Add `Palantirnet\PalantirBehatExtension\Context\NodeContext` to your `behat.yml` for extra steps for general Drupal tasks like viewing and editing nodes by title
* Extend `Palantirnet\PalantirBehatExtension\Context\MarkupContext` in your project and use the `assertRegionElementText` method to encapsulate complex markup in a custom context rather than in the Gherkin acceptance criteria
* Add `Palantirnet\PalantirBehatExtension\Context\DrupalCommentContext` to your `behat.yml` for extra steps to test commenting
* Add `Palantirnet\PalantirBehatExtension\Context\DrupalOrganicGroupsContext` to your `behat.yml` for extra steps to test access to Organic Groups
* Add `Palantirnet\PalantirBehatExtension\Context\EntityDataContext` to your `behat.yml` for extra steps to test field data and properties on nodes, terms, and users directly, without relying on output (... or write a simpletest...)
* Add `Palantirnet\PalantirBehatExtension\Context\DrupalAutoNodetitleContext` to your `behat.yml` and tag scenarios with `@disableAutoNodetitle` to bypass automatic title generation; sometimes this is required in order to have predictable test content
* Add `Palantirnet\PalantirBehatExtension\Context\DrupalWorkbenchModerationContext` to your `behat.yml` and tag scenarios with `@disableWorkbenchModeration` to bypass moderation

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
    "require": {
        "palantirnet/palantir-behat-extension": "dev-master"
    },
```

Then, add the specific contexts you want to use to your project's `behat.yml` file:

```
default:
  suites:
    default:
      contexts:
        - Palantirnet\PalantirBehatExtension\Context\NodeContext
```

----
@copyright (c) Copyright 2015 Palantir.net, Inc.
