<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\State;

class StateShortcodeScript extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'state:add-shortcode';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $states = State::where('country_id', 231)->get();
        foreach($states as $state) {
            $googleMapApiKey = config("wagenabled.google_map.api_key");
            $geo = file_get_contents('https://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($state->name).'&key='.$googleMapApiKey);
            $geo = json_decode($geo, true); // Convert the JSON to an array
            if(isset($geo['results'][0])) {
                foreach ($geo['results'][0]['address_components'] as $comp) {
                    //loop through each component in ['address_components']
                    foreach ($comp['types'] as $currType){
                        //for every type in the current component, check if it = the check
                        if($currType == 'country') {
                            if($comp['long_name'] == $state->name) {
                                $state->short_name = $comp['short_name'];
                                $state->save();
                            }
                            if($comp['long_name'] == "U.S. Virgin Islands" && $state->name == 'Virgin Islands') {
                                $state->short_name = $comp['short_name'];
                                $state->save();
                            }
                        }
                        if($currType == 'administrative_area_level_1'){
                            if($comp['long_name'] == $state->name) {
                                $state->short_name = $comp['short_name'];
                                $state->save();
                            } else {
                                if($state->name == 'Armed Forces America') {
                                    $state->short_name = 'AA';
                                }
                                if($state->name == 'Armed Forces Europe') {
                                    $state->short_name = 'AE';
                                }
                                $state->save();
                            }
                            //Do whatever with the component, print longname, whatever you need
                            //You can add $comp into another array to have an array of 'administrative_area_level_1' types
                        }
                    }
                }
            } else {
                if($state->name == 'Armed Forces Pacific') {
                    $state->short_name = 'AP';
                    $state->save();
                }
            }
            echo $state->name."\n";
        }
    }
}
