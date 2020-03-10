<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalOrganicGroupsContext.
 *
 * @copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\DrupalDriverManager;
use Palantirnet\PalantirBehatExtension\NotUpdatedException;

/**
 * Behat context with step definitions for testing Organic Groups in Drupal.
 *
 * For example:
 *
 * Scenario: Verify content access within a group
 *   Given I have the "member" role on the "project" group "My Test Group"
 *   Then I can create "post" content in the "project" group "My Test Group"
 */
class DrupalOrganicGroupsContext extends SharedDrupalContext
{

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
     * Verify the Organic Groups module is installed before the scenario begins.
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

            if (module_exists('og') === false) {
                throw new \Exception('The Organic Groups module is not installed.');
            }
        */

    }//end checkDependencies()


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
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if (empty($this->drupalContext->user) === false) {
            $account = user_load($this->drupalContext->user->uid);
        } else {
            $account = drupal_anonymous_user();
        }

        return $account;

    }//end getAccount()


    /**
     * Verify that the current user can have a role in a group, granting the role.
     *
     * Logs in a user, creates the group, and grants the group role to the user, if
     * necessary.
     *
     * @Given I am a/an :group_role on/of the :group_node_type group :group_node_title
     *
     * @param string $group_role       The name of an OG role.
     * @param string $group_node_type  Machine name of a Drupal OG content type.
     * @param string $group_node_title Node title of a Drupal OG.
     *
     * @return void
     */
    public function assertGroupRole($group_role, $group_node_type, $group_node_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->drupalContext->assertAuthenticatedByRole('authenticated user');

        $group_node = $this->getNodeByTitle($group_node_type, $group_node_title);

        // Add the logged-in user to the group.
        og_group(
            'node',
            $group_node->nid,
            array(
             'entity_type' => 'user',
             'entity'      => $this->getAccount(),
            )
        );

        $og_roles = og_get_user_roles_name();
        $og_rid   = array_search($group_role, $og_roles);
        if ($og_rid === false) {
            throw new \Exception(sprintf('Organic Groups role "%s" does not exist.', $group_role));
        }

        // Grant the group role to the logged-in user.
        og_role_grant('node', $group_node->nid, $this->getAccount()->uid, $og_rid);

        // Make sure it all worked.
        $this->assertHasGroupRole($group_role, $group_node_type, $group_node_title);

    }//end assertGroupRole()


    /**
     * Verify that the current user has a role in a group.
     *
     * Does not create a user or group; only checks whether the user has the
     * group role for that group.
     *
     * @Then I have the :group_role role on :group_node_type group :group_node_title
     *
     * @param string $group_role       The name of an OG role.
     * @param string $group_node_type  Machine name of a Drupal OG content type.
     * @param string $group_node_title Node title of a Drupal OG.
     *
     * @return void
     */
    public function assertHasGroupRole($group_role, $group_node_type, $group_node_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $group_node = $this->findNodeByTitle($group_node_type, $group_node_title);

        $user_og_roles = og_get_user_roles('node', $group_node->nid, $this->getAccount()->uid);
        if (in_array($group_role, $user_og_roles) === false) {
            throw new \Exception(sprintf('User does not have the Organic Groups role "%s" on %s group "%s"', $group_role, $group_node_type, $group_node_title));
        }

    }//end assertHasGroupRole()


    /**
     * Checks if an existing node is an organic group.
     *
     * @todo why does this return a node object when everything else returns void?
     *
     * @Given a :type group node called :title
     *
     * @param string $type  Machine name of a Drupal OG content type.
     * @param string $title Node title of a Drupal OG.
     *
     * @return stdclass
     *   The Drupal node object representing the group.
     */
    public function assertNodeIsGroup($type, $title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $node = $this->findNodeByTitle($type, $title);

        if (og_is_group('node', $node->nid) === false) {
            throw new \Exception(sprintf('"%s" node "%s" is not an Organic Group.', $type, $title));
        }

        return $node;

    }//end assertNodeIsGroup()


    /**
     * Verify the current user can create some type of content in a group.
     *
     * @Then I can create :type content in the :group_type group :group_title
     *
     * @param string $type        Machine name of a Drupal content type.
     * @param string $group_type  Machine name of a Drupal OG content type.
     * @param string $group_title Node title of a Drupal OG.
     *
     * @return void
     */
    public function assertCreateGroupContent($type, $group_type, $group_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (og_user_access('node', $group->nid, "create $type content", $this->getAccount()) === false) {
            throw new \Exception(sprintf('User can not create "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }

    }//end assertCreateGroupContent()


    /**
     * Verify that the current user can't create a type of content in a group.
     *
     * @Then I can not create :type content in the :group_type group :group_title
     *
     * Because of DrupalOrganicGroupsContext::assertGroupContent(), we can't
     * just negate assertCreateGroupContent() here.
     *
     * @param string $type        Machine name of a Drupal content type.
     * @param string $group_type  Machine name of a Drupal OG content type.
     * @param string $group_title Node title of a Drupal OG.
     *
     * @return void
     */
    public function assertNotCreateGroupContent($type, $group_type, $group_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (og_user_access('node', $group->nid, "create $type content", $this->getAccount()) === true) {
            throw new \Exception(sprintf('User can create "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }

    }//end assertNotCreateGroupContent()


    /**
     * Verify that the current user can edit a type of content in a specific group.
     *
     * @Then I can edit :type content in the :group_type group :group_title
     *
     * @param string $type        Machine name of a Drupal content type.
     * @param string $group_type  Machine name of a Drupal OG content type.
     * @param string $group_title Node title of a Drupal OG.
     *
     * @return void
     */
    public function assertEditGroupContent($type, $group_type, $group_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (og_user_access('node', $group->nid, "update any $type content", $this->getAccount()) === false) {
            throw new \Exception(sprintf('User can not edit "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }

    }//end assertEditGroupContent()


    /**
     * Verify the current user can not edit a type of content in a specific group.
     *
     * @Then I can not edit :type content in the :group_type group :group_title
     *
     * @param string $type        Machine name of a Drupal content type.
     * @param string $group_type  Machine name of a Drupal OG content type.
     * @param string $group_title Node title of a Drupal OG.
     *
     * @return void
     */
    public function assertNotEditGroupContent($type, $group_type, $group_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (og_user_access('node', $group->nid, "update any $type content", $this->getAccount()) === true) {
            throw new \Exception(sprintf('User can edit "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }

    }//end assertNotEditGroupContent()


    /**
     * Verify that the group node is actually a group, and the content type is a
     * group content type.
     *
     * @todo This doesn't seem to do what the comment says.
     *
     * @param string $type        Machine name of a Drupal content type.
     * @param string $group_type  Machine name of a Drupal OG content type.
     * @param string $group_title Node title of a Drupal OG.
     *
     * @return void
     */
    protected function assertGroupContent($type, $group_type, $group_title)
    {
        throw new NotUpdatedException('Method not yet updated for Drupal 8.');

        if (og_is_group_content_type('node', $type) === false) {
            throw new \Exception(sprintf('Content of type "%s" can not be added to any group because it is not a group content type.'));
        }

    }//end assertGroupContent()


}//end class
