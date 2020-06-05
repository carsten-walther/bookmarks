<?php
declare(strict_types = 1);

namespace Walther\Bookmarks\Domain\Repository;

/**
 * Class FrontendUserRepository
 *
 * @package Walther\Bookmarks\Domain\Repository
 */
class FrontendUserRepository extends \TYPO3\CMS\Extbase\Domain\Repository\FrontendUserRepository
{
    /**
     * @return void
     */
    public function persistAll() : void
    {
        $this->persistenceManager->persistAll();
    }
}
