<?php
/**
 * Copyright © 2017 Divante, Inc. All rights reserved.
 * See LICENSE for license details.
 */
namespace OpenLoyalty\Domain\Campaign;

interface CampaignRepository
{
    public function byId(CampaignId $campaignId);

    public function findAll();

    public function findAllPaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    public function findAllVisiblePaginated($page = 1, $perPage = 10, $sortField = null, $direction = 'DESC');

    /**
     * @param SegmentId[] $segmentIds
     * @param LevelId     $levelId
     * @param int         $page
     * @param int         $perPage
     * @param null        $sortField
     * @param string      $direction
     *
     * @return Campaign[]
     */
    public function getActiveCampaignsForLevelAndSegment(array $segmentIds = [], LevelId $levelId = null, $page = 1, $perPage = 10, $sortField = null, $direction = 'ASC');

    public function getVisibleCampaignsForLevelAndSegment(array $segmentIds = [], LevelId $levelId = null, $page = 1, $perPage = 10, $sortField = null, $direction = 'ASC');

    public function countTotal($onlyVisible = false);

    public function save(Campaign $campaign);

    public function remove(Campaign $campaign);
}
