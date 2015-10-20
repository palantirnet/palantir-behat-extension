<?php
/**
 * @file
 * Behat context for use with Workbench Moderation.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Hook\Scope\BeforeNodeCreateScope;

class DrupalWorkbenchModerationContext extends SharedDrupalContext
{

    /**
     * @var bool
     *   Whether workbench_moderation should be disabled for nodes created
     *   during the current scenario.
     */
    var $disableWorkbenchModeration = FALSE;

    /**
     * @BeforeScenario @disableWorkbenchModeration
     *
     * Tag scenarios with "@disableWorkbenchModeration" to bypass moderation
     * during particular tests.
     */
    public function disableWorkbenchModeration()
    {
        $this->disableWorkbenchModeration = TRUE;
    }

    /**
     * @BeforeNodeCreate
     */
    public function prepareWorkbenchModerationNode(BeforeNodeCreateScope $scope)
    {
        if ($this->disableWorkbenchModeration) {
            $node = $scope->getEntity();
            $node->status = 1;
            
            // This is a hack; workbench_moderation_node_update() will return
            // without applying moderation if it thinks that it is being called
            // recursively.
            $node->updating_live_revision = 'behat_skip';
        }
    }

}
