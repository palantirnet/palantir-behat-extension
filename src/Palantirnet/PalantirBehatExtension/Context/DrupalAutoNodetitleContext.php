<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalAutoNodetitleContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Hook\Scope\BeforeNodeCreateScope;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context for use with Auto Nodetitle.
 */
class DrupalAutoNodetitleContext extends SharedDrupalContext
{

    /**
     * Whether auto_nodetitle should be disabled for nodes created during the current
     * scenario.
     *
     * @var bool
     */
    protected $disableAutoNodetitle = false;


    /**
     * Called before a scenario begins.
     *
     * Tag scenarios with "@disableAutoNodetitle" to bypass automatic title
     * generation during particular tests; sometimes this is required in order
     * to have predictable test content.
     *
     * @BeforeScenario @disableAutoNodetitle
     *
     * @return void
     */
    public function disableAutoNodetitle()
    {
        $this->disableAutoNodetitle = true;

    }//end disableAutoNodetitle()


    /**
     * Called automatically by the RawDrupalContext class before creating a node.
     *
     * This hijacks the auto_nodetitle_applied property to prevent Auto Nodetitle
     * from trying to generate a title.
     *
     * @BeforeNodeCreate
     *
     * @param BeforeNodeCreateScope $scope The Behat hook scope.
     *
     * @return void
     */
    public function prepareAutoNodetitleNode(BeforeNodeCreateScope $scope)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if ($this->disableAutoNodetitle === true) {
            $node = $scope->getEntity();
            $node->auto_nodetitle_applied = true;
        }

    }//end prepareAutoNodetitleNode()


}//end class
