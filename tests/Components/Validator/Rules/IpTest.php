<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class IpTest extends Rule
{
    public function testIp()
    {
        $this->object->setInputs([
            'ip'              => '127.0.0.1',
            'not_ip'          => 'no ip',
            'ip_required'     => '127.0.0.1',
            'ip_not_required' => '',
            /* IPv4 */
            'ip_v4'           => '127.0.0.1',
            'not_ip_v4'       => '2001:db8::85a3::ac1f:8001',
            /* IPv6 */
            'ip_v6'           => '2001:db8:0:85a3::ac1f:8001',
            'not_ip_v6'       => '2001:db8::85a3::ac1f:8001',
        ])->setRules([
            'ip'              => 'ip',
            'not_ip'          => '!ip',
            'ip_required'     => 'required|ip',
            'ip_not_required' => '!required|ip',
            /* IPv4 */
            'ip_v4'           => 'ip:4',
            'not_ip_v4'       => '!ip:4',
            /* IPv6 */
            'ip_v6'           => 'ip:6',
            'not_ip_v6'       => '!ip:6'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => 'no ip',
            'not_must' => '127.0.0.1'
        ])->setRules([
            'must'     => 'ip',
            'not_must' => '!ip'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }

    /**
     * @expectedException \Exception
     */
    public function testIpException()
    {
        $this->object
            ->addInput('field', '127.0.0.1')
            ->addRule('field', 'ip:7')
            ->isValid();
    }
}
