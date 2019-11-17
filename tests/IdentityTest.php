<?php

namespace FPMatchSimple\Tests;

use PHPUnit\Framework\TestCase;
use FPMatchSimple\Core\Identity;
use FPMatchSimple\Exceptions\IdentityException;

class IdentityTest extends TestCase
{
    /**
     * @dataProvider personIdentitiesData
     */
    public function testIdentityInstanciation($id, $fps, $success)
    {
        if ($success) {
            $identity = new Identity($id, $fps);
            $this->assertSame(Identity::class, get_class($identity));
        } else {
            $this->expectException(IdentityException::class);
            $identity = new Identity($id, $fps);
        }
    }

    public function personIdentitiesData()
    {
        return [
            [1, ['azer', 'aert'], true],
            [0, ['azer', 'aert'], false],
            [15, [' ', ''], false],
            [21, [], false],
            [19, ['erer', ''], true],
            [8, ['erer', false], true],
        ];
    }
}