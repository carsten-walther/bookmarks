<?php
declare(strict_types = 1);

namespace Walther\Bookmarks\Controller;

/**
 * Class AjaxController
 *
 * @package Walther\Bookmarks\Controller
 */
class AjaxController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{
    /**
     * @var string
     */
    protected $defaultViewObjectName = \Walther\Bookmarks\View\JsonView::class;

    /**
     * @var \Walther\Bookmarks\View\JsonView
     */
    protected $view;

    /**
     * @var \Walther\Bookmarks\Service\SessionHandler
     */
    protected $sessionHandler;

    /**
     * @var \Walther\Bookmarks\Domain\Repository\BookmarkRepository
     */
    protected $bookmarkRepository;

    /**
     * @var \Walther\Bookmarks\Domain\Repository\FrontendUserRepository
     */
    protected $frontendUserRepository;

    /**
     * @var array
     */
    protected $configuration = [
        'bookmarks' => [
            '_descendAll' => [
                '_only' => ['uid', 'parentUid', 'parentPid', 'parentTable', 'id', 'uri', 'title', 'type', 'data'],
                '_descend' => [
                    'data' => ['*']
                ],
            ],
        ],
        'bookmark' => [
            '_descend' => [
                '_only' => ['uid', 'parentUid', 'parentPid', 'parentTable', 'id', 'uri', 'title', 'type', 'data'],
                '_descend' => [
                    'data' => ['*']
                ],
            ],
        ],
    ];

    /**
     * @param \Walther\Bookmarks\Service\SessionHandler $sessionHandler
     */
    public function injectSessionHandler(\Walther\Bookmarks\Service\SessionHandler $sessionHandler) : void
    {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * @param \Walther\Bookmarks\Domain\Repository\BookmarkRepository $bookmarkRepository
     *
     * @return void
     */
    public function injectBookmarkRepository(\Walther\Bookmarks\Domain\Repository\BookmarkRepository $bookmarkRepository) : void
    {
        $this->bookmarkRepository = $bookmarkRepository;
    }

    /**
     * @param \Walther\Bookmarks\Domain\Repository\FrontendUserRepository $frontendUserRepository
     *
     * @return void
     */
    public function injectFrontendUserRepository(\Walther\Bookmarks\Domain\Repository\FrontendUserRepository $frontendUserRepository) : void
    {
        $this->frontendUserRepository = $frontendUserRepository;
    }

    /**
     * @return bool
     */
    protected function isUserLoggedIn() : bool
    {
        return (bool)$GLOBALS['TSFE']->fe_user->user['uid'];
    }

    /**
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function listAction() : void
    {
        if ($this->isUserLoggedIn()) {
            /** @var \Walther\Bookmarks\Domain\Model\FrontendUser $frontendUser */
            $frontendUser = $this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            $bookmarks = $frontendUser->getBookmarks()->getArray();
        } else {
            $bookmarks = $this->sessionHandler->getFromSession('bookmarks');
        }

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $this->objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);
        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $allowedBookmarkableTables = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($extbaseFrameworkConfiguration['plugin.']['tx_bookmarks.']['allowedBookmarkableTables.']);

        $fields = [];

        foreach ($allowedBookmarkableTables as $allowedBookmarkableTable) {
            $fields[] = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $allowedBookmarkableTable['fields'], true);
        }

        $fieldArray = array_merge([], ...$fields);

        $this->configuration['bookmarks']['_descendAll']['_descend']['data'] = $fieldArray;
        $this->configuration['bookmark']['_descend']['_descend']['data'] = $fieldArray;

        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(['bookmarks']);
        $this->view->assign('bookmarks', $bookmarks);
    }

    /**
     * @return void
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function initializeCreateAction() : void
    {
        $propertyMappingConfiguration = $this->arguments->getArgument('bookmark')->getPropertyMappingConfiguration();
        $propertyMappingConfiguration->setTypeConverterOption(
            \TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter::class,
            \TYPO3\CMS\Extbase\Property\TypeConverter\PersistentObjectConverter::CONFIGURATION_CREATION_ALLOWED,
            true
        );
        $propertyMappingConfiguration->allowAllProperties();
    }

    /**
     * @param \Walther\Bookmarks\Domain\Model\Bookmark $bookmark
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     */
    public function createAction(\Walther\Bookmarks\Domain\Model\Bookmark $bookmark) : void
    {
        if ($this->isUserLoggedIn()) {
            /** @var \Walther\Bookmarks\Domain\Model\FrontendUser $frontendUser */
            $frontendUser = $this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            $frontendUser->addBookmark($bookmark);
            $this->frontendUserRepository->update($frontendUser);
            $this->frontendUserRepository->persistAll();
        } else {
            $this->sessionHandler->addToSession($bookmark, 'bookmarks');
        }
        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(['bookmark']);
        $this->view->assign('bookmark', $bookmark);
    }

    /**
     * @param \Walther\Bookmarks\Domain\Model\Bookmark|null $bookmark
     *
     * @return void
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\IllegalObjectTypeException
     * @throws \TYPO3\CMS\Extbase\Persistence\Exception\UnknownObjectException
     * @throws \TYPO3\CMS\Extbase\Mvc\Exception\NoSuchArgumentException
     */
    public function deleteAction(\Walther\Bookmarks\Domain\Model\Bookmark $bookmark = null) : void
    {
        if ($bookmark && $this->isUserLoggedIn()) {
            /** @var \Walther\Bookmarks\Domain\Model\FrontendUser $frontendUser */
            $frontendUser = $this->frontendUserRepository->findByUid($GLOBALS['TSFE']->fe_user->user['uid']);
            $frontendUser->removeBookmark($bookmark);
            $this->frontendUserRepository->update($frontendUser);
            $this->frontendUserRepository->persistAll();
        } else {
            $bookmarks = $this->sessionHandler->getFromSession('bookmarks');
            foreach ($bookmarks as $item) {
                if ($item->getUid() === (int)$this->request->getArgument('bookmark')) {
                    $bookmark = $item;
                }
            }
            $this->sessionHandler->removeFromSession($bookmark, 'bookmarks');
        }
        $this->view->setConfiguration($this->configuration);
        $this->view->setVariablesToRender(['bookmark']);
        $this->view->assign('bookmark', $bookmark);
    }
}
