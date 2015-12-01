<?php
/**
 * @file
 * Behat context for validating Drupal configuration.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

class DrupalSetupContext extends SharedDrupalContext
{


    /**
     * @Given a Drupal site
     */
    public function assertDrupal()
    {
        if (!$this->getDriver()->isBootstrapped()) {
            throw new \Exception('The Drupal site is not bootstrapped.');
        }

    }//end assertDrupal()


    /**
     * @Then the :module module is installed
     */
    public function assertModuleInstalled($module)
    {
        if (!module_exists('features')) {
            throw new \Exception(sprintf('The "%s" module is not installed.', $module));
        }

    }//end assertModuleInstalled()


    /**
     * @Then no Drupal features are overridden
     */
    public function assertDefaultDrupalFeatures()
    {
        $this->assertModuleInstalled('features');

        module_load_include('inc', 'features', 'features.export');
        $features = features_get_features(null, true);

        $overridden = array();
        foreach ($features as $k => $m) {
            if (features_get_storage($m->name) == FEATURES_OVERRIDDEN) {
                $overridden[] = $m->name;
            }
        }

        if (!empty($overridden)) {
            throw new \Exception(sprintf('%d Drupal features are overridden: %s.', count($overridden), implode(', ', $overridden)));
        }

    }//end assertDefaultDrupalFeatures()


}//end class
