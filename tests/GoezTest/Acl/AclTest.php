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

        $admin = $this->_acl->getRole('admin');
        $this->assertInstanceOf('\Goez\Acl\Role', $admin);

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
        $author = $this->_acl->addRole('author')->getRole('author');

        $author->allow('read', 'article');
        $author->allow('write', 'article');
        $author->deny('create', 'page');
        $author->deny('create', 'site');

        $this->assertFalse($author->can('create', 'page'));
        $this->assertFalse($author->can('create', 'site'));

        $this->assertTrue($author->can('read', 'article'));
        $this->assertTrue($author->can('write', 'article'));

    }

    public function testRuleOverride()
    {
        $author = $this->_acl->addRole('author')->getRole('author');

        $author->allow('read', 'article');
        $author->allow('write', 'article');
        $author->deny('read', 'article');
        $author->deny('write', 'article');

        $this->assertFalse($author->can('read', 'article'));
        $this->assertFalse($author->can('write', 'article'));

    }

    public function testRuleForAdmin()
    {
        $admin = $this->_acl->addRole('admin')->getRole('admin');
        $admin->fullPrivileges();

        $this->assertTrue($admin->can('create', 'page'));
        $this->assertTrue($admin->can('create', 'site'));

        $this->assertTrue($admin->can('read', 'article'));
        $this->assertTrue($admin->can('write', 'article'));

    }

}
