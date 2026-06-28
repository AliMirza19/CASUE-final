<?php

namespace Database\Seeders;

use App\Models\Announcement;
use App\Models\User;
use App\Models\RoleAssignment;
use Illuminate\Database\Seeder;

class AnnouncementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing announcements
        Announcement::truncate();

        // Find HOD, Patron, and President users
        $hodUserId = RoleAssignment::where('role', 'hod')->pluck('user_id')->first();
        $hodUser = $hodUserId ? User::find($hodUserId) : User::where('role', 'admin')->first();

        $patronUserId = RoleAssignment::where('role', 'patron')->pluck('user_id')->first();
        $patronUser = $patronUserId ? User::find($patronUserId) : User::where('role', 'faculty')->first();

        $presidentUser = User::where('role', 'president')->first() ?? User::where('role', 'admin')->first();

        $announcements = [
            // HOD Announcements
            [
                'user_id' => $hodUser->id,
                'title' => 'Term 2026 Fall Budgets Locked',
                'description' => 'All society budgets for the Fall 2026 term have been finalized and locked. No further modifications will be accepted without HOD approval.',
                'image_url' => 'https://images.unsplash.com/photo-1554224155-6726b3ff858f?auto=format&fit=crop&q=80&w=1000',
                'link_url' => '#',
            ],
            [
                'user_id' => $hodUser->id,
                'title' => 'New University Policy on External Guests',
                'description' => 'A new policy regarding external guest speakers has been implemented. All societies must submit guest profiles 14 days prior to the event.',
                'image_url' => 'https://images.unsplash.com/photo-1517245386807-bb43f82c33c4?auto=format&fit=crop&q=80&w=1000',
                'link_url' => '#',
            ],
            // Patron Announcements
            [
                'user_id' => $patronUser->id,
                'title' => 'Annual Grand Dinner Approved',
                'description' => 'We are excited to announce that the Annual Grand Dinner has been approved for November 15th. Preparation committees are being formed now.',
                'image_url' => 'https://images.unsplash.com/photo-1511795409834-ef04bbd61622?auto=format&fit=crop&q=80&w=1000',
                'link_url' => '#',
            ],
            [
                'user_id' => $patronUser->id,
                'title' => 'Strict Deadlines for Event Proposals',
                'description' => 'To ensure smooth processing, all event proposals must be submitted at least 21 days before the planned date. Late submissions will not be entertained.',
                'image_url' => 'https://images.unsplash.com/photo-1454165833767-1314d7912421?auto=format&fit=crop&q=80&w=1000',
                'link_url' => '#',
            ],
            // President Announcements
            [
                'user_id' => $presidentUser->id,
                'title' => 'Registration Open for Mega Hackathon!',
                'description' => 'The biggest event of the year is here! Registration for the 48-hour Mega Hackathon is now open. Team up and show your skills!',
                'image_url' => 'https://images.unsplash.com/photo-1504384308090-c894fdcc538d?auto=format&fit=crop&q=80&w=1000',
                'link_url' => '#',
            ],
            [
                'user_id' => $presidentUser->id,
                'title' => 'Call for Volunteers for Next Week\'s Seminar',
                'description' => 'We need 20 enthusiastic volunteers for the upcoming "Future of AI" seminar. Experience certificates will be provided to all participants.',
                'image_url' => 'https://images.unsplash.com/photo-1515187029135-18ee286d815b?auto=format&fit=crop&q=80&w=1000',
                'link_url' => '#',
            ],
        ];

        foreach ($announcements as $data) {
            Announcement::create($data);
        }
    }
}
