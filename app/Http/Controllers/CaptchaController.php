<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CaptchaController extends Controller
{
    /**
     * Generate a simple math captcha
     */
    public static function generate(): array
    {
        $num1 = rand(1, 10);
        $num2 = rand(1, 10);
        $operators = ['+', '-'];
        $operator = $operators[array_rand($operators)];
        
        if ($operator === '-' && $num1 < $num2) {
            // Swap to avoid negative results
            $temp = $num1;
            $num1 = $num2;
            $num2 = $temp;
        }
        
        $answer = $operator === '+' ? $num1 + $num2 : $num1 - $num2;
        
        // Store answer in session
        Session::put('captcha_answer', $answer);
        
        return [
            'question' => "{$num1} {$operator} {$num2} = ?",
            'num1' => $num1,
            'num2' => $num2,
            'operator' => $operator,
        ];
    }

    /**
     * Verify captcha answer
     */
    public static function verify($userAnswer): bool
    {
        $correctAnswer = Session::get('captcha_answer');
        
        if ($correctAnswer === null) {
            return false;
        }
        
        // Clear the captcha after verification attempt
        Session::forget('captcha_answer');
        
        return (int)$userAnswer === (int)$correctAnswer;
    }

    /**
     * Refresh captcha via AJAX
     */
    public function refresh()
    {
        $captcha = self::generate();
        
        return response()->json([
            'question' => $captcha['question']
        ]);
    }
}
