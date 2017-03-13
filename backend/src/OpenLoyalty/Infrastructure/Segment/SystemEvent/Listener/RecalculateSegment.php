<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Infrastructure\Segment\SystemEvent\Listener;

use OpenLoyalty\Domain\Segment\SystemEvent\SegmentChangedSystemEvent;
use Symfony\Component\HttpKernel\Kernel;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 * Class CalculateCustomerLevelListener.
 */
class RecalculateSegment
{
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function handle(SegmentChangedSystemEvent $event)
    {
        $application = new Application($this->kernel);
        $application->setAutoExit(false);
        $input = new ArrayInput(array(
            'command' => 'oloy:segment:recreate',
            '--segmentId' => $event->getSegmentId()->__toString(),
        ));
        $output = new BufferedOutput();
        $application->run($input, $output);
    }
}
