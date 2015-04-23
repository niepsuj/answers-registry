<?php

namespace Niepsuj;

class AnswersRegistryTest extends \PHPUnit_Framework_TestCase
{

    static $values = array('value a', 'value b', 'value c', 'value d');

    public function testSetting()
    {
        $reg = new AnswersRegistry();
        $reg->set('value a', 'test1');
        $reg->set('value b', 'rest2');

        $this->assertEquals(2, count($reg));
        $this->assertEquals(array('value a', 'value b'), $reg->get());
        $this->assertEquals(array('value a'), $reg->get('test1'));
    }

    public function testAdding()
    {
        $reg = new AnswersRegistry();
        $reg->add(self::$values, 'primary');
        $reg->set('value b', 'other');
        $reg->set('value e', 'primary');

        $this->assertEquals(5, count($reg));
        $this->assertEquals(4, count($reg->get('primary')));
        $this->assertEquals(1, count($reg->get('other')));
    }

    public function testGetting()
    {
        $reg = new AnswersRegistry();
        $reg->add(self::$values, 'group');

        $this->assertEquals(self::$values, $reg->get('group'));
    }

    public function testDrawing()
    {
        $reg = new AnswersRegistry();
        $reg->add(self::$values, 'group');
        $reg->add('value e');
        $reg->add('value f');

        $this->assertTrue(in_array($reg->draw('group'), self::$values));
    }

    public function testDefaultGroup()
    {
        $reg = new AnswersRegistry();
        $reg->add(self::$values);

        $this->assertEquals(4, count($reg));
        $this->assertEquals(4, count($reg->get()));
        $this->assertEquals(4, count($reg->get('default')));
    }

    public function testInitDefaultGroup()
    {
        $reg = new AnswersRegistry('other');
        $reg->add(self::$values);

        $this->assertEquals(4, count($reg));
        $this->assertEquals(4, $reg->count());
        $this->assertEquals(4, $reg->count('other'));
    }

    public function testChangeDefaultGroup()
    {
        $reg = new AnswersRegistry();
        $reg->group('other');
        $reg->add(self::$values);

        $this->assertEquals(4, count($reg));
        $this->assertEquals(4, $reg->count());
        $this->assertEquals(4, $reg->count('other'));
    }

    public function testArrayAccess()
    {
        $reg = new AnswersRegistry();
        $reg['primary']     = self::$values;
        $reg['other']       = 'value b';
        $reg['primary']     = 'value e';

        $this->assertEquals(5, count($reg));
        $this->assertEquals(4, count($reg['primary']));
        $this->assertEquals(1, count($reg['other']));
        $this->assertEquals('value b', $reg->draw('other'));
    }

    public function testTesting()
    {
        $reg = new AnswersRegistry();
        $reg[] = self::$values;
        $reg[] = 'value e';
        $reg['other'] = 'value f';

        $this->assertFalse(isset($reg['value f']), '1');
        $this->assertTrue(isset($reg['other']), '2');
        $this->assertFalse($reg->has('other'), '3');
        $this->assertTrue($reg->has('value f'), '4');
        $this->assertFalse($reg->has('value f', 'default'), '5');
        $this->assertTrue($reg->has('value f', 'other'), '6');
        $this->assertFalse($reg->defined('value f'), '7');
        $this->assertTrue($reg->defined('other'), '8');
    }

    public function testRemoving()
    {
        $reg = new AnswersRegistry();
        $reg[] = self::$values;
        $reg['other'] = 'value e';

        $reg->remove('value a');
        $reg->drop('other');

        $this->assertEquals(3, count($reg['default']), '1');
        $this->assertTrue(isset($reg['default']), '2');
        $this->assertFalse(isset($reg['other']), '3');
    }
}