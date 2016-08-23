<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalCommentContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\Gherkin\Node\TableNode;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\DrupalDriverManager;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context with steps for testing Drupal commenting functionality.
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
     * Context from the scope environment, which gives us access to the current
     * logged-in user.
     *
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    protected $drupalContext;


    /**
     * Set up the Drupal context, which is used to access the current logged-in user.
     *
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope The Behat hook scope.
     *
     * @return void
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment         = $scope->getEnvironment();
        $this->drupalContext = $environment->getContext('Drupal\DrupalExtension\Context\DrupalContext');

    }//end gatherContexts()


    /**
     * Verify the Comment module is installed before the scenario begins.
     *
     * @BeforeScenario
     *
     * @param BeforeScenarioScope $scope The Behat hook scope.
     *
     * @return void
     */
    public function checkDependencies(BeforeScenarioScope $scope)
    {
        /*
            @todo Update for Drupal 8
            @see NotUpdatedException

            if (module_exists('comment') === false) {
                throw new \Exception('The Comment module is not available.');
            }
        */

    }//end checkDependencies()


    /**
     * Remove any created comments.
     *
     * @AfterScenario
     *
     * @return void
     */
    public function cleanComments()
    {
        /*
            @todo Update for Drupal 8
            @see NotUpdatedException

            // Remove any comments that were created.
            foreach ($this->comments as $comment) {
                comment_delete($comment->cid);
            }
        */

        $this->comments = array();

    }//end cleanComments()


    /**
     * Get the Drupal user object for the logged-in user.
     *
     * $this->drupalContext->user contains the user info, but not an actual user
     * object.
     *
     * @todo this is the same as DrupalOrganicGroupsContext::getAccount(); should
     *       these methods, and the gatherContexts() method, be moved to the
     *       SharedDrupalContext class?
     *
     * @return stdClass
     *   A Drupal user object.
     */
    protected function getAccount()
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if (empty($this->drupalContext->user) === false) {
            $account = user_load($this->drupalContext->user->uid);
        } else {
            $account = drupal_anonymous_user();
        }

        return $account;

    }//end getAccount()


    /**
     * Save a comment.
     *
     * For the comment data object, fields should be a simple value or flat array,
     * rather than using the Drupal field data structure. Comments may use either the
     * 'uid' or 'author' fields to attribute the comment to a particular Drupal user.
     *
     * @param stdclass $comment Comment data.
     *
     * @return stdclass
     *   A Drupal comment object.
     */
    protected function createComment($comment)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        // Assign authorship if none exists and `author` is passed.
        if (isset($comment->uid) === false && empty($comment->author) === false) {
            $account = user_load_by_name($comment->author);
            if ($account !== false) {
                $comment->uid = $account->uid;
            }
        }

        // The created field may come in as a readable date, rather than a
        // timestamp.
        if (isset($entity->created) === true && is_numeric($entity->created) === false) {
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
            if (isset($comment->$key) === false) {
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
     * @param string    $entity_type A Drupal entity type machine name.
     * @param \stdClass $entity      Entity object.
     *
     * @return void
     */
    protected function expandEntityFields($entity_type, \stdClass $entity)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $field_types = $this->getDriver()->getCore()->getEntityFieldTypes($entity_type);

        foreach ($field_types as $field_name => $type) {
            if (isset($entity->$field_name) === true) {
                $entity->$field_name = $this->getDriver()->getCore()
                    ->getFieldHandler($entity, $entity_type, $field_name)
                    ->expand($entity->$field_name);
            }
        }

    }//end expandEntityFields()


    /**
     * Assert commenting is open on a particular node.
     *
     * @Given comments on the :type content :title are open
     *
     * @param string $type  The machine name of a Drupal content type.
     * @param string $title The title of a piece of Drupal content.
     *
     * @return void
     */
    public function assertCommentsAreOpen($type, $title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $node = $this->getNodeByTitle($type, $title);
        if ((int) $node->comment !== COMMENT_NODE_OPEN) {
            throw new \Exception(sprintf('Comments on "%s" content "%s" are not open.', $type, $title));
        }

    }//end assertCommentsAreOpen()


    /**
     * Add a comment to a node, creating the node if it doesn't already exist.
     *
     * @When I add the comment :text to the :type content :title
     *
     * @param string $text  The comment body text.
     * @param string $type  A Drupal content type machine name.
     * @param string $title The title of a Drupal node.
     *
     * @return void
     */
    function createCommentOnContent($text, $type, $title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertCommentsAreOpen($type, $title);

        $node = $this->getNodeByTitle($type, $title);

        $comment      = new stdclass;
        $comment->nid = $node->nid;
        $comment->uid = $this->getAccount()->uid;
        $comment->comment_body = $text;

        $this->createComment($comment, $node);

        // Set internal page to the commented-on node.
        $this->getSession()->visit($this->locatePath('/node/'.$node->nid));

    }//end createCommentOnContent()


    /**
     * Add multiple comments to a piece of content.
     *
     * @Given comments on :type content :title:
     *
     *   Given comments on "post" content "My Test Post":
     *     | comment_subject               | comment_body             | author   |
     *     | This comment is about kitties | Kitties are the bestest. | Somebody |
     *     | ...                           | ...                      | ...      |
     *
     * @param string    $type          A Drupal content type machine name.
     * @param string    $title         The title of a Drupal node.
     * @param TableNode $commentsTable Comments data.
     *
     * @return void
     */
    public function createCommentsOnContent($type, $title, TableNode $commentsTable)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertCommentsAreOpen($type, $title);

        $node = $this->getNodeByTitle($type, $title);

        foreach ($commentsTable->getHash() as $commentHash) {
            $comment      = (object) $commentHash;
            $comment->nid = $node->nid;
            $this->createComment($comment);
        }

    }//end createCommentsOnContent()


}//end class
