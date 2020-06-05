<?php
declare(strict_types = 1);

namespace Walther\Bookmarks\Domain\Repository;

/**
 * Class BookmarkRepository
 *
 * @package Walther\Bookmarks\Domain\Repository
 */
class BookmarkRepository extends \TYPO3\CMS\Extbase\Persistence\Repository
{
    /**
     * @return void
     */
    public function persistAll() : void
    {
        $this->persistenceManager->persistAll();
    }
}
