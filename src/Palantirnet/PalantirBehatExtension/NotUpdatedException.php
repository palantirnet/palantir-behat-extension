<?php
/**
 * Contains Palantirnet\PalantirBehatExtension\NotUpdatedException.
 *
 * @copyright 2016 Palantir.net, Inc.
 */

namespace Palantirnet\PalantirBehatExtension;
use Behat\Behat\Context\Exception\ContextException;
use Symfony\Component\Config\Definition\Exception\InvalidDefinitionException;

/**
 * Exception for when a method has not yet been updated for Drupal 8.
 */
class NotUpdatedException extends InvalidDefinitionException implements ContextException
{
}//end class
