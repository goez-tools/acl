<?php

namespace Goez\Acl;

class Acl
{
    protected $_roles = array();

    /**
     * @param string $name
     * @return \Goez\Acl\Acl
     */
    public function addRole($name)
    {
        $name = strtolower($name);

        if (!$this->hasRole($name)) {
            $this->_roles[$name] = new Role($name);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasRole($name)
    {
        return in_array(strtolower($name), array_keys($this->_roles));
    }

    /**
     * @param string $name
     * @return \Goez\Acl\Role
     * @throws \Goez\Acl\Exception
     */
    public function getRole($name)
    {
        $name = strtolower($name);
        if ($this->hasRole($name)) {
            return $this->_roles[$name];
        }

        throw new Exception("Can't find role of '$name'.");
    }


}
