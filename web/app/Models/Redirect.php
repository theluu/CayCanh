<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['from_path', 'to_path'])]
class Redirect extends Model
{
    public static function record(string $from, string $to): void
    {
        if ($from === $to) {
            return;
        }

        static::where('from_path', $to)->delete();
        static::where('to_path', $from)->update(['to_path' => $to]);
        static::updateOrCreate(['from_path' => $from], ['to_path' => $to]);
    }
}
