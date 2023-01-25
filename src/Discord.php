<?php

namespace LaravelRestcord;

use Illuminate\Support\Collection;
use LaravelRestcord\Discord\ApiClient;
use LaravelRestcord\Discord\Guild;

class Discord
{
    /** @var ApiClient */
    public static $api;

    /** @var string */
    public static $key;

    /** @var string */
    public static $secret;

    /** @var string */
    public static $callbackUrl;

    public function __construct(?ApiClient $apiClient = null)
    {
        if ($apiClient != null) {
            self::$api = $apiClient;
        }
    }

    /**
     * Guilds the current user has access to.  This is an abbreviated
     * version of the guilds endpoint so limited fields are provided.
     *
     * @see https://discord.com/developers/docs/resources/user#get-current-user-guilds
     */
    public function guilds() : Collection
    {
        $listOfGuilds = self::$api->get('/users/@me/guilds');

        $guilds = [];
        foreach ($listOfGuilds as $guildData) {
            $guilds[] = new Guild($guildData);
        }

        return new Collection($guilds);
    }

    /**
     * Guilds the current user has access to.  This is an abbreviated
     * version of the guilds endpoint so limited fields are provided.
     *
     * @see https://discord.com/developers/docs/resources/user#get-current-user-guilds
     */
    public function guild($gid): Guild
    {
        $guildItem = self::$api->get("/guilds/$gid");
        return new Guild($guildItem);
       
        
    }

    /**
     * Maintaining static accessibility on the client allows us to use this throughout
     * other classes in the package without constantly passing around the dependency.
     */
    public static function client() : ApiClient
    {
        return self::$api;
    }


    public static function setClient(ApiClient $apiClient)
    {
        self::$api = $apiClient;
    }


    public static function setKey(string $key)
    {
        self::$key = $key;
    }


    public static function key() : string
    {
        return self::$key;
    }


    public static function setSecret(string $secret)
    {
        self::$secret = $secret;
    }


    public static function secret() : string
    {
        return self::$secret;
    }


    public static function setCallbackUrl(string $callbackUrl)
    {
        self::$callbackUrl = $callbackUrl;
    }


    public static function callbackUrl() : string
    {
        return self::$callbackUrl.'/discord';
    }


    public static function addBotUrl(int $permissions, ?int $guildId = null, array $scopes =[]){
        $scopes = collect([...$scopes,'bot'])->explode('');
        $urlScopes = urlencode($scopes);
        $url = ApiClient::API_URL . '/oauth2/authorize?client_id=' . self::key() . '&scope='.$urlScopes.'&permissions=' . $permissions . '&redirect_uri=' . urlencode(self::callbackUrl() . '/bot-added') . '&response_type=code';
        if ($guildId != null) {
            $url .= '&guild_id=' . $guildId;
        }
        return $url;
    }
    

    public static function addWebHookUrl()
    {
        return  ApiClient::API_URL . '/oauth2/authorize?client_id=' . self::key() . '&scope=webhook.incoming&redirect_uri=' . urlencode(Discord::callbackUrl() . '/create-webhook') . '&response_type=code';
    }

    /**
     * Providing a guild id will pre-select that guild on the dropdown menu.
     *
     * @codecoverageignore
     */
    public static function redirecToAddBot(int $permissions, ?int $guildId =null, array $scopes = [])
    {
        header('Location: ' . self::addBotUrl($permissions, $guildId, $scopes));
        exit;
    }

    /**
     * @codecoverageignore
     */
    public static function redirectToCreateWebhook()
    {
        header('Location: ' . self::addWebHookUrl());
        exit;
    }


}
