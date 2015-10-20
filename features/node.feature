@api
Feature: Some Example Tests
  As a Palantir developer
  I want to try out the palantir-extension steps
  So that I know how to use them for later.

  Scenario: Verify I can view and edit a node
    Given a "page" with the title "My Test Page"
    When I view the "page" content "My Test Page"
    Then I should get a "200" HTTP response

  @disableWorkbenchModeration @disableAutoNodetitle
  Scenario: Verify I can view and edit a node
    Given a "page" with the title "My Test Page"
    And I am logged in as a user with the "bypass node access" permission
    When I edit the "page" content "My Test Page"
    Then I should get a "200" HTTP response
