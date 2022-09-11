<?php

namespace App\Request;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

class MediaRequest extends BaseRequest
{
    use ValidateRequestTrait;

    #[Assert\File(mimeTypes: ["image/jpeg", "image/png"])]
    public readonly ?UploadedFile $media;

    protected function autoValidateRequest(): bool
    {
        return true;
    }
}