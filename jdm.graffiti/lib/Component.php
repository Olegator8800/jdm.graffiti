<?php

namespace Jdm\Graffiti;

use CMain;
use Bitrix\Main\Loader;
use CBitrixComponent;
use Bitrix\Main\LoaderException;
use InvalidArgumentException;
use Exception;

abstract class Component extends CBitrixComponent
{
    /**
     * @var array
     */
    protected $cacheTags = [];

    /**
     * @return CMain
     */
    protected function getApplication()
    {
        return $GLOBALS['APPLICATION'];
    }

    /**
     * @return
     */
    protected function getCacheManager()
    {
        return $GLOBALS['CACHE_MANAGER'];
    }

    /**
     * @return bool
     */
    protected function isAjax()
    {
        return $this->arParams['AJAX'] == 'Y';
    }

    /**
     * @return string
     */
    protected function getComponentCachePath()
    {
        return false;
    }

    /**
     * @return string
     */
    protected function getAdditionalCacheKey()
    {
        $user = $GLOBALS['USER'];

        $groups = array_unique($user->getUserGroupArray());
        sort($groups);

        return 'ug_'.implode('_', $groups);
    }

    /**
     * @return array
     */
    protected function getCacheTags()
    {
        return $this->cacheTags;
    }

    /**
     * @param string
     */
    protected function addCacheTag($tag)
    {
        if (!in_array($tag, $this->cacheTags)) {
            $this->cacheTags[] = $tag;
        }
    }

    /**
     * @param array $cacheTags
     */
    protected function setCacheTags(array $cacheTags)
    {
        $this->cacheTags = array_unique($cacheTags);
    }

    /**
     * @return bool
     */
    protected function readDataFromCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        return !($this->startResultCache(
                    $this->arParams['CACHE_TIME'],
                    $this->getAdditionalCacheKey(),
                    $this->getComponentCachePath())
        );
    }

    /**
     * @return bool
     */
    protected function endCache()
    {
        if ($this->arParams['CACHE_TYPE'] == 'N') {
            return false;
        }

        $this->endResultCache();
    }

    protected function abortDataCache()
    {
        $this->abortResultCache();
    }

    protected function includeModules()
    {
        if (!Loader::includeModule('jdm.graffiti')) {
            throw new LoaderException('Ошибка подключения модуля jdm.graffiti');
        }
    }

    protected function executeBeforeCaching()
    {

    }

    protected function executeBeforeIncludingTemplate()
    {
        $cacheTags = $this->getCacheTags();

        if (!empty($cacheTags)) {
            $this->getCacheManager()->startTagCache($this->getComponentCachePath());
            array_map([$this->getCacheManager(), 'registerTag'], $cacheTags);
        }
    }

    protected function executeAfterIncludingTemplate()
    {
        $cacheTags = $this->getCacheTags();

        if (!empty($cacheTags)) {
            $this->getCacheManager()->endTagCache();
        }
    }

    protected function executeAfterCaching()
    {

    }

    /**
     * @param  array $params
     *
     * @return array
     */
    public function onPrepareComponentParams($params)
    {
        $params = parent::onPrepareComponentParams($params);

        if (!$params['GRAFFITY_MANAGER']) {
            throw new InvalidArgumentException('GRAFFITY_MANAGER не подключен к компоненту');
        }

        if (!$params['URL_GENERATOR']) {
            throw new InvalidArgumentException('URL_GENERATOR не подключен к компоненту');
        }

        $params['CACHE_TIME'] = $params['CACHE_TIME']?:3600;

        return $params;
    }

    protected function getGraffityManager()
    {
        return $this->arParams['GRAFFITY_MANAGER'];
    }

    protected function getUrlGenerator()
    {
        return $this->arParams['URL_GENERATOR'];
    }

    public function executeComponent()
    {
        try {
            $this->includeModules();

            $this->executeBeforeCaching();

            if ($this->isAjax()) {
                $this->getApplication()->restartBuffer();
            }

            if (!$this->readDataFromCache()) {

                $this->executeBeforeIncludingTemplate();

                $this->includeComponentTemplate();

                $this->executeAfterIncludingTemplate();
            }

            if ($this->isAjax()) {
                die;
            }

            $this->executeAfterCaching();

        } catch (Exception $e) {
            $this->abortDataCache();
            ShowError($e->getMessage());
        }
    }
}
