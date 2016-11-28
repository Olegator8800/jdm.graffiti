<?php
if(!defined('B_PROLOG_INCLUDED') || B_PROLOG_INCLUDED !== true) die();

use Jdm\Graffiti\Entity\PictureRepository;
use Jdm\Graffiti\GraffityManager;
use Jdm\Graffiti\ComponentUrlGenerator;
use Jdm\Graffiti\SecurityService;
use Bitrix\Main\Loader;
use CBitrixComponent;
use CComponentEngine;
use InvalidArgumentException;
use Exception;

class JdmGraffitiComponent extends CBitrixComponent
{
    /**
     * @var array
     */
    protected $defaultUrlTemplates404 = [];

    /**
     * @var array
     */
    protected $componentVariables = [];

    /**
     * @var string
     */
    protected $page = '';

    /**
     * @var GraffityManager
     */
    protected $graffityManager;

    /**
     * @var ComponentUrlGenerator
     */
    protected $urlGenerator;

    /**
     * @throws InvalidArgumentException If secret string not setted
     */
    protected function setSefDefaultParams()
    {
        $this->defaultUrlTemplates404 = [
            'index' => 'index.php',
            'new'   => 'new',
            'show'  => '#ELEMENT_ID#',
            'edit'  => '#ELEMENT_ID#/edit',
            'api_save' => 'api/save',
            'api_check' => 'api/check',
        ];

        $this->componentVariables = ['ELEMENT_ID'];
    }

    /**
     * @return GraffityManager
     */
    protected function getGraffityManager()
    {
        return $this->graffityManager;
    }

    /**
     * @return ComponentUrlGenerator
     */
    protected function getUrlGenerator()
    {
        return $this->urlGenerator;
    }

    /**
     * @throws LoaderException if not include modele
     */
    protected function includeModules()
    {
        if (!Loader::includeModule('jdm.graffiti')) {
            throw new LoaderException('Ошибка подключения модуля jdm.graffiti');
        }
    }

    /**
     * @return GraffityManager
     */
    protected function createGraffityManager()
    {
        $pictureDirPath = $this->arParams['PICTURE_DIR_PATH'];
        $pictureRepository = new PictureRepository();
        $securityService = new SecurityService();

        return new GraffityManager($pictureRepository, $securityService, $pictureDirPath);
    }

    /**
     * @param  string $urlTemplates
     * @param  string $folder
     *
     * @return ComponentUrlGenerator
     */
    protected function createUrlGenerator($urlTemplates, $folder)
    {
        return new ComponentUrlGenerator($urlTemplates, $folder);
    }

    protected function getResult()
    {
        $urlTemplates = [];

        if ($this->arParams['SEF_MODE'] == 'Y') {
            $variables = [];

            $urlTemplates = CComponentEngine::MakeComponentUrlTemplates(
                $this->defaultUrlTemplates404,
                $this->arParams['SEF_URL_TEMPLATES']
            );

            $variableAliases = CComponentEngine::MakeComponentVariableAliases(
                $this->defaultUrlTemplates404,
                $this->arParams['VARIABLE_ALIASES']
            );


            $engine = new CComponentEngine($this);

            $this->page = $engine->guessComponentPath(
                $this->arParams['SEF_FOLDER'],
                $urlTemplates,
                $variables
            );

            if (strlen($this->page) <= 0) {
                $this->page = 'index';
            }

            CComponentEngine::InitComponentVariables(
                $this->page,
                $this->componentVariables,
                $variableAliases,
                $variables
            );

        } else {
            $this->page = 'index';
        }

        $folder = $this->arParams['SEF_FOLDER'];

        $this->graffityManager = $this->createGraffityManager();
        $this->urlGenerator = $this->createUrlGenerator($urlTemplates, $folder);

        $this->arResult = [
           'FOLDER' => $this->arParams['SEF_FOLDER'],
           'URL_TEMPLATES' => $urlTemplates,
           'VARIABLES' => $variables,
           'ALIASES' => $variableAliases,
           'GRAFFITY_MANAGER' => $this->graffityManager,
           'URL_GENERATOR' => $this->urlGenerator,
        ];
    }

    public function executeComponent()
    {
        try {
            $this->setSefDefaultParams();
            $this->includeModules();
            $this->getResult();
            $this->includeComponentTemplate($this->page);
        } catch (Exception $e) {
            ShowError($e->getMessage());
        }
    }
}
