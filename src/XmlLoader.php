<?php
namespace BorislavSabev\SimpleXmlLoader;

use BorislavSabev\SimpleXmlLoader\XmlLoaderPayload;
use BorislavSabev\SimpleXmlLoader\Exception\XmlLoaderException;

/**
 * Class XmlLoader
 *
 * @project SimpleXmlLoader
 * @package BorislavSabev\SimpleXmlLoader
 * @author Borislav Sabev <sabev.borislav@gmail.com>
 */
class XmlLoader
{
    /** @var XmlLoaderPayload|null The XmlLoaderPayload object */
    private $xmlPayload = null;
    /** @var bool $libXmlErrorState */
    private $libXmlErrorState;
    /* Default SimpleXml class names */
    const XML_ELEMENT_CLASS = \SimpleXMLElement::class;
    const XML_ITERATOR_CLASS = \SimpleXMLIterator::class;
    
    /** XmlLoader constructor. */
    public function __construct()
    {
        $this->libXmlErrorState = libxml_use_internal_errors(true);
    }

    /**
     * XmlLoader destructor
     * Restore the original state of SimpleXml's $use_errors
     */
    public function __destruct()
    {
        libxml_use_internal_errors($this->libXmlErrorState);
    }


    /**
     * Interprets an XML file into an object
     * 
     * Follows simplexml_load_file()'s method signature
     * 
     * @param $filename
     * @param string $xmlClass
     * @param int $options
     * @param string $xmlNamespace
     * @param bool $isPrefix
     * @throws XmlLoaderException
     * @return \SimpleXMLElement
     */
    public function loadFile($filename, $xmlClass = self::XML_ELEMENT_CLASS, $options = 0, $xmlNamespace = "", $isPrefix = false)
    {
        $this->resetState();
        $this->xmlPayload->xmlFilename = basename($filename);
        $this->xmlPayload->xmlElement  = simplexml_load_file($filename, $xmlClass, $options, $xmlNamespace, $isPrefix);

        if ($this->xmlPayload->xmlElement === false) {
            $this->handleLibXmlErrors();
        }
        
        return $this->xmlPayload->getXmlElement();
    }

    /**
     * Interprets a string of XML into an object
     * 
     * Follows simplexml_load_string()'s method signature
     * Overwrites LibXml's default empty string handling by throwing an exception, normally FALSE would be returned.
     * 
     * @param $data
     * @param string $xmlClass 
     * @param int $options
     * @param string $xmlNamespace
     * @param bool $isPrefix
     * @throws XmlLoaderException
     * @return \SimpleXMLElement
     */
    public function loadString($data, $xmlClass = self::XML_ELEMENT_CLASS, $options = 0, $xmlNamespace = "", $isPrefix = false)
    {
        $this->resetState();
        if (empty($data)) {
            throw new XmlLoaderException('$data is empty');
        }
        $this->xmlPayload->xmlElement = simplexml_load_string($data, $xmlClass, $options, $xmlNamespace, $isPrefix);
        if ($this->xmlPayload->xmlElement === false) {
            $this->handleLibXmlErrors();
        }
        
        return $this->xmlPayload->getXmlElement();
    }

    /**
     * Get the XmlLoaderPayload object or null if you did not call any of the loaders
     * @return \BorislavSabev\SimpleXmlLoader\XmlLoaderPayload|null
     */
    public function getXmlPayload()
    {
        return $this->xmlPayload;
    }

    /**
     * LibXml error handler 
     * 
     * Gets LibXml errors, pushes to XmlLoaderPayload, throws XmlLoaderException
     * The exception thrown contains the message and code of the last \LibXmlError
     * 
     * @throws XmlLoaderException
     */
    protected function handleLibXmlErrors()
    {
        /** @var \LibXMLError $libXmlError */
        $libXmlError = libxml_get_last_error();
        $this->xmlPayload->loadErrors(libxml_get_errors());

        throw new XmlLoaderException($libXmlError->message, $libXmlError->code);
    }
    
    /**
     * Reset the loader's internal state
     * - Clear libxml's error buffer
     * - Reinitialize XmlLoaderPayload
     * 
     * Intended to be called before each XML loader runs
     * 
     * @return void
     */
    private function resetState()
    {
        if (null !== $this->xmlPayload) {
            //Explicitly destroy the object's data
            unset($this->xmlPayload);
        }

        libxml_clear_errors();
        $this->xmlPayload = new XmlLoaderPayload();
    }
}
