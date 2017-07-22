<?php
namespace BorislavSabev\SimpleXmlLoader\Tests;

use BorislavSabev\SimpleXmlLoader\XmlLoader;
use BorislavSabev\SimpleXmlLoader\Exception\XmlLoaderException;

use PHPUnit\Framework\TestCase;

/**
 * Tests for the XmlLoader::loadFile() wrapper
 * 
 * @project       SimpleXmlLoader
 * @author Borislav Sabev <bsabev@it-cover.com>
 */
class LoadFileTest extends TestCase
{
    /** @var XmlLoader $xmlLoader */
    protected $xmlLoader;
    /** @var string $nonExistentXml */
    protected $nonExistentXml = __DIR__.'/assets/bad/nonexistent.xml';
    /** @var array $goodXmlPaths */
    protected $goodXmlPaths = [
        __DIR__.'assets/good/pain.001.001.08.xsd',
        __DIR__.'assets/good/pain.008.001.07.xsd'
    ];
    /** @var array $badXmlPaths */
    protected $badXmlPaths = [
        __DIR__.'assets/bad/pain.001.001.08.xsd',
        __DIR__.'assets/bad/pain.008.001.05.xsd',
        __DIR__.'assets/bad/pain.008.001.06.xsd',
        __DIR__.'assets/bad/pain.008.001.07.xsd'
    ];

    /** SetUp TestCase */
    protected function setUp()
    {
        $this->xmlLoader = new XmlLoader();
    }

    /** Test for a non-existing file (path) */
    public function testNonExistingFile()
    {
        $this->assertFileNotExists($this->nonExistentXml);

        try {
            $this->xmlLoader->loadFile($this->nonExistentXml);

        } catch (\Exception $e) {
            $this->assertEquals(1549, $e->getCode());
            $this->assertInstanceOf(XmlLoaderException::class, $e);

            $this->assertContainsOnlyInstancesOf(
                'LibXMLError',
                $this->xmlLoader->getXmlPayload()->getXmlErrors()
            );
        }
    }

    /** Test good assets */
    public function testGoodFiles()
    {

        foreach ($this->goodXmlPaths as $file) {
            try {
                $this->xmlLoader->loadFile($file);

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
    public function testBadFiles()
    {
        foreach ($this->badXmlPaths as $file) {
            try {
                $this->xmlLoader->loadFile($file);

            } catch (\Exception $e) {
                $this->assertInstanceOf(XmlLoaderException::class, $e);

                $this->assertContainsOnlyInstancesOf(
                    \LibXMLError::class,
                    $this->xmlLoader->getXmlPayload()->getXmlErrors()
                );
            }
        }
    }

    /** Tests SimpleXml return class overwrite over good assets */
    public function testXmlClassOverloading()
    {
        foreach ($this->goodXmlPaths as $file) {
            try {
                $this->xmlLoader->loadString($file, XmlLoader::XML_ITERATOR_CLASS);

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
