<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Jdm\Graffiti\Component;
use CHTTP;
use InvalidArgumentException;
use Exception;

class JdmGraffitiEditComponent extends Component
{
    /**
     * @return string
     */
    protected function getComponentCachePath()
    {
        return false;
    }

    public function onPrepareComponentParams($params)
    {
        $params = parent::onPrepareComponentParams($params);

        $params['NEW'] = $params['NEW'] == 'Y'?'Y':'N';
        $params['ELEMENT_ID'] = (int) $params['ELEMENT_ID'];
        $params['CACHE_TYPE'] = 'N';
        $params['CACHE_TIME'] = 0;

        return $params;
    }

    protected function executeBeforeIncludingTemplate()
    {
        $urlGenerator = $this->getUrlGenerator();
        $gm = $this->getGraffityManager();

        if ($this->arParams['NEW'] != 'Y') {

            $graffitiId = $this->arParams['ELEMENT_ID'];
            $graffiti = $gm->getGraffityById($graffitiId);

            if (!$graffiti) {
                CHTTP::SetStatus('404 Not Found');
                throw new InvalidArgumentException('Графити не найдено');
            }

            $this->arResult['ITEM'] = $gm->prepareGraffityAsArray($graffiti);
        }

        $this->arResult['URL_LIST'] = $urlGenerator->generate('index');
        $this->arResult['URL_API_SAVE'] = $urlGenerator->generate('api_save');
        $this->arResult['URL_API_CHECK'] = $urlGenerator->generate('api_check');

        $this->arResult['GRAFFITI_WIDTH'] = $gm->getGraffitiWidth();
        $this->arResult['GRAFFITI_HEIGHT'] = $gm->getGraffitiHeight();

        parent::executeBeforeIncludingTemplate();
    }
}
