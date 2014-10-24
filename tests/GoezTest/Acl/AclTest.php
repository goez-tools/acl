<?php

namespace GoezTest\Acl;

use Goez\Acl\Acl;
use Goez\Acl\Role;

class AclTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \Goez\Acl\Acl;
     */
    protected $_acl = null;

    public function setUp()
    {
        $this->_acl = new Acl();
    }

    public function testAddEmptyRole()
    {
        $this->setExpectedException('Exception', 'Name of role must be non-empty.');
        $this->_acl->addRole('');
    }

    public function testNotValidResource()
    {
        $this->setExpectedException('Exception', 'Resource must be string or object.');
        $role = new Role('guest');
        $role->allow('read', null);
    }

    public function testNoRules()
    {
        $this->assertFalse($this->_acl->can('guest', 'create', 'article'));
    }

    public function testNoRole()
    {
        $this->setExpectedException('Exception', 'Can\'t find role of \'guest\'');
        $this->_acl->getRole('guest');
    }

    public function testAddRole()
    {
        $this->_acl->addRole('admin');
        $this->assertTrue($this->_acl->hasRole('admin'));

        $roleAdmin = $this->_acl->getRole('admin');
        $this->assertInstanceOf('\Goez\Acl\Role', $roleAdmin);

    }

    public function testResourceIsObject()
    {
        $resource = (object) [ 'id' => 1 ];
        $this->_acl->allow('guest', 'read', $resource);
        $this->_acl->deny('guest', 'write', $resource);
        $this->assertTrue($this->_acl->can('guest', 'read', $resource));
    }

    public function testRuleForAllowOnly()
    {
        $this->_acl->allow('guest', 'read', 'article');
        $this->_acl->deny('guest', 'write', 'article');

        $this->assertTrue($this->_acl->can('guest', 'read', 'article'));
        $this->assertFalse($this->_acl->can('guest', 'write', 'article'));
    }

    public function testRuleForDenyOnly()
    {
        $this->_acl->deny('guest', 'write', 'article');
        $this->assertFalse($this->_acl->can('guest', 'write', 'article'));
    }

    public function testRuleForReadAndRewrite()
    {
        $this->_acl->allow('author', 'read', 'article');
        $this->_acl->allow('author', 'write', 'article');
        $this->_acl->deny('author', 'create', 'page');
        $this->_acl->deny('author', 'create', 'site');

        $this->assertFalse($this->_acl->can('author', 'create', 'page'));
        $this->assertFalse($this->_acl->can('author', 'create', 'site'));

        $this->assertTrue($this->_acl->can('author', 'read', 'article'));
        $this->assertTrue($this->_acl->can('author', 'write', 'article'));
    }

    public function testRuleOverride()
    {
        $this->_acl->allow('author', 'read', 'article');
        $this->_acl->allow('author', 'write', 'article');
        $this->_acl->deny('author', 'read', 'article');
        $this->_acl->deny('author', 'write', 'article');

        $this->assertFalse($this->_acl->can('author', 'read', 'article'));
        $this->assertFalse($this->_acl->can('author', 'write', 'article'));

    }

    public function testRuleForAdmin()
    {
        $this->_acl->fullPrivileges('admin');

        $this->assertTrue($this->_acl->can('admin', 'create', 'page'));
        $this->assertTrue($this->_acl->can('admin', 'create', 'site'));

        $this->assertTrue($this->_acl->can('admin', 'read', 'article'));
        $this->assertTrue($this->_acl->can('admin', 'write', 'article'));

    }

}
