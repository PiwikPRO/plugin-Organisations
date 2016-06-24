<?php
namespace Piwik\Plugins\Organisations\tests\System;

use Piwik\Plugins\Organisations\tests\Fixtures\TrackVisitsWithOrganisationsFixture;
use Piwik\Tests\Framework\TestCase\SystemTestCase;

/**
 * @group Organisations
 * @group ApiTest
 * @group Plugins
 */
class ApiTest extends SystemTestCase
{
    /**
     * @var TrackVisitsWithOrganisationsFixture
     */
    public static $fixture = null; // initialized below class definition

    /**
     * @dataProvider getApiForTesting
     */
    public function testApi($api, $params)
    {
        $this->runApiTests($api, $params);
    }

    public function getApiForTesting()
    {
        $apiToTest = array();

        $apiToTest[] = array(
            array('Organisations.getOrganisation'),
            array(
                'idSite'  => 1,
                'date'    => self::$fixture->dateTime,
                'periods' => array('day'),
            )
        );


        $apiToTest[] = array(
            array('API.getReportMetadata'),
            array(
                'idSite'  => 1,
                'date'    => self::$fixture->dateTime,
                'periods' => array('day')
            )
        );

        $apiToTest[] = array(
            array('API.getSegmentsMetadata'),
            array(
                'idSite'  => 1,
                'date'    => self::$fixture->dateTime,
                'periods' => array('year'),
            )
        );

        $apiToTest[] = array(
            array('API.getProcessedReport'),
            array(
                'idSite'                 => 1,
                'date'                   => self::$fixture->dateTime,
                'periods'                => array('year'),
                'otherRequestParameters' => array(
                    'apiModule' => 'Organisations',
                    'apiAction' => 'getOrganisation'
                )
            )
        );

        $apiToTest[] = array(
            array('Live.getLastVisitsDetails'),
            array(
                'idSite'  => 1,
                'date'    => self::$fixture->dateTime,
                'periods' => array('year'),
            )
        );

        return $apiToTest;
    }

    public static function getOutputPrefix()
    {
        return '';
    }

    public static function getPathToTestDirectory()
    {
        return dirname(__FILE__);
    }

}

ApiTest::$fixture = new TrackVisitsWithOrganisationsFixture();