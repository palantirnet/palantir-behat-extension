<?php
/**
 * Contains assertions to test for field types on a form.
 *
 * @copyright 2016 Palantir.net
 */
namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Gherkin\Node\TableNode;

/**
 * Class FieldContext contains assertions to test for field types on a form.
 *
 * @package Palantirnet\PalantirBehatExtension\Context
 */
class FieldContext extends SharedDrupalContext
{


    /**
     * Asserts a page has fields provided in the form of a given type:
     *
     * Assumes fields are targeted with #edit-<fieldname>. For example, "body"
     * checks for the existence of the element, "#edit-body". Note, for almost
     * everything this will begin with "field-", like "field-tags".
     *
     * @Then the form at :path has the expected fields:
     * @Then the content type :type has the expected fields:
     *
     * @param TableNode $fieldsTable  A table of fields to check with field types.
     * @param String    $path         The path to the form to check fields on.
     * @param String    $content_type Optional content type to derive the form path from.
     * | field               | tag      | type  |
     * | title               | input    | text  |
     * | body                | textarea |       |
     * | field-subheadline   | input    | text  |
     * | field-author        | input    | text  |
     * | field-summary       | textarea |       |
     * | field-full-text     | textarea |       |
     * | field-ref-sections  | select   |       |
     *
     * @return bool
     * @throws \Exception
     */
    public function assertFields(TableNode $fieldsTable, $path = '', $content_type = '')
    {
        // Load the page with the form on it.
        if (true === empty($path)) {
            $path = 'node/add/'.$content_type;
        }

        $this->getSession()->visit($this->locatePath($path));
        $page = $this->getSession()->getPage();

        foreach ($fieldsTable->getHash() as $row) {
            $fieldSelector = '#edit-'.$row['field'];
            $page->hasField($fieldSelector);
            $this->assertFieldType('#edit-'.$row['field'], $row['tag'], $row['type']);
        }

        return true;

    }


    /**
     * Test a field on the page to see if it matches the expected HTML field type.
     *
     * @Then the ":field" field is ":tag"
     * @Then the ":field" field is ":tag" with type ":type"
     *
     * @param string $field        Field selector used when testing this field.
     * @param string $expectedTag  Expected tag for this field.
     * @param string $expectedType Expected "type" used with this field.
     *
     * @return bool
     * @throws Exception
     */
    public function assertFieldType($field, $expectedTag, $expectedType = '')
    {
        $callback = 'assert'.ucfirst($expectedTag);
        if (false === method_exists($this, $callback)) {
            throw new Exception(sprintf('%s is not a field tag we know how to validate.', $expectedTag));
        }

        $this->$callback($field, $expectedType);

        return true;

    }


    /**
     * Verify the field is a textarea.
     *
     * @param string $field        Field selector used when testing this field.
     * @param string $expectedType Expected "type" used with this field.
     *
     * @return bool
     * @throws Exception
     */
    public function assertTextarea($field, $expectedType = '')
    {
        $element = $this->getSession()->getPage()->find('css', $field);
        if (null === $element->find('css', 'textarea.form-textarea')) {
            throw new Exception(sprintf("Couldn't find %s of type textarea.", $field));
        }

        return true;

    }


    /**
     * Verify the field is an input field of the given type.
     *
     * @param string $field        Field selector used when testing this field.
     * @param string $expectedType Expected "type" used with this field.
     *
     * @return bool
     * @throws Exception
     */
    public function assertInput($field, $expectedType = '')
    {
        $element = $this->getSession()->getPage()->find('css', $field);
        if (null === $element || null === $element->find('css', 'input[type="'.$expectedType.'"]')) {
            throw new Exception(sprintf("Couldn't find %s of type %s", $field, $expectedType));
        }

        return true;

    }


    /**
     * Verify the field is a select list.
     *
     * @param string $field        Field selector used when testing this field.
     * @param string $expectedType Expected "type" used with this field.
     *
     * @return bool
     * @throws Exception
     */
    public function assertSelect($field, $expectedType = '')
    {
        $element = $this->getSession()->getPage()->find('css', $field);
        if (null === $element->find('css', 'select.form-select')) {
            throw new Exception(sprintf("Couldn't find %s of type select.", $field));
        }

        // Verify that the select list is not part of a multivalue widget.
        if (false === $element->find('css', 'select.form-select')->isVisible()) {
            throw new Exception(sprintf("Couldn't find %s of type select.", $field));
        }

        return true;

    }


}
