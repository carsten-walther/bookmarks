<?php
declare(strict_types = 1);

namespace Walther\Bookmarks\Service;

/**
 * Class SessionHandler
 *
 * @package Walther\Bookmarks\Controller
 */
class SessionHandler implements \TYPO3\CMS\Core\SingletonInterface
{
    private $prefixKey = 'tx_bookmarks_';

    /**
     * @param $prefixKey
     */
    public function setPrefixKey($prefixKey) : void
    {
        $this->prefixKey = $prefixKey;
    }

    /**
     * @param string $key
     *
     * @return array|object
     */
    public function getFromSession(string $key)
    {
        return $this->restoreFromSession($key);
    }

    /**
     * @param        $object
     * @param string $key
     *
     * @return \Walther\Bookmarks\Service\SessionHandler
     */
    public function addToSession($object, string $key) : \Walther\Bookmarks\Service\SessionHandler
    {
        $data = $this->restoreFromSession($key);

        $object->_setProperty('uid', (int)end(\TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode('.', uniqid('bookmark', true), true)));
        $data[] = $object;

        $this->cleanUpSession($key);
        $this->writeToSession($data, $key);
        return $this;
    }

    /**
     * @param        $object
     * @param string $key
     *
     * @return \Walther\Bookmarks\Service\SessionHandler
     */
    public function removeFromSession($object, string $key) : \Walther\Bookmarks\Service\SessionHandler
    {
        $data = $this->restoreFromSession($key);
        foreach ($data as $k => $item) {
            if ($item->getUid() === $object->getUid()) {
                unset($data[$k]);
            }
        }
        $this->cleanUpSession($key);
        $this->writeToSession($data, $key);
        return $this;
    }

    /**
     * @param string $key
     *
     * @return mixed
     */
    public function restoreFromSession(string $key)
    {
        $sessionData = $GLOBALS['TSFE']->fe_user->getKey('ses', $this->prefixKey . $key);
        return $sessionData ? unserialize($sessionData) : [];
    }

    /**
     * @param mixed $object
     * @param string $key
     *
     * @return \Walther\Bookmarks\Service\SessionHandler
     */
    public function writeToSession($object, string $key) : \Walther\Bookmarks\Service\SessionHandler
    {
        $sessionData = serialize($object);
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, $sessionData);
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }

    /**
     * @param string $key
     *
     * @return \Walther\Bookmarks\Service\SessionHandler
     */
    public function cleanUpSession(string $key) : \Walther\Bookmarks\Service\SessionHandler
    {
        $GLOBALS['TSFE']->fe_user->setKey('ses', $this->prefixKey . $key, '.');
        $GLOBALS['TSFE']->fe_user->storeSessionData();
        return $this;
    }
}
