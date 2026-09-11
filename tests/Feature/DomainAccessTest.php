<?php

namespace Tests\Feature;

use Tests\TestCase;

class DomainAccessTest extends TestCase
{
    public function test_portal_subdomain_redirects_non_admin_routes_to_main_domain(): void
    {
        // Owner login accessed on portal subdomain
        $response = $this->get('http://portal.carrental.local/owner/login');
        $response->assertRedirect('http://carrental.local/owner/login');

        // Customer login accessed on portal subdomain
        $response = $this->get('http://portal.carrental.local/customer/login');
        $response->assertRedirect('http://carrental.local/customer/login');

        // Public cars page accessed on portal subdomain
        $response = $this->get('http://portal.carrental.local/cars');
        $response->assertRedirect('http://carrental.local/cars');
    }

    public function test_portal_subdomain_allows_admin_routes(): void
    {
        // Admin login page on portal
        $response = $this->get('http://portal.carrental.local/admin/login');
        $response->assertStatus(200);

        // Root URL on portal redirects to admin login for unauthenticated users
        $response = $this->get('http://portal.carrental.local/');
        $response->assertRedirect(route('admin.login'));
    }

    public function test_main_domain_redirects_admin_routes_to_portal_subdomain(): void
    {
        // Admin dashboard accessed on main domain
        $response = $this->get('http://carrental.local/admin/dashboard');
        $response->assertRedirect('http://portal.carrental.local/admin/dashboard');
    }
}
