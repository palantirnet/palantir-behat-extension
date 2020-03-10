<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalFileContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context class with additional file-related steps.
 */
class DrupalFileContext extends SharedDrupalContext
{


    /**
     * Create a Drupal file record.
     *
     * @Given the file :filename
     *
     * @param string $filename The name of a file within the MinkExtension's files_path directory.
     * @param int    $status   1 if the file is permanent and should not be deleted; 0 if the file is temporary. Defaults to 1.
     *
     * @return void
     */
    public function createFile($filename, $status = 1)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $file = (object) array(
                          'filename' => $filename,
                          'status'   => $status,
                         );

        $file = $this->expandFile($file);

        $this->fileCreate($file);

    }//end createFile()


    /**
     * Create a set of Drupal file records.
     *
     * @Given files:
     *
     *   Given files:
     *     | filename    | status | author   |
     *     | example.pdf | 1      | Somebody |
     *     | test.png    | 0      | Admin    |
     *     | ...         | ...    | ...      |
     *
     * @param TableNode $filesTable A hash of file property objects.
     *
     * @return void
     */
    public function createFiles(TableNode $filesTable)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        foreach ($filesTable->getHash() as $fileHash) {
            $file = (object) $fileHash;
            $file = $this->expandFile($file);

            $this->fileCreate($file);
        }

    }//end createFiles()


}//end class
