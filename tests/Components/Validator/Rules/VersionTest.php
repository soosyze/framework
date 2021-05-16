<?php

namespace Soosyze\Tests\Components\Validator\Rules;

class VersionTest extends Rule
{
    public function testVersion(): void
    {
        $this->object->setInputs([
            'must_1'  => '1.2.3',
            'must_2'  => '10.20.30',
            'must_3'  => '1.1.2-prerelease+meta',
            'must_4'  => '1.1.2+meta',
            'must_5'  => '1.1.2+meta-valid',
            'must_6'  => '1.0.0-alpha',
            'must_7'  => '1.0.0-beta',
            'must_8'  => '1.0.0-alpha.beta',
            'must_9'  => '1.0.0-alpha.beta.1',
            'must_10' => '1.0.0-alpha.1',
            'must_11' => '1.0.0-alpha0.valid',
            'must_12' => '1.0.0-alpha.0valid',
            'must_13' => '1.0.0-alpha-a.b-c-somethinglong+build.1-aef.1-its-okay',
            'must_14' => '1.0.0-rc.1+build.1',
            'must_15' => '2.0.0-rc.1+build.123',
            'must_16' => '1.2.3-beta',
            'must_17' => '10.2.3-DEV-SNAPSHOT',
            'must_18' => '1.2.3-SNAPSHOT-123',
            'must_19' => '1.0.0',
            'must_20' => '2.0.0',
            'must_21' => '1.1.7',
            'must_22' => '2.0.0+build.1848',
            'must_23' => '2.0.1-alpha.1227',
            'must_24' => '1.0.0-alpha+beta',
            'must_25' => '1.2.3----RC-SNAPSHOT.12.9.1--.12+788',
            'must_26' => '1.2.3----R-S.12.9.1--.12+meta',
            'must_27' => '1.2.3----RC-SNAPSHOT.12.9.1--.12',
            'must_28' => '1.0.0+0.build.1-rc.10000aaa-kk-0.1',
            'must_29' => '99999999999999999999999.999999999999999999.99999999999999999',
            'must_30' => '1.0.0-0A.is.legal'
        ])->setRules([
            'must_1'  => 'version',
            'must_2'  => 'version',
            'must_3'  => 'version',
            'must_4'  => 'version',
            'must_5'  => 'version',
            'must_6'  => 'version',
            'must_7'  => 'version',
            'must_8'  => 'version',
            'must_9'  => 'version',
            'must_10' => 'version',
            'must_11' => 'version',
            'must_12' => 'version',
            'must_13' => 'version',
            'must_14' => 'version',
            'must_15' => 'version',
            'must_16' => 'version',
            'must_17' => 'version',
            'must_18' => 'version',
            'must_19' => 'version',
            'must_20' => 'version',
            'must_21' => 'version',
            'must_22' => 'version',
            'must_23' => 'version',
            'must_24' => 'version',
            'must_25' => 'version',
            'must_26' => 'version',
            'must_27' => 'version',
            'must_28' => 'version',
            'must_29' => 'version',
            'must_30' => 'version'
        ]);

        $this->assertTrue($this->object->isValid());

        $this->object->setInputs([
            'must'     => '1.0',
            'not_must' => '1.0.0'
        ])->setRules([
            'must'     => 'version',
            'not_must' => '!version'
        ]);

        $this->assertFalse($this->object->isValid());
        $this->assertCount(2, $this->object->getErrors());
    }
}
