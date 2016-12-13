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
namespace Piwik\Plugins\Organisations\tests\Integration;

use Piwik\Plugins\Organisations\Model;
use Piwik\Tests\Framework\TestCase\IntegrationTestCase;

/**
 * @group Plugins
 * @group Organisations
 */
class ModelTest extends IntegrationTestCase
{
    public function setUp()
    {
        parent::setUp();
        \Piwik\Plugin\Manager::getInstance()->loadPlugin('Organisations');
    }

    private $organisation1 = array(
        'name'     => 'test orgl',
        'ipranges' => array(
            '127.0.0.1',
            '192.168.2.0/24',
            '2001:5c0:1000:b::90f8/128'
        )
    );

    private function setupOrganisation()
    {
        $model = new Model();
        $model->deleteOrganisation(1);
        $model->createOrganisation($this->organisation1);
    }

    public function testCreateDeleteOrganisation()
    {
        $this->setupOrganisation();
        $model = new Model();
        $result = $model->getOrganisation(1);
        $this->assertArraySubset($this->organisation1, $result);
        $model->deleteOrganisation(1);
        $result = $model->getOrganisation(1);
        $this->assertEmpty($result);
    }

    public function testUpdateOrganisation()
    {
        $this->setupOrganisation();
        $model = new Model();
        $result = $model->getOrganisation(1);
        $this->assertArraySubset($this->organisation1, $result);
        $newOrgData = array(
            'name'     => 'updated org',
            'ipranges' => array(
                '145.5.3.34/8',
            )
        );
        $model->updateOrganisation(1, $newOrgData);
        $result = $model->getOrganisation(1);
        $this->assertArraySubset($newOrgData, $result);
    }

}