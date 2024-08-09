<?php

namespace Database\Seeders;

use App\Models\Comment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //
        Comment::create([
            'content' => 'test', 'ticket_id' => 1, 'user_id' => 1
        ]);
        Comment::create([
            'content' => 'demo', 'ticket_id' => 1, 'user_id' => 1
        ]);
    }
}
