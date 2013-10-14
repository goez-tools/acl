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

}
