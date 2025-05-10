<?php
namespace Database\Seeders;
use App\Models\User;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create an admin user
        $admin = User::create([
            'firstname' => 'Admin',
            'lastname' => 'User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Create a regular user
        $user = User::create([
            'firstname' => 'John',
            'lastname' => 'Doe',
            'email' => 'user@example.com',
            'password' => bcrypt('password'),
            'role' => 'user',
        ]);

        // Create posts
        $post1 = Post::create([
            'title' => 'Introduction to Laravel',
            'content' => 'This is a blog post about Laravel.',
            'user_id' => $admin->id,
            'status' => 'published',
            'categories' => ['Technology', 'Programming'],
            'tags' => ['Laravel', 'PHP'],
        ]);

        $post2 = Post::create([
            'title' => 'Flutter for Beginners',
            'content' => 'Learn how to build apps with Flutter.',
            'user_id' => $admin->id,
            'status' => 'published',
            'categories' => ['Mobile', 'Programming'],
            'tags' => ['Flutter', 'Dart'],
        ]);

        // Create comments
        Comment::create([
            'content' => 'Great post!',
            'user_id' => $user->id,
            'post_id' => $post1->id,
            'status' => 'approved',
        ]);

        Comment::create([
            'content' => 'Very helpful, thanks!',
            'user_id' => $user->id,
            'post_id' => $post2->id,
            'status' => 'pending',
        ]);
    }
}