<?php

namespace App\Services\Cms\Blog;

use App\Http\Resources\Cms\Blog\BlogResource;
use App\Models\Cms\Blog\Blog;
use App\Services\Service;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class BlogService extends Service
{
    protected $uploadPath = 'blog';

    public function __construct(protected Blog $blog) {}

    public function paginate($limit, $data)
    {
        $blog = $this->blog->where(function ($qry) use ($data) {
            if (! empty($data['title'])) {
                $qry->where('title', 'like', '%'.$data['title'].'%');
            }

            if (isset($data['type']) && ! empty($data['type'])) {
                $qry->whereType($data['type']);
            }

            if (isset($data['category_id']) && ! empty($data['category_id'])) {
                $qry->whereCategoryId($data['category_id']);
            }
            if (isset($data['publish_date_from']) && ! empty($data['publish_date_from'])) {
                $qry->whereDate('publish_date', '>=', Carbon::parse($data['publish_date_from']));
            }

            if (isset($data['publish_date_to']) && ! empty($data['publish_date_to'])) {
                $qry->whereDate('publish_date', '<=', Carbon::parse($data['publish_date_to']));
            }

            if (isset($data['is_active']) && ! empty($data['is_active'])) {
                $qry->whereIsActive($data['is_active']);
            }

            if (isset($data['type']) && $data['type'] == 'event' && isset($data['filter_by']) && ! empty($data['filter_by'])) {
                if ($data['filter_by'] == 'upcomming') {
                    $qry->whereDate('event_date', '>=', Carbon::now());
                } else {
                    $qry->whereDate('event_date', '<=', Carbon::now());
                }
            }
        })->orderBy('id', 'DESC')->paginate($limit);

        return BlogResource::collection($blog);
    }

    public function store($data)
    {
        try {
            if (isset($data['social_share_image_file']) && ! empty($data['social_share_image_file'])) {
                $data['social_share_image'] = $this->uploadFile($data['social_share_image_file'], $this->uploadPath);
            }
            if (isset($data['image']) && ! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            if (isset($data['author_image']) && ! empty($data['author_image'])) {
                $data['author_image'] = $this->uploadFile($data['author_image'], $this->uploadPath);
            }
            if (! empty($data['publish_date'])) {
                $data['publish_date'] = Carbon::create($data['publish_date'])->toDateTimeString();
            }
            if (! empty($data['event_end'])) {
                $data['event_end'] = Carbon::create($data['event_end'])->toDateTimeString();
            }

            return $this->blog->create($data);
        } catch (\Exception $ex) {
            throw $ex;

            return false;
        }
    }

    public function getById($id)
    {
        $blog = $this->blog->find($id);

        return new BlogResource($blog);
    }

    public function update($id, $data)
    {
        try {
            $blog = $this->getById($id);
            if (! empty($data['social_share_image'])) {
                if (! empty($blog->social_share_image)) {
                    $this->deleteFile($this->uploadPath, $blog->social_share_image);
                }
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            if (! empty($data['image'])) {
                if (! empty($blog->image)) {
                    $this->deleteFile($this->uploadPath, $blog->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            if (! empty($data['author_image'])) {
                if (! empty($blog->author_image)) {
                    $this->deleteFile($this->uploadPath, $blog->author_image);
                }
                $data['author_image'] = $this->uploadFile($data['author_image'], $this->uploadPath);
            }

            if (! empty($data['publish_date']) && isset($data['publish_date'])) {
                $data['publish_date'] = Carbon::create($data['publish_date'])->toDateTimeString();
            }

            return $blog->update($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $blog = $this->getById($id);
            if (! empty($blog->image)) {
                $this->deleteFile($this->uploadPath, $blog->image);
            }

            return $blog->delete();
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getAllActive()
    {
        $blogs = $this->blog->whereIsActive(1)->get();

        return BlogResource::collection($blogs);
    }

    public function getByCategoryIds($categoryIds, $limit = 3)
    {
        $blogs = $this->blog->whereIsActive(1)->whereIn('category_id', $categoryIds)->take($limit)->get();

        return BlogResource::collection($blogs);
    }

    public function getBySlug($slug)
    {
        return $this->blog->whereSlug($slug)->first();
    }

    public function findByColumn($column, $value)
    {
        return $this->blog->where($column, $value)->first();
    }

    public function getUserForLogin($email, $password)
    {
        $blog = $this->blog->whereEmail($email)->first();
        if (empty($blog)) {
            return false;
        }

        if (Hash::check($password, $blog->password)) {
            return $blog;
        }

        return false;
    }

    public function findByColumns($data, $all = false, $resource = true)
    {
        $result = $this->blog->where(function ($query) use ($data) {
            if (count($data) > 0) {
                foreach ($data as $k => $v) {
                    $query->where($k, $data[$k]);
                }
            }
        });
        if ($all) {
            $result = $result->get();

            return $resource ? BlogResource::collection($result) : $result;
        } else {
            $result = $result->first();
            if (empty($result)) {
                return null;
            }

            return $resource ? new BlogResource($result) : $result;
        }
    }

    public function searchByKey($key, $limit, $limitText = 100)
    {
        $results = $this->blog
            ->where('title', 'like', '%'.$key.'%')
            ->orWhere('content', 'like', '%'.$key.'%')
            ->orWhere('type', 'like', $key.'%')
            ->take($limit)
            ->whereIsActive(1)
            ->orderBy('id', 'DESC')
            ->get();
        $blogs = [];
        $news = [];
        $events = [];
        if ($results->count() > 0) {
            foreach ($results as $p) {
                $temp = [
                    'title' => $p->title,
                    'route' => route('blog-detail', $p->slug),
                    'description' => trim(substr($p->content, 0, $limitText)).'...',
                ];
                if ($p->type = 'blog') {
                    array_push($blogs, $temp);
                } elseif ($p->type = 'news') {
                    array_push($news, $temp);
                } else {
                    array_push($events, $temp);
                }
            }
        }

        return [
            $news,
            $blogs,
            $events,
        ];
    }

    public function getUpcomingEventByDay($limit = 25, $type = 'blog', $all = false)
    {
        $todayDate = Carbon::now()->toDateString();
        $blogs = $this->blog->whereIsActive(1)->whereType($type)->whereDate('publish_date', '<=', $todayDate)->orderBy('id', 'DESC')->paginate($limit);
        if ($all) {
            return $blogs;
        } else {
            $newBlogs = [];
            if ($blogs->count() > 0) {
                foreach ($blogs as $blog) {
                    $newBlogs[] = (object) [
                        'title' => $blog->title,
                        'image' => $blog->image,
                        'image_path' => $blog->image_path,
                        'author_name' => $blog->author_name,
                        'categories' => $blog->categories ? $blog->categories[0]->title : null,
                        'publish_date' => ! empty($blog->publish_date) ? formatDate($blog->publish_date) : null,
                        'type' => $blog->type,
                        'url' => $blog->type == 'blog' ? route('blog.detail', $blog->slug) : route('news.detail', $blog->slug),
                        'content' => $blog->content,
                        'is_active' => $blog->is_active,
                    ];
                }
            }
        }

        return $newBlogs;
    }
}
