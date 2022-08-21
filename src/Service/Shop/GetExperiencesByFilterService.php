<?php

namespace App\Service\Shop;

use App\Repository\EventRepository;
use App\Repository\ExperienceRepository;
use Exception;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetExperiencesByFilterService
{
    use GetExpectedExperienceData;

    private array $validFilters = [
        'host_id',
        'category_id',
        'purchasable',
    ];

    /**
     * @throws Exception
     */
    public function getExperience(
        $filters,
        EventRepository $eventRepository,
        ExperienceRepository $experienceRepository,

    ): JsonResponse|array
    {
        foreach ($filters as $filter => $value) {
            if (!in_array($filter, $this->validFilters)) {
                return new JsonResponse(['error' => 'Invalid filter'], 400);
            } else {
                if ($filter == 'host_id') {
                    $result['Filtered By Host Id'] =
                        self::parse($experienceRepository->ExperienceFilterByHostId((int)$value));
                } else if ($filter == 'category_id') {
                    $result['Filtered By Category Id'] =
                        self::parse($experienceRepository->ExperienceFilterByCategoryId((int)$value));
                } else {
                    $result['Purchasable'] = self::parse($eventRepository->purchasableEvents());
                }
            }
        }
        return $result;

    }

}