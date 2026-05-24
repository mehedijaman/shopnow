<?php

namespace Modules\Slider\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Inertia\Response;
use Modules\Slider\Http\Requests\SliderValidate;
use Modules\Slider\Models\Slider;
use Modules\Support\Http\Controllers\BackendController;
use Modules\Support\Traits\UploadFile;

class SliderController extends BackendController
{
    use UploadFile;

    public function index(): Response
    {
        $sliders = Slider::latest()
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($slider) => [
                'id' => $slider->id,
                'title' => $slider->title,
                'description' => $slider->description,
                'image_url' => $slider->image_url,
                'bg_color' => $slider->bg_color,
                'url' => $slider->url,
                'button_text' => $slider->button_text,
                'order' => $slider->order,
                'active' => $slider->active,
            ]);

        return inertia('Slider/SliderIndex', [
            'sliders' => $sliders,
        ]);
    }

    public function create(): Response
    {
        return inertia('Slider/SliderForm');
    }

    public function store(SliderValidate $request): RedirectResponse
    {
        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated = array_merge($validated, $this->uploadFile('image', 'slider', 'originalUUID', 'public'));
        }

        Slider::create($validated);

        return redirect()->route('slider.index')
            ->with('success', 'Slider created.');
    }

    public function edit(int $id): Response
    {
        $slider = Slider::find($id);

        return inertia('Slider/SliderForm', [
            'slider' => $slider,
        ]);
    }

    public function update(SliderValidate $request, int $id): RedirectResponse
    {
        $slider = Slider::findOrFail($id);

        $validated = $request->validated();

        if ($request->hasFile('image')) {
            $validated = array_merge($validated, $this->uploadFile('image', 'slider', 'originalUUID', 'public'));
        } elseif ($request->input('remove_previous_image')) {
            $validated['image'] = null;
        } else {
            unset($validated['image']);
        }

        $slider->update($validated);

        return redirect()->route('slider.index')
            ->with('success', 'Slider updated.');
    }

    public function destroy(int $id): RedirectResponse
    {
        Slider::findOrFail($id)->delete();

        return redirect()->route('slider.index')
            ->with('success', 'Slider deleted.');
    }

    public function recycleBin(): Response
    {
        $sliders = Slider::onlyTrashed()
            ->orderBy('id')
            ->search(request('searchContext'), request('searchTerm'))
            ->paginate(request('rowsPerPage', 10))
            ->withQueryString()
            ->through(fn ($slider) => [
                'id' => $slider->id,
                'title' => $slider->title,
                'deleted_at' => $slider->deleted_at ? Carbon::parse($slider->deleted_at)->format('d/m/Y') : null,
                'active' => $slider->active,
            ]);

        return inertia('Slider/SliderRecycleBin', [
            'sliders' => $sliders,
        ]);
    }

    public function restore(int $id): RedirectResponse
    {
        Slider::onlyTrashed()->findOrFail($id)->restore(); // Restore soft deleted record

        return redirect()->route('slider.recycleBin.index')
            ->with('success', 'Slider Restored.');
    }

    public function destroyForce(int $id): RedirectResponse
    {

        $slider = Slider::onlyTrashed()->findOrFail($id);

        $slider->forceDelete();

        return redirect()->route('slider.recycleBin.index')->with('success', 'Slider deleted.');
    }

    public function emptyRecycleBin(): RedirectResponse
    {
        $sliders = Slider::onlyTrashed()->get();

        foreach ($sliders as $slider) {
            $slider->forceDelete();
        }

        return redirect()->route('slider.recycleBin.index')
            ->with('success', 'Recycle bin emptied.');
    }

    public function restoreRecycleBin(): RedirectResponse
    {
        Slider::onlyTrashed()->restore(); // Restore soft deleted records

        return redirect()->route('slider.recycleBin.index')
            ->with('success', 'Slider Restored.');
    }
}
