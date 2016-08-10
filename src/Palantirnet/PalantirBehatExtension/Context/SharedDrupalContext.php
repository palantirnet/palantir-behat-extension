<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\SharedDrupalContext.
 *
 * @todo this work is currently tied to Drupal 7, because it runs some Drupal
 *       code, rather than using the DrupalDriver cores. This needs to be fixed,
 *       but lots of the functionality we want is not available in the core
 *       classes. At the very least, we should be able to get the Drupal version
 *       with $this->getDriver()->getDrupalVersion().
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use Drupal\file\Entity\File;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context class with functionality that is shared across custom contexts.
 *
 * This class should not contain new Gherkin syntax.
 *
 * @todo can this class be abstract?
 */
class SharedDrupalContext extends RawDrupalContext
{


    /**
     * Get node object by its title.
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title       The title of a Drupal node.
     *
     * @return stdclass
     *   The Drupal node object, if it exists.
     */
    public function findNodeByTitle($contentType, $title)
    {
        throw new NotUpdatedException();

        $query = new \EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'node')
            ->entityCondition('bundle', $contentType)
            ->propertyCondition('title', $title)
            ->execute();

        if (empty($entities['node']) === false && count($entities['node']) === 1) {
            $nid = key($entities['node']);
            return node_load($nid);
        } else if (empty($entities['node']) === false && count($entities['node']) > 1) {
            throw new \Exception(sprintf('Found more than one "%s" node entitled "%s"', $contentType, $title));
        } else {
            throw new \Exception(sprintf('No "%s" node entitled "%s" exists', $contentType, $title));
        }

    }//end findNodeByTitle()


    /**
     * Get node object by its title, creating the node if it does not yet exist.
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title       The title of a Drupal node.
     *
     * @return stdclass
     *   A Drupal node object.
     */
    protected function getNodeByTitle($contentType, $title)
    {
        throw new NotUpdatedException();

        try {
            $node = $this->findNodeByTitle($contentType, $title);
        }
        catch (\Exception $e) {
            $new_node = (object) array(
                                  'title' => $title,
                                  'type'  => $contentType,
                                  'body'  => $this->getRandom()->string(255),
                                 );

            $node = $this->nodeCreate($new_node);
        }

        return $node;

    }//end getNodeByTitle()


    /**
     * Get a term object by name and vocabulary.
     *
     * @param string $termName   A Drupal taxonomy term name.
     * @param string $vocabulary The machine name of a Drupal taxonomy vocabulary.
     *
     * @return stdclass
     *   The Drupal term object, if it exists.
     */
    public function findTermByName($termName, $vocabulary)
    {
        throw new NotUpdatedException();

        $query = new \EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'taxonomy_term')
            ->entityCondition('bundle', $vocabulary)
            ->propertyCondition('name', $termName)
            ->execute();

        if (empty($entities['taxonomy_term']) === false && count($entities['taxonomy_term']) === 1) {
            $id = key($entities['taxonomy_term']);
            return taxonomy_term_load($id);
        } else if (empty($entities['taxonomy_term']) === false && count($entities['taxonomy_term']) > 1) {
            throw new \Exception(sprintf('Found more than one "%s" term entitled "%s"', $vocabulary, $termName));
        } else {
            throw new \Exception(sprintf('No "%s" term entitled "%s" exists', $vocabulary, $termName));
        }

    }//end findTermByName()


    /**
     * Get a user object by name.
     *
     * @param string $userName The name of a Drupal user.
     *
     * @return stdclass
     *   The Drupal user object, if it exists.
     */
    public function findUserByName($userName)
    {
        throw new NotUpdatedException();

        $query = new \EntityFieldQuery();

        $entities = $query->entityCondition('entity_type', 'user')
            ->propertyCondition('name', $userName)
            ->execute();

        if (empty($entities['user']) === false && count($entities['user']) === 1) {
            $id = key($entities['user']);
            return user_load($id);
        } else if (empty($entities['user']) === false && count($entities['user']) > 1) {
            throw new \Exception(sprintf('Found more than one user named "%s"', $userName));
        } else {
            throw new \Exception(sprintf('No user named "%s" exists', $userName));
        }

    }//end findUserByName()


}//end class
