<?php

namespace App\Service\Shop;

use App\Repository\ExperienceRepository;
use Symfony\Component\HttpFoundation\JsonResponse;

class GetExperiencesByFilterService
{
    use ApplyExperienceDOTTrait;

    private array $validFilters = [
        'host_id',
        'category_id',
        'purchasable',
    ];

    public function getExperience(
        $filters,
        ExperienceRepository $experienceRepository,

    ): JsonResponse|array
    {
        foreach ($filters as $filter => $value) {
            if (!in_array($filter, $this->validFilters)) {
                $result['ok'] = false;
                $result['message'] = 'Invalid filter';
                $result['status'] = 400;
                return $result;
            } else {
                if ($filter == 'host_id') {
                    $events['Filtered By Host Id'] =
                        self::parse($experienceRepository->ExperienceFilterByHostId((int)$value));

                } else if ($filter == 'category_id') {
                    $events['Filtered By Category Id'] =
                        self::parse($experienceRepository->ExperienceFilterByCategoryId((int)$value));
                } else {
                    $events['purchasable'] = self::parse($experienceRepository->purchasableExperience());
                }
                $result['data'] = $events;
            }
        }
        $result['ok'] = true;
        $result['message'] = 'Experiences retrieved successfully.';
        $result['status'] = 200;
        return $result;

    }

}