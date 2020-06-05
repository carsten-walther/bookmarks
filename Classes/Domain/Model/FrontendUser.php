<?php
declare(strict_types=1);

namespace Walther\Bookmarks\Domain\Model;

/**
 * Class FrontendUser
 *
 * @package Walther\Bookmarks\Domain\Model
 */
class FrontendUser extends \TYPO3\CMS\Extbase\Domain\Model\FrontendUser
{
    /**
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Walther\Bookmarks\Domain\Model\Bookmark>
     * @TYPO3\CMS\Extbase\Annotation\ORM\Lazy
     * @TYPO3\CMS\Extbase\Annotation\ORM\Cascade("remove")
     */
    protected $bookmarks;

    /**
     * FrontendUser constructor.
     *
     * @param string $username
     * @param string $password
     */
    public function __construct($username = '', $password = '')
    {
        parent::__construct($username, $password);

        $this->bookmarks = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Walther\Bookmarks\Domain\Model\Bookmark> $bookmarks
     */
    public function setBookmarks(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $bookmarks) : void
    {
        $this->bookmarks = $bookmarks;
    }

    /**
     * @param \Walther\Bookmarks\Domain\Model\Bookmark $bookmark
     */
    public function addBookmark(\Walther\Bookmarks\Domain\Model\Bookmark $bookmark) : void
    {
        $this->bookmarks->attach($bookmark);
    }

    /**
     * @param \Walther\Bookmarks\Domain\Model\Bookmark $bookmark
     */
    public function removeBookmark(\Walther\Bookmarks\Domain\Model\Bookmark $bookmark) : void
    {
        $this->bookmarks->detach($bookmark);
    }

    /**
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Walther\Bookmarks\Domain\Model\Bookmark>
     */
    public function getBookmarks() : \TYPO3\CMS\Extbase\Persistence\ObjectStorage
    {
        return $this->bookmarks;
    }
}
