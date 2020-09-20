ClashOfClans API Client
=======================

(no stable version yet)

Client make requests to the ClashOfClans game API. 

The API token can be requested on the [developer website](https://developer.clashofclans.com) 


## Install

```bash
    composer require fivem/clashofclans:@dev
```

## Usage

```php
    define('API_KEY', '', false);

    $client = new \Fivem\ClashOfClans\ApiClient(
        new \Fivem\ClashOfClans\HttpClientFactory\SymfonyHttpClientFactory(),
        API_KEY
    );

    $clans = $client->searchClans(\Fivem\ClashOfClans\Query\SearchClansQuery::fromArray([
        'limit' => 10,
        'name' => 'coconut',
        'locationId' => $client->findLocationByCountryCode('FR')->id
    ]));
    
    foreach($clans as $clan) {
        echo sprintf('Clan name : %s', $clan->name) . PHP_EOL;
    }
```

## Run the tests

```bash
    vendor/bin/phpunit -v
```
