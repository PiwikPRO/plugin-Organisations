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


namespace Piwik\Plugins\Organisations;

use Piwik\Common;
use Piwik\Db;
use Piwik\DbHelper;
use Piwik\Network\IP;
use Piwik\Option;
use Piwik\Tracker\Cache;

class Model
{
    const TRACKER_CACHE_KEY = 'organisationMapping';
    const OPTION_KEY        = 'Organisations.hashed';
    private static $rawPrefix = 'organisation';
    private        $table;

    public function __construct()
    {
        $this->table = Common::prefixTable(self::$rawPrefix);
    }

    /**
     * Return all organisations
     *
     * @return array
     */
    public function getAll()
    {
        $organisations = $this->getDb()->fetchAll('SELECT * FROM ' . $this->table);
        foreach ($organisations as &$organisation) {
            $organisation['ipranges'] = $this->splitIpRanges($organisation['ipranges']);
        }
        return $organisations;
    }

    /**
     * Returns the organisation with the given id
     *
     * @param int $idOrg organisation id to read
     * @return array
     * @throws \Exception
     */
    public function getOrganisation($idOrg)
    {
        $query        = 'SELECT * FROM ' . $this->table . ' WHERE idorg = ?';
        $bind         = array($idOrg);
        $organisation = Db::fetchRow($query, $bind);
        if ($organisation && array_key_exists('ipranges', $organisation)) {
            $organisation['ipranges'] = $this->splitIpRanges($organisation['ipranges']);
        }
        return $organisation;
    }

    /**
     * Removes the organisation with the given id
     *
     * @param int $idOrg organisation id to remove
     * @throws \Exception
     */
    public function deleteOrganisation($idOrg)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE idorg = ?';
        $bind  = array($idOrg);
        Db::query($query, $bind);
    }

    /**
     * Updates the organisation with the given id and data
     *
     * @param int $idOrg organisation id to update
     * @param array $organisation data to update
     */
    public function updateOrganisation($idOrg, $organisation)
    {
        $organisation['ipranges'] = $this->combineIpRanges($organisation['ipranges']);
        $this->getDb()->update($this->table, $organisation, "idorg = " . (int)$idOrg);
    }

    /**
     * Creates a new organisation with the given data
     *
     * @param array $organisation organisation data
     * @return int
     */
    public function createOrganisation($organisation)
    {
        $nextId                   = $this->getNextOrganisationId();
        $organisation['idorg']    = $nextId;
        $organisation['ipranges'] = $this->combineIpRanges($organisation['ipranges']);

        $this->getDb()->insert($this->table, $organisation);

        return $nextId;
    }

    /**
     * Returns IP range mapping for organisations
     *
     * @return array
     */
    private function getIpRangeMapping()
    {
        $ipRanges      = array();
        $organisations = $this->getAll();
        foreach ($organisations as $organisation) {
            foreach ($organisation['ipranges'] as $ipRange) {
                $ipRanges[$ipRange] = $organisation['idorg'];
            }
        }

        return $ipRanges;
    }

    /**
     * Return organisation id based on the given IP
     *
     * Tracker cache is used to get ip mapping
     *
     * @param string $ip
     * @return int
     */
    public function getOrganisationFromIp($ip)
    {
        $cache        = new Cache();
        $cacheContent = $cache->getCacheGeneral();

        if (!array_key_exists(self::TRACKER_CACHE_KEY, $cacheContent)) {
            $ipRanges = $this->getIpRangeMapping();
        } else {
            $ipRanges = $cacheContent[self::TRACKER_CACHE_KEY];
        }

        $ip = IP::fromStringIP($ip);

        foreach ($ipRanges as $ipRange => $orgId) {
            if ($ip->isInRange($ipRange)) {
                return $orgId;
            }
        }

        return 0;
    }

    /**
     * Used to set IP range to organisation mapping to Tracker Cache
     *
     * @see Organisations::setTrackerCacheGeneral
     *
     * @param $cacheContent
     * @return mixed
     */
    public function setTrackerCache($cacheContent)
    {
        $cacheContent[self::TRACKER_CACHE_KEY] = $this->getIpRangeMapping();
        return $cacheContent;
    }

    /**
     * Clears the tracker cache if there were changes
     *
     * Uses a md5-hash over all organisation data save in opton table to identify changes
     *
     * @return boolean
     */
    public function clearTrackerCacheIfRequired()
    {
        $cachedHashed = Option::get(self::OPTION_KEY);

        $allOrganisations    = $this->getAll();
        $hashedOrganisations = md5(serialize($allOrganisations));

        if ($cachedHashed != $hashedOrganisations) {
            Cache::clearCacheGeneral();
            Option::set(self::OPTION_KEY, $hashedOrganisations);
            return true;
        }

        return false;
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
        $db       = $this->getDb();
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

    /**
     * Installs the required database table
     */
    public static function install()
    {
        $orgTable = "`idorg` INT(11) NOT NULL,
					    `name` VARCHAR(100) NOT NULL,
					    `ipranges` VARCHAR(255) NOT NULL,
					    PRIMARY KEY (`idorg`)";

        DbHelper::createTable(self::$rawPrefix, $orgTable);
    }
}
