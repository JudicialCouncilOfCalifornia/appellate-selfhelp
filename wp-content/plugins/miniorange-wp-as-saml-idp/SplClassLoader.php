<?php

namespace IDP;

final class SplClassLoader
{
    /** @var string $_fileExtension */
    private $_fileExtension = '.php';
    /** @var string $_namespace */
    private $_namespace;
    /** @var string $_includePath  */
    private $_includePath;
    /** @var string $_namespaceSeparator */
    private $_namespaceSeparator = '\\';

    public function __construct($ns = null, $includePath = null)
    {
        $this->_namespace = $ns;
        $this->_includePath = $includePath;
    }

    /** @throws \Exception */
    public function register()
    {
        spl_autoload_register(array($this, 'loadClass'));
    }

    public function unregister()
    {
        spl_autoload_unregister(array($this, 'loadClass'));
    }

    public function loadClass($className)
    {
        if (null === $this->_namespace
            || $this->_namespace . $this->_namespaceSeparator === substr($className, 0, strlen($this->_namespace . $this->_namespaceSeparator))) {
            $fileName = '';
            $namespace = '';
            if (false !== ($lastNsPos = strripos($className, $this->_namespaceSeparator))) {
                $namespace = strtolower(substr($className, 0, $lastNsPos));
                $className = substr($className, $lastNsPos + 1);
                $fileName = str_replace($this->_namespaceSeparator, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
            }
            $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . $this->_fileExtension;
            $fileName = str_replace("idp",MSI_NAME,$fileName); //repalce the idp namespace with plugin folder name
            require ($this->_includePath !== null ? $this->_includePath . DIRECTORY_SEPARATOR : '') . $fileName;
        }
    }
}