<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;

class Model
{
    private static $rawPrefix = 'organisation';
    private $table;

    public function __construct()
    {
        $this->table = Common::prefixTable(self::$rawPrefix);
    }

    public function getAll()
    {
        $organisations = $this->getDb()->fetchAll('SELECT * FROM ' . $this->table);
        foreach ($organisations as $organisation) {
            $organisation['iprange'] = $this->splitIpRanges($organisation['iprange']);
        }
        return $organisations;
    }

    public function getOrganisation($idOrg)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE idorg = ?';
        $bind  = array($idOrg);
        $organisation = Db::fetchOne($query, $bind);
        $organisation['iprange'] = $this->splitIpRanges($organisation['iprange']);
        return $organisation;
    }

    public function deleteOrganisation($idOrg)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE idorg = ?';
        $bind  = array($idOrg);
        Db::query($query, $bind);
    }

    public function updateOrganisation($idOrg, $organisation)
    {
        $organisation['iprange'] = $this->combineIpRanges($organisation['iprange']);
        $this->getDb()->update($this->table, $organisation, "idorg = " . (int) $idOrg);
    }

    public function createOrganisation($organisation)
    {
        $nextId = $this->getNextOrganisationId();
        $organisation['idorg'] = $nextId;
        $organisation['iprange'] = $this->combineIpRanges($organisation['iprange']);

        $this->getDb()->insert($this->table, $organisation);

        return $nextId;
    }

    public function getIpRangeMapping()
    {
        // @todo implement caching (tracker cache)
        $ipRanges = array();
        $organisations = $this->getAll();
        foreach ($organisations as $organisation) {
            foreach ($organisation['ipranges'] as $ipRange) {
                $ipRanges[$ipRange] = $organisation['idorg'];
            }
        }

        return $ipRanges;
    }

    /**
     * @param string $ipRanges
     * @return array
     */
    private function splitIpRanges($ipRanges)
    {
        return explode(';', $ipRanges);
    }

    /**
     * @param array $ipRanges
     * @return string
     */
    private function combineIpRanges($ipRanges)
    {
        return implode(';', $ipRanges);
    }

    private function getNextOrganisationId()
    {
        $db = $this->getDb();
        $idReport = $db->fetchOne("SELECT max(idorg) + 1 FROM " . $this->table);

        if ($idReport == false) {
            $idReport = 1;
        }

        return $idReport;
    }

    private function getDb()
    {
        return Db::get();
    }

    public static function install()
    {
        $orgTable = "`idorg` INT(11) NOT NULL,
					    ---`idsite` INTEGER(11) NOT NULL,
					    `name` VARCHAR(100) NOT NULL,
					    `ipranges` VARCHAR(255) NOT NULL,
					    PRIMARY KEY (`idorg`)";

        DbHelper::createTable(self::$rawPrefix, $orgTable);
    }
}
