<?php

/**
 * @package     ToKu.Joomla
 * @subpackage  JToKu
 *
 * @copyright   (C) 2025 ToKu <https://www.toku.cz>
 * @license     GNU General Public License version 3 or later
 */

namespace ToKu\Library\MVC;

use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Content\Site\Model\ArticlesModel as BaseModel;
use ToKu\Library\Content\Model\ArticlesModel;

\defined('_JEXEC') or die;

class MVCFactory implements MVCFactoryInterface
{
    public function __construct(private MVCFactoryInterface $factory)
    {
    }

    public function createModel($name, $prefix = '', array $config = [])
    {
        $model = $this->factory->createModel($name, $prefix, $config);

        if ($model instanceof BaseModel) {
            return new ArticlesModel($config);
        }

        return $model;
    }

    public function createView($name, $prefix = '', $type = '', array $config = [])
    {
        return $this->factory->createView($name, $prefix, $type, $config);
    }

    public function createController($name, $prefix, array $config, $app, $input)
    {
        return $this->factory->createController($name, $prefix, $config, $app, $input);
    }

    public function createTable($name, $prefix = '', array $config = [])
    {
        return $this->factory->createTable($name, $prefix, $config);
    }
}
