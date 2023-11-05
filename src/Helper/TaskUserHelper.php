<?php

namespace App\Helper;

use App\Entity\Project;
use App\Entity\ProjectUser;
use App\Entity\Task;
use App\Entity\TaskUser;
use DateTime;
use function PHPUnit\Framework\matches;

class TaskUserHelper
{
    /**
     * @param iterable<ProjectUser> $projectUsers
     * @return array
     */
    public static function FromProjectUsers(iterable $projectUsers): array
    {
        $taskUsers = [];
        foreach ($projectUsers as $projectUser) {
            $permissions = [TaskUser::CAN_COMMENT, TaskUser::CAN_EDIT_TASK];
            foreach ($projectUser->getPermissions() as $permission) {
                if ($permission === ProjectUser::FULL_PRIVILEGE) {
                    $permissions = [TaskUser::FULL_PRIVILEGE];
                    break;
                }
            }
            $taskUsers[] = (new TaskUser())
                ->setUser($projectUser->getUser())
                ->setPermissions(array_unique($permissions));
        }
        return $taskUsers;
    }
}
