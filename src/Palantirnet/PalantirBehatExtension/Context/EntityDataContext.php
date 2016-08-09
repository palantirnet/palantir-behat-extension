<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\EntityDataContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalDriverManager;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context class with steps to examine entity property and field data.
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

    protected $currentEntity     = null;
    protected $currentEntityType = null;


    /**
     * Verify field and property values of a node entity.
     *
     * @When I examine the :contentType( node) with title :title
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title       The title of a Drupal node.
     *
     * @return void
     */
    public function assertNodeByTitle($contentType, $title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $node = $this->findNodeByTitle($contentType, $title);

        $this->currentEntity     = $node;
        $this->currentEntityType = 'node';

    }//end assertNodeByTitle()


    /**
     * Verify field and property values of a taxonomy term entity.
     *
     * @When I examine the :termName term in the :vocabulary( vocabulary)
     *
     * @param string $termName   A Drupal taxonomy term name.
     * @param string $vocabulary The machine name of a Drupal taxonomy vocabulary.
     *
     * @return void
     */
    public function assertTermByName($termName, $vocabulary)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $term = $this->findTermByName($termName, $vocabulary);

        $this->currentEntity     = $term;
        $this->currentEntityType = 'taxonomy_term';

    }//end assertTermByName()


    /**
     * Verify field and property values of a user entity.
     *
     * @When I examine the user :userName
     *
     * @param string $userName The name of a Drupal user.
     *
     * @return void
     */
    public function assertUserByName($userName)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $account = $this->findUserByName($userName);

        $this->currentEntity     = $account;
        $this->currentEntityType = 'user';

    }//end assertUserByName()


    /**
     * Verify that an entity property is equal to a particular value.
     *
     * @Then entity property :property should be :value
     *
     * @param string $property A Drupal entity property name.
     * @param mixed  $value    The value to look for.
     *
     * @return void
     */
    public function assertEntityPropertyValue($property, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        if ($wrapper->$property->value() !== $value) {
            throw new \Exception(sprintf('Property "%s" is not "%s"', $property, $value));
        }

    }//end assertEntityPropertyValue()


    /**
     * Verify that a user has one or more roles.
     *
     * @Then the user should have the role(s) :role
     *
     * @param string $role One or more role names, separated by commas.
     *
     * @return void
     */
    public function assertUserHasRoles($role)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertEntityIsUser();

        $roles = $this->getRoles($role);

        $this->assertUserRoles($this->currentEntity, $roles);

    }//end assertUserHasRoles()


    /**
     * Verify that a user does not have one or more roles.
     *
     * @Then the user should not have the role(s) :role
     *
     * @param string $role One or more role names, separated by commas.
     *
     * @return void
     */
    public function assertNotUserHasRoles($role)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertEntityIsUser();

        $roles = $this->getRoles($role);

        $this->assertNotUserRoles($this->currentEntity, $roles);

    }//end assertNotUserHasRoles()


    /**
     * Verify that the current entity is a user.
     *
     * @return void
     */
    public function assertEntityIsUser()
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if ('user' !== $this->currentEntityType) {
            throw new \Exception(sprintf('Entity is not a user.'));
        }

    }//end assertEntityIsUser()


    /**
     * Verify that a user account has a set of roles.
     *
     * @param stdclass $account A Drupal user account object.
     * @param array    $roles   An array of Drupal role objects.
     *
     * @return void
     */
    public function assertUserRoles($account, $roles)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        foreach ($roles as $role) {
            if (false === user_has_role($role->rid, $account)) {
                throw new \Exception(sprintf('User "%s" does not have role "%s".', $account->name, $role->name));
            }
        }

    }//end assertUserRoles()


    /**
     * Verify that a user account does not have a set of roles.
     *
     * @param stdclass $account A Drupal user account object.
     * @param array    $roles   An array of Drupal role objects.
     *
     * @return void
     */
    public function assertNotUserRoles($account, $roles)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        foreach ($roles as $role) {
            if (true === user_has_role($role->rid, $account)) {
                throw new \Exception(sprintf('User "%s" has role "%s".', $account->name, $role->name));
            }
        }

    }//end assertNotUserRoles()


    /**
     * Get an array of role objects from a string of one or more role names.
     *
     * @param string $roles A comma-separated list of one or more role names.
     *
     * @return array An array of Drupal role objects from user_role_load_by_name().
     */
    public function getRoles($roles)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $role_objects = array();

        $role_names = array_map('trim', explode(',', $roles));
        foreach ($role_names as $role_name) {
            $r = user_role_load_by_name($role_name);
            if (false === $r) {
                throw new \Exception(sprintf('Role "%s" does not exist.', $role_name));
            }

            $role_objects[$r->rid] = $r;
        }

        return $role_objects;

    }//end getRoles()


    /**
     * Verify that an entity property is not equal to a particular value.
     *
     * @Then entity property :property should not be :value
     *
     * @param string $property A Drupal entity property name.
     * @param mixed  $value    The value to look for.
     *
     * @return void
     */
    public function assertNotEntityPropertyValue($property, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        if ($wrapper->$property->value() === $value) {
            throw new \Exception(sprintf('Property "%s" is "%s"', $property, $value));
        }

    }//end assertNotEntityPropertyValue()


    /**
     * Verify that a field contains a value.
     *
     * @Then entity field :field should contain :value
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @return void
     */
    public function assertEntityFieldValue($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if (empty($field) === true || empty($value) === true) {
            return;
        }

        // Use a per-field-type test method, if it is present.
        $field_info = field_info_field($field);
        if (empty($field_info) === true) {
            throw new \Exception(sprintf('Field "%s" does not exist', $field));
        }

        $method_name = 'assertEntityFieldValue'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field_info['type'])));
        if (method_exists($this, $method_name) === true) {
            return $this->$method_name($field, $value);
        }

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();
        if (is_array($field_value) === false) {
            $field_value = array($field_value);
        }

        if (in_array($value, $field_value) === false) {
            throw new \Exception(sprintf('Field "%s" does not contain "%s"', $field, $value));
        }

    }//end assertEntityFieldValue()


    /**
     * Verify a field does not contain a particular value.
     *
     * @Then entity field :field should not contain :value
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @return void
     */
    public function assertNotEntityFieldValue($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        try {
            $this->assertEntityFieldValue($field, $value);
        }
        catch (\Exception $e) {
            // Ignore the exception and return, since we're looking for NOT.
            return;
        }

        throw new \Exception(sprintf('Field "%s" contains "%s"', $field, $value));

    }//end assertNotEntityFieldValue()


    /**
     * Test a link field for its URL value.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function assertEntityFieldValueLinkField($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();
        if (isset($field_value['url']) === true) {
            $field_value = array($field_value);
        }

        foreach ($field_value as $f) {
            if ($f['url'] === $value) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain "%s"', $field, $value));

    }//end assertEntityFieldValueLinkField()


    /**
     * Test a text field for a partial string.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueTextLong($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $items = field_get_items($this->currentEntityType, $this->currentEntity, $field);

        if ($items === false) {
            throw new \Exception(sprintf('No field data available for "%s".', $field));
        }

        foreach ($items as $item) {
            if (strpos($item['value'], $value) !== false) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain partial text "%s"', $field, $value));

    }//end assertEntityFieldValueTextLong()


    /**
     * Test a file field for a Drupal stream wrapper URI.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueFile($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();

        // Note that file field values are array('fid' => '...', ... ),
        // which makes it somewhat hard to tell single values from multiple values.
        if (isset($field_value['fid']) === true) {
            $field_value = array($field_value);
        }

        foreach ($field_value as $f) {
            if (strpos($f['uri'], $value) !== false) {
                return;
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain file with URI "%s"', $field, $value));

    }//end assertEntityFieldValueFile()


    /**
     * Test an image field for a Drupal stream wrapper URI.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueImage($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertEntityFieldValueFile($field, $value);

    }//end assertEntityFieldValueImage()


    /**
     * Test a taxonomy term reference field for a term name.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueTaxonomyTermReference($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);

        $field_value = $wrapper->$field->value();

        if (empty($field_value) === false) {
            // Term field values are term objects.
            if (is_array($field_value) === false) {
                $field_value = array($field_value);
            }

            foreach ($field_value as $term) {
                if (is_object($term) === true && empty($term->name) === false && $term->name === $value) {
                    return;
                }
            }
        }

        throw new \Exception(sprintf('Field "%s" does not contain term "%s"', $field, $value));

    }//end assertEntityFieldValueTaxonomyTermReference()


    /**
     * Test an entity reference field for an entity label.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueEntityReference($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $field_info = field_info_field($field);
        $items      = field_get_items($this->currentEntityType, $this->currentEntity, $field);

        if (empty($items) === false) {
            foreach ($items as $item) {
                $entities = entity_load($field_info['settings']['target_type'], $item);
                $label    = entity_label($field_info['settings']['target_type'], current($entities));

                if ($label === $value) {
                    return;
                }
            }

            throw new \Exception(sprintf('Field "%s" does not contain entity with label "%s" (has "%s" instead).', $field, $value, $label));
        }

        throw new \Exception(sprintf('Field "%s" is empty.', $field));

    }//end assertEntityFieldValueEntityReference()


    /**
     * Test a date field for some date.
     *
     * @param string $field A Drupal field name.
     * @param mixed  $value The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     *
     * @todo : Update method to handle date fields with start and end dates
     * The call to $wrapper->$field->value() returns either an array or a scalar
     * because entity_metadata_wrapper() makes the date field values array
     * unpredictable. When working with date fields that have both a start and
     * end time, an array is returned instead of a scalar. If we want to test
     * for start and end dates, we would want to use Behat syntax similar to
     * "Then entity field ":field should contain "<start_date> - <end_date>".
     * This method would need to be updated to handle that approach.
     */
    public function assertEntityFieldValueDatetime($field, $value)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper     = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        $field_value = $wrapper->$field->value();

        if (is_scalar($field_value) === true) {
            $field_value = array($field_value);
        }

        if (is_array($field_value) === false) {
            $field_value = array();
        }

        foreach ($field_value as $v) {
            if (is_array($v) === true) {
                // The value may exist as either the start date ('value') or the end
                // date ('value2').
                if (array_key_exists('value', $v) === true) {
                    if (strtotime($value) === strtotime($v['value'])) {
                        return;
                    }
                }

                if (array_key_exists('value2', $v) === true) {
                    if (strtotime($value) === strtotime($v['value2'])) {
                        return;
                    }
                }
            }

            if (strtotime($value) === $v) {
                return;
            }
        }//end foreach

        throw new \Exception(sprintf('Field "%s" does not contain datetime "%s" (%s)', $field, strtotime($value), $value));

    }//end assertEntityFieldValueDatetime()


    /**
     * Output the contents of a field on the current entity.
     *
     * @Then I dump the contents of( field) :field
     *
     * @param string $field A field machine name.
     *
     * @return void
     */
    public function dumpField($field)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $wrapper     = entity_metadata_wrapper($this->currentEntityType, $this->currentEntity);
        $field_value = $wrapper->$field->value();
        print_r($field_value);

    }//end dumpField()


}//end class
