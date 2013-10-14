<?php

namespace Goez\Acl;

class Role
{
    protected $_name = 'role';

    protected $_permissions = array();

    /**
     * @param $name
     * @throws \Goez\Acl\Exception
     */
    public function __construct($name)
    {
        if (empty($name)) {
            throw new Exception('Name of role must be non-empty.');
        }

        $this->_name = $name;
    }

    public function allow($action, $resource)
    {
        $resource = is_object($resource) ? get_class($resource) : $resource;

        if (!is_string($resource)) {
            throw new Exception('Resource must be string or object.');
        }

        if (!isset($this->_permissions[$resource])) {
            $this->_permissions[$resource] = array();
        }

        $action = strtolower($action);
        $this->_permissions[$resource][] = $action;

        return $this;
    }

    public function deny($action, $resource)
    {


        return $this;
    }

    public function can($action, $resource, $resourceValue = null)
    {
        return true;
    }

}
