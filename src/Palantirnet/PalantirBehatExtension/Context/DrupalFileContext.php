<?php
/**
 * @file
 * Behat context class with additional file-related steps.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;

class DrupalFileContext extends SharedDrupalContext
{

    /**
     * @Given the file :filename
     */
    public function createFile($filename, $status = 1)
    {
        $file = (object) array(
            'filename' => $filename,
            'status' => $status,
        );

        $file = $this->expandFile($file);

        $this->fileCreate($file);
    }

    /**
     * @Given files:
     *
     *   Given files:
     *     | filename    | status | author   |
     *     | example.pdf | 1      | Somebody |
     *     | test.png    | 0      | Admin    |
     *     | ...         | ...    | ...      |
     */
    public function createFiles(TableNode $filesTable)
    {
        foreach ($filesTable->getHash() as $fileHash) {
            $file = (object) $fileHash;
            $file = $this->expandFile($file);

            $this->fileCreate($file);
        }
    }

}
