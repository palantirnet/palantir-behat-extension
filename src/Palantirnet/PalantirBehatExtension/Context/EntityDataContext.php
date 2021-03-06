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
 *   And entity field "field_example_link" should contain "http://example.com" for property "uri"
 *   And I dump the contents of "field_example_link"
 */
class EntityDataContext extends SharedDrupalContext
{

    /**
     * @var $currentEntity \Drupal\Core\Entity\EntityInterface
     */
    protected $currentEntity = null;
    protected $currentEntityType = null;
    protected $currentEntityLanguage = null;


    /**
     * Verify field and property values of a node entity.
     *
     * @When I examine the :contentType( node) with title :title
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title The title of a Drupal node.
     *
     * @throws \Exception if no node with title $title was found.
     * @throws \Exception if multiple nodes with title $title are found.
     *
     * @return void
     */
    public function assertNodeByTitle($contentType, $title)
    {
        $node = $this->findNodeByTitle($contentType, $title);

        $this->currentEntity = $node;
        $this->currentEntityType = 'node';

    }//end assertNodeByTitle()

    /**
     * Verify field and property values of a node entity in a language.
     *
     * @When I examine the :contentType( node) with title :title in :language
     *
     * @param string $contentType A Drupal content type machine name.
     * @param string $title The title of a Drupal node.
     * @param string $language A language code
     *
     * @throws \Exception if no node with title $title was found.
     * @throws \Exception if node is not available in that language.
     * @throws \Exception if multiple nodes with title $title are found.
     *
     * @return void
     */
    public function assertNodeByTitleAndLanguage($contentType, $title, $language)
    {
        $node = $this->findNodeByTitle($contentType, $title, $language);

        $this->currentEntity = $node;
        $this->currentEntityType = 'node';
        $this->currentEntityLanguage = $language;

    }//end assertNodeByTitleAndLanguage()


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
     * Verify field and property values of a block entity.
     *
     * @When I examine the :blockType block with info :info
     *
     * @param string $blockType
     *  A Drupal block type machine name.
     * @param string $info
     *  The description of a Drupal block.
     *
     * @throws \Exception if block is not found.
     * @throws \Exception if multiple blocks with info $info are found.
     *
     * @return void
     */
    public function assertBlockByInfo($blockType, $info)
    {
        $block = $this->findBlockByInfo($blockType, $info);

        $this->currentEntity = $block;
        $this->currentEntityType = 'block_content';

    }//end assertBlockByInfo()

    /**
     * Verify field and property values of a block entity.
     *
     * @When I examine the :blockType block with info :info in :language
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
     * @return void
     */
    public function assertBlockByInfoAndLanguage($blockType, $info, $language)
    {
        $block = $this->findBlockByInfo($blockType, $info, $language);

        $this->currentEntity = $block;
        $this->currentEntityType = 'block_content';
        $this->currentEntityLanguage = $language;

    }//end assertBlockByInfoAndLanguage()


    /**
     * Verify field and property values of a media entity.
     *
     * @When I examine the :mediaType media with name :name
     *
     * @param string $mediaType
     *  A Drupal media type machine name.
     * @param string $name
     *  The name of a Drupal media entity.
     *
     * @throws \Exception if media is not found.
     * @throws \Exception if multiple media entities with name $name are found.
     *
     * @return void
     */
    public function assertMediaByName($mediaType, $name)
    {
        $media = $this->findMediaByName($mediaType, $name);

        $this->currentEntity = $media;
        $this->currentEntityType = 'media';

    }//end assertMediaByName()

    /**
     * Verify field and property values of a media entity.
     *
     * @When I examine the :mediaType media with name :name in :language
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
     * @return void
     */
    public function assertMediaByNameAndLanguage($mediaType, $name, $language)
    {
        $media = $this->findMediaByName($mediaType, $name, $language);

        $this->currentEntity = $media;
        $this->currentEntityType = 'media';
        $this->currentEntityLanguage = $language;

    }//end assertMediaByNameAndLanguage()


    /**
     * Verify field and property values of a user entity.
     *
     * @When I examine the user :userName
     *
     * @param string $userName The name of a Drupal user.
     *
     * @throws \Exception if user with name $userName is not found
     *
     * @return void
     */
    public function assertUserByName($userName)
    {
        $account = $this->findUserByName($userName);

        $this->currentEntity = $account;
        $this->currentEntityType = 'user';

    }//end assertUserByName()


    /**
     * @When I examine paragraph ":fieldWeight" on the ":fieldName" field
     *
     * This can drill down into a paragraph on a loaded entity.
     */
    public function assertParagraphByWeight($fieldWeight, $fieldName)
    {
        if (!$this->currentEntity->hasField($fieldName)) {
            throw new \Exception('Could not load the field');
        }

        $field = $this->currentEntity->get($fieldName);

        $paragraphs = $field->referencedEntities();

        if (!(is_array($paragraphs) && isset($paragraphs[$fieldWeight - 1]))){
            throw new \Exception('Could not find the paragraph in the field.');
        }

        $paragraph = $paragraphs[$fieldWeight - 1];

        if (isset($this->currentEntityLanguage) && $paragraph->hasTranslation($this->currentEntityLanguage)){
            $paragraph = $paragraph->getTranslation($this->currentEntityLanguage);
        }

        $this->currentEntity = $paragraph;
        $this->currentEntityType = 'paragraph';

    }//end assertParagraphByWeight()


    /**
     * Verify that a user has one or more roles.
     *
     * @Then the user should have the role(s) :role
     *
     * @param string $role One or more role names, separated by commas.
     *
     * @throws \Exception if one or more role names do not exist.
     *
     * @return void
     */
    public function assertUserHasRoles($role)
    {
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
     * @throws \Exception if one or more role names do not exist.
     *
     * @return void
     */
    public function assertNotUserHasRoles($role)
    {
        $this->assertEntityIsUser();

        $roles = $this->getRoles($role);

        $this->assertNotUserRoles($this->currentEntity, $roles);

    }//end assertNotUserHasRoles()


    /**
     * Verify that the current entity is a user.
     *
     * @return void
     *
     * @throws \Exception if current entity is not a user.
     */
    public function assertEntityIsUser()
    {
        if ('user' !== $this->currentEntityType) {
            throw new \Exception(sprintf('Entity is not a user.'));
        }

    }//end assertEntityIsUser()


    /**
     * Verify that a user account has a set of roles.
     *
     * @param \Drupal\User\Entity\User $account
     *  A Drupal user account object.
     * @param array $roles
     *  An array of Drupal role objects.
     *
     * @throws \Exception if user does not have one or more roles as defined in $roles
     *
     * @return void
     */
    public function assertUserRoles($account, $roles)
    {
        foreach ($roles as $role) {
            if (false === $account->hasRole($role->id())) {
                throw new \Exception(sprintf('User "%s" does not have role "%s".', $account->getAccountName(), $role->label()));
            }
        }

    }//end assertUserRoles()


    /**
     * Verify that a user account does not have a set of roles.
     *
     * @param \Drupal\User\Entity\User $account
     *   A Drupal user account object.
     * @param array $roles
     *   An array of Drupal role objects.
     *
     * @throws \Exception if user does not have one ore more roles as defined in $roles
     *
     * @return void
     */
    public function assertNotUserRoles($account, $roles)
    {
        foreach ($roles as $role) {
            if (true === $account->hasRole($role->id())) {
                throw new \Exception(sprintf('User "%s" has role "%s".', $account->getAccountName(), $role->label()));
            }
        }

    }//end assertNotUserRoles()


    /**
     * Get an array of role objects from a string of one or more role names.
     *
     * @param string $role_names A comma-separated list of one or more role names.
     *
     * @throws \Exception if role with one or more role names as defined in $role_names
     *   does not exist.
     *
     * @return array An array of Drupal role objects from user_role_load_by_name().
     */
    public function getRoles($role_names)
    {
        $roles = array();

        // Retrieve a list of all available role names keyed by role id.
        $r = user_role_names();
        $role_names = array_map('trim', explode(',', $role_names));
        foreach ($role_names as $role_name) {
            $role = FALSE;
            $rid = array_search($role_name, $r);

            if ($rid) {
                $role = \Drupal\user\Entity\Role::load($rid);
            }

            if (FALSE === $role) {
                throw new \Exception(sprintf('Role "%s" does not exist.', $role_name));
            }

            $roles[$role->id()] = $role;
        }

        return $roles;

    }//end getRoles()


    /**
     * Verify that an entity property is equal to a particular value.
     *
     * @Then entity property :property should be :value
     *
     * @param string $property
     *  A Drupal entity property name.
     * @param mixed $value
     *  The value to look for.
     *
     * @return void
     */
    public function assertEntityPropertyValue($property, $value)
    {
        // Properties and fields are accessed in the same way in Drupal 8.
        $this->assertEntityFieldValue($property, $value);

    }//end assertEntityPropertyValue()


    /**
     * Verify that a field contains a value.
     *
     * @Then entity field :field should contain :value
     *
     * @param string $field_name
     *  A Drupal field name.
     * @param mixed  $value
     *  The value to look for.
     *
     * @throws \Exception if field does not contain value.
     *
     * @return void
     */
    public function assertEntityFieldValue($field_name, $value)
    {
        /**
         * @var $field \Drupal\Core\Field\FieldItemList
         */
        $field = $this->currentEntity->get($field_name);

        /**
         * @var $definition \Drupal\Core\Field\BaseFieldDefinition
         */
        $definition = $field->getFieldDefinition();

        $field_type = $definition->getType();

        // If a method exists to handle the this field type, use it.
        $method_name = 'assertEntityFieldValue'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field_type)));

        if (method_exists($this, $method_name) === true) {
            return $this->$method_name($field, $value);
        }

        $this->assertEntityFieldHasPropertyValue($field, $value);
        return;
    }//end assertEntityFieldValue()


    /**
     * Verify that a field property contains a value.
     *
     * @Then entity field :field should contain :value for property :property
     *
     * @param string $field_name
     *  A Drupal field name.
     * @param mixed  $value
     *  The value to look for.
     * @param string $property
     *  The field property.
     *
     * @throws \Exception if field does not contain value.
     *
     * @return void
     */
    public function assertEntityFieldPropertyValue($field_name, $value, $property)
    {
        /**
         * @var $field \Drupal\Core\Field\FieldItemList
         */
        $field = $this->currentEntity->get($field_name);

        /**
         * @var $definition \Drupal\Core\Field\BaseFieldDefinition
         */
        $definition = $field->getFieldDefinition();

        $field_type = $definition->getType();

        // If a method exists to handle the property for this field type, use it.
        $method_name = 'assertEntityFieldPropertyValue'.str_replace(' ', '', ucwords(str_replace('_', ' ', $field_type)));

        if (method_exists($this, $method_name) === true) {
            return $this->$method_name($field, $value, $property);
        }

        $this->assertEntityFieldHasPropertyValue($field, $value, $property);

    }//end assertEntityFieldPropertyValue()

    /**
     * For a given field - and optional field property - check if a value is
     * present.
     *
     * @param $field
     *  A Drupal field object.
     * @param $value
     *  The value to look for.
     * @param $property
     *  The field property to look in.
     *
     * @throws \Exception if field does not contain value.
     */
    private function assertEntityFieldHasPropertyValue($field, $value, $property = 'value') {
        // Check if the provided value matches any of the field values - if no
        // property is defined, use the default `value`.
        $field_values = array_map(function ($field_value) use ($property) {
            return $field_value[$property];
        }, $field->getValue());

        // Special case for expecting nothing.
        if ($value === 'nothing') {
            if (!empty($field_values)) {
                throw new \Exception(sprintf('Field "%s" has a "%s" of "%s" and does not contain "%s"', $field->getName(), $property, json_encode($field_values), $value));
            }

            return;
        }

        if (in_array($value, $field_values) === false) {
            throw new \Exception(sprintf('Field "%s" has a "%s" of "%s" and does not contain "%s"', $field->getName(), $property, json_encode($field_values), $value));
        }
    }


    /**
     * Verify that an entity property is not equal to a particular value.
     *
     * @Then entity property :property should not be :value
     *
     * @param string $property
     *  A Drupal entity property name.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception if field does contain value.
     *
     * @return void
     */
    public function assertNotEntityPropertyValue($property, $value)
    {
        try {
            $this->assertEntityPropertyValue($property, $value);
        }
        catch (\Exception $e) {
            // Ignore the exception and return, since we're looking for NOT.
            return;
        }

        throw new \Exception(sprintf('Field "%s" contains "%s"', $field, $value));

    }//end assertNotEntityPropertyValue()


    /**
     * Verify a field does not contain a particular value.
     *
     * @Then entity field :field should not contain :value
     *
     * @param string
     *  $field A Drupal field name.
     * @param mixed
     *  $value The value to look for.
     *
     * @throws \Exception if field does not contain value.
     *
     * @return void
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

    }//end assertNotEntityFieldValue()


    /**
     * Verify a field does not contain a particular value.
     *
     * @Then entity field :field should not contain :value for property :property
     *
     * @param string
     *  $field A Drupal field name.
     * @param mixed
     *  $value The value to look for.
     * @param $property
     *  The field property to look in.
     *
     * @throws \Exception if field does not contain value.
     *
     * @return void
     */
    public function assertNotEntityFieldPropertyValue($field, $value, $property)
    {
        try {
            $this->assertEntityFieldPropertyValue($field, $value, $property);
        }
        catch (\Exception $e) {
            // Ignore the exception and return, since we're looking for NOT.
            return;
        }

        throw new \Exception(sprintf('Field "%s" for property "%s" contains "%s"', $field, $property, $value));

    }//end assertNotEntityFieldPropertyValue()


    /**
     * Verify that a paragraph field contains a paragraph of a certain type.
     *
     * @Then paragraph field :field should be of type :type
     *
     * @param string $field_name
     *  A Drupal field name.
     * @param mixed $type
     *  The type of paragraph.
     *
     * @throws \Exception if Paragraph does not contain a bundle of a certain type.
     *
     * @return void
     */
    public function assertEntityFieldValueParagraph($field_name, $type)
    {
        /**
         * @var $field \Drupal\Core\Field\FieldItemList
         */
        $field = $this->currentEntity->get($field_name);

        $types = [];

        /**
         * @var $entity \Drupal\paragraphs\Entity\Paragraph
         */
        foreach ($field->referencedEntities() as $entity) {
            $types[] = $entity->getType();
        }

        if (!in_array($type, $types)) {
            throw new \Exception(sprintf('Paragraph does not have type "%s", has types "%s".', $type, json_encode($types)));
        }

    }//end assertEntityFieldValueParagraph()


    /**
     * Test a link field for its URL value.
     *
     * @param \Drupal\Core\Field\FieldItemList $field
     *  A Drupal field object.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception when url was not found
     *
     * @return void
     */
    public function assertEntityFieldValueLink($field, $value)
    {
        // Check if the provided value matches any of the field values.
        $this->assertEntityFieldHasPropertyValue($field, $value, 'uri');

    }//end assertEntityFieldValueLink()


    /**
     * Test a text field for a partial string.
     *
     * @param \Drupal\Core\Field\FieldItemList $field
     *  A Drupal field object.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception when value was not found.
     *
     * @return void
     */
    public function assertEntityFieldValueTextLong($field, $value)
    {
        $property = 'value';
        // Filter out property values - default to 'uri' if property is not defined.
        $field_values = array_map(function ($field_value) use ($property) {
            return $field_value[$property];
        }, $field->getValue());

        // Special case for expecting nothing
        if ($value === 'nothing') {
            if (!empty($field_values)) {
                throw new \Exception(sprintf('Field "%s" has a "%s" of "%s" and does not contain "%s"', $field_name, $property, json_encode($field_value), $value));
            }

            return;
        }

        // Iterate over all field values and do a partial string comparison.
        foreach ($field_values as $field_value) {
            if (strpos($field_value, $value) !== false) {
                return;
            }
        }
    }//end assertEntityFieldValueTextLong()


    /**
     * Test a file field for a Drupal stream wrapper URI.
     *
     * @param \Drupal\Core\Field\FieldItemList $field A Drupal field name.
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
     * @param \Drupal\Core\Field\FieldItemList $field A Drupal field name.
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
     * Test an entity reference field for an entity label.
     *
     * @param \Drupal\Core\Field\FieldItemList $field
     *  A Drupal field object.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueEntityReference($field, $value)
    {
        $entities = $field->referencedEntities();

        if (empty($entities) === false) {
            $titles = [];

            /**
             * @var $entity \Drupal\Core\Entity\ContentEntityBase
             */
            foreach ($entities as $entity) {

                if ($entity->getEntityTypeId() === 'paragraph') {
                    throw new \Exception('Paragraphs do not have meaningful labels, so they must be tested by a different method.');
                    // If we get a single paragraph reference, we will assume
                    // that the rest are also paragraphs and exit the method.
                    return;
                }

                $labels[] = $entity->label();

                if ($entity->label() === $value) {
                    return;
                }
            }

            throw new \Exception(sprintf('Field does not contain entity with label "%s" (has "%s" labels instead).', $value, json_encode($labels)));
        }

        throw new \Exception('Field is empty.');

    }//end assertEntityFieldValueEntityReference()


    /**
     * Passes handling to the generic Entity Reference method.
     *
     * @param \Drupal\Core\Field\FieldItemList $field
     *  A Drupal field object.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueEntityReferenceRevisions($field, $value)
    {
        $this->assertEntityFieldValueEntityReference($field, $value);
    }//end assertEntityFieldValueEntityReferenceRevisions()


    /**
     * Webforms act like entity reference fields. This function
     * passes handling to the generic Entity Reference method.
     *
     * @param \Drupal\Core\Field\FieldItemList $field
     *  A Drupal field object.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     */
    public function assertEntityFieldValueWebform($field, $value)
    {
        $this->assertEntityFieldValueEntityReference($field, $value);
    }//end assertEntityFieldValueWebform()


    /**
     * Test a date field for some date.
     *
     * @param \Drupal\Core\Field\FieldItemList $field
     *  A Drupal field name.
     * @param mixed $value
     *  The value to look for.
     *
     * @throws \Exception
     *
     * @return void
     *
     * @todo : Update method to handle date fields with start and end dates
     * If we want to test for start and end dates, we would want to use Behat syntax
     * "Then entity field ":field should contain "<start_date> - <end_date>".
     */
    public function assertEntityFieldValueDatetime($field, $value)
    {

        if (strtotime($field->value) === strtotime($value)) {
            return;
        }

        throw new \Exception(sprintf('Field does not contain datetime "%s" (%s), contains "%s".', strtotime($value), $value, strtotime($field->value)));

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
