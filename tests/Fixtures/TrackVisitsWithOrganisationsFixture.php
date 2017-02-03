<?php
/**
 * Piwik PRO - Premium functionality and enterprise-level support for Piwik Analytics
 *
 * @link http://piwik.pro
 * @license http://www.gnu.org/licenses/gpl-3.0.html GPL v3 or later
 */
namespace Piwik\Plugins\Organisations\tests\Fixtures;

use Piwik\Date;
use Piwik\Plugins\Goals;
use Piwik\Plugins\Organisations\Model;
use Piwik\Tests\Framework\Fixture;
use Piwik\Plugin;
use Piwik\Tracker\Cache;

/**
 * Generates tracker testing data for our ApiTest
 *
 * This Simple fixture adds one website and tracks one visit with couple pageviews and an ecommerce conversion
 */
class TrackVisitsWithOrganisationsFixture extends Fixture
{
    public $dateTime = '2013-01-23 01:23:45';
    public $idSite = 1;

    public function setUp()
    {
        $this->setUpWebsites();
        $this->addGoals();
        $this->configureSomeOrganisations();
        $this->trackSomeVisits();
    }

    public function tearDown()
    {
        // empty
    }

    private function setUpWebsites()
    {
        if (!self::siteCreated($this->idSite)) {
            self::createWebsite($this->dateTime);
        }
    }

    private function addGoals()
    {
        Goals\API::getInstance()->addGoal($this->idSite, 'Has sub_en', 'url', 'sub_en', 'contains');
    }

    private function configureSomeOrganisations()
    {
        $model = new Model();
        $model->createOrganisation(array(
            'name' => 'Popular Organisation',
            'ipranges' => array(
                '192.168.24.*',
                '2001:db8:85a3:8d3:1319:8a2e:370:7344/120'
            )
        ));
        $model->createOrganisation(array(
            'name' => 'World Foundation',
            'ipranges' => array(
                '10.19.33.16/26',
                'fe80::200:f8ff:fe21:67cf'
            )
        ));

        Cache::deleteCacheWebsiteAttributes(1);
        Cache::clearCacheGeneral();
    }

    protected function trackSomeVisits()
    {
        $t = self::getTracker($this->idSite, $this->dateTime, $defaultInit = true);
        $t->setIp('192.168.24.19'); // Popular Organisation
        $t->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(0.1)->getDatetime());
        $t->setUrl('http://example.com/');
        self::checkResponse($t->doTrackPageView('Viewing homepage'));

        $t = self::getTracker($this->idSite, $this->dateTime, $defaultInit = true);
        $t->setIp('17.15.16.1'); // no matching organisation
        $t->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(0.4)->getDatetime());
        $t->setUrl('http://example.com/page1');
        self::checkResponse($t->doTrackPageView('Viewing a page'));

        $t = self::getTracker($this->idSite, $this->dateTime, $defaultInit = true);
        $t->setIp('2001:db8:85a3:8d3:1319:8a2e:370:7301'); // Popular Organisation
        $t->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(2.3)->getDatetime());
        $t->setUrl('http://example.com/');
        $t->doTrackGoal(1);
        self::checkResponse($t->doTrackPageView('Viewing homepage'));

        $t = self::getTracker($this->idSite, $this->dateTime, $defaultInit = true);
        $t->setIp('fe80::200:f8ff:fe21:67cf'); // World Foundation
        $t->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(4.5)->getDatetime());
        $t->setUrl('http://example.com/page2');
        self::checkResponse($t->doTrackPageView('Viewing another page'));

        $t = self::getTracker($this->idSite, $this->dateTime, $defaultInit = true);
        $t->setIp('fe80:0000:0000:0000:0202:b3ff:fe1e:8329'); // no matching organisation
        $t->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(9)->getDatetime());
        $t->setUrl('http://example.com/');
        self::checkResponse($t->doTrackPageView('Viewing homepage'));

        $t = self::getTracker($this->idSite, $this->dateTime, $defaultInit = true);
        $t->setIp('fe80:0000:0000:0000:0202:b3ff:fe1e:8329'); // no matching organisation
        $t->setCustomTrackingParameter('idorg', 1); // force idorg (Popular Organisation) as custom tracking param
        $t->setForceVisitDateTime(Date::factory($this->dateTime)->addHour(7)->getDatetime());
        $t->setUrl('http://example.com/');
        self::checkResponse($t->doTrackPageView('Viewing homepage'));
    }
}