<?php

namespace App\Services\Cms\Testimonial;

use App\DTOs\Filters\TestimonialFilterDTO;
use App\Http\Resources\Cms\Testimonial\TestimonialResource;
use App\Repositories\Cms\TestimonialRepositoryInterface;
use App\Services\Service;

class TestimonialService extends Service
{
    protected $uploadPath = 'testimonial';

    public function __construct(protected TestimonialRepositoryInterface $testimonialRepo) {}

    public function paginate(TestimonialFilterDTO $filter)
    {
        $testimonials = $this->testimonialRepo->getFilteredPaginated($filter);

        return TestimonialResource::collection($testimonials);
    }

    public function sort(array $sortedIds): bool
    {
        return $this->testimonialRepo->updatePositions($sortedIds);
    }

    public function store(array $data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            }

            return $this->testimonialRepo->create($data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function find($id)
    {
        $testimonial = $this->testimonialRepo->find($id);

        return $testimonial ? new TestimonialResource($testimonial) : null;
    }

    public function update($id, array $data)
    {
        try {
            if (! empty($data['image'])) {
                $data['image'] = $this->uploadFile($data['image'], $this->uploadPath);
            } else {
                unset($data['image']);
            }

            return $this->testimonialRepo->update($id, $data);
        } catch (\Exception $ex) {
            return false;
        }
    }

    public function delete($id): bool
    {
        try {
            return $this->testimonialRepo->delete($id);
        } catch (\Exception $ex) {
            return false;
        }
    }
}
