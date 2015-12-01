<?php
/**
 * @file
 * Behat context class with functionality that is shared across custom contexts.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Drupal\DrupalExtension\Context\MarkupContext as DrupalExtensionMarkupContext;

class MarkupContext extends DrupalExtensionMarkupContext
{


    /**
     * @Then I dump the :region region
     */
    public function dumpRegion($region)
    {
        print $this->getRegion($region)->getHTML()."\r\n";

    }//end dumpRegion()


    /**
     * @Then I dump the URL
     */
    public function dumpUrl()
    {
        print $this->getSession()->getCurrentUrl()."\r\n";

    }//end dumpUrl()


    /**
     * @Then I( should) see :text in the :tag element in the :region( region)
     */
    public function assertRegionElementText($text, $tag, $region)
    {
        $regionObj = $this->getRegion($region);
        $results   = $regionObj->findAll('css', $tag);

        $found = false;
        if (!empty($results)) {
            foreach ($results as $result) {
                if ($result->getText() == $text) {
                    $found = true;
                }
            }
        }

        if (!$found) {
            throw new \Exception(sprintf('The text "%s" was not found in the "%s" element in the "%s" region on the page %s', $text, $tag, $region, $this->getSession()->getCurrentUrl()));
        }

    }//end assertRegionElementText()


    /**
     * @Then I should not see :text in the :tag element in the :region( region)
     */
    public function assertNotRegionElementText($text, $tag, $region)
    {
        $regionObj = $this->getRegion($region);
        $results   = $regionObj->findAll('css', $tag);

        $found = false;
        if (!empty($results)) {
            foreach ($results as $result) {
                if ($result->getText() == $text) {
                    $found = true;
                }
            }
        }

        if ($found) {
            throw new \Exception(sprintf('The text "%s" was found in the "%s" element in the "%s" region on the page %s', $text, $tag, $region, $this->getSession()->getCurrentUrl()));
        }

    }//end assertNotRegionElementText()


    /**
     * @Then I should see a/an :label field
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
     * @Then I should not see a/an :label field
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
     * @Then I should see a/an :label multivalue field
     */
    public function assertFieldsetByLabel($label)
    {
        $page     = $this->getSession()->getPage();
        $legend   = $this->getSession()->getSelectorsHandler()->xpathLiteral($label);
        $fieldset = $page->find('named', array('fieldset', $legend));

        if (null === $fieldset) {
            throw new \Exception(sprintf('The multivalue field with label "%s" was not found on the page %s.', $label, $this->getSession()->getCurrentUrl()));
        }

    }//end assertFieldsetByLabel()


}//end class
