<?php
namespace Piwik\Plugins\Organisations;

use Piwik\Metrics;

class Archiver extends \Piwik\Plugin\Archiver
{
    const ORGANISATIONS_RECORD_NAME = 'Organisations_all';
    const ORGANISATION_FIELD = "organisation";

    /**
     * Aggregates reports for a single day
     *
     * @throws \Exception
     */
    public function aggregateDayReport()
    {
        $metrics = $this->getLogAggregator()->getMetricsFromVisitByDimension(self::ORGANISATION_FIELD);
        $query = $this->getLogAggregator()->queryConversionsByDimension(array(self::ORGANISATION_FIELD));
        if ($query === false) {
            return;
        }

        while ($conversionRow = $query->fetch()) {
            $metrics->sumMetricsGoals($conversionRow['organisation'], $conversionRow);
        }
        $metrics->enrichMetricsWithConversions();

        $report = $metrics->asDataTable()->getSerialized($this->maximumRows, null, Metrics::INDEX_NB_VISITS);
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
            $columnsAggregationOperation = null,
            $columnsToRenameAfterAggregation = null,
            $countRowsRecursive = array()
        );
    }
}
