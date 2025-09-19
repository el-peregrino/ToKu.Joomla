<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  mod_articlecarousel
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Module\ArticleCarousel\Site\Helper;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\Database\DatabaseAwareInterface;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

\defined('_JEXEC') or die;

/**
 * Helper for mod_articlecarousel
 *
 */
class ArticleCarouselHelper implements DatabaseAwareInterface
{
    use DatabaseAwareTrait;

    // TYPED CONSTANTS ARE SUPPORTED SINCE PHP 8.3

    public const NAME = 'ArticleCarousel';
    public const MODULE = 'mod_articlecarousel';

    public static function getAsset(string $name): string
    {
        return self::MODULE . ".$name";
    }

    /**
     * Retrieve articles data
     *
     * @param   Registry                $params  The module parameters.
     * @param   CMSApplicationInterface $app     The application.
     *
     * @return  array
     *
     */
    public function getArticles(Registry $params, CMSApplicationInterface $app): array
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
            // published items only
            ->where($db->quoteName('a.state') . ' = 1')
            ->where($db->quoteName('a.publish_up') . ' <= NOW() AND (' . $db->quoteName('a.publish_down') . ' is null OR ' . $db->quoteName('a.publish_down') . ' <= NOW())')
            // user access
            ->whereIn($db->quoteName('a.access'), $user->getAuthorisedViewLevels(), ParameterType::INTEGER);

        // filter current article
        $article = $input->get('id', 0, 'UINT');
        if ($article && !$params->get('current', 0)) {
            $query->where($db->quoteName('a.id') . ' != :article')
                ->bind(':article', $article, ParameterType::INTEGER);
        }

        // optional category filter
        $categories = $params->get('categories', []);
        if (!empty($categories)) {
            $query->whereIn($db->quoteName('a.catid'), $categories, ParameterType::INTEGER);
        }

        // optional tag filter
        $tags = $params->get('tags', []);
        if (!empty($tags)) {
            $query->join('INNER', 
                $db->quoteName('#__contentitem_tag_map', 'tm'),
                $db->quoteName('tm.content_item_id') . ' = ' . $db->quoteName('a.id') . ' AND ' . $db->quoteName('tm.type_alias') . ' = ' . $db->quote($db->escape('com_content.article')))
                ->whereIn($db->quoteName('tm.tag_id'), $tags, ParameterType::INTEGER);
        }

        // ordering
        $ordering = match ($params->get('ordering', 'newest'))
        {
            'random' => 'RAND()',
            'oldest' => 'a.publish_up ASC',
            'newest' => 'a.publish_up DESC',
            default => 'a.publish_up DESC'
        };

        $query->order($ordering);
        
        // set the query, no offset, limit
        $db->setQuery($query, 0, $params->get('limit', 10));

        return $db->loadObjectList();
    }
}