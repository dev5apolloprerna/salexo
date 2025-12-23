<?php
namespace App\Repositories\Service;
use JasonGuru\LaravelMakeRepository\Repository\BaseRepository;
use App\Models\Service;

class ServiceRepository implements ServiceRepositoryInterface
{

       public function all()
    {
        return Service::with('company')->paginate(env('PER_PAGE_COUNT'));
    }

    public function find($id)
    {
        return Service::findOrFail($id);
    }

    public function create(array $data)
    {
        if (isset($data['service_image']) && $data['service_image'] instanceof \Illuminate\Http\UploadedFile) {
            $image = $data['service_image'];
            $imageName = time() . '_' . $image->getClientOriginalName();
            $image->move(public_path('service_images'), $imageName);
            $data['service_image'] = 'service_images/' . $imageName;
        }

        return Service::create($data);
    }

    public function update($id, array $data)
    {
            $service = $this->find($id);

            // Handle image upload
            if (isset($data['service_image']) && $data['service_image'] instanceof \Illuminate\Http\UploadedFile) {
                // Delete old image if it exists
                if ($service->service_image && file_exists(public_path($service->service_image))) {
                    unlink(public_path($service->service_image));
                }

                // Upload new image to public folder
                $image = $data['service_image'];
                $imageName = time() . '_' . $image->getClientOriginalName();
                $image->move(public_path('service_images'), $imageName);
                $data['service_image'] = 'service_images/' . $imageName;
            } else {
                // Don't overwrite image if no new one is uploaded
                unset($data['service_image']);
            }

            $service->update($data);
            return $service;
    }

    public function delete($id)
    {
        $service = $this->find($id);

        // Delete image from public folder if it exists
        if ($service->service_image && file_exists(public_path($service->service_image))) {
            unlink(public_path($service->service_image));
        }

        return $service->delete();

    }
    

}

