<?php
namespace BorislavSabev\SimpleXmlLoader\Tests;

use BorislavSabev\SimpleXmlLoader\XmlLoader;
use BorislavSabev\SimpleXmlLoader\Exception\XmlLoaderException;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the XmlLoader::loadString() wrapper
 * 
 * @project       SimpleXmlLoader
 * @author Borislav Sabev <bsabev@it-cover.com>
 */
class LoadStringTest extends TestCase
{
    /** @var XmlLoader $xmlLoader */
    protected $xmlLoader;
    /** @var array $goodXmlStr */
    protected $goodXmlStr = [
        '<?xml version="1.0" encoding="UTF-8"?><rootElem><node><node></rootElem>',
        '<rootElem><node><node></rootElem>',
    ];
    /** @var array $badXmlStr */
    protected $badXmlStr = [
        '',
        '<xs:schema xmlns="urn:pain" xmlns:xs="http://www.w3.org/2001/XMLSchema" elementFormDefault="qualified"><xs:element name',
        '<!-- Generic XML comments-->',
        '<?xml version="1.0" encoding="UTF-8"?><xs:schem </xs:schema>'
    ];

    /** SetUp TestCase */
    protected function setUp()
    {
        $this->xmlLoader = new XmlLoader();
    }

    /** Test with empty $data */
    public function testEmptyData()
    {
        try {
            $this->xmlLoader->loadString('');
        } catch (\Exception $e) {
            $this->assertEquals(0, $e->getCode());
            $this->assertInstanceOf(XmlLoaderException::class, $e);

            $this->assertFalse($this->xmlLoader->getXmlPayload()->getXmlErrors());
        }
    }

    /** Test good assets */
    public function testGoodStrings()
    {
        foreach ($this->goodXmlStr as $data) {
            try {
                $this->xmlLoader->loadString($data);

                $this->assertNotEquals(false, $this->xmlLoader->getXmlPayload()->getXmlElement());
                $this->assertInstanceOf(\SimpleXMLElement::class, $this->xmlLoader->getXmlPayload()->getXmlElement());

            } catch (\Exception $e) {
                $this->assertInstanceOf(XmlLoaderException::class, $e);

                $this->assertContainsOnlyInstancesOf(
                    \LibXMLError::class,
                    $this->xmlLoader->getXmlPayload()->getXmlErrors()
                );
            }
        }
    }

    /** Test bad assets */
    public function testBadStrings()
    {
        foreach ($this->badXmlStr as $data) {
            try {
                $this->xmlLoader->loadString($data);

            } catch (\Exception $e) {
                $this->assertInstanceOf(XmlLoaderException::class, $e);
                if (empty($data)) {
                    $this->assertEquals(0, $e->getCode());
                } else {
                    $this->assertContainsOnlyInstancesOf(
                        \LibXMLError::class,
                        $this->xmlLoader->getXmlPayload()->getXmlErrors()
                    );
                }
            }
        }
    }

    /** Tests SimpleXml return class overwrite over good assets */
    public function testXmlClassOverloading()
    {
        foreach ($this->goodXmlStr as $data) {
            try {
                $this->xmlLoader->loadString($data, XmlLoader::XML_ITERATOR_CLASS);

                $this->assertNotEquals(false, $this->xmlLoader->getXmlPayload()->getXmlElement());
                $this->assertInstanceOf(\SimpleXMLIterator::class, $this->xmlLoader->getXmlPayload()->getXmlElement());

            } catch (\Exception $e) {
                $this->assertInstanceOf(XmlLoaderException::class, $e);

                $this->assertContainsOnlyInstancesOf(
                    \LibXMLError::class,
                    $this->xmlLoader->getXmlPayload()->getXmlErrors()
                );
            }
        }
    }
}