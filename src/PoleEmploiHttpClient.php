<?php

namespace MrDlef\PoleEmploi;

use Symfony\Component\HttpClient\Exception\ClientException;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PoleEmploiHttpClient
{
    private ?HttpClientInterface $apiClient = null;

    private function authenticate(): self
    {
        $httpQueryParams = [
            'realm' => '/partenaire',
        ];
        $httpResponse = HttpClient::createForBaseUri('https://entreprise.pole-emploi.fr')->request(
            'POST',
            sprintf('/connexion/oauth2/access_token?%s', http_build_query($httpQueryParams)),
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'body' => [
                    'grant_type' => 'client_credentials',
                    'client_id' => getenv('POLE_EMPLOI_CLIENT_ID'),
                    'client_secret' => getenv('POLE_EMPLOI_CLIENT_SECRET'),
                    'scope' => implode(' ', [
                        'api_rome-metiersv1',
                        'nomenclatureRome',
                    ]),
                ],
            ]
        );
        $content = json_decode($httpResponse->getContent());
        $accessToken = $content->access_token ?? null;
        $tokenType = $content->token_type ?? null;

        $this->apiClient = HttpClient::createForBaseUri('https://api.pole-emploi.io', [
            'headers' => [
                'Authorization' => sprintf('%s %s', $tokenType, $accessToken),
            ],
        ]);

        return $this;
    }

    private function onError(ClientException $e): void
    {
        $code = $e->getCode();
        var_dump($code);
        $content = json_decode($e->getResponse()->getContent());
        var_dump($content);
    }

    private function getCollection(string $uri)
    {
        if (null === $this->apiClient) {
            $this->authenticate();
        }
        try {
            $httpResponse = $this->apiClient->request('GET', $uri);

            // TODO: Add better way to avoid 429 error
            sleep(1);

            return json_decode($httpResponse->getContent());
        } catch (ClientException $e) {
            $this->onError($e);
        }

        return [];
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-th%C3%A8mes
     */
    public function themes(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/theme');
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-m%C3%A9tiers
     */
    public function professions(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/metier');
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-grands-domaines
     */
    public function mainProfessionalFields(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/grand-domaine');
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-domaines-professionnels
     */
    public function professionalFields(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/domaine-professionnel');
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-appellations
     */
    public function professionNames(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/appellation');
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-centres-d-int%C3%A9r%C3%AAts
     */
    public function careerInterests(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/centre-interet');
    }

    /**
     * @see https://pole-emploi.io/data/api/rome-4-0-metiers?tabgroup-api=documentation&doc-section=api-doc-section-lister-les-secteurs-d-activit%C3%A9s
     */
    public function activitySectors(): array
    {
        return $this->getCollection('/partenaire/rome-metiers/v1/metiers/secteur-activite');
    }
}