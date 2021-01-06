<?php

namespace App\Observers;

use App\Http\WagEnabledHelpers;
use App\Models\ProductReview;

class ProductReviewObserver
{
    /**
     * Handle the watch and learn "created" event.
     *
     * @param  \App\ProductReview  $ProductReview
     * @return void
     */

    public function creating(ProductReview $model)
    {
        $slugStr = WagEnabledHelpers::generateUniqueSlug( $model, $model->title  );
        $model->slug = $slugStr;
    }

    public function updating(ProductReview $model) 
    {
        if ($model->isDirty('title')) {          
           $slugStr = WagEnabledHelpers::generateUniqueSlug( $model, $model->title, $model->id  );             
           $model->slug = $slugStr;
        }
    }

    public function created(ProductReview $ProductReview)
    {
        //
    }

    /**
     * Handle the watch and learn "updated" event.
     *
     * @param  \App\ProductReview  $ProductReview
     * @return void
     */
    public function updated(ProductReview $ProductReview)
    {
        //
    }

    /**
     * Handle the watch and learn "deleted" event.
     *
     * @param  \App\ProductReview  $ProductReview
     * @return void
     */
    public function deleted(ProductReview $ProductReview)
    {
        //
    }

    /**
     * Handle the watch and learn "restored" event.
     *
     * @param  \App\ProductReview  $ProductReview
     * @return void
     */
    public function restored(ProductReview $ProductReview)
    {
        //
    }

    /**
     * Handle the watch and learn "force deleted" event.
     *
     * @param  \App\ProductReview  $ProductReview
     * @return void
     */
    public function forceDeleted(ProductReview $ProductReview)
    {
        //
    }
}
