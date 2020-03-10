<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\MarkupContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Context\MarkupContext as DrupalExtensionMarkupContext;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context class with functionality that is shared across custom contexts.
 */
class MarkupContext extends DrupalExtensionMarkupContext
{


    /**
     * Output the markup of a particular region.
     *
     * @Then I dump the :region region
     *
     * @param string $region A region name from the behat config.
     *
     * @return void
     */
    public function dumpRegion($region)
    {
        print $this->getRegion($region)->getHTML()."\r\n";

    }//end dumpRegion()


    /**
     * Output the current URL.
     *
     * @Then I dump the URL
     *
     * @return void
     */
    public function dumpUrl()
    {
        print $this->getSession()->getCurrentUrl()."\r\n";

    }//end dumpUrl()


    /**
     * Test for the presence of a tag containing some text.
     *
     * @Then I( should) see :text in the :tag element in the :region( region)
     *
     * @param string $text   The text to look for.
     * @param string $tag    A CSS selector.
     * @param string $region A region name from the behat config.
     *
     * @return void
     */
    public function assertRegionElementText($text, $tag, $region)
    {
        $regionObj = $this->getRegion($region);
        $results   = $regionObj->findAll('css', $tag);

        $found = false;
        if (empty($results) === false) {
            foreach ($results as $result) {
                if ($result->getText() === $text) {
                    $found = true;
                }
            }
        }

        if ($found === false) {
            throw new \Exception(sprintf('The text "%s" was not found in the "%s" element in the "%s" region on the page %s', $text, $tag, $region, $this->getSession()->getCurrentUrl()));
        }

    }//end assertRegionElementText()


    /**
     * Validate that a particular field label appears on the page.
     *
     * @Then I should see a/an :label field
     *
     * @param string $label The field label text.
     *
     * @return void
     */
    public function assertFieldByLabel($label)
    {
        $page  = $this->getSession()->getPage();
        $field = $page->findField($label);

        if (null === $field) {
            throw new \Exception(sprintf('Field with label "%s" was not found on the page %s.', $label, $this->getSession()->getCurrentUrl()));
        }

    }//end assertFieldByLabel()


    /**
     * Validate that a particular fieldset label does not appear on the page.
     *
     * @Then I should not see a/an :label field
     *
     * @param string $label The fieldset label text.
     *
     * @return void
     */
    public function assertNotFieldByLabel($label)
    {
        try {
            $this->assertFieldByLabel($label);
        }
        catch (\Exception $e) {
            return;
        }

        throw new \Exception(sprintf('Found field with label "%s" on page %s', $label, $this->getSession()->getCurrentUrl()));

    }//end assertNotFieldByLabel()


    /**
     * Validate that a particular fieldset label appears on the page.
     *
     * @Then I should see a/an :label multivalue field
     *
     * @param string $label The fieldset label text.
     *
     * @return void
     */
    public function assertFieldsetByLabel($label)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $page     = $this->getSession()->getPage();
        $legend   = $this->getSession()->getSelectorsHandler()->xpathLiteral($label);
        $fieldset = $page->find('named', array('fieldset', $legend));

        if (null === $fieldset) {
            throw new \Exception(sprintf('The multivalue field with label "%s" was not found on the page %s.', $label, $this->getSession()->getCurrentUrl()));
        }

    }//end assertFieldsetByLabel()


}//end class
