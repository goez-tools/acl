<?php

namespace Goez\Acl;

class Acl
{
    const ROLE_ADMINISTRATOR = 1;

    protected $_roles = array();

    /**
     * @param  string        $name
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
     * @param  string $name
     * @return bool
     */
    public function hasRole($name)
    {
        return in_array(strtolower($name), array_keys($this->_roles));
    }

    /**
     * @param  string              $name
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

    /**
     * @param  mixed         $roleIdentifier
     * @param  mixed         $action
     * @param  mixed         $resource
     * @return \Goez\Acl\Acl
     */
    public function allow($roleIdentifier, $action, $resource)
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        $actions = (array) $action;

        foreach ($actions as $act) {
            $this->getRole($roleIdentifier)->allow($act, $resource);
        }

        return $this;
    }

    /**
     * @param  mixed         $roleIdentifier
     * @param  mixed         $action
     * @param  mixed         $resource
     * @return \Goez\Acl\Acl
     */
    public function deny($roleIdentifier, $action, $resource)
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        $this->getRole($roleIdentifier)->deny($action, $resource);

        return $this;
    }

    /**
     * @param  mixed         $roleIdentifier
     * @return \Goez\Acl\Acl
     */
    public function fullPrivileges($roleIdentifier)
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        $this->getRole($roleIdentifier)->fullPrivileges();

        return $this;
    }

    /**
     * @param  mixed $roleIdentifier
     * @param  mixed $action
     * @param  mixed $resource
     * @return bool
     */
    public function can($roleIdentifier, $action, $resource)
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        return $this->getRole($roleIdentifier)->can($action, $resource);
    }

}
