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

    public function testWildcardForAction()
    {
        $this->_acl->allow('author', '*', 'article');

        $this->assertTrue($this->_acl->can('author', 'read', 'article'));
        $this->assertTrue($this->_acl->can('author', 'write', 'article'));

        $this->assertFalse($this->_acl->can('author', 'read', 'news'));
        $this->assertFalse($this->_acl->can('author', 'write', 'news'));
    }

    public function testWildcardForResource()
    {
        $this->_acl->allow('author', 'read', '*');

        $this->assertTrue($this->_acl->can('author', 'read', 'article'));
        $this->assertTrue($this->_acl->can('author', 'read', 'news'));
        $this->assertFalse($this->_acl->can('author', 'write', 'article'));
        $this->assertFalse($this->_acl->can('author', 'write', 'news'));
    }

    public function testSubResource()
    {
        $this->_acl->allow('author', 'read', 'article');
        $this->_acl->deny('author', 'read', 'article:comment');

        $this->assertTrue($this->_acl->can('author', 'read', 'article:title'));
        $this->assertTrue($this->_acl->can('author', 'read', 'article:content'));

        $this->assertFalse($this->_acl->can('author', 'write', 'article:title'));
        $this->assertFalse($this->_acl->can('author', 'write', 'article:content'));
        $this->assertFalse($this->_acl->can('author', 'write', 'article:comment'));
        $this->assertFalse($this->_acl->can('author', 'read', 'article:comment'));
    }

    public function testWildcardForSubResource()
    {
        $this->_acl->allow('author', 'write', 'news:*');
        $this->_acl->deny('author', 'write', 'news:comment');

        $this->assertTrue($this->_acl->can('author', 'write', 'news:title'));
        $this->assertTrue($this->_acl->can('author', 'write', 'news:content'));

        $this->assertFalse($this->_acl->can('author', 'read', 'news:title'));
        $this->assertFalse($this->_acl->can('author', 'read', 'news:content'));
        $this->assertFalse($this->_acl->can('author', 'read', 'news:comment'));
        $this->assertFalse($this->_acl->can('author', 'write', 'news:comment'));
    }

    public function testMixedRules()
    {
        $this->_acl->allow('guest', 'read', 'article');
        $this->_acl->allow('guest', 'write', 'article:comment');
        $this->_acl->allow('author', '*', 'article');
        $this->_acl->allow('author', '*', 'news:*');

        $this->assertTrue($this->_acl->can('author', 'read', 'article:title'));
        $this->assertTrue($this->_acl->can('author', 'read', 'article:content'));
        $this->assertTrue($this->_acl->can('author', 'read', 'article:comment'));
        $this->assertTrue($this->_acl->can('author', 'write', 'article:title'));
        $this->assertTrue($this->_acl->can('author', 'write', 'article:content'));
        $this->assertTrue($this->_acl->can('author', 'write', 'article:comment'));

        $this->assertTrue($this->_acl->can('author', 'read', 'news:title'));
        $this->assertTrue($this->_acl->can('author', 'read', 'news:content'));
        $this->assertTrue($this->_acl->can('author', 'read', 'news:comment'));
        $this->assertTrue($this->_acl->can('author', 'write', 'news:title'));
        $this->assertTrue($this->_acl->can('author', 'write', 'news:content'));
        $this->assertTrue($this->_acl->can('author', 'write', 'news:comment'));

        $this->assertTrue($this->_acl->can('guest', 'read', 'article:title'));
        $this->assertTrue($this->_acl->can('guest', 'read', 'article:content'));
        $this->assertTrue($this->_acl->can('guest', 'read', 'article:comment'));
        $this->assertTrue($this->_acl->can('guest', 'write', 'article:comment'));
        $this->assertFalse($this->_acl->can('guest', 'write', 'article:title'));
        $this->assertFalse($this->_acl->can('guest', 'write', 'article:content'));
    }
}
