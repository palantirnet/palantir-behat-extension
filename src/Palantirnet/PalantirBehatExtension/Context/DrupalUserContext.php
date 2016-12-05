<?php

/**
 * Contains Palantirnet\PalantirBehatExtension\Context\DrupalUserContext
 *
 * @copyright 2016 Palantir.net
 */

use Behat\Gherkin\Node\TableNode;
use Palantirnet\PalantirBehatExtension\Context\SharedDrupalContext;

/**
 * Class DrupalUserContext
 */
class DrupalUserContext extends SharedDrupalContext
{


    /**
     * Asserts a role has a list of permissions
     *
     * @Then the :role role should have the permission(s):
     *
     * @param String    $role  The role to check for the list of permissions.
     * @param TableNode $perms The permissions this role should have.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function assertRoleHasPermission($role, TableNode $perms)
    {
        // Get the role storage object so we can query it for permissions.
        $roleStorage = \Drupal::entityManager()->getStorage('user_role');

        // Convert the single role given to an array for the isPermissionInRoles() function.
        $rids = array($role);

        foreach ($perms->getHash() as $row) {
            // Grab the value out of the row. It will always be the first value.
            $perm = reset($row);

            // Check the permission against the role.
            if (false === $roleStorage->isPermissionInRoles($perm, $rids)) {
                throw new Exception('Role '.$role.' does not have permission '.$perm);
            }
        }

    }//end assertRoleHasPermission()


    /**
     * Asserts a role does not have a list of permissions.
     *
     * @Then the :role role should not have the permission(s):
     *
     * @param String    $role  The role to check for the list of permissions.
     * @param TableNode $perms The permissions this role should not have.
     *
     * @return void
     *
     * @throws \Exception
     */
    public function assertRoleHasNoPermission($role, TableNode $perms)
    {
        // Get the role storage object so we can query it for permissions.
        $roleStorage = \Drupal::entityManager()->getStorage('user_role');

        // Convert the single role given to an array for the isPermissionInRoles() function.
        $rids = array($role);

        foreach($perms->getHash() as $row){
            // Grab the value out of the row. It will always be the first value.
            $perm = reset($row);

            // Check the permission against the role.
            $has_permission = $roleStorage->isPermissionInRoles($perm, $rids);
            if($has_permission) {
                throw new \Exception('Role "'.$role.'" has permission "'.$perm.'" but it should not.');
            }
        }

    }//end assertRoleHasNoPermission()


}//end class}
