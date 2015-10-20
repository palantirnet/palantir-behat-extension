<?php
/**
 * @file
 * Behat context for use with Auto Nodetitle.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Hook\Scope\BeforeNodeCreateScope;

class DrupalAutoNodetitleContext extends SharedDrupalContext
{

    /**
     * @var bool
     *   Whether auto_nodetitle should be disabled for nodes created
     *   during the current scenario.
     */
    var $disableAutoNodetitle = FALSE;

    /**
     * @BeforeScenario @disableAutoNodetitle
     *
     * Tag scenarios with "@disableAutoNodetitle" to bypass automatic title
     * generation during particular tests; sometimes this is required in order
     * to have predictable test content.
     */
    public function disableAutoNodetitle()
    {
        $this->disableAutoNodetitle = TRUE;
    }

    /**
     * @BeforeNodeCreate
     */
    public function prepareAutoNodetitleNode(BeforeNodeCreateScope $scope)
    {
        if ($this->disableAutoNodetitle) {
            $node = $scope->getEntity();
            $node->auto_nodetitle_applied = TRUE;
        }
    }

}
