<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('Authentication', function () {
    
    it('redirects guests to login page', function () {
        $response = $this->get('/dashboard');
        
        $response->assertRedirect('/login');
    });

    it('shows login page', function () {
        $response = $this->get('/login');
        
        $response->assertStatus(200);
        $response->assertSee('TriadCo');
    });

    it('requires username to login', function () {
        $response = $this->post('/login', [
            'name' => '',
            'password' => 'password',
            'captcha_answer' => '1'
        ]);
        
        $response->assertSessionHasErrors('name');
    });

    it('requires password to login', function () {
        $response = $this->post('/login', [
            'name' => 'testuser',
            'password' => '',
            'captcha_answer' => '1'
        ]);
        
        $response->assertSessionHasErrors('password');
    });

    it('rejects invalid credentials', function () {
        User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('correctpassword'),
        ]);
        
        // Bypass captcha for testing
        session(['captcha_answer' => '5']);
        
        $response = $this->post('/login', [
            'name' => 'testuser',
            'password' => 'wrongpassword',
            'captcha_answer' => '5'
        ]);
        
        $response->assertSessionHasErrors();
    });

    it('allows valid user to login', function () {
        $user = User::factory()->create([
            'name' => 'testuser',
            'password' => bcrypt('correctpassword'),
            'role' => 'admin',
            'is_locked' => false,
            'failed_login_attempts' => 0,
        ]);
        
        // Bypass captcha for testing
        session(['captcha_answer' => '5']);
        
        $response = $this->post('/login', [
            'name' => 'testuser',
            'password' => 'correctpassword',
            'captcha_answer' => '5'
        ]);
        
        $this->assertAuthenticated();
    });

    it('logs out authenticated user', function () {
        $user = User::factory()->create();
        
        $this->actingAs($user)->post('/logout');
        
        $this->assertGuest();
    });

});

describe('Password Reset', function () {
    
    it('shows forgot password page', function () {
        $response = $this->get('/forgot-password');
        
        $response->assertStatus(200);
        $response->assertSee('Forgot Password');
    });

    it('requires valid email for password reset', function () {
        $response = $this->post('/forgot-password/verify-email', [
            'email' => 'nonexistent@example.com'
        ]);
        
        $response->assertSessionHasErrors('email');
    });

});
