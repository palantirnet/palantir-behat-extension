<?php
/**
 * @file
 * Behat context class with functionality that is shared across custom contexts.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Context\RawDrupalContext;

class SharedDrupalContext extends RawDrupalContext
{

    /**
     * Get node object by its title.
     *
     * @return stdclass
     *   The Drupal node object, if it exists.
     */
    public function findNodeByTitle($contentType, $title)
    {
        $query = new \EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', $contentType)
            ->propertyCondition('title', $title)
            ->execute();

        if (!empty($entities['node']) && count($entities['node']) == 1) {
            $nid = key($entities['node']);
            return node_load($nid);
        }
        elseif (!empty($entities['node']) && count($entities['node']) > 1) {
            throw new \Exception(sprintf('Found more than one "%s" node entitled "%s"', $contentType, $title));
        }
        else {
            throw new \Exception(sprintf('No "%s" node entitled "%s" exists', $contentType, $title));
        }
    }

    /**
     * Get node object by its title, creating the node if it does not yet exist.
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

    /**
     * Get a term object by name and vocabulary.
     *
     * @return stdclass
     *   The Drupal term object, if it exists.
     */
    public function findTermByName($termName, $vocabulary)
    {
        $query = new \EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'taxonomy_term')
            ->entityCondition('bundle', $vocabulary)
            ->propertyCondition('name', $termName)
            ->execute();

        if (!empty($entities['taxonomy_term']) && count($entities['taxonomy_term']) == 1) {
            $id = key($entities['taxonomy_term']);
            return taxonomy_term_load($id);
        }
        elseif (!empty($entities['taxonomy_term']) && count($entities['taxonomy_term']) > 1) {
            throw new \Exception(sprintf('Found more than one "%s" term entitled "%s"', $vocabulary, $termName));
        }
        else {
            throw new \Exception(sprintf('No "%s" term entitled "%s" exists', $vocabulary, $termName));
        }
    }

    /**
     * Get a user object by name.
     *
     * @return stdclass
     *   The Drupal user object, if it exists.
     */
    public function findUserByName($userName)
    {
        $query = new \EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'user')
            ->propertyCondition('name', $userName)
            ->execute();

        if (!empty($entities['user']) && count($entities['user']) == 1) {
            $id = key($entities['user']);
            return user_load($id);
        }
        elseif (!empty($entities['user']) && count($entities['user']) > 1) {
            throw new \Exception(sprintf('Found more than one user named "%s"', $userName));
        }
        else {
            throw new \Exception(sprintf('No user named "%s" exists', $userName));
        }
    }

}
