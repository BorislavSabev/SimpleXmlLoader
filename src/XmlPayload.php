<?php
namespace BorislavSabev\SimpleXmlLoader;

/**
 * Class XmlPayload
 * 
 * A payload for LibXML's stuff
 * 
 * @project SimpleXmlLoader
 * @package BorislavSabev\SimpleXmlLoader
 * @author Borislav Sabev <sabev.borislav@gmail.com>
 */
class XmlPayload
{
    /**  @var null|string XML file's filename if any */
    public $xmlFilename = null;
    /** @var null|\SimpleXMLElement The resulting XML element as returned by SimpleXml */
    public $xmlElement = null;
    /** @var bool Bit showing if there are errors on load */
    public $xmlHasErrors = false;
    /** @var array An array of LibXMLError objects if there are any */
    public $xmlErrors = [];

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
        if (empty($this->xmlErrors)) {
            return false;
        }

        return $this->xmlErrors;
    }

    /**
     * Does the payload currently contain errors?
     * @return bool
     */
    public function hasErrors()
    {
        return $this->xmlHasErrors;
    }

    /**
     * Load errors into the payload
     * @param $xmlErrors
     */
    public function loadErrors($xmlErrors)
    {
        if (!empty($xmlErrors)) {
            $this->xmlHasErrors = true;
            foreach ($xmlErrors as $xmlError) {
                $this->xmlErrors[] = $xmlError;
            }
        }
    }
}
