<?php

namespace Kizeo\ADFSSocialite;

use SocialiteProviders\Manager\OAuth2\AbstractProvider;
use Kizeo\ADFSSocialite\MicrosoftUser as User;

class Provider extends AbstractProvider
{
    /**
     * Unique Provider Identifier.
     */
    public const IDENTIFIER = 'MICROSOFTADFS';

    /**
     * {@inheritdoc}
     * https://docs.microsoft.com/fr-fr/windows-server/identity/ad-fs/development/ad-fs-openid-connect-oauth-concepts.
     */
    protected $scopes = ['User.Read'];

    /**
     * {@inheritdoc}
     */
    protected $scopeSeparator = ' ';

    /**
     * {@inheritdoc}
     */
    protected function getAuthUrl($state)
    {
        return
            $this->buildAuthUrlFromBase(
                sprintf(
                    '%s/oauth2/v2.0/authorize',
                    $this->config['base_uri']
                ),
                $state
            );
    }

    /**
     * {@inheritdoc}
     * https://docs.microsoft.com/fr-fr/windows-server/identity/ad-fs/development/ad-fs-openid-connect-oauth-concepts.
     */
    protected function getTokenUrl()
    {
        return sprintf('%s/oauth2/v2.0/token', $this->config['base_uri']);
    }

    /**
     * {@inheritdoc}
     */
    protected function getUserByToken($token)
    {
        $response = $this->getHttpClient()->get(
            sprintf('%s/userinfo', $this->config['base_uri']),
            [
                'headers' => [
                    'Accept'        => 'application/json',
                    'Authorization' => 'Bearer '.$token,
                ],
            ]
        );

        return json_decode($response->getBody()->getContents(), true);
    }

    /**
     * {@inheritdoc}
     */
    protected function mapUserToObject(array $user)
    {
        return (new User())->setRaw($user)->map([
            'id'       => $user['id'],
            'nickname' => null,
            'name'     => $user['displayName'],
            'email'    => $user['userPrincipalName'],
            'avatar'   => null,

            'businessPhones'    => $user['businessPhones'],
            'displayName'       => $user['displayName'],
            'givenName'         => $user['givenName'],
            'jobTitle'          => $user['jobTitle'],
            'mail'              => $user['mail'],
            'mobilePhone'       => $user['mobilePhone'],
            'officeLocation'    => $user['officeLocation'],
            'preferredLanguage' => $user['preferredLanguage'],
            'surname'           => $user['surname'],
            'userPrincipalName' => $user['userPrincipalName'],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    protected function getTokenFields($code)
    {
        return array_merge(parent::getTokenFields($code), [
            'grant_type' => 'authorization_code',
        ]);
    }

    /**
     * Add the additional configuration key 'tenant' to enable the branded sign-in experience.
     *
     * @return array
     */
    public static function additionalConfigKeys()
    {
        return ['tenant'];
    }
}
