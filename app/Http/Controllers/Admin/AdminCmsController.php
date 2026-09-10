<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cms\Album\Album;
use App\Models\Cms\Blog\Blog;
use App\Models\Cms\Blog\Category\BlogCategory;
use App\Models\Cms\Career\Career;
use App\Models\Cms\ContactUs\ContactUs;
use App\Models\Cms\Enquiry\Enquiry;
use App\Models\Cms\Faq\Category\FaqCategory;
use App\Models\Cms\Faq\Faq;
use App\Models\Cms\Menu\Menu;
use App\Models\Cms\NewsAndUpdates\NewsAndUpdates;
use App\Models\Cms\Notice\Notice;
use App\Models\Cms\Page\Page;
use App\Models\Cms\Partner\Partner;
use App\Models\Cms\Popup\Popup;
use App\Models\Cms\Service\Service;
use App\Models\Cms\SiteSetting\SiteSetting;
use App\Models\Cms\Slider\Slider;
use App\Models\Cms\Team\Team;
use App\Models\Cms\Testimonial\Testimonial;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminCmsController extends Controller
{
    /**
     * Display the Master CMS Management Dashboard in Admin.
     */
    public function index(Request $request)
    {
        $stats = [
            'faqs' => Faq::count(),
            'blogs' => Blog::count(),
            'services' => Service::count(),
            'teams' => Team::count(),
            'testimonials' => Testimonial::count(),
            'notices' => Notice::count(),
            'sliders' => Slider::count(),
            'popups' => Popup::count(),
            'pages' => Page::count(),
            'partners' => Partner::count(),
            'careers' => Career::count(),
            'enquiries' => Enquiry::count(),
            'contacts' => ContactUs::count(),
            'albums' => Album::count(),
            'menus' => Menu::count(),
            'news' => NewsAndUpdates::count(),
        ];

        return Inertia::render('admin/cms/Index', [
            'stats' => $stats,
        ]);
    }

    /**
     * Unified CMS Data API for Admin Vue CRUD.
     */
    public function getData(Request $request, string $module)
    {
        $limit = $request->input('limit', 50);

        return match ($module) {
            'faqs' => response()->json(['status' => 'OK', 'data' => Faq::with('category')->latest('id')->paginate($limit)]),
            'faq-categories' => response()->json(['status' => 'OK', 'data' => FaqCategory::latest('id')->get()]),
            'blogs' => response()->json(['status' => 'OK', 'data' => Blog::latest('id')->paginate($limit)]),
            'blog-categories' => response()->json(['status' => 'OK', 'data' => BlogCategory::latest('id')->get()]),
            'services' => response()->json(['status' => 'OK', 'data' => Service::latest('id')->paginate($limit)]),
            'teams' => response()->json(['status' => 'OK', 'data' => Team::latest('id')->paginate($limit)]),
            'testimonials' => response()->json(['status' => 'OK', 'data' => Testimonial::latest('id')->paginate($limit)]),
            'notices' => response()->json(['status' => 'OK', 'data' => Notice::latest('id')->paginate($limit)]),
            'sliders' => response()->json(['status' => 'OK', 'data' => Slider::latest('id')->paginate($limit)]),
            'popups' => response()->json(['status' => 'OK', 'data' => Popup::latest('id')->paginate($limit)]),
            'pages' => response()->json(['status' => 'OK', 'data' => Page::latest('id')->paginate($limit)]),
            'partners' => response()->json(['status' => 'OK', 'data' => Partner::latest('id')->paginate($limit)]),
            'careers' => response()->json(['status' => 'OK', 'data' => Career::latest('id')->paginate($limit)]),
            'enquiries' => response()->json(['status' => 'OK', 'data' => Enquiry::latest('id')->paginate($limit)]),
            'contacts' => response()->json(['status' => 'OK', 'data' => ContactUs::latest('id')->paginate($limit)]),
            'news' => response()->json(['status' => 'OK', 'data' => NewsAndUpdates::latest('id')->paginate($limit)]),
            'albums' => response()->json(['status' => 'OK', 'data' => Album::with('values')->latest('id')->paginate($limit)]),
            'menus' => response()->json(['status' => 'OK', 'data' => Menu::with('menuItems')->latest('id')->paginate($limit)]),
            'site-settings' => response()->json(['status' => 'OK', 'data' => SiteSetting::first() ?? new SiteSetting()]),
            default => response()->json(['status' => 'ERROR', 'message' => 'Invalid CMS module.'], 404),
        };
    }

    /**
     * Unified Store for CMS items.
     */
    public function storeItem(Request $request, string $module)
    {
        $data = $request->except(['_token']);

        $model = match ($module) {
            'faqs' => Faq::create($data),
            'faq-categories' => FaqCategory::create($data),
            'blogs' => Blog::create($data),
            'blog-categories' => BlogCategory::create($data),
            'services' => Service::create($data),
            'teams' => Team::create($data),
            'testimonials' => Testimonial::create($data),
            'notices' => Notice::create($data),
            'sliders' => Slider::create($data),
            'popups' => Popup::create($data),
            'pages' => Page::create($data),
            'partners' => Partner::create($data),
            'careers' => Career::create($data),
            'news' => NewsAndUpdates::create($data),
            'albums' => Album::create($data),
            'menus' => Menu::create($data),
            'site-settings' => SiteSetting::updateOrCreate(['id' => 1], $data),
            default => null,
        };

        if ($model) {
            return response()->json(['status' => 'OK', 'message' => 'Item created successfully.', 'data' => $model], 201);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to create item.'], 400);
    }

    /**
     * Unified Update for CMS items.
     */
    public function updateItem(Request $request, string $module, $id)
    {
        $data = $request->except(['_token', '_method']);

        $item = match ($module) {
            'faqs' => Faq::findOrFail($id),
            'faq-categories' => FaqCategory::findOrFail($id),
            'blogs' => Blog::findOrFail($id),
            'blog-categories' => BlogCategory::findOrFail($id),
            'services' => Service::findOrFail($id),
            'teams' => Team::findOrFail($id),
            'testimonials' => Testimonial::findOrFail($id),
            'notices' => Notice::findOrFail($id),
            'sliders' => Slider::findOrFail($id),
            'popups' => Popup::findOrFail($id),
            'pages' => Page::findOrFail($id),
            'partners' => Partner::findOrFail($id),
            'careers' => Career::findOrFail($id),
            'news' => NewsAndUpdates::findOrFail($id),
            'albums' => Album::findOrFail($id),
            'menus' => Menu::findOrFail($id),
            'site-settings' => SiteSetting::firstOrCreate(['id' => 1]),
            default => null,
        };

        if ($item) {
            $item->update($data);
            return response()->json(['status' => 'OK', 'message' => 'Item updated successfully.', 'data' => $item]);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Item not found.'], 404);
    }

    /**
     * Unified Delete for CMS items.
     */
    public function deleteItem(Request $request, string $module, $id)
    {
        $item = match ($module) {
            'faqs' => Faq::find($id),
            'faq-categories' => FaqCategory::find($id),
            'blogs' => Blog::find($id),
            'blog-categories' => BlogCategory::find($id),
            'services' => Service::find($id),
            'teams' => Team::find($id),
            'testimonials' => Testimonial::find($id),
            'notices' => Notice::find($id),
            'sliders' => Slider::find($id),
            'popups' => Popup::find($id),
            'pages' => Page::find($id),
            'partners' => Partner::find($id),
            'careers' => Career::find($id),
            'enquiries' => Enquiry::find($id),
            'contacts' => ContactUs::find($id),
            'news' => NewsAndUpdates::find($id),
            'albums' => Album::find($id),
            'menus' => Menu::find($id),
            default => null,
        };

        if ($item) {
            $item->delete();
            return response()->json(['status' => 'OK', 'message' => 'Item deleted successfully.']);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to delete.'], 404);
    }

    /**
     * Toggle Active Status for CMS items.
     */
    public function toggleStatus(Request $request, string $module, $id)
    {
        $item = match ($module) {
            'faqs' => Faq::findOrFail($id),
            'faq-categories' => FaqCategory::findOrFail($id),
            'blogs' => Blog::findOrFail($id),
            'services' => Service::findOrFail($id),
            'teams' => Team::findOrFail($id),
            'testimonials' => Testimonial::findOrFail($id),
            'notices' => Notice::findOrFail($id),
            'sliders' => Slider::findOrFail($id),
            'popups' => Popup::findOrFail($id),
            'pages' => Page::findOrFail($id),
            'partners' => Partner::findOrFail($id),
            'careers' => Career::findOrFail($id),
            'news' => NewsAndUpdates::findOrFail($id),
            'albums' => Album::findOrFail($id),
            'menus' => Menu::findOrFail($id),
            default => null,
        };

        if ($item) {
            $item->is_active = !$item->is_active;
            $item->save();

            return response()->json([
                'status' => 'OK',
                'message' => 'Status updated.',
                'is_active' => (bool)$item->is_active,
            ]);
        }

        return response()->json(['status' => 'ERROR', 'message' => 'Failed to toggle status.'], 404);
    }
}
