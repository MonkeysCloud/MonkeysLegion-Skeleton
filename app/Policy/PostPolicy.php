<?php
declare(strict_types=1);

namespace App\Policy;

use App\Entity\Post;
use App\Entity\User;

/**
 * Authorization policy for Post operations.
 */
final class PostPolicy
{
    /**
     * Can the user update this post?
     */
    public function update(User $user, Post $post): bool
    {
        // Authors can edit their own posts, admins can edit any
        return $user->id === $post->author->id
            || $user->hasRole('admin');
    }

    /**
     * Can the user delete this post?
     */
    public function delete(User $user, Post $post): bool
    {
        return $user->hasRole('admin');
    }

    /**
     * Can the user publish this post?
     */
    public function publish(User $user, Post $post): bool
    {
        return $user->id === $post->author->id
            || $user->hasRole('admin')
            || $user->hasRole('editor');
    }
}
