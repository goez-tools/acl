<?php

namespace Goez\Acl;

class Role
{
    protected $_name = 'role';

    protected $_rules = array(
        'allowed' => array(),
        'denied' => array(),
    );

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

    /**
     * @param  string              $action
     * @param  mixed               $resource
     * @return \Goez\Acl\Role
     * @throws \Goez\Acl\Exception
     */
    public function allow($action, $resource)
    {
        $this->_addRule('allowed', $action, $resource);

        return $this;
    }

    /**
     * @param  string              $action
     * @param  mixed               $resource
     * @return \Goez\Acl\Role
     * @throws \Goez\Acl\Exception
     */
    public function deny($action, $resource)
    {
        $this->_addRule('denied', $action, $resource);

        return $this;
    }

    /**
     * @return \Goez\Acl\Role
     */
    public function fullPrivileges()
    {
        $this->_addRule('allowed', null, 'all');

        return $this;
    }

    /**
     * @param  string              $type
     * @param  string              $action
     * @param  mixed               $resource
     * @return \Goez\Acl\Role
     * @throws \Goez\Acl\Exception
     */
    protected function _addRule($type, $action, $resource)
    {
        $resource = $this->_getResourceName($resource);

        if (!is_string($resource)) {
            throw new Exception('Resource must be string or object.');
        }

        if (!isset($this->_rules[$type][$resource])) {
            $this->_rules[$type][$resource] = array();
        }

        $action = strtolower($action);
        $this->_rules[$type][$resource][] = $action;
    }

    /**
     * @param  string $action
     * @param  string $resource
     * @return bool
     */
    public function can($action, $resource)
    {
        if (isset($this->_rules['allowed']['all'])) {
            return true;
        }

        $action = strtolower($action);
        $resource = $this->_getResourceName($resource);

        foreach (array('denied', 'allowed') as $type) {
            $rules = $this->_rules[$type];
            if (!isset($rules[$resource])) {
                continue;
            }

            if (in_array($action, $rules[$resource])) {
                return ($type === 'allowed');
            }
        }

        return false;
    }

    /**
     * @param  mixed  $resource
     * @return string
     */
    protected function _getResourceName($resource)
    {
        return is_object($resource) ? get_class($resource) : $resource;
    }

}
