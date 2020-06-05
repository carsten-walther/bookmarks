<?php
declare(strict_types = 1);

namespace Walther\Bookmarks\Controller;

/**
 * Class BookmarkController
 *
 * @package Walther\Bookmarks\Controller
 */
class BookmarkController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * index action
     *
     * @return void
     */
    public function indexAction() : void
    {
        $this->view->assign('data', $this->configurationManager->getContentObject()->data);
    }
}
