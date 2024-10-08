<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\User;
use App\Services\GitHub;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

final class SyncVerifiedUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(private User $user)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(GitHub $github): void
    {
        $user = type($this->user->fresh())->as(User::class);

        $user->update([
            'is_verified' => $user->github_username && $github->isSponsor($user->github_username),
            'is_company_verified' => $user->github_username && $github->isCompanySponsor($user->github_username),
        ]);
    }
}
