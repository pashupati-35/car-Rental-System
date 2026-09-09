<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate\EmailTemplate;
use App\Services\EmailTemplate\EmailTemplateService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EmailTemplateController extends Controller
{
    protected EmailTemplateService $emailTemplateService;

    public function __construct(EmailTemplateService $emailTemplateService)
    {
        $this->emailTemplateService = $emailTemplateService;
    }

    public function index(Request $request)
    {
        $templates = EmailTemplate::query()
            ->when($request->title, fn($q, $v) => $q->where('title', 'like', "%{$v}%"))
            ->when($request->role, fn($q, $v) => $q->where('role', $v))
            ->orderBy('id', 'desc')
            ->paginate(15);

        return Inertia::render('admin/email-templates/Index', [
            'templates' => $templates,
            'filters' => $request->only(['title', 'role']),
        ]);
    }

    public function edit($id)
    {
        $template = EmailTemplate::findOrFail($id);

        return Inertia::render('admin/email-templates/Edit', [
            'template' => $template,
        ]);
    }

    public function update(Request $request, $id)
    {
        $template = EmailTemplate::findOrFail($id);
        $data = $request->validate([
            'title' => 'required|string|max:255',
            'subject' => 'required|string|max:255',
            'description' => 'required|string',
            'message_content' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $template->update($data);

        return redirect()->route('admin.email-templates.index')->with('success', 'Email template updated successfully.');
    }
}
