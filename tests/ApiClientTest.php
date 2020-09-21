<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans\Tests;

use Fivem\ClashOfClans\ApiClient;
use Fivem\ClashOfClans\Exception\ApiErrorException;
use Fivem\ClashOfClans\Exception\UnknownApiErrorException;
use Fivem\ClashOfClans\Model\Clan\Clan;
use Fivem\ClashOfClans\Model\CurrentWar\CurrentWar;
use Fivem\ClashOfClans\Model\Paginator\GetWarLogPaginator;
use Fivem\ClashOfClans\Query\GetWarLogQuery;
use Fivem\ClashOfClans\Query\SearchClansQuery;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

/**
 * @covers \ApiClient
 */
final class ApiClientTest extends TestCase
{
    private const ERROR_PAYLOADS = [
        400 => [
            'payload' => <<<_JSON
{"reason":"badRequest","message":"At least one filtering parameter must exist"}
_JSON,
            'message' => 'At least one filtering parameter must exist',
        ],
        403 => [
            'payload' => <<<_JSON
{"reason":"accessDenied","message":"Invalid authorization"}
_JSON,
            'message' => 'Access denied, either because of missing/incorrect credentials or used API token does not grant access to the requested resource.',
        ],
        404 => [
            'payload' => <<<_JSON
{"reason":"notFound"}
_JSON,
            'message' => 'Resource was not found.',
        ],
        429 => [
            'payload' => <<<_JSON
{"reason":""}
_JSON,
            'message' => 'Request was throttled, because amount of requests was above the threshold defined for the used API token.',
        ],
        503 => [
            'payload' => <<<_JSON
{"reason":""}
_JSON,
            'message' => 'Service is temporarily unavailable because of maintenance.',
        ],
        500 => [
            'payload' => <<<_JSON
{"reason":""}
_JSON,
            'message' => 'Unknown error happened when handling the request.',
        ],
    ];

    /** @var MockObject|HttpClientInterface */
    private $httpClient;

    /** @var ApiClient  */
    private $apiClient;

    protected function setUp(): void
    {
        parent::setUp();

        $this->httpClient = $this->createMock(HttpClientInterface::class);
        $this->apiClient = new ApiClient(
            $this->httpClient,
            'apikey'
        );
    }

    public function testSearchClans(): void
    {
        $payload = <<<_JSON
{
	"items": [{
		"tag": "#2PUCJJ82L",
		"name": "coconut",
		"type": "open",
		"location": {
			"id": 32000113,
			"name": "India",
			"isCountry": true,
			"countryCode": "IN"
		},
		"badgeUrls": {
			"small": "https://api-assets.clashofclans.com/badges/70/xofVjeqosge12_F5WhuEaBfmuwiG2rmrpu-vWA9bzXA.png",
			"large": "https://api-assets.clashofclans.com/badges/512/xofVjeqosge12_F5WhuEaBfmuwiG2rmrpu-vWA9bzXA.png",
			"medium": "https://api-assets.clashofclans.com/badges/200/xofVjeqosge12_F5WhuEaBfmuwiG2rmrpu-vWA9bzXA.png"
		},
		"clanLevel": 3,
		"clanPoints": 11957,
		"clanVersusPoints": 12483,
		"requiredTrophies": 400,
		"warFrequency": "always",
		"warWinStreak": 3,
		"warWins": 10,
		"warTies": 0,
		"warLosses": 11,
		"isWarLogPublic": true,
		"warLeague": {
			"id": 48000006,
			"name": "Silver League I"
		},
		"members": 18,
		"labels": [{
			"id": 56000000,
			"name": "Clan Wars",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/labels/64/lXaIuoTlfoNOY5fKcQGeT57apz1KFWkN9-raxqIlMbE.png",
				"medium": "https://api-assets.clashofclans.com/labels/128/lXaIuoTlfoNOY5fKcQGeT57apz1KFWkN9-raxqIlMbE.png"
			}
		}, {
			"id": 56000001,
			"name": "Clan War League",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/labels/64/5w60_3bdtYUe9SM6rkxBRyV_8VvWw_jTlDS5ieU3IsI.png",
				"medium": "https://api-assets.clashofclans.com/labels/128/5w60_3bdtYUe9SM6rkxBRyV_8VvWw_jTlDS5ieU3IsI.png"
			}
		}, {
			"id": 56000009,
			"name": "Donations",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/labels/64/RauzS-02tv4vWm1edZ-q3gPQGWKGANLZ-85HCw_NVP0.png",
				"medium": "https://api-assets.clashofclans.com/labels/128/RauzS-02tv4vWm1edZ-q3gPQGWKGANLZ-85HCw_NVP0.png"
			}
		}]
	}, {
		"tag": "#PYCGG8LY",
		"name": "COCONUT ARMY",
		"type": "inviteOnly",
		"location": {
			"id": 32000114,
			"name": "Indonesia",
			"isCountry": true,
			"countryCode": "ID"
		},
		"badgeUrls": {
			"small": "https://api-assets.clashofclans.com/badges/70/9jCcEf5xESk-6lLeZsw9iQ-WmZ3N-8Dbyop0Lk7oVPo.png",
			"large": "https://api-assets.clashofclans.com/badges/512/9jCcEf5xESk-6lLeZsw9iQ-WmZ3N-8Dbyop0Lk7oVPo.png",
			"medium": "https://api-assets.clashofclans.com/badges/200/9jCcEf5xESk-6lLeZsw9iQ-WmZ3N-8Dbyop0Lk7oVPo.png"
		},
		"clanLevel": 8,
		"clanPoints": 23370,
		"clanVersusPoints": 16460,
		"requiredTrophies": 1500,
		"warFrequency": "always",
		"warWinStreak": 4,
		"warWins": 137,
		"warTies": 1,
		"warLosses": 46,
		"isWarLogPublic": true,
		"warLeague": {
			"id": 48000010,
			"name": "Crystal League III"
		},
		"members": 21,
		"labels": [{
			"id": 56000000,
			"name": "Clan Wars",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/labels/64/lXaIuoTlfoNOY5fKcQGeT57apz1KFWkN9-raxqIlMbE.png",
				"medium": "https://api-assets.clashofclans.com/labels/128/lXaIuoTlfoNOY5fKcQGeT57apz1KFWkN9-raxqIlMbE.png"
			}
		}, {
			"id": 56000001,
			"name": "Clan War League",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/labels/64/5w60_3bdtYUe9SM6rkxBRyV_8VvWw_jTlDS5ieU3IsI.png",
				"medium": "https://api-assets.clashofclans.com/labels/128/5w60_3bdtYUe9SM6rkxBRyV_8VvWw_jTlDS5ieU3IsI.png"
			}
		}, {
			"id": 56000014,
			"name": "Competitive",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/labels/64/DhBE-1SSnrZQtsfjVHyNW-BTBWMc8Zoo34MNRCNiRsA.png",
				"medium": "https://api-assets.clashofclans.com/labels/128/DhBE-1SSnrZQtsfjVHyNW-BTBWMc8Zoo34MNRCNiRsA.png"
			}
		}]
	}],
	"paging": {
		"cursors": {
			"after": "eyJwb3MiOjJ9"
		}
	}
}
_JSON;

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getContent')
            ->willReturn($payload);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $response = $this->apiClient->searchClans(SearchClansQuery::fromArray([]));

        self::assertContainsOnlyInstancesOf(Clan::class, $response->items);
        self::assertCount(2, $response->items);

        $firstItem = $response->items[0];
        self::assertEquals('coconut', $firstItem->name);
    }

    public function testFindClanByTagNotFoundReturnNull(): void
    {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(404);
        $response->method('getContent')
            ->willReturn(self::ERROR_PAYLOADS[404]['payload']);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $response = $this->apiClient->findClanByTag('tag');

        self::assertNull($response);
    }

    public function testFindClanByTag(): void
    {
        $payload = <<<_JSON
{
	"tag": "#2PPC8L2QP",
	"name": "Coconu’t",
	"type": "inviteOnly",
	"description": "Clan très actif sympas , humain, gdc très régulierement et donne beaucoup de troupes",
	"location": {
		"id": 32000087,
		"name": "France",
		"isCountry": true,
		"countryCode": "FR"
	},
	"badgeUrls": {
		"small": "https://api-assets.clashofclans.com/badges/70/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
		"large": "https://api-assets.clashofclans.com/badges/512/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
		"medium": "https://api-assets.clashofclans.com/badges/200/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png"
	},
	"clanLevel": 4,
	"clanPoints": 13955,
	"clanVersusPoints": 15913,
	"requiredTrophies": 800,
	"warFrequency": "always",
	"warWinStreak": 1,
	"warWins": 28,
	"warTies": 0,
	"warLosses": 22,
	"isWarLogPublic": true,
	"warLeague": {
		"id": 48000004,
		"name": "Silver League III"
	},
	"members": 39,
	"memberList": [{
		"tag": "#28GU9UY2Y",
		"name": "takilex",
		"role": "coLeader",
		"expLevel": 98,
		"league": {
			"id": 29000012,
			"name": "Crystal League I",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/leagues/72/kSfTyNNVSvogX3dMvpFUTt72VW74w6vEsEFuuOV4osQ.png",
				"tiny": "https://api-assets.clashofclans.com/leagues/36/kSfTyNNVSvogX3dMvpFUTt72VW74w6vEsEFuuOV4osQ.png",
				"medium": "https://api-assets.clashofclans.com/leagues/288/kSfTyNNVSvogX3dMvpFUTt72VW74w6vEsEFuuOV4osQ.png"
			}
		},
		"trophies": 2469,
		"versusTrophies": 2142,
		"clanRank": 1,
		"previousClanRank": 1,
		"donations": 1845,
		"donationsReceived": 692
	}, {
		"tag": "#YG89GUJQV",
		"name": "ninøn",
		"role": "admin",
		"expLevel": 74,
		"league": {
			"id": 29000009,
			"name": "Gold League I",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/leagues/72/CorhMY9ZmQvqXTZ4VYVuUgPNGSHsO0cEXEL5WYRmB2Y.png",
				"tiny": "https://api-assets.clashofclans.com/leagues/36/CorhMY9ZmQvqXTZ4VYVuUgPNGSHsO0cEXEL5WYRmB2Y.png",
				"medium": "https://api-assets.clashofclans.com/leagues/288/CorhMY9ZmQvqXTZ4VYVuUgPNGSHsO0cEXEL5WYRmB2Y.png"
			}
		},
		"trophies": 1817,
		"versusTrophies": 2091,
		"clanRank": 4,
		"previousClanRank": 4,
		"donations": 965,
		"donationsReceived": 657
	},{
		"tag": "#L8QLLP28L",
		"name": "ll Wartogue ll",
		"role": "member",
		"expLevel": 50,
		"league": {
			"id": 29000005,
			"name": "Silver League II",
			"iconUrls": {
				"small": "https://api-assets.clashofclans.com/leagues/72/8OhXcwDJkenBH2kPH73eXftFOpHHRF-b32n0yrTqC44.png",
				"tiny": "https://api-assets.clashofclans.com/leagues/36/8OhXcwDJkenBH2kPH73eXftFOpHHRF-b32n0yrTqC44.png",
				"medium": "https://api-assets.clashofclans.com/leagues/288/8OhXcwDJkenBH2kPH73eXftFOpHHRF-b32n0yrTqC44.png"
			}
		},
		"trophies": 1064,
		"versusTrophies": 1565,
		"clanRank": 23,
		"previousClanRank": 20,
		"donations": 32,
		"donationsReceived": 420
	}],
	"labels": [{
		"id": 56000000,
		"name": "Clan Wars",
		"iconUrls": {
			"small": "https://api-assets.clashofclans.com/labels/64/lXaIuoTlfoNOY5fKcQGeT57apz1KFWkN9-raxqIlMbE.png",
			"medium": "https://api-assets.clashofclans.com/labels/128/lXaIuoTlfoNOY5fKcQGeT57apz1KFWkN9-raxqIlMbE.png"
		}
	}, {
		"id": 56000011,
		"name": "Talkative",
		"iconUrls": {
			"small": "https://api-assets.clashofclans.com/labels/64/T1c8AYalTn_RruVkY0mRPwNYF5n802thTBEEnOtNTMw.png",
			"medium": "https://api-assets.clashofclans.com/labels/128/T1c8AYalTn_RruVkY0mRPwNYF5n802thTBEEnOtNTMw.png"
		}
	}, {
		"id": 56000015,
		"name": "Newbie Friendly",
		"iconUrls": {
			"small": "https://api-assets.clashofclans.com/labels/64/3oOuYkPdkjWVrBUITgByz9Ur0nmJ4GsERXc-1NUrjKg.png",
			"medium": "https://api-assets.clashofclans.com/labels/128/3oOuYkPdkjWVrBUITgByz9Ur0nmJ4GsERXc-1NUrjKg.png"
		}
	}]
}
_JSON;

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getContent')
            ->willReturn($payload);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $response = $this->apiClient->findClanByTag('tag');

        self::assertInstanceOf(Clan::class, $response);
    }

    public function testGetWarLog(): void
    {
        $payload = <<<_JSON
{
	"items": [{
		"result": "win",
		"endTime": "20200917T201529.000Z",
		"teamSize": 30,
		"clan": {
			"tag": "#2PPC8L2QP",
			"name": "Coconu’t",
			"badgeUrls": {
				"small": "https://api-assets.clashofclans.com/badges/70/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
				"large": "https://api-assets.clashofclans.com/badges/512/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
				"medium": "https://api-assets.clashofclans.com/badges/200/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png"
			},
			"clanLevel": 4,
			"attacks": 28,
			"stars": 62,
			"destructionPercentage": 73.36667,
			"expEarned": 143
		},
		"opponent": {
			"tag": "#2PUGGQC8P",
			"name": "THE GREAT CLAN",
			"badgeUrls": {
				"small": "https://api-assets.clashofclans.com/badges/70/d9v5S9tv7nJPKiVj4E_YSVksTLWEITyAUcYwEnPp-rk.png",
				"large": "https://api-assets.clashofclans.com/badges/512/d9v5S9tv7nJPKiVj4E_YSVksTLWEITyAUcYwEnPp-rk.png",
				"medium": "https://api-assets.clashofclans.com/badges/200/d9v5S9tv7nJPKiVj4E_YSVksTLWEITyAUcYwEnPp-rk.png"
			},
			"clanLevel": 2,
			"stars": 27,
			"destructionPercentage": 37.7
		}
	}, {
		"result": "lose",
		"endTime": "20200915T125233.000Z",
		"teamSize": 40,
		"clan": {
			"tag": "#2PPC8L2QP",
			"name": "Coconu’t",
			"badgeUrls": {
				"small": "https://api-assets.clashofclans.com/badges/70/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
				"large": "https://api-assets.clashofclans.com/badges/512/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
				"medium": "https://api-assets.clashofclans.com/badges/200/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png"
			},
			"clanLevel": 4,
			"attacks": 28,
			"stars": 34,
			"destructionPercentage": 39.625,
			"expEarned": 35
		},
		"opponent": {
			"tag": "#Y9ULGVR8",
			"name": "LETPADAN UNITED",
			"badgeUrls": {
				"small": "https://api-assets.clashofclans.com/badges/70/j6ZNFVsqOKdIRKWfWhh6Pv36oiCsTK3JcuQHtiMzQ68.png",
				"large": "https://api-assets.clashofclans.com/badges/512/j6ZNFVsqOKdIRKWfWhh6Pv36oiCsTK3JcuQHtiMzQ68.png",
				"medium": "https://api-assets.clashofclans.com/badges/200/j6ZNFVsqOKdIRKWfWhh6Pv36oiCsTK3JcuQHtiMzQ68.png"
			},
			"clanLevel": 5,
			"stars": 114,
			"destructionPercentage": 96.875
		}
	}],
	"paging": {
		"cursors": {}
	}
}
_JSON;

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getContent')
            ->willReturn($payload);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $response = $this->apiClient->getWarLog(GetWarLogQuery::fromArray([
            'clanTag' => 'sometag',
        ]));

        self::assertInstanceOf(GetWarLogPaginator::class, $response);
    }

    public function testGetClanCurrentWar(): void
    {
        $payload = <<<_JSON
{
	"state": "inWar",
	"teamSize": 30,
	"preparationStartTime": "20200918T060948.000Z",
	"startTime": "20200919T055008.000Z",
	"endTime": "20200920T055008.000Z",
	"clan": {
		"tag": "#2PPC8L2QP",
		"name": "Coconu’t",
		"badgeUrls": {
			"small": "https://api-assets.clashofclans.com/badges/70/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
			"large": "https://api-assets.clashofclans.com/badges/512/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png",
			"medium": "https://api-assets.clashofclans.com/badges/200/LV80YeWOjc9PmqboAQNI_-4uGeLxi85-VZFJvdD5Q9Y.png"
		},
		"clanLevel": 4,
		"attacks": 31,
		"stars": 55,
		"destructionPercentage": 76.7,
		"members": [{
			"tag": "#8PGY02UVY",
			"name": "trirax",
			"townhallLevel": 8,
			"mapPosition": 12,
			"attacks": [{
				"attackerTag": "#8PGY02UVY",
				"defenderTag": "#LUP8GYVPU",
				"stars": 1,
				"destructionPercentage": 96,
				"order": 23
			}, {
				"attackerTag": "#8PGY02UVY",
				"defenderTag": "#LGQ2PQYCY",
				"stars": 2,
				"destructionPercentage": 97,
				"order": 34
			}],
			"opponentAttacks": 2,
			"bestOpponentAttack": {
				"attackerTag": "#PCC082LCJ",
				"defenderTag": "#8PGY02UVY",
				"stars": 2,
				"destructionPercentage": 82,
				"order": 51
			}
		}, {
			"tag": "#LPYU9R9U2",
			"name": "matthieu",
			"townhallLevel": 7,
			"mapPosition": 19,
			"attacks": [{
				"attackerTag": "#LPYU9R9U2",
				"defenderTag": "#LG8VG2LCV",
				"stars": 2,
				"destructionPercentage": 64,
				"order": 10
			}, {
				"attackerTag": "#LPYU9R9U2",
				"defenderTag": "#LUQQGJYVR",
				"stars": 3,
				"destructionPercentage": 100,
				"order": 27
			}],
			"opponentAttacks": 1,
			"bestOpponentAttack": {
				"attackerTag": "#LGQ2PQYCY",
				"defenderTag": "#LPYU9R9U2",
				"stars": 1,
				"destructionPercentage": 63,
				"order": 37
			}
		}]
	},
	"opponent": {
		"tag": "#2Y902980Q",
		"name": "LOS AKATSUKI",
		"badgeUrls": {
			"small": "https://api-assets.clashofclans.com/badges/70/fWervXVIi92UOVSbkcHIcOJBRx63pSr-ExM6M-ZqO54.png",
			"large": "https://api-assets.clashofclans.com/badges/512/fWervXVIi92UOVSbkcHIcOJBRx63pSr-ExM6M-ZqO54.png",
			"medium": "https://api-assets.clashofclans.com/badges/200/fWervXVIi92UOVSbkcHIcOJBRx63pSr-ExM6M-ZqO54.png"
		},
		"clanLevel": 4,
		"attacks": 34,
		"stars": 50,
		"destructionPercentage": 64.0,
		"members": [{
			"tag": "#P0YL88Y92",
			"name": "PapaDios",
			"townhallLevel": 9,
			"mapPosition": 3,
			"attacks": [{
				"attackerTag": "#P0YL88Y92",
				"defenderTag": "#PC0PYQ28U",
				"stars": 3,
				"destructionPercentage": 100,
				"order": 55
			}],
			"opponentAttacks": 0
		}, {
			"tag": "#LUP8GYVPU",
			"name": "ALEX",
			"townhallLevel": 8,
			"mapPosition": 14,
			"attacks": [{
				"attackerTag": "#LUP8GYVPU",
				"defenderTag": "#LL9JPY0UJ",
				"stars": 1,
				"destructionPercentage": 63,
				"order": 46
			}, {
				"attackerTag": "#LUP8GYVPU",
				"defenderTag": "#LV92GYCQL",
				"stars": 1,
				"destructionPercentage": 59,
				"order": 60
			}],
			"opponentAttacks": 1,
			"bestOpponentAttack": {
				"attackerTag": "#8PGY02UVY",
				"defenderTag": "#LUP8GYVPU",
				"stars": 1,
				"destructionPercentage": 96,
				"order": 23
			}
		}, {
			"tag": "#LGQ2PQYCY",
			"name": "Ruddy Ruddy",
			"townhallLevel": 7,
			"mapPosition": 20,
			"attacks": [{
				"attackerTag": "#LGQ2PQYCY",
				"defenderTag": "#Q00U9YLL0",
				"stars": 1,
				"destructionPercentage": 76,
				"order": 31
			}, {
				"attackerTag": "#LGQ2PQYCY",
				"defenderTag": "#LPYU9R9U2",
				"stars": 1,
				"destructionPercentage": 63,
				"order": 37
			}],
			"opponentAttacks": 2,
			"bestOpponentAttack": {
				"attackerTag": "#8PGY02UVY",
				"defenderTag": "#LGQ2PQYCY",
				"stars": 2,
				"destructionPercentage": 97,
				"order": 34
			}
		}]
	}
}
_JSON;

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn(200);
        $response->method('getContent')
            ->willReturn($payload);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        $response = $this->apiClient->getCurrentWar('tag');
        self::assertInstanceOf(CurrentWar::class, $response);
    }

    /**
     * @dataProvider provideBadStatusCodes
     */
    public function testBadStatusCodes(
        int $statusCode,
        string $jsonResponseContent,
        string $expectedMessage,
        string $methodName,
        array $methodArgs
    ): void {
        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')
            ->willReturn($statusCode);
        $response->method('getContent')
            ->willReturn($jsonResponseContent);

        $this->httpClient
            ->method('request')
            ->willReturn($response);

        try {
            \call_user_func_array([$this->apiClient, $methodName], $methodArgs);
        } catch (\Exception $e) {
            if (!$e instanceof UnknownApiErrorException
                && !$e instanceof ApiErrorException
            ) {
                self::fail('exception not expected');
            }

            self::assertEquals($expectedMessage, $e->getMessage());
            self::assertEquals($statusCode, $e->getResponseStatusCode());
        }
    }

    public function provideBadStatusCodes(): ?\Generator
    {
        /*
         * findClanByTag
         */

        yield 'findClanByTag-400' => [
            400,
            self::ERROR_PAYLOADS[400]['payload'],
            self::ERROR_PAYLOADS[400]['message'],
            'findClanByTag',
            ['tag'],
        ];

        yield 'findClanByTag-403' => [
            403,
            self::ERROR_PAYLOADS[403]['payload'],
            self::ERROR_PAYLOADS[403]['message'],
            'findClanByTag',
            ['tag'],
        ];

        yield 'findClanByTag-429' => [
            429,
            self::ERROR_PAYLOADS[429]['payload'],
            self::ERROR_PAYLOADS[429]['message'],
            'findClanByTag',
            ['tag'],
        ];

        yield 'findClanByTag-500' => [
            500,
            self::ERROR_PAYLOADS[500]['payload'],
            self::ERROR_PAYLOADS[500]['message'],
            'findClanByTag',
            ['tag'],
        ];

        yield 'findClanByTag-503' => [
            503,
            self::ERROR_PAYLOADS[503]['payload'],
            self::ERROR_PAYLOADS[503]['message'],
            'findClanByTag',
            ['tag'],
        ];

        /*
         * findLocationByCountryCode
         */

        yield 'findLocationByCountryCode-400' => [
            400,
            self::ERROR_PAYLOADS[400]['payload'],
            self::ERROR_PAYLOADS[400]['message'],
            'findLocationByCountryCode',
            ['FR'],
        ];

        yield 'findLocationByCountryCode-403' => [
            403,
            self::ERROR_PAYLOADS[403]['payload'],
            self::ERROR_PAYLOADS[403]['message'],
            'findLocationByCountryCode',
            ['FR'],
        ];

        yield 'findLocationByCountryCode-429' => [
            429,
            self::ERROR_PAYLOADS[429]['payload'],
            self::ERROR_PAYLOADS[429]['message'],
            'findLocationByCountryCode',
            ['FR'],
        ];

        yield 'findLocationByCountryCode-500' => [
            500,
            self::ERROR_PAYLOADS[500]['payload'],
            self::ERROR_PAYLOADS[500]['message'],
            'findLocationByCountryCode',
            ['FR'],
        ];

        yield 'findLocationByCountryCode-503' => [
            503,
            self::ERROR_PAYLOADS[503]['payload'],
            self::ERROR_PAYLOADS[503]['message'],
            'findLocationByCountryCode',
            ['FR'],
        ];
        /*
         * findLocationByCountryCode
         */

        yield 'searchClans-400' => [
            400,
            self::ERROR_PAYLOADS[400]['payload'],
            self::ERROR_PAYLOADS[400]['message'],
            'searchClans',
            [SearchClansQuery::fromArray([])],
        ];

        yield 'searchClans-403' => [
            403,
            self::ERROR_PAYLOADS[403]['payload'],
            self::ERROR_PAYLOADS[403]['message'],
            'searchClans',
            [SearchClansQuery::fromArray([])],
        ];

        yield 'searchClans-429' => [
            429,
            self::ERROR_PAYLOADS[429]['payload'],
            self::ERROR_PAYLOADS[429]['message'],
            'searchClans',
            [SearchClansQuery::fromArray([])],
        ];

        yield 'searchClans-500' => [
            500,
            self::ERROR_PAYLOADS[500]['payload'],
            self::ERROR_PAYLOADS[500]['message'],
            'searchClans',
            [SearchClansQuery::fromArray([])],
        ];

        yield 'searchClans-503' => [
            503,
            self::ERROR_PAYLOADS[503]['payload'],
            self::ERROR_PAYLOADS[503]['message'],
            'searchClans',
            [SearchClansQuery::fromArray([])],
        ];
    }

}
