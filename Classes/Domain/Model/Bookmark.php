<?php
declare(strict_types=1);

namespace Walther\Bookmarks\Domain\Model;

/**
 * Class Bookmark
 *
 * @package Walther\Bookmarks\Domain\Model
 */
class Bookmark extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{
    /**
     * @var int
     */
    protected $uid;

    /**
     * @var int
     */
    protected $parentUid;

    /**
     * @var int
     */
    protected $parentPid;

    /**
     * @var string
     */
    protected $parentTable;

    /**
     * @var \Walther\Bookmarks\Domain\Model\FrontendUser|null
     */
    protected $feuser;

    /**
     * @var array
     */
    protected $data;

    /**
     * @param int $uid
     *
     * @return void
     */
    public function setUid(int $uid) : void
    {
        $this->uid = $uid;
    }

    /**
     * @return int
     */
    public function getParentUid() : int
    {
        return $this->parentUid;
    }

    /**
     * @param int $parentUid
     *
     * @return void
     */
    public function setParentUid(int $parentUid) : void
    {
        $this->parentUid = $parentUid;
    }

    /**
     * @return int
     */
    public function getParentPid() : int
    {
        return $this->parentPid;
    }

    /**
     * @param int $parentPid
     *
     * @return void
     */
    public function setParentPid(int $parentPid) : void
    {
        $this->parentPid = $parentPid;
    }

    /**
     * @return string
     */
    public function getParentTable() : string
    {
        return $this->parentTable;
    }

    /**
     * @param string $parentTable
     *
     * @return void
     */
    public function setParentTable(string $parentTable) : void
    {
        $this->parentTable = $parentTable;
    }

    /**
     * @return \Walther\Bookmarks\Domain\Model\FrontendUser|null
     */
    public function getFeuser() : ?\Walther\Bookmarks\Domain\Model\FrontendUser
    {
        return $this->feuser ?: null;
    }

    /**
     * @param \Walther\Bookmarks\Domain\Model\FrontendUser $feuser
     *
     * @return void
     */
    public function setFeuser(\Walther\Bookmarks\Domain\Model\FrontendUser $feuser) : void
    {
        $this->feuser = $feuser;
    }

    /**
     * @return string
     */
    public function getId() : string
    {
        return 'bookmark-table:' . $this->parentTable . '-pid:' . $this->parentPid . '-uid:' . $this->parentUid . '';
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function getUri() : string
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $allowedBookmarkableTables = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($extbaseFrameworkConfiguration['plugin.']['tx_bookmarks.']['allowedBookmarkableTables.']);

        /** @var \TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder $uriBuilder */
        $uriBuilder = $objectManager->get(\TYPO3\CMS\Extbase\Mvc\Web\Routing\UriBuilder::class);

        $uriBuilder
            ->reset()
            ->setTargetPageUid($this->parentPid)
            ->setSection('c' . $this->parentUid)
            ->setCreateAbsoluteUri(true);

        if (is_array($allowedBookmarkableTables)) {
            foreach ($allowedBookmarkableTables as $key => $value) {
                if ($value['table'] === $this->parentTable) {
                    if (is_array($value['linkParams'])) {
                        $linkParams = $value['linkParams'];
                        $uriBuilder->uriFor($linkParams['action'], [$linkParams['elementName'] => $this->parentUid], $linkParams['controller'], $linkParams['extensionName'], $linkParams['pluginName']);
                    }
                }
            }
        }

        return $uriBuilder->build();
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function getTitle() : string
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $allowedBookmarkableTables = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($extbaseFrameworkConfiguration['plugin.']['tx_bookmarks.']['allowedBookmarkableTables.']);

        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class);

        $string = '';

        if (is_array($allowedBookmarkableTables)) {
            foreach ($allowedBookmarkableTables as $key => $value) {
                if ($value['table'] === $this->parentTable) {
                    $fields = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $value['fields'], true);

                    if ($value['repositoryClassName'] !== '') {

                        /** @var \TYPO3\CMS\Extbase\Persistence\RepositoryInterface $repository */
                        $repository = $objectManager->get($value['repositoryClassName']);

                        /** @var \TYPO3\CMS\Extbase\Persistence\QueryInterface $query */
                        $query = $repository->createQuery();
                        $query->getQuerySettings()->setRespectStoragePage(false);

                        $result = $query->matching(
                            $query->equals('uid', $this->parentUid)
                        )->execute()->getFirst();

                        if ($result) {

                            $result = $result->_getCleanProperties();

                            foreach ($fields as $field) {
                                if (strpos($field, '.') !== false) {

                                    $props = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('.', $field, true);

                                    if ($result[$props[0]]) {

                                        $subItem = $result[$props[0]]->_getCleanProperties();

                                        if ($subItem[$props[1]] !== '' || $subItem[$props[1]] !== null) {
                                            $string = $subItem[$props[1]];
                                            break 2;
                                        }
                                    }
                                } else {
                                    if ($result[$field] !== '' || $result[$field] !== null) {
                                        $string = $result[$field];
                                        break 2;
                                    }
                                }
                            }
                        }
                    } else {
                        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
                        $queryBuilder = $connectionPool->getQueryBuilderForTable($value['table']);
                        $queryBuilder->getRestrictions()->removeAll();
                        $queryBuilder->select('*')->from($value['table'])->where($queryBuilder->expr()->eq('uid', $this->parentUid));

                        $result = $queryBuilder->execute()->fetch();

                        foreach ($fields as $field) {
                            if (strpos($field, '.') !== false) {

                                $props = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('.', $field, true);

                                if ($result[$props[0]]) {

                                    $subItem = $result[$props[0]]->_getCleanProperties();

                                    if ($subItem[$props[1]] !== '' || $subItem[$props[1]] !== null) {
                                        $string = $subItem[$props[1]];
                                        break 2;
                                    }
                                }
                            } else {
                                if ($result[$field] !== '' || $result[$field] !== null) {
                                    $string = $result[$field];
                                    break 2;
                                }
                            }
                        }
                    }
                }
            }
        }

        return $string ? strip_tags($string) : \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('bookmark', 'bookmarks', [$this->parentUid]);
    }

    /**
     * @return mixed|object
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function getData()
    {
        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $allowedBookmarkableTables = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($extbaseFrameworkConfiguration['plugin.']['tx_bookmarks.']['allowedBookmarkableTables.']);

        /** @var \TYPO3\CMS\Core\Database\ConnectionPool $connectionPool */
        $connectionPool = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Database\ConnectionPool::class);

        if (is_array($allowedBookmarkableTables)) {
            foreach ($allowedBookmarkableTables as $key => $value) {
                if ($value['table'] === $this->parentTable) {
                    if ($value['repositoryClassName'] !== '') {
                        /** @var \TYPO3\CMS\Extbase\Persistence\Repository $repository */
                        $repository = $objectManager->get($value['repositoryClassName']);

                        $query = $repository->createQuery();
                        $query->getQuerySettings()->setRespectStoragePage(false);

                        $result = $query->matching(
                            $query->equals('uid', $this->parentUid)
                        )->execute()->getFirst();

                        return $result;
                    } else {
                        /** @var \TYPO3\CMS\Core\Database\Query\QueryBuilder $queryBuilder */
                        $queryBuilder = $connectionPool->getQueryBuilderForTable($value['table']);
                        $queryBuilder->getRestrictions()->removeAll();
                        $queryBuilder->select('*')->from($value['table'])->where($queryBuilder->expr()->eq('uid', $this->parentUid));
                        return $queryBuilder->execute()->fetch();
                    }
                }
            }
        }
    }

    /**
     * @return string
     * @throws \TYPO3\CMS\Extbase\Configuration\Exception\InvalidConfigurationTypeException
     */
    public function getType() : string
    {
        $type = 'None';

        /** @var \TYPO3\CMS\Extbase\Object\ObjectManager objectManager */
        $objectManager = \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Extbase\Object\ObjectManager::class);

        /** @var \TYPO3\CMS\Extbase\Configuration\ConfigurationManager $configurationManager */
        $configurationManager = $objectManager->get(\TYPO3\CMS\Extbase\Configuration\ConfigurationManager::class);

        $extbaseFrameworkConfiguration = $configurationManager->getConfiguration(\TYPO3\CMS\Extbase\Configuration\ConfigurationManagerInterface::CONFIGURATION_TYPE_FULL_TYPOSCRIPT);
        $allowedBookmarkableTables = \TYPO3\CMS\Core\Utility\GeneralUtility::removeDotsFromTS($extbaseFrameworkConfiguration['plugin.']['tx_bookmarks.']['allowedBookmarkableTables.']);

        if (is_array($allowedBookmarkableTables)) {
            foreach ($allowedBookmarkableTables as $key => $value) {

                if ($value['table'] === $this->parentTable) {
                    $type = \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate($value['label'], 'bookmarks');
                }
            }
        }

        return $type;
    }
}
