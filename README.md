# Palantir Behat Drupal Extension

The Palantir Behat Drupal Extension provides additional step definitions for testing Drupal sites using [Behat](http://behat.org),
[Mink Extension](https://github.com/Behat/MinkExtension).

## Usage

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
