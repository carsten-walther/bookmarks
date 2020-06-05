<?php
declare(strict_types = 1);

namespace Walther\Bookmarks\View;

/**
 * Class JsonView
 * @package Walther\Bookmarks\View
 */
class JsonView extends \TYPO3\CMS\Extbase\Mvc\View\JsonView
{
    /**
     * @param mixed $value
     * @param array $configuration
     *
     * @return mixed
     */
    protected function transformValue($value, array $configuration)
    {
        if ($value instanceof \TYPO3\CMS\Extbase\Persistence\ObjectStorage) {
            $value = $value->toArray();
        }
        return parent::transformValue($value, $configuration);
    }
}
