<?php

namespace App\Observers;

use App\Http\WagEnabledHelpers;
use App\Models\WatchAndLearn;

class WatchAndLearnObserver
{
    /**
     * Handle the watch and learn "created" event.
     *
     * @param  \App\WatchAndLearn  $watchAndLearn
     * @return void
     */

    public function creating(WatchAndLearn $model)
    {
        $slugStr = WagEnabledHelpers::generateUniqueSlug( $model, $model->title  );
        $model->slug = $slugStr;
    }

    public function updating(WatchAndLearn $model) 
    {
        if ($model->isDirty('title')) {          
           $slugStr = WagEnabledHelpers::generateUniqueSlug( $model, $model->title, $model->id  );             
           $model->slug = $slugStr;
        }
    }

    public function created(WatchAndLearn $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "updated" event.
     *
     * @param  \App\WatchAndLearn  $watchAndLearn
     * @return void
     */
    public function updated(WatchAndLearn $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "deleted" event.
     *
     * @param  \App\WatchAndLearn  $watchAndLearn
     * @return void
     */
    public function deleted(WatchAndLearn $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "restored" event.
     *
     * @param  \App\WatchAndLearn  $watchAndLearn
     * @return void
     */
    public function restored(WatchAndLearn $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "force deleted" event.
     *
     * @param  \App\WatchAndLearn  $watchAndLearn
     * @return void
     */
    public function forceDeleted(WatchAndLearn $watchAndLearn)
    {
        //
    }
}
