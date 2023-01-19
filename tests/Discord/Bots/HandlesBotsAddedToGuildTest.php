<?php

namespace LaravelRestcord\Discord\Webhooks;

use Exception;
use Illuminate\Http\RedirectResponse;
use LaravelRestcord\Discord;
use LaravelRestcord\Discord\Bots\HandlesBotAddedToGuild;
use PHPUnit\Framework\TestCase;

class HandlesBotsAddedToGuildTest extends TestCase
{
    /** @var WebhookHandlerStub */
    protected $stub;

    public function setUp(): void
    {
        parent::setUp();

        $this->stub = new BotAddToGuildStub();
    }

    /** @test */
    public function throwsExceptions()
    {
        $exception = new Exception();

        $this->expectException(Exception::class);

        $this->stub->errored($exception);
    }
}

class BotAddToGuildStub
{
    use HandlesBotAddedToGuild;

    /**
     * After adding the bot to a guild, your user will be redirected back to a controller
     * that'll interpret the response and call this method, empowering you to control
     * what happens next.
     */
    public function botAdded(string $accessToken, int $expiresIn, string $refreshToken, Discord\Guild $Guild): RedirectResponse
    {
    }

    /**
     * If the user hits cancel, we'll need to handle the error.  Usually
     * $error = "access_denied".
     */
    public function botNotAdded(string $error): RedirectResponse
    {
    }
}
