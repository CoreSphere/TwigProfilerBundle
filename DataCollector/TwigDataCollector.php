<?php
namespace CoreSphere\TwigProfilerBundle\DataCollector;

use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Symfony\Component\HttpKernel\DataCollector\DataCollectorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

use Symfony\Component\Templating\EngineInterface;

class TwigDataCollector extends DataCollector
{
    private $templating;

    public function __construct($templating)
    {
        $this->templating = $templating;

        $this->data = array();
    }

    private function retrieveProperty($obj, $name)
    {
        $refobj = new \ReflectionObject($obj);
        $property = $refobj->getProperty($name);
        $property->setAccessible(TRUE);
        return $property->getValue($obj);
    }

    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
        // Trigger Twigs function and filter initialization
        $this->templating->getFilter("");
        $this->templating->getFunction("");

        $funcs = array();
        foreach ($this->retrieveProperty($this->templating, 'extensions') as $key => $value)
        {
            $funcs[$key] = get_class($value);
        }
        ksort($funcs);
        $this->data['extensions'] = $funcs;

        $funcs = array();
        $meths = array();
        foreach ($this->retrieveProperty($this->templating, 'filters') as $key => $value)
        {
            if ($value instanceof \Twig_Filter_Function)
            {
                $funcs[$key] = $this->retrieveProperty($value,'function');
            }
            else
            {
                $meths[$key] = get_class($this->retrieveProperty($value,'extension')) . "::" . $this->retrieveProperty($value,'method');
            }
        }
        ksort($funcs);
        ksort($meths);

        $this->data['filterfunctions'] = $funcs;
        $this->data['filtermethods'] = $meths;

        $funcs = array();
        $meths = array();
        foreach ($this->retrieveProperty($this->templating, 'functions') as $key => $value)
        {
            if ($value instanceof \Twig_Filter_Function)
            {
                $funcs[$key] = $this->retrieveProperty($value,'function');
            }
            else
            {
                $meths[$key] = get_class($this->retrieveProperty($value,'extension')) . "::" . $this->retrieveProperty($value,'method');
            }
        }
        ksort($funcs);
        ksort($meths);

        $this->data['functions'] = $funcs;
        $this->data['methods'] = $meths;

    }

    public function collectTemplateParameters($name, $variables)
    {
        $this->data['templates'][] = array(
            'name' => $name,
            'variables' => array_keys($variables),
        );
    }

    public function getExtensions()
    {
        return $this->data['extensions'];
    }

    public function getFilterFunctions()
    {
        return $this->data['filterfunctions'];
    }

    public function getFilterMethods()
    {
        return $this->data['filtermethods'];
    }

    public function getFunctions()
    {
        return $this->data['functions'];
    }

    public function getMethods()
    {
        return $this->data['methods'];
    }

    public function getTemplates()
    {
        return $this->data['templates'];
    }

    public function getName()
    {
        return 'twigdata';
    }
}