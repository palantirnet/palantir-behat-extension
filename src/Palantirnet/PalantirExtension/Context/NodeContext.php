<?php

namespace Palantirnet\PalantirExtension\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;

class NodeContext extends SharedContext
{

    /**
     * @When I view the :contentType content :title
     */
    public function assertNodeByTitle($contentType, $title)
    {
        $node = $this->findNodeByTitle($contentType, $title);
        $this->getSession()->visit($this->locatePath("node/{$node->nid}"));
    }

    /**
     * @When I edit the :contentType content :title
     */
    public function assertEditNodeByTitle($contentType, $title)
    {
        $node = $this->findNodeByTitle($contentType, $title);
        $this->getSession()->visit($this->locatePath("node/{$node->nid}/edit"));
        $this->assertSession()->statusCodeEquals('200');
    }
}
