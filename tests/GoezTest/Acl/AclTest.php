<?php

namespace GoezTest\Acl;

use Goez\Acl\Acl;

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

    public function testAddRole()
    {
        $this->_acl->addRole('admin');
        $this->assertTrue($this->_acl->hasRole('admin'));

        $role = $this->_acl->getRole('admin');
        $this->assertInstanceOf('\Goez\Acl\Role', $role);

    }

    public function testRuleForReadOnly()
    {
        $this->_acl->allow('guest', 'read', 'article');
        $this->_acl->deny('guest', 'write', 'article');

        $this->assertTrue($this->_acl->can('guest', 'read', 'article'));
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
