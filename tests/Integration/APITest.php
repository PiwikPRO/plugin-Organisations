<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Organisations\tests\Integration;

use Piwik\Plugins\Organisations\API;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group UsersManager
 * @group APITest
 * @group Plugins
 */
class APITest extends IntegrationTestCase
{
    /**
     * @var API
     */
    private $api;

    public function setUp()
    {
        parent::setUp();

        $this->api = API::getInstance();
    }

    public function getInvalidIpRangeDataSet()
    {
        return [
            ['999.999.999.999'],
            ['1.c.3.4/10'],
            ['20:0:2d0:2df:f123:0'],
            ['20:0:2d0:2zf:0:0:f123:0/4'],
        ];
    }

    /**
     * @dataProvider getInvalidIpRangeDataSet
     * @expectedException \Exception
     */
    public function test_addOrganisaion_throws_whenNoValidIPRangePassed($ip)
    {
        $this->api->addOrganisation('no-valid-ip-range', [ $ip ]);
    }

    public function getOverlappingIpRanges()
    {
        return [
            [[
                // overlapping IPv4
                '8.8.8.8/32',
                '10.10.10.0/24', // overlap
                '10.0.0.0/8',    // overlap
                '127.*.*.*'
            ]],
            [[
                // overlapping IPv6
                '2620:0:2d0:2df::7/121',
                '20:0:2d0:2df::0/96',        // overlap
                '20:0:2d0:2df::f123:0/118',  // overlap
            ]],
            [[
                // duplicate ip ranges should trigger overlap, aswell
                '10.10.10.42/32',
                '10.10.10.42/32'
            ]]
        ];
    }

    /**
     * @dataProvider getOverlappingIpRanges
     * @expectedException \Exception
     */
    public function test_addOrganisation_throws_whenIPRangesOverlap($ipRanges)
    {
        $this->api->addOrganisation('overlapping-ip-range', $ipRanges);
    }

    /**
     * @expectedException \Exception
     */
    public function test_updateOrganisaion_throws_whenNoValidIPRangePassed()
    {
        $this->api->updateOrganisation(1, 'no-valid-ip-range', [ '999.999.999.999' ]);
    }

    /**
     * @dataProvider getOverlappingIpRanges
     * @expectedException \Exception
     */
    public function test_updateOrganisation_throws_whenIPRangesOverlap($ipRanges)
    {
        $this->api->updateOrganisation(1, 'overlapping-ip-range', $ipRanges);
    }


    /**
     * @expectedException \Exception
     */
    public function test_exception_thrown_whenIPRangesOverlapAcrossOrganisations_inside()
    {
        $this->api->addOrganisation('existing-ip-range', [
            '20.0.0.0/8'
        ]);

        $this->api->addOrganisation('overlapping-ip-range', [
            '20.10.0.0/16'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_exception_thrown_whenIPRangesOverlapAcrossOrganisations_outside()
    {
        $this->api->addOrganisation('existing-ip-range', [
            '30.30.0.0/16'
        ]);

        $this->api->addOrganisation('overlapping-ip-range', [
            '30.0.0.0/8'
        ]);
    }

    public function test_updateOrganisation_doesNotTriggerOverlapForItself()
    {
        $name  = 'update-overlapping';
        $idOrg = $this->api->addOrganisation($name, [ '8.8.8.0/24' ]);

        $this->api->updateOrganisation($idOrg, $name, [ '8.8.8.8/32' ]);
    }

    public function test_something()
    {
        $orgId1 = $this->api->addOrganisation('first org', [
            '139.191.16.0/24'
        ]);

        $orgId2 = $this->api->addOrganisation('secod org', [
            '139.191.8.0/24'
        ]);

        $this->api->updateOrganisation($orgId1, 'updated org', [
            '139.191.16.0/24',
            '86.169.0.0/16'
        ]);

        $this->api->updateOrganisation($orgId2, 'updated org 2', [
            '139.191.8.0/24',
            '186.169.0.0/16'
        ]);
    }
}
