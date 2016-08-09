<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalWorkbenchModerationContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Hook\Scope\BeforeNodeCreateScope;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context for use with Workbench Moderation.
 */
class DrupalWorkbenchModerationContext extends SharedDrupalContext
{

    /**
     * Whether workbench_moderation should be disabled for nodes created during the
     * current scenario.
     *
     * @var bool
     */
    protected $disableWorkbenchModeration = false;


    /**
     * Called before a scenario begins.
     *
     * Tag scenarios with "@disableWorkbenchModeration" to bypass moderation
     * during particular tests.
     *
     * @BeforeScenario @disableWorkbenchModeration
     *
     * @return void
     */
    public function disableWorkbenchModeration()
    {
        $this->disableWorkbenchModeration = true;

    }//end disableWorkbenchModeration()


    /**
     * Called automatically by the RawDrupalContext class before creating a node.
     *
     * This hijacks the updating_live_revision property to disable Workbench
     * Moderation for the node being created.
     *
     * @BeforeNodeCreate
     *
     * @param BeforeNodeCreateScope $scope The Behat hook scope.
     *
     * @return void
     */
    public function prepareWorkbenchModerationNode(BeforeNodeCreateScope $scope)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if ($this->disableWorkbenchModeration === true) {
            $node         = $scope->getEntity();
            $node->status = 1;

            // This is a hack; workbench_moderation_node_update() will return
            // without applying moderation if it thinks that it is being called
            // recursively.
            $node->updating_live_revision = 'behat_skip';
        }

    }//end prepareWorkbenchModerationNode()


}//end class
