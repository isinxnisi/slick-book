<?php

namespace Modules\ContentModule\Samples\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Modules\ContentModule\Core\Application\Services\ContentService;

class ContentFrontController extends Controller
{
    public function __construct(
        private ContentService $contentService
    ) {}

    /**
     * @param string $slug
     */
    public function show(string $slug)
    {
        $entity = $this->contentService->findBySlug($slug)
               ?? throw new NotFoundHttpException();

        if ($entity->getStatus() !== 'published') {
            throw new NotFoundHttpException();
        }

        $strategy = $this->contentService->resolveStrategy($entity->getContentType(), $entity->getContentKind());

        return view('content-module::front.contents.show', compact('entity', 'strategy'));
    }
}
