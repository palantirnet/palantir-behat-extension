<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalFileContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;
use Behat\Mink\Element\NodeElement;
use Drupal;
use Drupal\file\Entity\File;
use Drupal\DrupalExtension\Hook\Scope\EntityScope;
use PHPUnit_Framework_Assert as Assert;

/**
 * Behat context class with additional file-related steps.
 */
class DrupalFileContext extends SharedDrupalContext
{


    /**
     * Register fields which hold files so we can grab the fid on node save.
     *
     * @var array File fields used in this context.
     */
    protected $fileFields = [];

    /**
     * Keep track of files so they can be cleaned up.
     *
     * @var array
     */
    protected $files = [];

    /**
     * Get the files managed in this context.
     * @return array
     */
    public function getFiles()
    {
        return $this->files;
    }//end getFiles()


    /**
     * Add a file to manage.
     *
     * @param string $filename The name of the file to manage.
     * @param File   $file     The file to manage.
     *
     * @return $this
     */
    public function addFile($filename, File $file)
    {

        $this->files[$filename] = $file->id();

        return $this;

    }//end addFile()

    /**
     * Add a file based on file id. Looks up the filename if not passed.
     *
     * @param int    $fid      The file id.
     * @param string $filename [optional] The name of the file.
     *
     * @return $this
     */
    public function addFileById($fid, $filename = '')
    {

        $filename = $filename ?: File::load($fid)->getFilename();
        $this->files[$filename] = $fid;

        return $this;

    }//end addFileById()


    /**
     * Get a specific file from the managed files.
     *
     * @param String $filename Original name of the file to get from behat step.
     *
     * @return File
     */
    public function getFile($filename)
    {
        return File::load($this->files[$filename]);
    }//end getFile()


    /**
     * Get the fields which may hold files.
     *
     * @return array
     */
    public function getFileFields()
    {
        return $this->fileFields;
    }//end getFileFields()


    /**
     * Add a field which may hold files.
     *
     * @param String $filename  Original name of the file to get from behat step.
     * @param String $fileField The field machine name.
     *
     * @return DrupalFileContext $this
     */
    public function addFileField($filename, $fileField)
    {
        $this->fileFields[$filename] = $fileField;
        return $this;
    }//end addFileField()


    /**
     * Replace file fields in an entity.
     *
     * @beforeNodeCreate
     *
     * @param EntityScope $scope The BeforeNodeCreateScope for this hook.
     *
     * @return \stdClass
     */
    public function replaceFiles(EntityScope $scope)
    {
        $fields = (array) $scope->getEntity();
        $node = $scope->getEntity();
        $prefix = 'file: {';
        foreach ($fields as $fieldname => $file) {
            if (TRUE === is_string($file) && 0 === strpos($file, $prefix)) {
                $filename = substr($file, strlen($prefix), strlen($file) - strlen($prefix) - 1);
                $this->addFileField($filename, $fieldname);
                $node->$fieldname = rtrim(realpath($this->getMinkParameter('files_path')), DIRECTORY_SEPARATOR).DIRECTORY_SEPARATOR.$filename;
            }
        }
    }//end replaceFiles()


    /**
     * Register files created on node create so we can delete them later.
     *
     * @afterNodeCreate
     *
     * @param EntityScope $scope The AfterNodeCreateScope of this hook.
     *
     * @return null
     */
    public function registerFiles(EntityScope $scope)
    {

        $node = $scope->getEntity();
        foreach ($this->getFileFields() as $filename => $field) {
            $value = $node->{$field};
            $this->addFileById($value['target_id'], $filename);
        }

    }//end registerFiles()


    /**
     * Assert an image is displayed on the page.
     *
     * @Then I should see the image :filename
     *
     * @param String $filename The filename of the image we expect to find.
     *
     * @return bool
     *
     * @throws \Exception
     */
    public function assertImage($filename)
    {

        // @var File $file
        $file = $this->getFile($filename);
        $filename = $file->getFilename();

        $imageElements = $this->getSession()->getPage()->findAll('css', 'img');
        $found = false;

        // @var NodeElement $image
        foreach ($imageElements as $image) {
            $imageUrl = $image->getAttribute('src');
            $imageFilename = basename(parse_url($imageUrl, PHP_URL_PATH));


            if ($imageFilename === $filename) {
                $found = true;
                $this->getSession()->visit($imageUrl);
                $statusCode = $this->getSession()->getStatusCode();
                $message = "Expected to find the image, $filename at $imageUrl. Got status code: {$statusCode}.";
                Assert::assertEquals("200", $statusCode, $message);
            }
        }

        Assert::assertTrue($found, "Could not find image: $filename");
    }//end assertImage()



    /**
     * Remove any created files.
     *
     * @AfterScenario
     *
     * @return void
     */
    public function cleanFiles()
    {
        $files = array_map(
            function ($filename) {
                return $this->getFile($filename);
            },
            array_keys($this->files)
        );

        $manager = Drupal::entityTypeManager()->getStorage('file');
        $manager->delete($files);

        $this->files = [];

    }//end cleanFiles()


}//end class
