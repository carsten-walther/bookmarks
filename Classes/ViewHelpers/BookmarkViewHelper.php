<?php
declare(strict_types=1);

namespace Walther\Bookmarks\ViewHelpers\Link;

/**
 * Use e.g.:
 *
 *      <bookmark:link.bookmark />
 *
 * or
 *
 *      <bookmark:link.bookmark>Foobar</bookmark:link.bookmark>
 *
 * or you can use it to set the uid, pid, table and value:
 *
 *      <bookmark:link.bookmark uid="12" pid="3" table="tx_extension_domain_model_foobar">Foobar</bookmark:link.bookmark>
 *
 * or
 *
 *      <bookmark:link.bookmark uid="12" pid="3" table="tx_extension_domain_model_foobar" />
 *
 * code:
 *
 *      <a id="bookmark-table:tt_content-pid:43-uid:269" href="/page#bookmark-table:tt_content-pid:43-uid:269" data-bookmark="" data-bookmark-uid="269" data-bookmark-pid="43" data-bookmark-table="tt_content" onclick="return false;">Bookmark this</a>
 *
 */

/**
 * Class BookmarkViewHelper
 *
 * @package Walther\Bookmarks\ViewHelpers
 */
class BookmarkViewHelper extends \TYPO3Fluid\Fluid\Core\ViewHelper\AbstractTagBasedViewHelper
{
    /**
     * @var string
     */
    protected $tagName = 'a';

    /**
     * initialize arguments
     *
     * @return void
     */
    public function initializeArguments() : void
    {
        parent::initializeArguments();

        $this->registerUniversalTagAttributes();

        $this->registerTagAttribute('uid', 'int', 'Data uid to bookmark');
        $this->registerTagAttribute('pid', 'int', 'Data pid to bookmark');
        $this->registerTagAttribute('table', 'string', 'Table to bookmark');
    }

    /**
     * render
     *
     * @return string
     */
    public function render() : string
    {
        $variableProvider = $this->renderingContext->getVariableProvider();
        $data = $this->templateVariableContainer->get('data');

        $uid = (string)$this->arguments['uid'] ?: $variableProvider['data']['uid'];
        $pid = (string)$this->arguments['pid'] ?: $variableProvider['data']['pid'] ?: $data['pid'] ?: 0;
        $table = (string)$this->arguments['table'] ?: 'tt_content';

        $section = 'bookmark-table:' . $table . '-pid:' . $pid . '-uid:' . $uid;

        $uriBuilder = $this->renderingContext->getControllerContext()->getUriBuilder();
        $uri = $uriBuilder->reset()->setSection($section)->build();

        $this->tag->setTagName($this->tagName);

        $this->tag->addAttribute('id', $section);
        $this->tag->addAttribute('href', $uri);
        $this->tag->addAttribute('data-bookmark', '');
        $this->tag->addAttribute('data-bookmark-parent-uid', $uid);
        $this->tag->addAttribute('data-bookmark-parent-pid', $pid);
        $this->tag->addAttribute('data-bookmark-parent-table', $table);
        $this->tag->addAttribute('onClick', 'return false;');

        $this->tag->forceClosingTag(true);

        $content = $this->renderChildren() ?: \TYPO3\CMS\Extbase\Utility\LocalizationUtility::translate('bookmark_this', 'bookmarks');

        $this->tag->setContent($content);

        return $this->tag->render();
    }
}
