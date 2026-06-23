<?php

namespace App\Policies;

use App\Models\Issue;
use App\Models\User;

class IssuePolicy
{
    public function view(User $user, Issue $issue): bool
    {
        return $this->ownsIssue($user, $issue);
    }

    public function update(User $user, Issue $issue): bool
    {
        return $this->ownsIssue($user, $issue);
    }

    public function delete(User $user, Issue $issue): bool
    {
        return $this->ownsIssue($user, $issue);
    }

    private function ownsIssue(User $user, Issue $issue): bool
    {
        $issue->loadMissing('project');

        return $issue->project->user_id === $user->id;
    }
}
