<?php


namespace BorislavSabev\SimpleXmlLoader\Exception;

/**
 * Class XmlLoaderException
 *
 * @project SimpleXmlLoader
 * @package BorislavSabev\SimpleXmlLoader
 * @author Borislav Sabev <sabev.borislav@gmail.com>
 */
class XmlLoaderException extends \RuntimeException
{
    const ERROR_CODE_FILE = 1;
    const ERROR_CODE_STRING = 2;
    const ERROR_CODE_XML_ERRORS = 3;
}
