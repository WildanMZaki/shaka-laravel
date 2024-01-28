<?php

namespace App\Models;

use App\Helpers\Formatter;
use Exception;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, SoftDeletes;
    use Formatter;

    protected $fillable = [
        'user_id', 'title', 'message', 'has_read',
    ];

    /**
     * @param user_id, user notif receiver
     * @param message_name: if it string, find from databse, or it array like ['title', 'Your message']
     * @param replacer: it must associative array
     */
    public static function sendTo($user_id, string|array $message_name, ?array $replacer = null)
    {
        if (is_array($message_name)) {
            if (count($message_name) != 2) {
                throw new Exception('Message name dalam array harus punya 2 string element');
            }
        }
        $msg = is_string($message_name) ? NotificationMessage::of($message_name) : $message_name[1];
        if (!isset($replacer['time'])) {
            $time = self::greetingTime(now());
            $msg = str_replace('{time}', $time, $msg);
        }
        if (!isset($replacer['name'])) {
            $user = User::find($user_id);
            $userName = $user ? $user->name : 'Unknown';
            $msg = str_replace('{name}', $userName, $msg);
        }
        if ($replacer != null) {
            $keys = array_keys($replacer);
            foreach ($keys as $key) {
                $msg = str_replace("{" . $key . "}", $replacer[$key], $msg);
            }
        }

        $notif = new Notification();
        $notif->user_id = $user_id;
        $notif->title = is_string($message_name) ? $message_name : $message_name[0];
        $notif->message = $msg;
        return $notif->save();
    }
}
