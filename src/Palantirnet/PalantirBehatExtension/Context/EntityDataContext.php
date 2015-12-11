<?php
/**
 * @file
 * Behat context class with steps to examine entity property and field data.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalDriverManager;

/**
 * Behat context for examining Drupal data.
 *
 * For example:
 *
 * Scenario: Verify field values on a node.
 *   Given I am an anonymous user
 *   When I examine the "page" node with title "My Test Page"
 *   Then entity property "status" should be "1"
 *   And entity field "field_example" should contain "Example value"
 *   And entity link field "field_example_link" url should contain "http://example.com"
 *   And I dump the contents of "field_example_link"
 */
class EntityDataContext extends SharedDrupalContext
{

    protected $currentEntity = NULL;
    protected $currentEntityType = NULL;


    /**
     * @When I examine the :contentType( node) with title :title
     */
    public function assertNodeByTitle($contentType, $title)
    {
        $node = $this->findNodeByTitle($contentType, $title);

        $this->currentEntity = $node;
        $this->currentEntityType = 'node';
    }

    /**
     * @When I examine the :termName term in the :vocabulary( vocabulary)
     */
    public function assertTermByName($termName, $vocabulary)
    {
        $term = $this->findTermByName($termName, $vocabulary);

        $this->currentEntity = $term;
        $this->currentEntityType = 'taxonomy_term';
    }

    /**
     * @When I examine the user :userName
     */
    public function assertUserByName($userName)
    {
        $account = $this->findUserByName($userName);

        $this->currentEntity = $account;
        $this->currentEntityType = 'user';
    }

    /**
     * @Then entity property :property should be :value
     */
    public function assertEntityPropertyValue($property, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        if ($wrapper->$property->value() != $value) {
            throw new \Exception(sprintf('Property "%s" is not "%s"', $property, $value));
        }
    }

    /**
     * @Then entity property :property should not be :value
     */
    public function assertNotEntityPropertyValue($property, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        if ($wrapper->$property->value() == $value) {
            throw new \Exception(sprintf('Property "%s" is "%s"', $property, $value));
        }
    }

    /**
     * @Then entity field :field should contain :value
     */
    public function assertEntityFieldValue($field, $value)
    {
        if (empty($field) || empty($value)) { return; }

        // Use a per-field-type test method, if it is present.
        $field_info = field_info_field($field);
        if (empty($field_info)) {
            throw new \Exception(sprintf('Field "%s" does not exist', $field));
        }

        $method_name = 'assertEntityFieldValue' . str_replace(' ', '', ucwords(str_replace('_', ' ', $field_info['type'])));
        if (method_exists($this, $method_name)) {
          return $this->$method_name($field, $value);
        }

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();
        $field_value = is_array($field_value) ? $field_value : array($field_value);

        if (!in_array($value, $field_value)) {
            throw new \Exception(sprintf('Field "%s" does not contain "%s"', $field, $value));
        }
    }

    /**
     * @Then entity field :field should not contain :value
     */
    public function assertNotEntityFieldValue($field, $value)
    {
        try {
            $this->assertEntityFieldValue($field, $value);
        }
        catch (\Exception $e) {
            // Ignore the exception and return, since we're looking for NOT.
            return;
        }

        throw new \Exception(sprintf('Field "%s" contains "%s"', $field, $value));
    }

    /**
     * Test a link field for its URL value.
     *
     * @param string $field
     *   A Drupal field name.
     * @param mixed $value
     *   The value to look for.
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueLinkField($field, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();
        $field_value = !isset($field_value['url']) ? $field_value : array($field_value);

        foreach ($field_value as $f) {
            if ($f['url'] == $value) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain "%s"', $field, $value));
    }

    /**
     * Test a text field for a partial string.
     *
     * @param string $field
     *   A Drupal field name.
     * @param mixed $value
     *   The value to look for.
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueTextLong($field, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();

        // Note that text field values may be:
        // - a string
        // - an array('value' => '...', 'format' => '...', 'safe_value' => '...')
        // - an array of array('value' => '...', 'format' => '...', 'safe_value' => '...')
        // ... which makes it somewhat hard to tell single values from multiple
        // values.
        if (is_string($field_value)) {
          $field_value = array(array('value' => $field_value));
        }
        elseif (is_array($field_value) && isset($field_value['value'])) {
          $field_value = array($field_value);
        }
        elseif (!is_array($field_value)) {
          $field_value = array();
        }

        foreach ($field_value as $f) {
            if (strpos($f['value'], $value) !== FALSE) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain partial text "%s"', $field, $value));
    }

    /**
     * Test a file field for a Drupal stream wrapper URI.
     *
     * @param string $field
     *   A Drupal field name.
     * @param mixed $value
     *   The value to look for.
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueFile($field, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();

        // Note that file field values are array('fid' => '...', ... ),
        // which makes it somewhat hard to tell single values from multiple values.
        $field_value = !isset($field_value['fid']) ? $field_value : array($field_value);

        foreach ($field_value as $f) {
            if (strpos($f['uri'], $value) !== FALSE) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain file with URI "%s"', $field, $value));
    }

    /**
     * Test a taxonomy term reference field for a term name.
     *
     * @param string $field
     *   A Drupal field name.
     * @param mixed $value
     *   The value to look for.
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueTaxonomyTermReference($field, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();

        if (!empty($field_value)) {
            // Term field values are term objects.
            $field_value = is_array($field_value) ? $field_value : array($field_value);

            foreach ($field_value as $term) {
                if (is_object($term) && !empty($term->name) && $term->name == $value) {
                    return;
                }
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain term "%s"', $field, $value));
    }

    /**
     * Test an entity reference field for an entity label.
     *
     * @param string $field
     *   A Drupal field name.
     * @param mixed $value
     *   The value to look for.
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueEntityReference($field, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        $field_value = $wrapper->$field->value();

        // Use the entityreference selection handler to get the label for each
        // referenced entity.
        $field_info = field_info_field($field);
        $handler = entityreference_get_selection_handler($field_info);

        if (!empty($field_value)) {
            foreach ($field_value as $entity) {
                $label = $handler->getLabel($entity);
                if ($label === $value) {
                    return;
                }
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain entity with label "%s"', $field, $value));
    }

    /**
     * Test a date field for some date.
     *
     * @param string $field
     *   A Drupal field name.
     * @param mixed $value
     *   The value to look for.
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueDatetime($field, $value)
    {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        $field_value = $wrapper->$field->value();

        if (is_scalar($field_value)) {
            $field_value = array($field_value);
        }

        if (!is_array($field_value)) {
            $field_value = array();
        }

        foreach ($field_value as $v) {
            if (is_array($v)) {
                if (array_key_exists('value', $v)) {
                    if (strtotime($value) == strtotime($v['value'])) {
                        return;
                    }
                }

                if (array_key_exists('value2', $v)) {
                    if (strtotime($value) == strtotime($v['value2'])) {
                        return;
                    }
                }
            }

            if (strtotime($value) == $v) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain datetime "%s" (%s)', $field, strtotime($value), $value));
    }

    /**
     * @Then I dump the contents of( field) :field
     */
    public function dumpField($field) {
        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        $field_value = $wrapper->$field->value();
        print_r($field_value);
    }
}
