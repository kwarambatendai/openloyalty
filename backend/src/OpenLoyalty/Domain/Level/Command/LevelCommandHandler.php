<?php

namespace OpenLoyalty\Domain\Level\Command;

use Broadway\CommandHandling\CommandHandler;
use OpenLoyalty\Domain\Level\Level;
use OpenLoyalty\Domain\Level\LevelRepository;
use OpenLoyalty\Domain\Level\Model\Reward;
use OpenLoyalty\Domain\Level\SpecialReward;
use OpenLoyalty\Domain\Level\SpecialRewardId;
use OpenLoyalty\Domain\Level\SpecialRewardRepository;

/**
 * Class LevelCommandHandler.
 */
class LevelCommandHandler extends CommandHandler
{
    /**
     * @var LevelRepository
     */
    private $levelRepository;

    /**
     * @var SpecialRewardRepository
     */
    private $specialRewardRepository;

    /**
     * LevelCommandHandler constructor.
     *
     * @param LevelRepository         $levelRepository
     * @param SpecialRewardRepository $specialRewardRepository
     */
    public function __construct(LevelRepository $levelRepository, SpecialRewardRepository $specialRewardRepository)
    {
        $this->levelRepository = $levelRepository;
        $this->specialRewardRepository = $specialRewardRepository;
    }

    public function handleActivateLevel(ActivateLevel $command)
    {
        /** @var Level $level */
        $level = $this->levelRepository->byId($command->getLevelId());
        $level->setActive(true);
        $this->levelRepository->save($level);
    }

    public function handleDeactivateLevel(DeactivateLevel $command)
    {
        /** @var Level $level */
        $level = $this->levelRepository->byId($command->getLevelId());
        $level->setActive(false);
        $this->levelRepository->save($level);
    }

    public function handleCreateLevel(CreateLevel $command)
    {
        $data = $command->getLevelData();
        $level = new Level($command->getLevelId(), $data['name'], $data['conditionValue']);
        if (isset($data['description'])) {
            $level->setDescription($data['description']);
        }
        if (isset($data['minOrder'])) {
            $level->setMinOrder($data['minOrder']);
        }

        $rewardData = $data['reward'];
        $level->setReward(new Reward($rewardData['name'], $rewardData['value'], $rewardData['code']));
        if (isset($data['specialRewards']) && is_array($data['specialRewards'])) {
            foreach ($data['specialRewards'] as $specialReward) {
                $newReward = new SpecialReward(
                    new SpecialRewardId($specialReward['id']),
                    $level,
                    $specialReward['name'],
                    $specialReward['value'],
                    $specialReward['code']
                );
                $newReward->setActive($specialReward['active']);
                $newReward->setStartAt(isset($specialReward['startAt']) ? $specialReward['startAt'] : null);
                $newReward->setEndAt(isset($specialReward['endAt']) ? $specialReward['endAt'] : null);

                $level->addSpecialReward($newReward);
            }
        }

        $this->levelRepository->save($level);
    }

    public function handleUpdateLevel(UpdateLevel $command)
    {
        /** @var Level $level */
        $level = $this->levelRepository->byId($command->getLevelId());
        $data = $command->getLevelData();
        $level->setName(isset($data['name']) ? $data['name'] : null);
        $level->setDescription(isset($data['description']) ? $data['description'] : null);
        $level->setConditionValue(isset($data['conditionValue']) ? $data['conditionValue'] : null);
        $rewardData = $data['reward'];
        $level->setReward(new Reward($rewardData['name'], $rewardData['value'], $rewardData['code']));
        $oldSpecialRewards = $level->getSpecialRewards();
        $newSpecialRewards = [];
        if (isset($data['minOrder'])) {
            $level->setMinOrder($data['minOrder']);
        }
        if (isset($data['specialRewards']) && is_array($data['specialRewards']) && count($data['specialRewards']) > 0) {
            foreach ($data['specialRewards'] as $key => $specialReward) {
                if (isset($oldSpecialRewards[$key]) && $oldSpecialRewards[$key] instanceof SpecialReward) {
                    /** @var SpecialReward $newReward */
                    $newReward = $oldSpecialRewards[$key];
                    $newReward->setName(isset($specialReward['name']) ? $specialReward['name'] : null);
                    $newReward->setValue(isset($specialReward['value']) ? $specialReward['value'] : null);
                    $newReward->setCode(isset($specialReward['code']) ? $specialReward['code'] : null);
                    unset($oldSpecialRewards[$key]);
                } else {
                    $newReward = new SpecialReward(
                        new SpecialRewardId($specialReward['id']),
                        $level,
                        $specialReward['name'],
                        $specialReward['value'],
                        $specialReward['code']
                    );
                }
                $newReward->setActive($specialReward['active']);
                $newReward->setStartAt(isset($specialReward['startAt']) ? $specialReward['startAt'] : null);
                $newReward->setEndAt(isset($specialReward['endAt']) ? $specialReward['endAt'] : null);

                $this->specialRewardRepository->save($newReward);

                $newSpecialRewards[] = $newReward;
            }
            $level->setSpecialRewards($newSpecialRewards);
            foreach ($oldSpecialRewards as $old) {
                $this->specialRewardRepository->remove($old);
            }
        } else {
            foreach ($level->getSpecialRewards() as $old) {
                $this->specialRewardRepository->remove($old);
            }
            $level->setSpecialRewards([]);
        }

        $this->levelRepository->save($level);
    }
}
