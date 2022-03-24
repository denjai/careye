<?php

declare(strict_types=1);

namespace App\Services;

use App\Entity\Car;
use App\Entity\CarResult;
use App\Entity\Image;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

class CarResultTransformer
{
    public function transform(CarResult $carResult): Car
    {
        return (new Car())
            ->setTitle($carResult->getTitle())
            ->setPrice($carResult->getPrice())
            ->setCreated($carResult->getCratedAt())
            ->setUpdated($carResult->getUpdatedAt())
            ->setRemoteId($carResult->getRemoteId())
            ->setSource($carResult->getSource())
        ;
    }

    public function transformImages(CarResult $carResult): Collection
    {
        $images = new ArrayCollection();
        foreach ($carResult->getImages() as $image) {
            $content = file_get_contents($image);
            if ($content === false) {
                continue;
            }

            $images[] = (new Image())
                ->setExtension($this->getExtension($image))
                ->setImage(base64_encode($content))
            ;
        }

        return $images;
    }

    private function getExtension(string $image)
    {
        $info = getimagesize($image);
        return trim(image_type_to_extension($info[2]), '.');
    }
}