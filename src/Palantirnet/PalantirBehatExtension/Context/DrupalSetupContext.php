<?php
/**
 * Contains the Palantirnet\PalantirBehatExtension\Context\DrupalSetupContext class.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

/**
 * Behat context for validating Drupal configuration.
 */
class DrupalSetupContext extends SharedDrupalContext
{


    /**
     * Verify that Drupal is running.
     *
     * @Given a Drupal site
     *
     * @return void
     */
    public function assertDrupal()
    {
        if ($this->getDriver()->isBootstrapped() === false) {
            throw new \Exception('The Drupal site is not bootstrapped.');
        }

    }//end assertDrupal()


    /**
     * Verify that a module is installed.
     *
     * @Then the :module module is installed
     *
     * @param string $module The machine name of a Drupal module.
     *
     * @return void
     */
    public function assertModuleInstalled($module)
    {
        if (module_exists($module) === false) {
            throw new \Exception(sprintf('The "%s" module is not installed.', $module));
        }

    }//end assertModuleInstalled()


    /**
     * Verify that all exported Features are in their default states.
     *
     * @Then no Drupal features are overridden
     *
     * @return void
     */
    public function assertDefaultDrupalFeatures()
    {
        $this->assertModuleInstalled('features');

        module_load_include('inc', 'features', 'features.export');
        $features = features_get_features(null, true);

        $overridden = array();
        foreach ($features as $k => $m) {
            if (features_get_storage($m->name) === FEATURES_OVERRIDDEN) {
                $overridden[] = $m->name;
            }
        }

        if (empty($overridden) === false) {
            throw new \Exception(sprintf('%d Drupal features are overridden: %s.', count($overridden), implode(', ', $overridden)));
        }

    }//end assertDefaultDrupalFeatures()


}//end class
