<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Metrics;

class Archiver extends \Piwik\Plugin\Archiver
{
    const ORGANISATIONS_RECORD_NAME = 'Organisations_all';
    const ORGANISATION_FIELD = "organisation";

    public function aggregateDayReport()
    {
        $metrics = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::ORGANISATION_FIELD)->asDataTable();
        $report = $metrics->getSerialized($this->maximumRows, null, Metrics::INDEX_NB_VISITS);
        $this->getProcessor()->insertBlobRecord(self::ORGANISATIONS_RECORD_NAME, $report);
    }

    public function aggregateMultipleReports()
    {
        $columnsAggregationOperation = null;

        $this->getProcessor()->aggregateDataTableRecords(
            array(self::ORGANISATIONS_RECORD_NAME),
            $this->maximumRows,
            $maximumRowsInSubDataTable = null,
            $columnToSortByBeforeTruncation = null,
            $columnsAggregationOperation,
            $columnsToRenameAfterAggregation = null,
            $countRowsRecursive = array()
        );
    }
}
