<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Bundle\UserBundle\Command;

use Elasticsearch\Common\Exceptions\Missing404Exception;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class CreateProjectionIndexCommand.
 */
class CreateProjectionIndexCommand extends ContainerAwareCommand
{
    protected $repos = [
        'oloy.user.read_model.repository.customer_details',
        'oloy.points.account.repository.account_details',
        'oloy.points.account.repository.points_transfer_details',
        'oloy.user.read_model.repository.customers_belonging_to_one_level',
        'oloy.transaction.read_model.repository.transaction_details',
        'oloy.user.read_model.repository.seller_details',
        'oloy.segment.read_model.repository.segmented_customers',
        'oloy.campaign.read_model.repository.coupon_usage',
    ];

    protected function configure()
    {
        $this->setName('oloy:user:projections:index:create');
        $this->addOption('drop-old', 'drop-old', InputOption::VALUE_NONE);
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        foreach ($this->repos as $repo) {
            $repo = $this->getContainer()->get($repo);
            if ($input->getOption('drop-old')) {
                if (method_exists($repo, 'deleteIndex')) {
                    try {
                        $repo->deleteIndex();
                    } catch (Missing404Exception $e) {
                    }
                }
            }
            if (method_exists($repo, 'createIndex')) {
                $repo->createIndex();
            }
        }
    }
}
