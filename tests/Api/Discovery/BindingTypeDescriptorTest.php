<?php

/*
 * This file is part of the puli/repository-manager package.
 *
 * (c) Bernhard Schussek <bschussek@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Puli\RepositoryManager\Tests\Api\Discovery;

use PHPUnit_Framework_TestCase;
use Puli\RepositoryManager\Api\Discovery\BindingParameterDescriptor;
use Puli\RepositoryManager\Api\Discovery\BindingTypeDescriptor;

/**
 * @since  1.0
 * @author Bernhard Schussek <bschussek@gmail.com>
 */
class BindingTypeDescriptorTest extends PHPUnit_Framework_TestCase
{
    public function testCreate()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', 'The description.', array(
            $param = new BindingParameterDescriptor('param'),
        ));

        $this->assertSame('vendor/type', $descriptor->getName());
        $this->assertSame('The description.', $descriptor->getDescription());
        $this->assertSame(array('param' => $param), $descriptor->getParameters());
        $this->assertSame($param, $descriptor->getParameter('param'));
        $this->assertTrue($descriptor->hasParameter('param'));
        $this->assertFalse($descriptor->hasParameter('foo'));
    }

    public function testCreateWithDefaultValues()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type');

        $this->assertSame('vendor/type', $descriptor->getName());
        $this->assertNull($descriptor->getDescription());
        $this->assertSame(array(), $descriptor->getParameters());
    }

    public function getValidNames()
    {
        return array(
            array('my/type'),
            array('my/type-name'),
            array('my/type-name'),
            array('my123/type-name-123')
        );
    }

    /**
     * @dataProvider getValidNames
     */
    public function testValidName($name)
    {
        $descriptor = new BindingTypeDescriptor($name);

        $this->assertSame($name, $descriptor->getName());
    }

    public function getInvalidNames()
    {
        return array(
            array(1234),
            array(''),
            array('no-vendor'),
            array('my/Type'),
            array('my/type_name'),
            array('123my/digits-first'),
        );
    }

    /**
     * @dataProvider getInvalidNames
     * @expectedException \InvalidArgumentException
     */
    public function testFailIfInvalidName($name)
    {
        new BindingTypeDescriptor($name);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDescriptionMustBeStringOrNull()
    {
        new BindingTypeDescriptor('vendor/type', 1234);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testDescriptionMustNotBeEmpty()
    {
        new BindingTypeDescriptor('vendor/type', '');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testParametersMustBeValidInstances()
    {
        new BindingTypeDescriptor('vendor/type', null, array(new \stdClass()));
    }

    /**
     * @expectedException \Puli\Discovery\Api\Binding\NoSuchParameterException
     */
    public function testGetParameterFailsIfUnknownParameter()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type');

        $descriptor->getParameter('foobar');
    }

    public function testGetParameterValues()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', false, 'value'),
        ));

        $this->assertSame(array('param' => 'value'), $descriptor->getParameterValues());
    }

    public function testGetParameterValuesIgnoresRequiredParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', true),
        ));

        $this->assertSame(array(), $descriptor->getParameterValues());
    }

    public function testGetParameterValue()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', false, 'value'),
        ));

        $this->assertSame('value', $descriptor->getParameterValue('param'));
    }

    public function testGetParameterValueReturnsNullForRequiredParameter()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', true),
        ));

        $this->assertNull($descriptor->getParameterValue('param'));
    }

    /**
     * @expectedException \Puli\Discovery\Api\Binding\NoSuchParameterException
     */
    public function testGetParameterValueFailsIfUnknownParameter()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type');

        $descriptor->getParameterValue('foobar');
    }

    public function testHasParameterValues()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', false, 'value'),
        ));

        $this->assertTrue($descriptor->hasParameterValues());
    }

    public function testHasParameterValuesIgnoresRequiredParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', true),
        ));

        $this->assertFalse($descriptor->hasParameterValues());
    }

    public function testHasParameterValue()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', false, 'value'),
        ));

        $this->assertTrue($descriptor->hasParameterValue('param'));
        $this->assertFalse($descriptor->hasParameterValue('foo'));
    }

    public function testHasParameterValueIgnoresRequiredParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', true),
        ));

        $this->assertFalse($descriptor->hasParameterValue('param'));
    }

    public function testHasRequiredParametersReturnsTrueIfRequiredParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', true),
        ));

        $this->assertTrue($descriptor->hasRequiredParameters());
    }

    public function testHasRequiredParametersReturnsFalseIfNoRequiredParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', false),
        ));

        $this->assertFalse($descriptor->hasRequiredParameters());
    }

    public function testHasRequiredParametersReturnsFalseIfNoParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type');

        $this->assertFalse($descriptor->hasRequiredParameters());
    }

    public function testHasOptionalParametersReturnsTrueIfOptionalParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', false),
        ));

        $this->assertTrue($descriptor->hasOptionalParameters());
    }

    public function testHasOptionalParametersReturnsFalseIfNoOptionalParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type', null, array(
            new BindingParameterDescriptor('param', true),
        ));

        $this->assertFalse($descriptor->hasOptionalParameters());
    }

    public function testHasOptionalParametersReturnsFalseIfNoParameters()
    {
        $descriptor = new BindingTypeDescriptor('vendor/type');

        $this->assertFalse($descriptor->hasOptionalParameters());
    }

    /**
     * @dataProvider getValidNames
     */
    public function testToBindingType($name)
    {
        // Check that valid names are also accepted by BindingType
        $descriptor = new BindingTypeDescriptor($name, 'The description.', array(
            $param = new BindingParameterDescriptor('param'),
        ));

        $type = $descriptor->toBindingType();

        $this->assertInstanceOf('Puli\Discovery\Api\Binding\BindingType', $type);
        $this->assertSame($name, $type->getName());
        $this->assertCount(1, $type->getParameters());
        $this->assertInstanceOf('Puli\Discovery\Api\Binding\BindingParameter', $type->getParameter('param'));
    }
}