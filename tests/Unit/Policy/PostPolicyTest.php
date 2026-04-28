<?php
declare(strict_types=1);

namespace Tests\Unit\Policy;

use App\Entity\Post;
use App\Entity\User;
use App\Policy\PostPolicy;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

#[CoversClass(PostPolicy::class)]
final class PostPolicyTest extends TestCase
{
    private PostPolicy $policy;

    protected function setUp(): void
    {
        $this->policy = new PostPolicy();
    }

    private function makeUser(string $role = 'user'): User
    {
        $user = new User();
        $user->name = 'Test';
        $user->email = 'test@test.com';
        $user->password_hash = 'hash';
        // We need to set roles via reflection since $roles is protected
        $ref = new \ReflectionProperty($user, 'roles');
        $ref->setValue($user, [$role]);
        return $user;
    }

    private function makePost(User $author): Post
    {
        $post = new Post();
        $post->title = 'Test';
        $post->body = 'Body';
        $post->slug = 'test';
        $post->author = $author;
        return $post;
    }

    #[Test]
    public function authorCanUpdateOwnPost(): void
    {
        $author = $this->makeUser();
        // Set ID via reflection
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);

        $post = $this->makePost($author);

        $this->assertTrue($this->policy->update($author, $post));
    }

    #[Test]
    public function nonAuthorCannotUpdatePost(): void
    {
        $author = $this->makeUser();
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);

        $other = $this->makeUser();
        $ref2 = new \ReflectionProperty($other, 'id');
        $ref2->setValue($other, 2);

        $post = $this->makePost($author);

        $this->assertFalse($this->policy->update($other, $post));
    }

    #[Test]
    public function adminCanUpdateAnyPost(): void
    {
        $author = $this->makeUser();
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);

        $admin = $this->makeUser('admin');
        $ref2 = new \ReflectionProperty($admin, 'id');
        $ref2->setValue($admin, 99);

        $post = $this->makePost($author);

        $this->assertTrue($this->policy->update($admin, $post));
    }

    #[Test]
    public function onlyAdminCanDelete(): void
    {
        $author = $this->makeUser();
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);
        $post = $this->makePost($author);

        $this->assertFalse($this->policy->delete($author, $post));

        $admin = $this->makeUser('admin');
        $this->assertTrue($this->policy->delete($admin, $post));
    }

    #[Test]
    public function authorCanPublishOwnPost(): void
    {
        $author = $this->makeUser();
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);
        $post = $this->makePost($author);

        $this->assertTrue($this->policy->publish($author, $post));
    }

    #[Test]
    public function editorCanPublishAnyPost(): void
    {
        $author = $this->makeUser();
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);

        $editor = $this->makeUser('editor');
        $ref2 = new \ReflectionProperty($editor, 'id');
        $ref2->setValue($editor, 2);

        $post = $this->makePost($author);

        $this->assertTrue($this->policy->publish($editor, $post));
    }

    #[Test]
    public function regularUserCannotPublishOthersPost(): void
    {
        $author = $this->makeUser();
        $ref = new \ReflectionProperty($author, 'id');
        $ref->setValue($author, 1);

        $other = $this->makeUser();
        $ref2 = new \ReflectionProperty($other, 'id');
        $ref2->setValue($other, 2);

        $post = $this->makePost($author);

        $this->assertFalse($this->policy->publish($other, $post));
    }
}
