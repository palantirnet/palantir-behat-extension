<?php

namespace Palantirnet\PalantirExtension\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;

class SharedContext extends RawDrupalContext
{

    /**
     * Get node object by its title.
     *
     * @return stdclass
     *   The Drupal node object, if it exists.
     */
    public function findNodeByTitle($contentType, $title)
    {
        $query = new EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', $contentType)
            ->propertyCondition('title', $title)
            ->range(0, 1)
            ->execute();

        if (!empty($entities['node'])) {
            $nid = key($entities['node']);
            return node_load($nid);
        }
        else {
            throw new \Exception(sprintf('No published "%s" node entitled "%s" exists', $contentType, $title));
        }
    }

    /**
     * Returns an existing node object, or creates one if it doesn't exist yet.
     *
     * @return stdclass
     *   A Drupal node object.
     */
    protected function getNodeByTitle($contentType, $title)
    {
        try {
            $node = $this->findNodeByTitle($contentType, $title);
        }
        catch (\Exception $e) {
            $new_node = (object) array(
              'title' => $title,
              'type' => $contentType,
              'body' => $this->getRandom()->string(255),
            );

            $node = $this->nodeCreate($new_node);
        }

        return $node;
    }

}
