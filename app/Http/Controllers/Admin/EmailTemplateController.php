<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\EmailTemplate\EmailTemplateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    public function __construct(
        protected EmailTemplateService $emailTemplateService,
    ) {}

    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', $request->input('per_pages', 15));

        $templates = $this->emailTemplateService->paginate($perPage, $request);
        $roleCounts = $this->emailTemplateService->getRoleCounts();

        return Inertia::render('admin/email-templates/Index', [
            'templates' => $templates,
            'counts' => $roleCounts,
            'filters' => $request->only(['title', 'role', 'per_page']),
        ]);
    }

    public function edit($id)
    {
        $template = $this->emailTemplateService->findRaw($id);

        return Inertia::render('admin/email-templates/Edit', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = $this->emailTemplateService->findRaw($id);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'role' => 'required|string|in:owner,customer,admin',
            'is_active' => 'boolean',
        ]);

        $template->update($data);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully.');
    }
}
