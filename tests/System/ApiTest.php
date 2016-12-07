<?php
/*
 *  Piwik - free/libre analytics platform

 *  Piwik is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU Lesser General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.

 *  Piwik is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 *  GNU Lesser General Public License for more details.

 *  @link http://piwik.pro
 *  @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 *
 */
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