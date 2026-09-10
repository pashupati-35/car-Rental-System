<?php

namespace App\Services\Cms\Blog;

use App\DTOs\Cms\BlogDTO;
use App\DTOs\Filters\BlogFilterDTO;
use App\Http\Resources\Cms\Blog\BlogResource;
use App\Repositories\Cms\BlogRepositoryInterface;
use App\Services\Service;
use Carbon\Carbon;

class BlogService extends Service
{
    protected $uploadPath = 'blog';

    public function __construct(protected BlogRepositoryInterface $blogRepo) {}

    public function paginate(BlogFilterDTO $filter)
    {
        $blogs = $this->blogRepo->getFilteredPaginated($filter);
        return BlogResource::collection($blogs);
    }

    public function store(array $data)
    {
        try {
            if (isset($data['social_share_image_file']) && !empty($data['social_share_image_file'])) {
                $data['social_share_image'] = $this->uploadFile($data['social_share_image_file'], $this->uploadPath);
            }
            if (isset($data['image']) && !empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            if (isset($data['author_image']) && !empty($data['author_image'])) {
                $data['author_image'] = $this->uploadFile($data['author_image'], $this->uploadPath);
            }
            if (!empty($data['publish_date'])) {
                $data['publish_date'] = Carbon::create($data['publish_date'])->toDateTimeString();
            }
            if (!empty($data['event_end'])) {
                $data['event_end'] = Carbon::create($data['event_end'])->toDateTimeString();
            }

            return $this->blogRepo->create($data);
        } catch (\Exception $ex) {
            throw $ex;
        }
    }

    public function getById($id)
    {
        $blog = $this->blogRepo->find($id);
        return $blog ? new BlogResource($blog) : null;
    }

    public function update($id, array $data)
    {
        try {
            $blog = $this->blogRepo->findOrFail($id);
            if (!empty($data['social_share_image'])) {
                if (!empty($blog->social_share_image)) {
                    $this->deleteFile($this->uploadPath, $blog->social_share_image);
                }
                $data['social_share_image'] = $this->uploadFile($data['social_share_image'], $this->uploadPath);
            }

            if (!empty($data['image'])) {
                if (!empty($blog->image)) {
                    $this->deleteFile($this->uploadPath, $blog->image);
                }
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }
            if (!empty($data['author_image'])) {
                if (!empty($blog->author_image)) {
                    $this->deleteFile($this->uploadPath, $blog->author_image);
                }
                $data['author_image'] = $this->uploadFile($data['author_image'], $this->uploadPath);
            }

            if (!empty($data['publish_date'])) {
                $data['publish_date'] = Carbon::create($data['publish_date'])->toDateTimeString();
            }

            return $this->blogRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id)
    {
        try {
            $blog = $this->blogRepo->find($id);
            if ($blog && !empty($blog->image)) {
                $this->deleteFile($this->uploadPath, $blog->image);
            }

            return $this->blogRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function getAllActive()
    {
        $blogs = $this->blogRepo->getActive();
        return BlogResource::collection($blogs);
    }
}
