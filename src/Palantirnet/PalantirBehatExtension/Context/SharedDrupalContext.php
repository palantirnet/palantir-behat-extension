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
     * @param string $contentType
     *  A Drupal content type machine name.
     * @param string $title
     *  The title of a Drupal node.
     * @param string $language
     *  The language the node is in.
     *
     * @throws \Exception if no node with title $title was found.
     * @throws \Exception if node is not available in that language.
     * @throws \Exception if multiple nodes with title $title are found.
     *
     * @return stdclass
     *   The Drupal node object, if it exists.
     */
    public function findNodeByTitle($contentType, $title, $language = NULL)
    {
        /**
         * @var $query \Drupal\Core\Entity\Query\QueryInterface
         */
        $query = \Drupal::entityQuery('node');

        $entities = $query
            ->condition('type', $contentType)
            ->condition('title', $title)
            ->execute();

        if (count($entities) === 1) {
            $node_storage = \Drupal::entityManager()->getStorage('node');

            // `entityQuery` will return an array of node IDs with key and
            // value equal to the nids.
            // Example: `[123 => '123', 456 => '456']`. For this reason, even
            // though there is only a single element, we cannot access the
            // first element using `$entities[0]`.
            $nid = array_shift($entities);

            $node = $node_storage->load($nid);

            if (!is_null($language)) {
                if ($node->hasTranslation($language)) {
                    $node = $node->getTranslation($language);
                }
                else {
                    throw new \Exception('The node is not available in that language.');
                }
            }

            return $node;
        } else if (count($entities) > 1) {
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
     * @return Drupal\taxonomy\TermInterface
     *   The Drupal term object, if it exists.
     */
    public function findTermByName($termName, $vocabulary)
    {
        /**
         * @var $query \Drupal\Core\Entity\Query\QueryInterface
         */
        $query = \Drupal::entityQuery('taxonomy_term');

        $entities = $query
            ->condition('name', $termName)
            ->condition('vid', $vocabulary)
            ->execute();

        if (count($entities) === 1) {
            $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
            $tid = array_shift($entities);

            $term = $term_storage->load($tid);

            return $term;
        } else if (count($entities) > 1) {
            throw new \Exception(sprintf('Found more than one term with name "%s"', $termName));
        } else {
            throw new \Exception(sprintf('No term with name "%s" exists', $termName));
        }
    }//end findTermByName()

    /**
     * Get a term object by machine name and vocabulary.
     *
     * @param string $machineName   A Drupal taxonomy term machine name.
     * @param string $vocabulary The machine name of a Drupal taxonomy vocabulary.
     *
     * @return Drupal\taxonomy\TermInterface
     *   The Drupal term object, if it exists.
     */
    public function findTermByMachineName($machineName, $vocabulary)
    {
        /**
         * @var $query \Drupal\Core\Entity\Query\QueryInterface
         */
        $query = \Drupal::entityQuery('taxonomy_term');

        $entities = $query
            ->condition('machine_name', $machineName)
            ->condition('vid', $vocabulary)
            ->execute();

        if (count($entities) === 1) {
            $term_storage = \Drupal::entityTypeManager()->getStorage('taxonomy_term');
            $tid = array_shift($entities);

            $term = $term_storage->load($tid);

            return $term;
        } else if (count($entities) > 1) {
            throw new \Exception(sprintf('Found more than one term with machine name "%s"', $machineName));
        } else {
            throw new \Exception(sprintf('No term with machine name "%s" exists', $machineName));
        }
    }//end findTermByMachineName()


    /**
     * Get block object by its description.
     *
     * @param string $blockType
     *  A Drupal block type machine name.
     * @param string $info
     *  The info of a Drupal block.
     * @param string $language
     *  Optional language code.
     *
     * @throws \Exception if block is not found.
     * @throws \Exception if multiple blocks with info $info are found.
     *
     * @return \Drupal\block_content\Entity\BlockContent|bool
     *  The Drupal block object, if it exists or FALSE.
     */
    public function findBlockByInfo($blockType, $info, $language = NULL)
    {
        /**
         * @var $query \Drupal\Core\Entity\Query\QueryInterface
         */
        $query = \Drupal::entityQuery('block_content');

        $entities = $query
            ->condition('type', $blockType)
            ->condition('info', $info)
            ->execute();

        if (count($entities) === 1) {
            $block_storage = \Drupal::entityTypeManager()->getStorage('block_content');
            $id = array_shift($entities);

            $block = $block_storage->load($id);

            if (!is_null($language)) {
                if ($block->hasTranslation($language)) {
                    $block = $block->getTranslation($language);
                }
                else {
                    throw new \Exception('The block is not available in that language.');
                }
            }

            return $block;
        } else if (count($entities) > 1) {
            throw new \Exception(sprintf('Found more than one "%s" block with info "%s"', $blockType, $info));
        } else {
            throw new \Exception(sprintf('No "%s" blocks with info "%s" exists', $blockType, $info));
        }

    }//end findBlockByInfo()


    /**
     * Get media object by its name.
     *
     * @param string $mediaType
     *  A Drupal media type machine name.
     * @param string $name
     *  The name of a Drupal media entity.
     * @param string $language
     *  Optional language code.
     *
     * @throws \Exception if media is not found.
     * @throws \Exception if multiple media entities with name $name are found.
     *
     * @return \Drupal\media\Entity\Media|bool
     *  The Drupal media object, if it exists or FALSE.
     */
    public function findMediaByName($mediaType, $name, $language = NULL)
    {
        /**
         * @var $query \Drupal\Core\Entity\Query\QueryInterface
         */
        $query = \Drupal::entityQuery('media');

        $entities = $query
            ->condition('bundle', $mediaType)
            ->condition('name', $name)
            ->execute();

        if (count($entities) === 1) {
            $block_storage = \Drupal::entityTypeManager()->getStorage('media');
            $id = array_shift($entities);

            $media = $block_storage->load($id);

            if (!is_null($language)) {
                if ($media->hasTranslation($language)) {
                    $media = $media->getTranslation($language);
                }
                else {
                    throw new \Exception('The media entity is not available in that language.');
                }
            }

            return $media;
        } else if (count($entities) > 1) {
            throw new \Exception(sprintf('Found more than one "%s" media entity with name "%s"', $mediaType, $name));
        } else {
            throw new \Exception(sprintf('No "%s" media entities with name "%s" exists', $mediaType, $name));
        }

    }//end findMediaByName()


    /**
     * Get a user object by name.
     *
     * @param string $userName The name of a Drupal user.
     *
     * @throws \Exception if user is not found.
     * @throws \Exception if multiple user with name $userName are found.
     *
     * @return \Drupal\User\Entity\User|bool
     *   A Drupal user object or FALSE if it does not exist.
     */
    public function findUserByName($userName)
    {
        /**
         * @var $query \Drupal\Core\Entity\Query\QueryInterface
         */
        $query = \Drupal::entityQuery('user');

        $entities = $query
            ->condition('name', $userName)
            ->execute();

        if (count($entities) === 1) {
            $user_storage = \Drupal::entityTypeManager()->getStorage('user');
            $uid = array_shift($entities);

            $user = $user_storage->load($uid);

            return $user;
        } else if (count($entities) > 1) {
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
