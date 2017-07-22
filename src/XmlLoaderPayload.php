<?php
namespace BorislavSabev\SimpleXmlLoader;

/**
 * Class XmlLoaderPayload
 * 
 * A payload for LibXML's stuff
 * 
 * @project SimpleXmlLoader
 * @package BorislavSabev\SimpleXmlLoader
 * @author Borislav Sabev <sabev.borislav@gmail.com>
 */
class XmlLoaderPayload
{
    /**  @var null|string XML file's filename if any */
    public $xmlFilename = null;
    /** @var null|\SimpleXMLElement The resulting XML element as returned by SimpleXml */
    public $xmlElement = null;
    /** @var array An array of LibXMLError objects if there are any */
    private $xmlErrors = [];

    /**
     * Get the loaded \SimpleXMLElement
     * @return null|\SimpleXMLElement
     */
    public function getXmlElement()
    {
        return $this->xmlElement;
    }
    
    /**
     * Get an array with LibXMLError objects if there are any; False otherwise
     * @return array|bool
     */
    public function getXmlErrors()
    {
        if (!$this->hasErrors()) {
            return false;
        }

        return $this->xmlErrors;
    }

    /**
     * Load errors into the payload
     * @param array $xmlErrors
     * @param bool $strictFiltering
     */
    public function loadErrors($xmlErrors, $strictFiltering = false)
    {
        if (!empty($xmlErrors)) {
            foreach ($xmlErrors as $xmlError) {
                if ($strictFiltering && !($xmlError instanceof \LibXMLError)) {
                    continue;
                }
                $this->xmlErrors[] = $xmlError;
            }
        }
    }
    
    /**
     * Does the payload currently contain errors?
     * @return bool True if errors are present
     */
    public function hasErrors()
    {
        return (!empty($this->xmlErrors));
    }
}
