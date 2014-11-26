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
        $this->_addRule('allowed', null, '*');

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
        list($main, $sub) = $this->_getResourceName($resource);

        if (!isset($this->_rules[$type][$main])) {
            $this->_rules[$type][$main] = array();
        }

        if (!isset($this->_rules[$type][$main][$sub])) {
            $this->_rules[$type][$main][$sub] = array();
        }

        $action = strtolower($action);
        $this->_rules[$type][$main][$sub][] = $action;
    }

    /**
     * @param  string $action
     * @param  string $resource
     * @return bool
     */
    public function can($action, $resource)
    {
        list($main, $sub) = $this->_getResourceName($resource);
        $action = strtolower($action);
        $allowedRules = $this->_rules['allowed'];

        if (isset($allowedRules['*']['*'])) {
            $actions = $this->_rules['allowed']['*']['*'];
            if ("" === $actions[0] || in_array($action, $actions)) {
                return true;
            }
        }

        foreach (array('denied', 'allowed') as $type) {
            $rules = $this->_rules[$type];

            // if there is no matched action
            if (!isset($rules[$main][$sub]) && !isset($rules[$main]['*'])) {
                continue;
            }

            // Check action
            $actions = isset($rules[$main][$sub]) ? $rules[$main][$sub] : $rules[$main]['*'];
            if ($actions[0] === '*' || in_array($action, $actions)) {
                return ($type === 'allowed');
            }
        }

        return false;
    }

    /**
     * @param  mixed  $resource
     * @return array
     * @throws Exception
     */
    protected function _getResourceName($resource)
    {
        if (is_object($resource)) {
            $resource = get_class($resource);
        }

        if (!is_string($resource)) {
            throw new Exception('Resource must be string or object.');
        }

        $resource = explode(':', $resource);
        if (!array_key_exists(1, $resource)) {
            $resource[1] = '*';
        }

        return $resource;
    }

}
