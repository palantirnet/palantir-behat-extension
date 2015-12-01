<?php
/**
 * @file
 * Behat context with steps for testing Drupal commenting functionality.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\DrupalDriverManager;

/**
 * Behat context for testing comments in Drupal.
 *
 * For example:
 *
 * Scenario: Verify comment display
 *   Given I am logged in as a user with the "authenticated user" role
 *   When I add the comment "Blah blah" to the "post" content "My Test Post"
 *   Then I see the text "Blah blah"
 *
 * Scenario: Verify comment display with fields
 *   Given comments on "post" content "My Test Post":
 *     | field_subject                 | field_body               |
 *     | This comment is about kitties | Kitties are the bestest. |
 *   When I view the "post" content "My Test Post"
 *   Then I see the heading "This comment is about kitties"
 *   And I see the text "Kitties are the bestest."
 */
class DrupalCommentContext extends SharedDrupalContext
{

    /**
     * Keep track of comments so they can be cleaned up.
     *
     * @var array
     */
    protected $comments = array();

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $drupalContext;


    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment         = $scope->getEnvironment();
        $this->drupalContext = $environment->getContext('Drupal\DrupalExtension\Context\DrupalContext');

    }//end gatherContexts()


    /**
     * @BeforeScenario
     */
    public function checkDependencies(BeforeScenarioScope $scope)
    {
        if (!module_exists('comment')) {
            throw new \Exception('The Comment module is not available.');
        }

    }//end checkDependencies()


    /**
     * Remove any created comments.
     *
     * @AfterScenario
     */
    public function cleanComments()
    {
        // Remove any comments that were created.
        foreach ($this->comments as $comment) {
            comment_delete($comment->cid);
        }

        $this->comments = array();

    }//end cleanComments()


    /**
     * Get the Drupal user object for the logged-in user.
     *
     * $this->drupalContext->user contains the user info, but not an actual user
     * object.
     *
     * @return stdClass
     *   A Drupal user object.
     */
    protected function getAccount()
    {
        return !empty($this->drupalContext->user) ? user_load($this->drupalContext->user->uid) : drupal_anonymous_user();

    }//end getAccount()


    /**
     * Save a comment.
     *
     * @param stdclass $comment
     *   A simple object representing comment data. Fields should be a simple
     *   value or flat array, rather than using the Drupal field data structure.
     *   Comments may use either the 'uid' or 'author' fields to attribute the
     *   comment to a particular Drupal user.
     *
     * @return stdclass
     *   A Drupal comment object.
     */
    protected function createComment($comment)
    {
        // Assign authorship if none exists and `author` is passed.
        if (!isset($comment->uid) && !empty($comment->author) && ($account = user_load_by_name($comment->author))) {
            $comment->uid = $account->uid;
        }

        // The created field may come in as a readable date, rather than a
        // timestamp.
        if (isset($entity->created) && !is_numeric($entity->created)) {
            $entity->created = strtotime($entity->created);
        }

        // Add default values.
        $defaults = array(
                     'uid'    => 0,
                     'cid'    => null,
                     'pid'    => null,
                     'status' => 1,
                    );

        foreach ($defaults as $key => $default) {
            if (!isset($comment->$key)) {
                $comment->$key = $default;
            }
        }

        // Turn values into field data.
        $this->parseEntityFields('comment', $comment);

        // Attempt to decipher any fields that may be specified.
        $this->expandEntityFields('comment', $comment);

        comment_save($comment);

        $this->comments[] = $comment;

        return $comment;

    }//end createComment()


    /**
     * Copy of \Drupal\Driver\Cores\AbstractCore:expandEntityFields().
     *
     * Expands properties on the given entity object to the expected structure.
     *
     * @param \stdClass $entity
     *   Entity object.
     */
    protected function expandEntityFields($entity_type, \stdClass $entity)
    {
        $field_types = $this->getDriver()->getCore()->getEntityFieldTypes($entity_type);

        foreach ($field_types as $field_name => $type) {
            if (isset($entity->$field_name)) {
                $entity->$field_name = $this->getDriver()->getCore()
                    ->getFieldHandler($entity, $entity_type, $field_name)
                    ->expand($entity->$field_name);
            }
        }

    }//end expandEntityFields()


    /**
     * @When I add the comment :text to the :type content :title
     */
    function createCommentOnContent($text, $type, $title)
    {
        $node = $this->getNodeByTitle($type, $title);
        if ($node->comment != COMMENT_NODE_OPEN) {
            throw new \Exception(sprintf('Comments on "%s" content "%s" are not open.', $type, $title));
        }

        $comment      = new stdclass;
        $comment->nid = $node->nid;
        $comment->uid = $this->getAccount()->uid;
        $comment->comment_body = $text;

        $this->createComment($comment, $node);

        // Set internal page to the commented-on node.
        $this->getSession()->visit($this->locatePath('/node/'.$node->nid));

    }//end createCommentOnContent()


    /**
     * @Given comments on :type content :title:
     *
     *   Given comments on "post" content "My Test Post":
     *     | comment_subject               | comment_body             | author   |
     *     | This comment is about kitties | Kitties are the bestest. | Somebody |
     *     | ...                           | ...                      | ...      |
     */
    public function createCommentsOnContent($type, $title, TableNode $commentsTable)
    {
        $node = $this->getNodeByTitle($type, $title);

        if ($node->comment != COMMENT_NODE_OPEN) {
            throw new \Exception(sprintf('Comments on "%s" content "%s" are not open.', $type, $title));
        }

        foreach ($commentsTable->getHash() as $commentHash) {
            $comment      = (object) $commentHash;
            $comment->nid = $node->nid;
            $this->createComment($comment);
        }

    }//end createCommentsOnContent()


}//end class
