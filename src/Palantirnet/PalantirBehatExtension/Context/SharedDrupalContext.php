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

use Drupal\DrupalExtension\Context\RawDrupalContext;
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
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

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
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

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
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

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
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

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


    /**
     * Save a file.
     *
     * @param stdclass $file A simple object representing file data.
     *   Properties should be scalar values, and files may use either the 'uid' or
     *   'author' fields to attribute the file to a particular Drupal user.
     *
     * @return stdclass
     *   A Drupal file object.
     */
    public function fileCreate($file)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        // Save the file and overwrite if it already exists.
        $dest   = file_build_uri(drupal_basename($file->uri));
        $result = file_copy($file, $dest, FILE_EXISTS_REPLACE);

        // Stash the file object for later cleanup.
        if (empty($result->fid) === false) {
            $this->files[] = $result;
        } else {
            throw new \Exception(sprintf('File "%s" could not be copied from "%s" to "%s".', $file->filename, $file->uri, $result->uri));
        }

        return $result;

    }//end fileCreate()


    /**
     * Add required file properties.
     *
     * @param stdclass $file A simple object representing file data. The 'filename'
     *   property is required.
     *
     * @return stdclass
     *   A file object with at least the filename, uri, uid, and status
     *   properties.
     */
    public function expandFile($file)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if (empty($file->filename) === true) {
            throw new \Exception("Can't create file with no source filename; this should be the name of a file within the MinkExtension's files_path directory.");
        }

        // Set the URI to the path to the file within the MinkExtension's
        // files_path parameter.
        $file->uri = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$file->filename;

        // Assign authorship if none exists and `author` is passed.
        if (isset($file->uid) === false && empty($file->author) === false) {
            $account = user_load_by_name($file->author);
            if ($account !== false) {
                $file->uid = $account->uid;
            }
        }

        // Add default values.
        $defaults = array(
                     'uid'    => 0,
                     'status' => 1,
                    );

        foreach ($defaults as $key => $default) {
            if (isset($file->$key) === false) {
                $file->$key = $default;
            }
        }

        return $file;

    }//end expandFile()


    /**
     * Keep track of files so they can be cleaned up.
     *
     * @var array
     */
    protected $files = array();


    /**
     * Remove any created files.
     *
     * @AfterScenario
     *
     * @return void
     */
    public function cleanFiles()
    {
        /*
            @todo Update for Drupal 8
            @see NotUpdatedException

            foreach ($this->files as $file) {
                file_delete($file, true);
            }
        */

        $this->files = array();

    }//end cleanFiles()


}//end class
