<?php

namespace Tests\Feature\Blog;

use App\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Mrchimp\Chimpcom\Models\File;
use Tests\TestCase;

class BlogShowTest extends TestCase
{
    use DatabaseMigrations;

    /** @test */
    public function blog_post_can_be_viewed()
    {
        User::factory()->create([
            'name' => 'mrchimp'
        ]);
        $blog_dir = $this->createPath('/home/mrchimp/blog');
        $post = File::factory()->create([
            'name' => 'postname',
            'content' => 'Here is a post',
        ]);
        $blog_dir->files()->save($post);

        $this->get('blog/mrchimp/postname')
            ->assertStatus(200)
            ->assertSee('Here is a post');
    }

    /** @test */
    public function blog_post_returns_404_if_path_is_invalid()
    {
        $this->get('blog/!£$!£$!£$!£$/postname')
            ->assertStatus(404);
    }

    /** @test */
    public function blog_post_returns_404_if_path_does_not_exist()
    {
        $this->get('blog/unknownuser/post')
            ->assertStatus(404);
    }

    /** @test */
    public function blog_post_returns_404_if_path_is_a_directory()
    {
        User::factory()->create([
            'name' => 'mrchimp'
        ]);
        $this->createPath('/home/mrchimp/blog/post');

        $this->get('blog/mrchimp/post')
            ->assertStatus(404);
    }
}
