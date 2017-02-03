<?php
/**
 * Copyright (C) Piwik PRO - All rights reserved.
 *
 * Using this code requires that you first get a license from Piwik PRO.
 * Unauthorized copying of this file, via any medium is strictly prohibited.
 *
 * @link http://piwik.pro
 */

namespace Piwik\Plugins\Organisations\Exception;

use Exception;
use Piwik\Piwik;

class InvalidOrganisationSegmentException extends Exception
{
    const MSG = "Organisations_InvalidOrganisationSegment";

    public function __construct($organisationNames, $code = 0, Exception $previous = null)
    {
        parent::__construct(
            sprintf(
                Piwik::translate(self::MSG),
                implode(', ', $organisationNames)
            ),
            $code,
            $previous
        );
    }
}
