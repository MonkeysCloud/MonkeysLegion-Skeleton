<?php
declare(strict_types=1);

namespace App\Controller\Api;

use MonkeysLegion\Router\Attributes\Route;
use MonkeysLegion\Router\Attributes\RoutePrefix;
use MonkeysLegion\Router\Attributes\Middleware;
use MonkeysLegion\Auth\Attribute\Authenticated;
use MonkeysLegion\Http\Message\Response;

use Psr\Http\Message\ServerRequestInterface;

use App\Dto\CreatePostRequest;
use App\Resource\PostResource;
use App\Service\PostService;
use App\Repository\PostRepository;

/**
 * Blog post API.
 */
#[RoutePrefix('/api/v2/posts')]
#[Middleware(['cors'])]
final class PostController
{
    public function __construct(
        private readonly PostService $service,
        private readonly PostRepository $posts,
    ) {}

    #[Route('GET', '/', name: 'posts.index', summary: 'List published posts', tags: ['Posts'])]
    public function index(ServerRequestInterface $request): Response
    {
        $search = $request->getQueryParams()['q'] ?? null;

        $posts = $search !== null
            ? $this->posts->search($search)
            : $this->posts->findPublished();

        return PostResource::collection($posts);
    }

    #[Route('GET', '/{id:\d+}', name: 'posts.show', summary: 'Get a post by ID', tags: ['Posts'])]
    public function show(ServerRequestInterface $request, string $id): Response
    {
        $post = $this->posts->findOrFail((int) $id);

        return PostResource::make($post);
    }

    #[Route('POST', '/', name: 'posts.create', summary: 'Create a new post', tags: ['Posts'])]
    #[Authenticated]
    public function create(
        CreatePostRequest $dto,
        ServerRequestInterface $request,
    ): Response {
        $userId = $request->getAttribute('userId');
        $author = $this->service->findPost($userId); // Will be resolved via DI

        // For now, create with the authenticated user context
        $post = $this->service->createPost($dto, $request->getAttribute('user'));

        return PostResource::make($post, 201);
    }

    #[Route('POST', '/{id:\d+}/publish', name: 'posts.publish', summary: 'Publish a post', tags: ['Posts'])]
    #[Authenticated]
    public function publish(string $id): Response
    {
        $post = $this->service->publish((int) $id);

        return PostResource::make($post);
    }

    #[Route('DELETE', '/{id:\d+}', name: 'posts.destroy', summary: 'Delete a post', tags: ['Posts'])]
    #[Authenticated]
    public function destroy(string $id): Response
    {
        $this->service->deletePost((int) $id);

        return Response::noContent();
    }
}
