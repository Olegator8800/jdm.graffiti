<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Jdm\Graffiti\Component;
use Exception;

class JdmGraffitiListComponent extends Component
{
    /**
     * @return string
     */
    protected function getComponentCachePath()
    {
        return 'jdm.graffiti';
    }

    protected function executeBeforeIncludingTemplate()
    {
        $gm = $this->getGraffityManager();
        $urlGenerator = $this->getUrlGenerator();

        //$limit = 10;
        $limit = null;

        $items = $gm->getGraffityList(1, $limit);

        foreach ($items as $item) {

            $preparedGraffity = $gm->prepareGraffityAsArray($item);

            $preparedGraffity['url_edit'] = $urlGenerator->generate('edit', ['ELEMENT_ID' => $item->getId()]);
            $preparedGraffity['url_show'] = $urlGenerator->generate('show', ['ELEMENT_ID' => $item->getId()]);

            $this->arResult['ITEMS'][] = $preparedGraffity;

            $this->addCacheTag("jdm-graffiti-{$item->getId()}");
        }

        $this->addCacheTag('jdm-graffiti-list');

        $this->arResult['URL_ADD'] = $urlGenerator->generate('new');
        $this->arResult['ITEMS_TOTAL_COUNT'] = $gm->getGraffityCount();

        parent::executeBeforeIncludingTemplate();
    }
}
