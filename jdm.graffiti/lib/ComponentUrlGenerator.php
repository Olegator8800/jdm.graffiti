<?php

namespace Jdm\Graffiti;

use InvalidArgumentException;

class ComponentUrlGenerator
{
    /**
     * @var array
     */
    private $urlTemplates;

    /**
     * @var string
     */
    private $urlRoot;

    /**
     * @param array  $urlTemplates
     * @param string $urlRoot
     */
    public function __construct(array $urlTemplates, $urlRoot = '')
    {
        $this->urlTemplates = $urlTemplates;
        $this->urlRoot = $urlRoot;
    }

    /**
     * @param string $urlRoot
     */
    public function setUrlRoot($urlRoot)
    {
        $this->urlRoot = $urlRoot;
    }

    /**
     * @return string
     */
    public function getUrlRoot()
    {
        return $this->urlRoot;
    }

    /**
     * @return array
     */
    public function getUrlTemplates()
    {
        return $this->urlTemplates;
    }

    /**
     * @param  string $urlChunk
     *
     * @return string
     */
    protected function normalizeSlashes($urlChunk)
    {
        return implode('/', array_filter(explode('/', $urlChunk)));
    }

    /**
     * @return string
     */
    protected function getAbsolutePath()
    {
        $protocol = 'http';

        if ($_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || $_SERVER['HTTPS'] == 'on') {
            $protocol = 'https';
        }

        return $protocol.'://'.$_SERVER['HTTP_HOST'];
    }

    /**
     * @param  string  $name
     * @param  array   $parameters
     * @param  boolean $absolutePath
     *
     * @return string
     */
    public function generate($name, $parameters = [], $absolutePath = false)
    {
        $urlTemplates = $this->getUrlTemplates();

        if (!isset($urlTemplates[$name])) {
            throw new InvalidArgumentException(sprintf('Маршрут "%s" для ЧПУ не найден', $name));
        }

        $url = $urlTemplates[$name];

        if ($parameters) {
            $searchParameters = array_keys($parameters);

            $searchParameters = array_map(function($val) { return "#{$val}#"; }, $searchParameters);

            $url = str_replace($searchParameters, array_values($parameters), $url);
        }

        $urlRoot = $this->getUrlRoot();
        $url = $urlRoot.'/'.$url;

        $url = '/'.$this->normalizeSlashes($url);

        if ($absolutePath) {
            $url = $this->getAbsolutePath().$url;
        }

        return $url;
    }
}
