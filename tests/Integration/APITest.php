<?php
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

    private $login = 'userLogin';

    public function setUp()
    {
        parent::setUp();

        $this->api = API::getInstance();
    }


    /**
     * @expectedException \Exception
     */
    public function test_addOrganisaion_throws_whenNoValidIPRangePassed()
    {
        $this->api->addOrganisation('no-valid-ip-range', [ '999.999.999.999' ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_addOrganisation_throws_whenIPRangesOverlap()
    {
        $this->api->addOrganisation('overlapping-ip-range', [
            '8.8.8.8/32',
            '10.10.10.0/24', // overlap
            '10.0.0.0/8',    // overlap
            '127.*.*.*'
        ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_addOrganisation_throws_whenIPRangesAreDuplicateTriggeringOverlap()
    {
        $this->api->addOrganisation('overlapping-ip-range', [ '10.10.10.42/32', '10.10.10.42/32' ]);
    }


    /**
     * @expectedException \Exception
     */
    public function test_updateOrganisaion_throws_whenNoValidIPRangePassed()
    {
        $this->api->updateOrganisation(1, 'no-valid-ip-range', [ '999.999.999.999' ]);
    }

    /**
     * @expectedException \Exception
     */
    public function test_updateOrganisation_throws_whenIPRangesOverlap()
    {
        $this->api->updateOrganisation(1, 'overlapping-ip-range', [
            '8.8.8.8/32',
            '10.10.10.0/24', // overlap
            '10.0.0.0/8',    // overlap
            '127.*.*.*'
        ]);
    }


    /**
     * @expectedException \Exception
     */
    public function test_updateOrganisation_throws_whenIPRangesAreDuplicateTriggeringOverlap()
    {
        $this->api->updateOrganisation(1, 'overlapping-ip-range', [ '10.10.10.42/32', '10.10.10.42/32' ]);
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
    }
}
