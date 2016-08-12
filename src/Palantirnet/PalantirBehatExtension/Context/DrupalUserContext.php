<?php

/**
 * @file
 *
 * @copyright Copyright (c) 2016 Palantir.net
 */

use Behat\Gherkin\Node\TableNode;
use Palantirnet\PalantirBehatExtension\Context\SharedDrupalContext;

class DrupalUserContext extends SharedDrupalContext {
  /**
   * Asserts a role has a list of permissions
   *
   * @Then the :role role should have the permission(s):
   *
   * @param String $role
   * @param TableNode $perms
   */
  public function assertRoleHasPermission($role, TableNode $perms) {
    // get the role storage object so we can query it for permissions
    $roleStorage = \Drupal::entityManager()->getStorage('user_role');
    
    // convert the single role given to an array for the isPermissionInRoles() function
    $rids = array($role);
    
    foreach($perms->getHash() as $row){
      // grab the value out of the row. it will always be the first value
      $perm = reset($row);
      
      // check the permission against the role
      if(!$roleStorage->isPermissionInRoles($perm, $rids)){
        throw new Exception('Role '.$role.' does not have permission '.$perm);
      }
    }
  }
}