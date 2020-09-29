<?php

namespace Kizeo\ADFSSocialite;

use SocialiteProviders\Manager\SocialiteWasCalled;

class MicrosoftADFSExtendSocialite
{
    /**
     * Register the provider.
     *
     * @param \SocialiteProviders\Manager\SocialiteWasCalled $socialiteWasCalled
     */
    public function handle(SocialiteWasCalled $socialiteWasCalled)
    {
        $socialiteWasCalled->extendSocialite('microsoftadfs', Provider::class);
    }
}
