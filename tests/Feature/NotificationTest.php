<?php

namespace Tests\Feature;
use Database\Factories\QuestionFactory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Illuminate\Support\Facades\Notification;
use App\Models\User;
use App\Models\Question;
use App\Notifications\QuestionNotification;
use Illuminate\Support\Facades\Hash;

class NotificationTest extends TestCase
{

    use RefreshDatabase;

    /** @test */
    public function it_sends_a_question_notification()
    {
        // إيقاف الإشعارات الفعلية مؤقتًا
        Notification::fake();

        // إنشاء مستخدم وهمي
        $admin = User::factory()->create([
            'name' =>'test',
            'email'=>'wwwasyad5@gmail.com',
            'is_admin' => '1',
            'password' => 'password123' // تأكد من تشفير كلمة المرور
        ]);

        // إنشاء سؤال وهمي
        $question = Question::factory()->create();
        // إرسال الإشعار
        $admin->notify(new QuestionNotification($question));

        // التحقق من أن الإشعار تم إرساله إلى المستخدم الصحيح
        Notification::assertSentTo(
            [$admin], QuestionNotification::class
        );
    }
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
