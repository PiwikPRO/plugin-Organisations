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
        return $this->getDb()->fetchAll('SELECT * FROM ' . $this->table);
    }

    public function getOrganisation($idOrg)
    {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE idorg = ?';
        $bind  = array($idOrg);
        return Db::fetchOne($query, $bind);
    }

    public function deleteOrganisation($idOrg)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE idorg = ?';
        $bind  = array($idOrg);
        Db::query($query, $bind);
    }

    public function updateOrganisation($idOrg, $organisation)
    {
        $this->getDb()->update($this->table, $organisation, "idorg = " . (int) $idOrg);
    }

    public function createOrganisation($organisation)
    {
        $nextId = $this->getNextOrganisationId();
        $organisation['idorg'] = $nextId;

        $this->getDb()->insert($this->table, $organisation);

        return $nextId;
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
