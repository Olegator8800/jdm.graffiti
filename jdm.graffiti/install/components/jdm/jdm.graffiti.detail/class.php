<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Jdm\Graffiti\Component;
use CHTTP;
use InvalidArgumentException;
use Exception;

class JdmGraffitiDetailComponent extends Component
{
    /**
     * @return string
     */
    protected function getComponentCachePath()
    {
        return 'jdm.graffiti.detail/'.$this->arParams['ELEMENT_ID'];
    }

    public function onPrepareComponentParams($params)
    {
        $params = parent::onPrepareComponentParams($params);

        $params['ELEMENT_ID'] = (int) $params['ELEMENT_ID'];

        return $params;
    }

    protected function executeBeforeIncludingTemplate()
    {
        $gm = $this->getGraffityManager();
        $urlGenerator = $this->getUrlGenerator();

        $graffitiId = $this->arParams['ELEMENT_ID'];

        $graffiti = $gm->getGraffityById($graffitiId);

        if (!$graffiti) {
            CHTTP::SetStatus('404 Not Found');
            throw new InvalidArgumentException('Графити не найдено');
        }

        $preparedGraffity = $gm->prepareGraffityAsArray($graffiti);
        $preparedGraffity['url_edit'] = $urlGenerator->generate('edit', ['ELEMENT_ID' => $graffiti->getId()]);

        $this->arResult['ITEM'] = $preparedGraffity;
        $this->arResult['URL_LIST'] = $urlGenerator->generate('index');

        $this->addCacheTag("jdm-graffiti-detail-{$graffitiId}");
        $this->addCacheTag('jdm-graffiti-detail');

        parent::executeBeforeIncludingTemplate();
    }
}
