<?php
/**
 * @file
 * Behat context with step definitions for testing Drupal's Organic Groups.
 *
 * @copyright (c) Copyright 2015 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension\Context;

use Behat\Behat\Tester\Exception\PendingException;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Drupal\DrupalExtension\Context\RawDrupalContext;
use Drupal\DrupalExtension\Context\DrupalContext;
use Drupal\DrupalDriverManager;

/**
 * Behat context for testing Organic Groups in Drupal.
 *
 * For example:
 *
 * Scenario: Verify content access within a group
 *   Given I have the "member" role on the "project" group "My Test Group"
 *   Then I can create "post" content in the "project" group "My Test Group"
 */
class DrupalOrganicGroupsContext extends SharedContext
{

    /**
     * @var \Behat\MinkExtension\Context\MinkContext
     */
    private $drupalContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->drupalContext = $environment->getContext('Drupal\DrupalExtension\Context\DrupalContext');
    }

    /**
     * @BeforeScenario
     */
    public function checkDependencies(BeforeScenarioScope $scope)
    {
        if (!module_exists('og')) {
            throw new \Exception('The Organic Groups module is not installed.');
        }
    }

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
    }

    /**
     * @Given I am a/an :group_role on/of the :group_node_type group :group_node_title
     *
     * Creates a user and group if necessary. Grants the group role to the user.
     */
    public function assertGroupRole($group_role, $group_node_type, $group_node_title)
    {
        $this->drupalContext->assertAuthenticatedByRole('authenticated user');

        $group_node = $this->getNodeByTitle($group_node_type, $group_node_title);

        // Add the logged-in user to the group.
        og_group('node', $group_node->nid, array(
            'entity_type' => 'user',
            'entity' => $this->getAccount(),
        ));

        $og_roles = og_get_user_roles_name();
        $og_rid = array_search($group_role, $og_roles);
        if ($og_rid === FALSE) {
            throw new \Exception(sprintf('Organic Groups role "%s" does not exist.', $group_role));
        }

        // Grant the group role to the logged-in user.
        og_role_grant('node', $group_node->nid, $this->getAccount()->uid, $og_rid);

        // Make sure it all worked.
        $this->assertHasGroupRole($group_role, $group_node_type, $group_node_title);
    }

    /**
     * @Then I have the :group_role role on :group_node_type group :group_node_title
     *
     * Does not create a user or group; only checks whether the user has the
     * group role for that group.
     */
    public function assertHasGroupRole($group_role, $group_node_type, $group_node_title)
    {
        $group_node = $this->findNodeByTitle($group_node_type, $group_node_title);

        $user_og_roles = og_get_user_roles('node', $group_node->nid, $this->getAccount()->uid);
        if (!in_array($group_role, $user_og_roles)) {
            throw new \Exception(sprintf('User does not have the Organic Groups role "%s" on %s group "%s"', $group_role, $group_node_type, $group_node_title));
        }
    }

    /**
     * @Given a :type group node called :title
     *
     * Checks if an existing node is an organic group.
     *
     * @return stdclass
     *   The Drupal node object representing the group.
     */
    public function assertNodeIsGroup($type, $title)
    {
        $node = $this->findNodeByTitle($type, $title);

        if (!og_is_group('node', $node->nid)) {
            throw new \Exception(sprintf('"%s" node "%s" is not an Organic Group.', $type, $title));
        }

        return $node;
    }

    /**
     * @Then I can create :type content in the :group_type group :group_title
     */
    public function assertCreateGroupContent($type, $group_type, $group_title)
    {
        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (!og_user_access('node', $group->nid, "create $type content", $this->getAccount())) {
            throw new \Exception(sprintf('User can not create "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }
    }

    /**
     * @Then I can not create :type content in the :group_type group :group_title
     *
     * Because of DrupalOrganicGroupsContext::assertGroupContent(), we can't
     * just negate assertCreateGroupContent() here.
     */
    public function assertNotCreateGroupContent($type, $group_type, $group_title)
    {
        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (og_user_access('node', $group->nid, "create $type content", $this->getAccount())) {
            throw new \Exception(sprintf('User can create "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }
    }

    /**
     * @Then I can edit :type content in the :group_type group :group_title
     */
    public function assertEditGroupContent($type, $group_type, $group_title)
    {
        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (!og_user_access('node', $group->nid, "update any $type content", $this->getAccount())) {
            throw new \Exception(sprintf('User can not edit "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }
    }

    /**
     * @Then I can not edit :type content in the :group_type group :group_title
     */
    public function assertNotEditGroupContent($type, $group_type, $group_title)
    {
        $this->assertGroupContent($type, $group_type, $group_title);

        $group = $this->assertNodeIsGroup($group_type, $group_title);
        if (og_user_access('node', $group->nid, "update any $type content", $this->getAccount())) {
            throw new \Exception(sprintf('User can edit "%s" content in the "%s" group "%s".', $type, $group_type, $group_title));
        }
    }

    /**
     * Verify that the group node is actually a group, and the content type is a
     * group content type.
     */
    protected function assertGroupContent($type, $group_type, $group_title)
    {
        if (!og_is_group_content_type('node', $type)) {
            throw new \Exception(sprintf('Content of type "%s" can not be added to any group because it is not a group content type.'));
        }
    }

}
