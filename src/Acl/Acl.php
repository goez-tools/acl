<?php

namespace Goez\Acl;

class Acl
{
    const ROLE_ADMINISTRATOR = 1;

    protected $_roles = array();

    /**
     * @param string $name
     * @return Acl
     * @throws Exception
     */
    public function addRole($name): Acl
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
    public function hasRole($name): bool
    {
        return in_array(strtolower($name), array_keys($this->_roles));
    }

    /**
     * @param  string              $name
     * @return Role
     * @throws Exception
     */
    public function getRole($name): Role
    {
        $name = strtolower($name);
        if ($this->hasRole($name)) {
            return $this->_roles[$name];
        }

        throw new Exception("Can't find role of '$name'.");
    }

    /**
     * @param mixed $roleIdentifier
     * @param mixed $action
     * @param mixed $resource
     * @return Acl
     * @throws Exception
     */
    public function allow($roleIdentifier, $action, $resource): Acl
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        $role = $this->getRole($roleIdentifier);
        $actions = is_array($action) ? $action : [ $action ];
        $resources = is_array($resource) ? $resource : [ $resource ];

        foreach ($actions as $act) {
            foreach ($resources as $res) {
                $role->allow($act, $res);
            }
        }

        return $this;
    }

    /**
     * @param mixed $roleIdentifier
     * @param mixed $action
     * @param mixed $resource
     * @return Acl
     * @throws Exception
     */
    public function deny($roleIdentifier, $action, $resource): Acl
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        $this->getRole($roleIdentifier)->deny($action, $resource);

        return $this;
    }

    /**
     * @param mixed $roleIdentifier
     * @return Acl
     * @throws Exception
     */
    public function fullPrivileges($roleIdentifier): Acl
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        $this->getRole($roleIdentifier)->fullPrivileges();

        return $this;
    }

    /**
     * @param mixed $roleIdentifier
     * @param mixed $action
     * @param mixed $resource
     * @return bool
     * @throws Exception
     */
    public function can($roleIdentifier, $action, $resource)
    {
        if (!$this->hasRole($roleIdentifier)) {
            $this->addRole($roleIdentifier);
        }

        return $this->getRole($roleIdentifier)->can($action, $resource);
    }

}
