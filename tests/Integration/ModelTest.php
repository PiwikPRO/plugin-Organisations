<?php
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

    public function testCreateOrganisation()
    {
        $org = array(
            'name' => 'test orgl',
            'ipranges' => array(
                '127.0.0.1',
                '192.168.2.0/24',
                '2001:5c0:1000:b::90f8/128'
            )
        );

        $model = new Model();
        $model->createOrganisation($org);

        $result = $model->getOrganisation(1);
        $this->assertArraySubset($org, $result);
    }


}