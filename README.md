# SimpleXMlLoader
  A simple wrapper around SimpleXML's interface for loading XML resources or string data. 

  Main goals:
  - provide a transparent OOP interface to SimpleXML's `simplexml_load_file()` and `simplexml_load_string()` functions
  - handle SimpleXML's internal errors
  - follow LibXMl's functional API
  - throw catchable Exceptions (XmlLoaderException)

  This wrapper will:
   - try to load your XML resource into a \SimpleXMLElement object
   - handle any LibXML errors that occur during loading

  This wrapper will not: 
   - validate your XML
   - do any operation over the \SimpleXMLElement

# Basic usage
  There are two main functions in the SimpleXMlLoader:
  ```
  public function loadFile($filename, $class_name = "SimpleXMLElement", $options = 0, $ns = "", $is_prefix = false);
  
  public function loadString($data, $class_name = "SimpleXMLElement", $options = 0, $ns = "", $is_prefix = false);
  ```
  **NOTE:** Notice that these functions follow the signature of `simplexml_load_file()` and `simplexml_load_string()`. You should check the docs of [simplexml_load_file()](http://php.net/manual/en/function.simplexml-load-file.php) and [simplexml_load_string()](http://php.net/manual/en/function.simplexml-load-string.php) for details. And NO the author does not like the function parameters' names :).
 
  The loader throws XmlLoaderException on any error that occurs you should always wrap it in a try/catch block and handle any errors yourself: 
```
   use BorislavSabev\SimpleXmlLoader\XmlLoader;
   use BorislavSabev\SimpleXmlLoader\Exception\XmlLoaderException;

   $xmlLoader = new XmlLoader;
   try {
       /** @var \SimpleXMLElement $simpleXmlElement */
       $simpleXmlElement = $xmlLoader->loadFile($filename);
       
       //Now do something with the loaded \SimpleXMLElement...
   } catch (XmlLoaderException $e) {
       /** @var array $xmlErrors */
       $xmlErrors = $xmlLoader->getXmlPayload()
                              ->getXmlErrors();
   }
```

  The XmlLoader instance is meant to be reused thus:
  - Each call to `XmlLoader->loadFile()` or `XmlLoader->loadString()` will clear any LibXml errors stored
  - Each call to `XmlLoader->loadFile()` or `XmlLoader->loadString()`  will replace it's internal XmlPayload object

## Using it in a loop
  As each consecutive call to a loader method resets the state of LibXML and the payload you must extract all the data that you need between consecutive calls.

```
   use BorislavSabev\SimpleXmlLoader\XmlLoader;
   use BorislavSabev\SimpleXmlLoader\Exception\XmlLoaderException;

   $xmlLoader = new XmlLoader;
   
   foreach ($aBunchOfXmlStrings as $xmlString {
       try {
            /** @var \SimpleXMLElement $simpleXmlElement */
            $simpleXmlElement = $xmlLoader->loadString($xmlString);
       } catch (XmlLoaderException $e) {
           /** @var array $xmlErrors */
           $xmlErrors = $xmlLoader->getXmlPayload()
                                  ->getXmlErrors();
       }
       
       //Any data within LibXML or our XmlPayload will be lost after this iteration of the loop
   }
```

# Reusing XmlPayload
  You can also reuse the XmlPayload in your application if you which. Say for example you use this wrapper to parse different XML file but then want to pass the result to different Services in your code that will handle any business logic. You could just get the XmlPayload and pass it along to a specific service (or whatever really):
    
```
   use BorislavSabev\SimpleXmlLoader\XmlLoader;
   use BorislavSabev\SimpleXmlLoader\Exception\XmlLoaderException;

   $xmlLoader = new XmlLoader;
   try {
       $xmlLoader->loadFile($filename);
       /** @var XmlPayload $xmlPayload */
       $xmlPayload = $xmlLoader->getXmlPayload();

       //Generic example:
       $serviceBroker->pass(
           MyCoolService::class,
           $xmlPayload
       );

       //Now do something with the loaded \SimpleXMLElement...
   } catch (XmlLoaderException $e) {
       /** @var array $xmlErrors */
       $xmlErrors = $xmlLoader->getXmlPayload()
                              ->getXmlErrors();
   }
```
  **NOTE:** Generally you should have you own payload objects to pass data around in your Domain. The idea of XmlPayload is to be internal for XmlLoader thus it cannot contain any logic outside of that task.

# The XmlLoaderException and LibXML's libXMLError
  XmlLoaderException's codes are specific to this wrapper.
  LibXML's libXMLError objects are just returned in an array and as received from SimpleXML/LibXML. 

# Personal Opinion on the SimpleXML PHP Extension
  Generally SimpleXML is not a solid PHP extension and, in my mind, it should be used rarely when you need to do something simple fast. Any serious work should be done via DomDocument.   
  The previous two sentences are the author's personal opinion which as with any opinion should be taken with a grain of salt.

# Contributing
  Please do! PR's are very welcome. The author is far from thinking that this wrapper library is perfect.