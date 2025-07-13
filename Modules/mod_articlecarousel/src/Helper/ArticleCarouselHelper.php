<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_articlecarousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\ArticleCarousel\Site\Helper;

defined('_JEXEC') or die;

use Joomla\CMS\Application\SiteApplication;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Registry\Registry;

/**
 * Helper for mod_articlecarousel
 *
 */
class ArticleCarouselHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    /**
     * Retrieve articles data
     *
     * @param   Registry            $params  The module parameters.
     * @param   SiteApplication     $app     The application.
     *
     * @return  array
     *
     */
    public function getArticles(Registry $params, SiteApplication $app): array
    {
        $input = $app->getInput();
        $user = $app->getIdentity();
        
        // build query
        $db = $this->getDatabase();
        $query = $db->getQuery(true)
            ->select([
                $db->quoteName('a.id'),
                $db->quoteName('a.title'),
                $db->quoteName('a.introtext'),
                $db->quoteName('a.images')
            ])
            ->from($db->quoteName('#__content', 'a'))
            ->where($db->quoteName('a.state') . ' = 1') // published only
            ->where($db->quoteName('a.access') . ' IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');

        // filter current article
        $articleId = $input->get('id', 0, 'UINT');
        if ($articleId) {
            $query->where($db->quoteName('a.id') . ' != ' . (int) $articleId);
        }

        // optional category filter
        $categoryIds = $params->get('filter_categories', []);
        if (!empty($categoryIds)) {
            $query->where($db->quoteName('a.catid') . ' IN (' . implode(',', array_map('intval', $categoryIds)) . ')');
        }

        // optional tag filter
        $tagIds = $params->get('filter_tags', []);
        if (!empty($tagIds)) {
            $query->join('INNER', $db->quoteName('#__contentitem_tag_map') . ' AS ' . $db->quoteName('tm')
                . ' ON ' . $db->quoteName('tm.content_item_id') . ' = ' . $db->quoteName('a.id')
                . ' AND ' . $db->quoteName('tm.type_alias') . ' = ' . $db->quote($db->escape('com_content.article')))
                ->where($db->quoteName('tm.tag_id') . ' IN (' . implode(',', array_map('intval', $tagIds)) . ')');
        }

        $query->order('RAND()');

        $db->setQuery($query, 0, $params->get('limit', 10));

        return $db->loadObjectList();
    }
}