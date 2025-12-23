    <div class="col-lg-6 col-md-6">
        <label>Company <span style="color:red;">*</span></label>
        <select name="company_id" class="form-control" required>
            <option value="">Select Company</option>
            @foreach($companies as $id => $name)
                <option value="{{ $id }}" {{ old('company_id', $service->company_id ?? '') == $id ? 'selected' : '' }}>
                    {{ $name }}
                </option>
            @endforeach
        </select>
         @if($errors->has('company_id'))
         <span class="text-danger">
            {{ $errors->first('company_id') }}
        </span>
    @endif
    </div>

    <div class="col-lg-6 col-md-6">
        <label>Service Name <span style="color:red;">*</span>
</label>
        <input type="text" name="service_name" class="form-control" value="{{ old('service_name', $service->service_name ?? '') }}" maxlength="100" required>
         @if($errors->has('service_name'))
                 <span class="text-danger">
                    {{ $errors->first('service_name') }}
                </span>
            @endif
    </div>

    <div class="col-lg-6 col-md-6">
        <label>Description <span style="color:red;">*</span>
</label>
        <textarea name="service_description" class="form-control">{{ old('service_description', $service->service_description ?? '') }}</textarea>
         @if($errors->has('service_description'))
                 <span class="text-danger">
                    {{ $errors->first('service_description') }}
                </span>
            @endif
    </div>

    <div class="col-lg-6 col-md-6">
        <label>Service Image <span style="color:red;">*</span></label>
        <input type="file" name="service_image" id="image" class="form-control" onchange="return validateFile()">
        @if(isset($service->service_image))
            <img src="{{ asset($service->service_image) }}" width="100" class="mt-2" />
        @endif

         @if($errors->has('service_image'))
             <span class="text-danger">
                {{ $errors->first('service_image') }}
            </span>
        @endif
    </div>

