<?php

namespace CoreSphere\TwigProfilerBundle;

use Symfony\Bundle\TwigBundle\TwigEngine;

use Symfony\Bundle\FrameworkBundle\Templating\GlobalVariables;
use Symfony\Component\Templating\TemplateNameParserInterface;

class TwigProfiledEngine extends TwigEngine
{
    protected $profilerService;

    /**
     * Constructor.
     *
     * @param \Twig_Environment           $environment A \Twig_Environment instance
     * @param TemplateNameParserInterface $parser      A TemplateNameParserInterface instance
     * @param GlobalVariables|null        $globals     A GlobalVariables instance or null
     */
    public function __construct(\Twig_Environment $environment, TemplateNameParserInterface $parser, GlobalVariables $globals = null, $profilerService)
    {
        $this->environment = $environment;
        $this->parser = $parser;

        if (null !== $globals) {
            $environment->addGlobal('app', $globals);
        }

        $this->profilerService = $profilerService;
    }

    /**
     * Renders a template.
     *
     * @param mixed $name       A template name
     * @param array $parameters An array of parameters to pass to the template
     *
     * @return string The evaluated template as a string
     *
     * @throws \InvalidArgumentException if the template does not exist
     * @throws \RuntimeException         if the template cannot be rendered
     */
    public function render($name, array $parameters = array())
    {
        $this->profilerService->collectTemplateParameters($name, $parameters);
        return $this->load($name)->render($parameters);
    }
}
