<?php

declare(strict_types=1);

namespace Fivem\ClashOfClans;

use Fivem\ClashOfClans\Exception\ApiClientExceptionInterface;
use Fivem\ClashOfClans\Exception\ApiError\ApiErrorException;
use Fivem\ClashOfClans\Exception\ApiError\UnknownApiErrorException;
use Fivem\ClashOfClans\Exception\DeserializationException;
use Fivem\ClashOfClans\HttpClientFactory\HttpClientFactory;
use Fivem\ClashOfClans\Model\ApiError;
use Fivem\ClashOfClans\Model\Clan\Clan;
use Fivem\ClashOfClans\Model\CurrentWar\CurrentWar;
use Fivem\ClashOfClans\Model\Location;
use Fivem\ClashOfClans\Model\Paginator\GetWarLogPaginator;
use Fivem\ClashOfClans\Model\Paginator\ListLocationsPaginator;
use Fivem\ClashOfClans\Model\Paginator\SearchClansPaginator;
use Fivem\ClashOfClans\Query\GetWarLogQuery;
use Fivem\ClashOfClans\Query\ListLocationsQuery;
use Fivem\ClashOfClans\Query\SearchClansQuery;
use Doctrine\Common\Annotations\AnnotationReader;
use Symfony\Component\PropertyAccess\PropertyAccessor;
use Symfony\Component\PropertyInfo\Extractor\PhpDocExtractor;
use Symfony\Component\PropertyInfo\Extractor\ReflectionExtractor;
use Symfony\Component\PropertyInfo\PropertyInfoExtractor;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Exception\InvalidArgumentException as SerializerInvalidArgumentException;
use Symfony\Component\Serializer\Mapping\Factory\ClassMetadataFactory;
use Symfony\Component\Serializer\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\ArrayDenormalizer;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Normalizer\PropertyNormalizer;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;

class ApiClient
{
    private const BASE_URL = 'https://api.clashofclans.com/v1/';

    private SerializerInterface $serializer;
    private HttpClientInterface $httpClient;
    private ?ResponseInterface $lastResponse;

    public function __construct(
        HttpClientFactory $httpClientFactory,
        string $apiKey
    ) {
        $this->httpClient = $httpClientFactory->build(self::BASE_URL, $apiKey);

        $extractor = new PropertyInfoExtractor([], [new PhpDocExtractor(), new ReflectionExtractor()]);
        $classMetadataFactory = new ClassMetadataFactory(new AnnotationLoader(new AnnotationReader()));
        $this->serializer = new Serializer([
            new ObjectNormalizer(
                $classMetadataFactory, null, new PropertyAccessor(), $extractor
            ),
            new PropertyNormalizer(),
            new ArrayDenormalizer(),
        ], [
            new JsonEncoder(),
        ]);

        $this->lastResponse = null;
    }

    /**
     * @throws ApiClientExceptionInterface
     */
    public function searchClans(SearchClansQuery $query): SearchClansPaginator
    {
        /** @var SearchClansPaginator $response */
        $response = $this->doGetRequest(
            SearchClansPaginator::class,
            'clans',
            $query->toArray()
        );

        return $response;
    }

    /**
     * @throws ApiClientExceptionInterface
     */
    public function listLocations(ListLocationsQuery $query): ListLocationsPaginator
    {
        /** @var ListLocationsPaginator $response */
        $response = $this->doGetRequest(
            ListLocationsPaginator::class,
            'locations',
            $query->toArray()
        );

        return $response;
    }

    /**
     * @throws ApiClientExceptionInterface
     */
    public function findLocationByCountryCode(string $countryCode): ?Location
    {
        $items = array_filter(
            $this->listLocations(ListLocationsQuery::fromArray([]))->items,
            static function (Location $location) use ($countryCode) {
                return $location->isCountry && ($location->countryCode === $countryCode);
            });

        if (\count($items) > 1) {
            throw new \UnexpectedValueException(sprintf(
                'There is more than one location matching the country code "%s"',
                $countryCode
            ));
        }

        return $items ? reset($items) : null;
    }

    public function findClanByTag(string $clanTag): ?Clan
    {
        /** @var \Fivem\ClashOfClans\Model\Clan\Clan $response */
        $response = $this->doGetRequest(
            Clan::class,
            sprintf('clans/%s', urlencode($clanTag)),
            []
        );

        return $response;
    }

    public function getWarLog(GetWarLogQuery $query): ?GetWarLogPaginator
    {
        /** @var GetWarLogPaginator $response */
        $response = $this->doGetRequest(
            GetWarLogPaginator::class,
            sprintf('clans/%s/warlog', urlencode($query->clanTag)),
            []
        );

        return $response;
    }

    public function getCurrentWar(string $clanTag): ?CurrentWar
    {
        /** @var CurrentWar $response */
        $response = $this->doGetRequest(
            CurrentWar::class,
            sprintf('clans/%s/currentwar', urlencode($clanTag)),
            []
        );

        return $response;
    }

    public function getLastResponse(): ResponseInterface
    {
        return $this->lastResponse;
    }

    public function createExceptionFromResponse(ResponseInterface $response): ApiClientExceptionInterface
    {
        $responseStatusCode = $response->getStatusCode();
        $responseContent = $response->getContent(false);

        try {
            /** @var ApiError $apiError */
            $apiError = $this->serializer->deserialize(
                $responseContent,
                ApiError::class,
                JsonEncoder::FORMAT,
                [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => true,
                ]
            );

            return new ApiErrorException($responseStatusCode, $responseContent, $apiError);
        } catch (\Exception $e) {
            return new UnknownApiErrorException($responseStatusCode, $responseContent, $e);
        }
    }

    /**
     * @throws ApiClientExceptionInterface
     * @throws DeserializationException
     */
    private function doGetRequest(string $responseClassName, string $url, array $data): object
    {
        $this->lastResponse = $this->httpClient->request('GET', $url, [
            'query' => $data,
        ]);

        if (200 !== $this->lastResponse->getStatusCode()) {
            throw $this->createExceptionFromResponse($this->lastResponse);
        }

        try {
            $apiResponse = $this->serializer->deserialize(
                $this->lastResponse->getContent(false),
                $responseClassName,
                JsonEncoder::FORMAT,
                [
                    AbstractNormalizer::ALLOW_EXTRA_ATTRIBUTES => false,
                ]
            );
        } catch (SerializerInvalidArgumentException $e) {
            foreach ($e->getTrace() as $trace) {
                if (!isset($trace['class'], $trace['function'], $trace['args'])
                    || (AbstractObjectNormalizer::class !== $trace['class'])
                    || ('validateAndDenormalize' !== $trace['function'])
                ) {
                    continue;
                }

                $args = $trace['args'];
                throw new DeserializationException($args[0], $args[1], $args[2]);
            }

            throw $e;
        }

        return $apiResponse;
    }
}
