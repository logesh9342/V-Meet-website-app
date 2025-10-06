<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ChatRoom;

class ChatRoomSeeder extends Seeder
{
    public function run()
    {
        ChatRoom::create(['name' => 'General', 'type' => 'group']);
        ChatRoom::create(['name' => 'Team Meeting', 'type' => 'meeting']);
        ChatRoom::create(['name' => 'Direct Chat', 'type' => 'direct']);
    }
}
