<?php

use App\Settings\TaxSetting;

return [

    /*
     * Each settings class used in your application must be registered, you can
     * put them (manually) here.
     */
    'settings' => [
        TaxSetting::class,

    ],
    /*
     * When no repository was set for a settings class the following repository
     * will be used for loading and saving settings.
     */

];
