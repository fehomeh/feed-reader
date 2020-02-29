<?php

declare(strict_types=1);

namespace FeedReader\Tests\Acceptance\Feed;

use FeedReader\Tests\Acceptance\AcceptanceTestCase;
use GuzzleHttp\Psr7\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

/**
 * @author Serhii Fomenko <fehomehal@gmail.com>
 * @package FeedReader
 */
final class FeedControllerTest extends AcceptanceTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        $this->client->disableReboot();
    }

    public function testAuthenticationSuccessful(): void
    {
        $role = 'ROLE_USER';
        $user = $this->createUser([$role]);
        $this->logIn($user, $role);
        $this->addGuzzleResponse(
            new Response(
                SymfonyResponse::HTTP_OK,
                ['Content-type' => 'application/atom+xml'],
                file_get_contents(self::$kernel->getProjectDir() . '/tests/resources/feed.atom')
            )
        );

        $client = $this->request('GET', 'feeds');

        $response = $this->safeJsonDecode($client->getResponse()->getContent());

        self::assertTrue($response['success']);
        self::assertSame(SymfonyResponse::HTTP_OK, $client->getResponse()->getStatusCode());
        self::assertSame($this->expectedResponseFeed(), $response['feed']);
    }

    private function expectedResponseFeed(): array
    {
        return [
            'mostPopularWords' => [
                'windows' => 5,
                'new' => 3,
                'microsoft' => 3,
                'apps' => 2,
                'surface' => 2,
                'devs' => 2,
                'support' => 2,
                'neo' => 2,
                'dual-screen' => 2,
                'round' => 1,
            ],
            'items' => [
                [
                    'title' => 'Microsoft brings the pane: You\'ll be looking at Xamarin and React Native to design apps for dual-screen gizmos',
                    'link' => 'https://go.theregister.co.uk/feed/www.theregister.co.uk/2020/02/13/designing_windows_and_android_apps_for_dualscreen_devices/',
                    'summary' => '<h4>Surface Neo is the most interesting new Windows device in years, but will weary devs support it?</h4> <p>Microsoft\'s dual-screen Surface devices for Windows (Neo) and Android (Duo) come out later this year, but how will devs write or re-write their apps to support them?…</p>',
                    'lastModified' => '2020-02-13T15:58:18+00:00',
                    'authorName' => '',
                    'authorEmail' => '',
                    'authorUri' => '',
                ],
                [
                    'title' => 'A new entry in the franchise: Microsoft Windows and the Goblet of Meh',
                    'link' => 'https://go.theregister.co.uk/feed/www.theregister.co.uk/2020/02/10/microsoft_roundup/',
                    'summary' => '<h4>Good news for Windows on ARM64 users in this week\'s round up</h4> <p><strong>Roundup</strong>  The Microsoft gang managed to find time away from <a target="_blank" href="https://www.theregister.co.uk/2020/02/05/windows_10_binged/">breaking Bing</a> and <a target="_blank" href="https://www.theregister.co.uk/2020/02/03/teams_down/">trashing Teams</a> last week to emit new Windows and update Visual Studio Code.…</p>',
                    'lastModified' => '2020-02-10T10:50:54+00:00',
                    'authorName' => '',
                    'authorEmail' => '',
                    'authorUri' => '',
                ],
            ],
            'title' => 'The Register - Software',
            'logo' => 'https://www.theregister.co.uk/Design/graphics/Reg_default/The_Register_r.png',
            'url' => '',
        ];
    }
}
