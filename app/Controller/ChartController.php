<?php

declare(strict_types=1);

namespace Energycalculator\Controller;

use Chubbyphp\Deserialization\DeserializerInterface;
use Chubbyphp\Security\Authentication\AuthenticationInterface;
use Chubbyphp\Security\Authorization\AuthorizationInterface;
use Energycalculator\ErrorHandler\ErrorResponseHandler;
use Energycalculator\Model\DateRange;
use Energycalculator\Model\Day;
use Energycalculator\Repository\DayRepository;
use Energycalculator\Service\TwigRender;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

final class ChartController
{
    /**
     * @var AuthenticationInterface
     */
    private $authentication;

    /**
     * @var AuthorizationInterface
     */
    private $authorization;

    /**
     * @var DayRepository
     */
    private $dayRepository;

    /**
     * @var DeserializerInterface
     */
    private $deserializer;

    /**
     * @var ErrorResponseHandler
     */
    private $errorResponseHandler;

    /**
     * @var TwigRender
     */
    private $twig;

    /**
     * @param AuthenticationInterface $authentication
     * @param AuthorizationInterface  $authorization
     * @param DayRepository           $dayRepository
     * @param DeserializerInterface   $deserializer
     * @param ErrorResponseHandler    $errorResponseHandler
     * @param TwigRender              $twig
     */
    public function __construct(
        AuthenticationInterface $authentication,
        AuthorizationInterface $authorization,
        DayRepository $dayRepository,
        DeserializerInterface $deserializer,
        ErrorResponseHandler $errorResponseHandler,
        TwigRender $twig
    ) {
        $this->authentication = $authentication;
        $this->authorization = $authorization;
        $this->dayRepository = $dayRepository;
        $this->deserializer = $deserializer;
        $this->errorResponseHandler = $errorResponseHandler;
        $this->twig = $twig;
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function weight(Request $request, Response $response): Response
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'CHART_WEIGHT')) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                'comestible.error.permissiondenied'
            );
        }

        $dateRange = new DateRange();
        $dateRange->setFrom(new \DateTime('-1week'));
        $dateRange->setTo(new \DateTime('now'));

        /** @var DateRange $dateRange */
        $dateRange = $this->deserializer->deserializeByObject($request->getQueryParams(), $dateRange);

        $from = $dateRange->getFrom();
        $to = $dateRange->getTo();

        $days = $this->dayRepository->getInRange($from, $to, $authenticatedUser);

        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minWeight = $this->getMinWeight($days);
        $maxWeight = $this->getMaxWeight($days);

        return $this->twig->render($response, '@Energycalculator/chart/weight.html.twig',
            $this->twig->aggregate($request, [
                'dateRange' => $dateRange,
                'alldays' => $allDays,
                'minweight' => $minWeight,
                'maxweight' => $maxWeight,
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function calorie(Request $request, Response $response): Response
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'CHART_CALORIE')) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                'comestible.error.permissiondenied'
            );
        }

        $dateRange = new DateRange();
        $dateRange->setFrom(new \DateTime('-1week'));
        $dateRange->setTo(new \DateTime('now'));

        /** @var DateRange $dateRange */
        $dateRange = $this->deserializer->deserializeByObject($request->getQueryParams(), $dateRange);

        $from = $dateRange->getFrom();
        $to = $dateRange->getTo();

        $days = $this->dayRepository->getInRange($from, $to, $authenticatedUser);

        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minCalorie = $this->getMinCalorie($days);
        $maxCalorie = $this->getMaxCalorie($days);

        return $this->twig->render($response, '@Energycalculator/chart/calorie.html.twig',
            $this->twig->aggregate($request, [
                'dateRange' => $dateRange,
                'alldays' => $allDays,
                'mincalorie' => $minCalorie,
                'maxcalorie' => $maxCalorie,
            ])
        );
    }

    /**
     * @param Request  $request
     * @param Response $response
     *
     * @return Response
     */
    public function energymix(Request $request, Response $response): Response
    {
        $authenticatedUser = $this->authentication->getAuthenticatedUser($request);

        if (!$this->authorization->isGranted($authenticatedUser, 'CHART_ENERGYMIX')) {
            return $this->errorResponseHandler->errorReponse(
                $request,
                $response,
                403,
                'comestible.error.permissiondenied'
            );
        }

        $dateRange = new DateRange();
        $dateRange->setFrom(new \DateTime('-1week'));
        $dateRange->setTo(new \DateTime('now'));

        /** @var DateRange $dateRange */
        $dateRange = $this->deserializer->deserializeByObject($request->getQueryParams(), $dateRange);

        $from = $dateRange->getFrom();
        $to = $dateRange->getTo();

        $days = $this->dayRepository->getInRange($from, $to, $authenticatedUser);

        $allDays = $this->getDaysOrNull($days, $from, $to);

        $minEnergyMix = $this->getMinEnergyMix($days);
        $maxEnergyMix = $this->getMaxEnergyMix($days);

        return $this->twig->render($response, '@Energycalculator/chart/energymix.html.twig',
            $this->twig->aggregate($request, [
                'dateRange' => $dateRange,
                'alldays' => $allDays,
                'minenergymix' => $minEnergyMix,
                'maxenergymix' => $maxEnergyMix,
            ])
        );
    }

    /**
     * @param Day[]     $days
     * @param \DateTime $from
     * @param \DateTime $to
     *
     * @return Day[]
     */
    private function getDaysOrNull(array $days, \DateTime $from, \DateTime $to): array
    {
        $from = clone $from;

        $daysPerDate = array();
        foreach ($days as $day) {
            $daysPerDate[$day->getDate()->format('d.m.Y')] = $day;
        }

        $return = array();

        while ($from->format('Ymd') <= $to->format('Ymd')) {
            $fromAsString = $from->format('d.m.Y');
            $return[$fromAsString] = isset($daysPerDate[$fromAsString]) ? $daysPerDate[$fromAsString] : null;
            $from->modify('+1day');
        }

        return $return;
    }

    /**
     * @param Day[] $days
     *
     * @return float
     */
    private function getMinWeight(array $days): float
    {
        $minWeight = null;
        foreach ($days as $day) {
            if (null === $minWeight || $day->getWeight() < $minWeight) {
                $minWeight = $day->getWeight();
            }
        }

        return null !== $minWeight ? $minWeight : 0;
    }

    /**
     * @param Day[] $days
     *
     * @return float
     */
    private function getMaxWeight(array $days): float
    {
        $maxWeight = null;
        foreach ($days as $day) {
            if (null === $maxWeight || $day->getWeight() > $maxWeight) {
                $maxWeight = $day->getWeight();
            }
        }

        return null !== $maxWeight ? $maxWeight : 500;
    }

    /**
     * @param Day[] $days
     *
     * @return float
     */
    private function getMinCalorie(array $days): float
    {
        $minCalorie = null;
        foreach ($days as $day) {
            if (null === $minCalorie || $day->getCalorie() < $minCalorie) {
                $minCalorie = $day->getCalorie();
            }
        }

        return null !== $minCalorie ? $minCalorie : 0;
    }

    /**
     * @param Day[] $days
     *
     * @return float
     */
    private function getMaxCalorie(array $days): float
    {
        $maxCalorie = null;
        foreach ($days as $day) {
            if (null === $maxCalorie || $day->getCalorie() > $maxCalorie) {
                $maxCalorie = $day->getCalorie();
            }
        }

        return null !== $maxCalorie ? $maxCalorie : 10000;
    }

    /**
     * @param Day[] $days
     *
     * @return float
     */
    private function getMinEnergyMix(array $days): float
    {
        $minEnergyMix = null;
        foreach ($days as $day) {
            if (null === $minEnergyMix || $day->getProtein() < $minEnergyMix) {
                $minEnergyMix = $day->getProtein();
            }
            if (null === $minEnergyMix || $day->getCarbohydrate() < $minEnergyMix) {
                $minEnergyMix = $day->getCarbohydrate();
            }
            if (null === $minEnergyMix || $day->getFat() < $minEnergyMix) {
                $minEnergyMix = $day->getFat();
            }
        }

        return null !== $minEnergyMix ? $minEnergyMix : 0;
    }

    /**
     * @param Day[] $days
     *
     * @return float
     */
    private function getMaxEnergyMix(array $days): float
    {
        $maxEnergyMix = null;
        foreach ($days as $day) {
            if (null === $maxEnergyMix || $day->getProtein() > $maxEnergyMix) {
                $maxEnergyMix = $day->getProtein();
            }
            if (null === $maxEnergyMix || $day->getCarbohydrate() > $maxEnergyMix) {
                $maxEnergyMix = $day->getCarbohydrate();
            }
            if (null === $maxEnergyMix || $day->getFat() > $maxEnergyMix) {
                $maxEnergyMix = $day->getFat();
            }
        }

        return null !== $maxEnergyMix ? $maxEnergyMix : 1000;
    }
}
