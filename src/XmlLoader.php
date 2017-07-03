<?php

namespace BorislavSabev\SimpleXmlLoader;

use BorislavSabev\SimpleXmlLoader\XmlPayload;
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
    /** @var \BorislavSabev\SimpleXmlLoader\XmlPayload|null The XmlPayload object */
    private $xmlPayload = null;

    /**
     * XmlLoader constructor.
     */
    public function __construct()
    {
        libxml_use_internal_errors(true);
    }

    /**
     * Interprets an XML file into an object
     * Follows simplexml_load_file()'s method signature
     * 
     * @param $filename
     * @param string $class_name
     * @param int $options
     * @param string $ns
     * @param bool $is_prefix
     * @return \SimpleXMLElement
     */
    public function loadFile($filename, $class_name = "SimpleXMLElement", $options = 0, $ns = "", $is_prefix = false)
    {
        if (!file_exists($filename)) {
            throw new XmlLoaderException("(File does not exist: {$filename}", XmlLoaderException::ERROR_CODE_FILE);
        }
        
        libxml_clear_errors();
        $this->xmlPayload = new XmlPayload();
        $this->xmlPayload->xmlFilename = basename($filename);
        $this->xmlPayload->xmlElement = simplexml_load_file($filename, $class_name, $options, $ns, $is_prefix);

        if ($this->xmlPayload->xmlElement === false) {
            $this->handleLibXmlErrors();
        }
        
        return $this->xmlPayload->getXmlElement();
    }

    /**
     * Interprets a string of XML into an object
     * Follows simplexml_load_string()'s method signature
     * @param $data
     * @param string $class_name 
     * @param int $options
     * @param string $ns
     * @param bool $is_prefix
     * @throws XmlLoaderException
     * @return \SimpleXMLElement
     */
    public function loadString($data, $class_name = "SimpleXMLElement", $options = 0, $ns = "", $is_prefix = false)
    {
        libxml_clear_errors();
        $this->xmlPayload = new XmlPayload();
        $this->xmlPayload->xmlElement = simplexml_load_string($data, $class_name, $options, $ns, $is_prefix);
        if ($this->xmlPayload->xmlElement === false) {
            $this->handleLibXmlErrors();
        }
        
        return $this->xmlPayload->getXmlElement();
    }

    /**
     * Get the XmlPayload object or null if you did not call any of the loaders
     * @return \BorislavSabev\SimpleXmlLoader\XmlPayload|null
     */
    public function getXmlPayload()
    {
        return $this->xmlPayload;
    }

    /**
     * Handle LibXml errors internally
     * @throws XmlLoaderException
     */
    private function handleLibXmlErrors()
    {
        $this->xmlPayload->loadErrors(libxml_get_errors());
        throw new XmlLoaderException('Encountered LibXML errors', XmlLoaderException::ERROR_CODE_XML_ERRORS);
    }
}
