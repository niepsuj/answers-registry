<?php

namespace Niepsuj;

class AnswersRegistry implements \ArrayAccess, \IteratorAggregate, \Countable
{
    protected $answers  = array();

    protected $group = 'default';

    public function __construct($group = null)
    {
        if(null !== $group)
            $this->group($group);
    }

    public function group($group)
    {
        if(!is_string($group))
            throw new \Exception('String expected');

        $this->group = $group;
    }

    public function get($group = null, $sort = null, $limit = null)
    {
        $result = array();
        if(null === $group){
            $result = array_keys($this->answers);
        } else {
            foreach($this->answers as $value => $groupName){
                if($group === $groupName)
                    $result[] = $value;
            }
        }
        
        if(null !== $sort){
            if(!is_callable($sort))
                throw new \Exception('Callable expected');

            usort($result, $sort);
        }

        if(null !== $limit){
            return array_chunk($result, $limit);
        }

        return $result;
    }

    public function draw($group = null)
    {
        $result = $this->get($group);
        return $result[array_rand($result, 1)];
    }

    public function add($values, $group = null)
    {
        if(!is_array($values))
            $values = array($values);
        
        foreach($values as $value)
            $this->set($value, $group);

        return $this;
    }

    public function set($value, $group = null)
    {
        if(null === $group)
            $group = $this->group;

        $this->answers[$value] = $group;
        return $this;
    }

    public function remove($value)
    {
        unset($this->answers[$value]);
        return $this;
    }

    public function drop($group)
    {
        $this->answers = array_diff($this->answers, array($group));
        return $this;
    }

    public function has($value, $group = null)
    {
        if(null !== $group)
            return isset($this->answers[$value]) && $this->answers[$value] == $group;

        return isset($this->answers[$value]);
    }

    public function defined($group)
    {
        return in_array($group, $this->answers);
    }

    public function offsetGet($group)
    {
        return $this->get($group);
    }

    public function offsetSet($group, $value)
    {
        $this->add($value, $group);
    }

    public function offsetUnset($group)
    {
        $this->drop($group);
    }

    public function offsetExists($group)
    {
        return $this->defined($group);
    }

    public function getIterator()
    {
        return $this->answers;
    }

    public function count($group = null)
    {
        return count($this->answers);
    }

}