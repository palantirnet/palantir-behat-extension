<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\NodeContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context class with additional node-related steps.
 */
class NodeContext extends SharedDrupalContext
{


    /**
     * Verify that a node exists and the current user can access its node page.
     *
     * @When I view the :contentType content :title
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title       The title of a Drupal node.
     *
     * @return void
     */
    public function assertNodeByTitle($contentType, $title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $node = $this->findNodeByTitle($contentType, $title);
        $this->getSession()->visit($this->locatePath("node/{$node->nid}"));

    }//end assertNodeByTitle()


    /**
     * Verify that a node exists and the current user can visit its edit form.
     *
     * @When I edit the :contentType content :title
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title       The title of a Drupal node.
     *
     * @return void
     */
    public function assertEditNodeByTitle($contentType, $title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $node = $this->findNodeByTitle($contentType, $title);
        $this->getSession()->visit($this->locatePath("node/{$node->nid}/edit"));
        $this->assertSession()->statusCodeEquals('200');

    }//end assertEditNodeByTitle()


}//end class
