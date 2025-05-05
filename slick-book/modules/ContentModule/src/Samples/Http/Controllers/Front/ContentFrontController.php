<?php

namespace Modules\ContentModule\Infrastructure\Http\Controllers\Front;

use Illuminate\Routing\Controller;
use Modules\ContentModule\Application\Services\ContentService;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

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
