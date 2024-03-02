<?php

namespace App\Controller;

use App\Entity\Image;
use App\Enum\AttachmentType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Vich\UploaderBundle\Mapping\Annotation\Uploadable;

class CreateImageObjectAction extends AbstractController
{
    public function __invoke(Request $request): Image
    {
        $uploadedFile = $request->files->get('file');
        if (!$uploadedFile) {
            throw new BadRequestHttpException('"file" is required');
        }
        $imageObject = new Image();
        $imageObject->file = $uploadedFile;
        $imageObject->type = AttachmentType::from($request->get('type'));
        $this->save($imageObject);

        return $imageObject;
    }
}
