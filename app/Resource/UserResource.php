<?php
declare(strict_types=1);

namespace App\Resource;

use App\Entity\User;
use MonkeysLegion\Http\Message\Response;

/**
 * JSON:API resource for User entity.
 */
final class UserResource
{
    /**
     * Transform a single user to JSON:API format.
     *
     * @return array<string, mixed>
     */
    public static function toArray(User $user): array
    {
        return [
            'id'         => $user->id,
            'type'       => 'users',
            'attributes' => [
                'email'         => $user->email,
                'name'          => $user->name,
                'is_verified'   => $user->isVerified,
                'created_at'    => $user->created_at->format('c'),
                'updated_at'    => $user->updated_at->format('c'),
            ],
        ];
    }

    public static function make(User $user, int $status = 200): Response
    {
        return Response::json(['data' => self::toArray($user)], $status);
    }

    /**
     * @param list<User> $users
     */
    public static function collection(array $users, int $status = 200): Response
    {
        $data = array_map(self::toArray(...), $users);

        return Response::json([
            'data'  => $data,
            'meta'  => ['total' => count($data)],
        ], $status);
    }
}
