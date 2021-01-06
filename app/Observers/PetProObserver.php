<?php

namespace App\Observers;

use App\Http\WagEnabledHelpers;
use App\Models\PetPro;

class PetProObserver
{
    /**
     * Handle the watch and learn "created" event.
     *
     * @param  \App\PetPro  $watchAndLearn
     * @return void
     */

    public function creating(PetPro $model)
    {
        $slugStr = WagEnabledHelpers::generateUniqueSlug( $model, $model->store_name  );
        $model->slug = $slugStr;
    }

    public function updating(PetPro $model) 
    {
        if ($model->isDirty('store_name')) {          
           $slugStr = WagEnabledHelpers::generateUniqueSlug( $model, $model->store_name, $model->id  );             
           $model->slug = $slugStr;
        }
    }

    public function created(PetPro $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "updated" event.
     *
     * @param  \App\PetPro  $watchAndLearn
     * @return void
     */
    public function updated(PetPro $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "deleted" event.
     *
     * @param  \App\PetPro  $watchAndLearn
     * @return void
     */
    public function deleted(PetPro $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "restored" event.
     *
     * @param  \App\PetPro  $watchAndLearn
     * @return void
     */
    public function restored(PetPro $watchAndLearn)
    {
        //
    }

    /**
     * Handle the watch and learn "force deleted" event.
     *
     * @param  \App\PetPro  $watchAndLearn
     * @return void
     */
    public function forceDeleted(PetPro $watchAndLearn)
    {
        //
    }
}
