<?php

/**
 * (c) Copyright Spryker Systems GmbH 2015
 */

namespace SprykerEngine\Shared\EventJournal;

use SprykerFeature\Shared\Library\ConfigInterface;

interface EventJournalConfig extends ConfigInterface
{

    const WRITERS = 'EVENT_JOURNAL_WRITERS';

    const COLLECTORS = 'EVENT_JOURNAL_COLLECTORS';

    const FILTERS = 'EVENT_JOURNAL_FILTERS';

    const WRITER_OPTIONS = 'EVENT_JOURNAL_WRITER_OPTIONS';

    const COLLECTOR_OPTIONS = 'EVENT_JOURNAL_COLLECTOR_OPTIONS';

    const FILTER_OPTIONS = 'EVENT_JOURNAL_FILTER_OPTIONS';

}
