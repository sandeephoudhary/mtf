<?php
/**
 * {license_notice}
 *
 * @copyright   {copyright}
 * @license     {license_link}
 */

namespace Mtf\ObjectManager\ConfigLoader;

/**
 * Class Primary
 * MTF Primary DI configuration loader
 *
 * @package Mtf\ObjectManager\ConfigLoader
 * @internal
 */
class Primary
{
    /**
     * Framework mode
     *
     * @var string
     */
    protected $_appMode = 'developer';

    /**
     * Load MTF primary DI configuration
     *
     * @return array
     */
    public function load()
    {
        $reader = new \Mtf\ObjectManager\Config\Reader\Dom(
            new \Mtf\Config\FileResolver\Primary(),
            new \Magento\ObjectManager\Config\Mapper\Dom(
                $this->createArgumentInterpreter()
            ),
            new \Magento\ObjectManager\Config\SchemaLocator(),
            new \Mtf\Config\ValidationState($this->_appMode)
        );

        return $reader->read();
    }


    /**
     * Return newly created instance on an argument interpreter, suitable for processing DI arguments
     *
     * @return \Magento\Data\Argument\InterpreterInterface
     */
    protected function createArgumentInterpreter()
    {
        $booleanUtils =  new \Magento\Stdlib\BooleanUtils();
        $constInterpreter = new \Magento\Data\Argument\Interpreter\Constant();
        $result = new \Magento\Data\Argument\Interpreter\Composite(
            [
                'boolean' => new \Magento\Data\Argument\Interpreter\Boolean($booleanUtils),
                'string' => new \Magento\Data\Argument\Interpreter\String($booleanUtils),
                'number' => new \Magento\Data\Argument\Interpreter\Number(),
                'null' => new \Magento\Data\Argument\Interpreter\NullType(),
                'object' => new \Magento\Data\Argument\Interpreter\Object($booleanUtils),
                'const' => $constInterpreter,
                'init_parameter' => new \Magento\App\Arguments\ArgumentInterpreter($constInterpreter)
            ],
            \Magento\ObjectManager\Config\Reader\Dom::TYPE_ATTRIBUTE
        );
        // Add interpreters that reference the composite
        $result->addInterpreter('array', new \Magento\Data\Argument\Interpreter\ArrayType($result));
        return $result;
    }
}