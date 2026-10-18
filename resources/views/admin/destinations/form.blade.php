@php
    $isEdit = isset($destination) && $destination->exists;
    $destination = $destination ?? null;
@endphp

@if ($errors->any())
    <div class="admin-alert admin-alert-error" role="alert" tabindex="-1" id="admin-validation-summary">
        <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0 3.75h.008v.008H12v-.008zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <div>
            <p style="font-weight: 700">
                {{ $errors->count() }} {{ Str::plural('field', $errors->count()) }} need{{ $errors->count() === 1 ? 's' : '' }} your attention.
            </p>
            <ul style="margin-top: 6px; padding-left: 19px; list-style: disc;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
@endif

<div class="admin-form">

    {{-- 1. BASICS --}}
    <fieldset class="admin-fieldset">
        <legend class="admin-legend">
            <span class="admin-legend-num">1</span>
            Basic details
        </legend>

        <div class="admin-grid admin-grid-2">
            <div class="admin-field @error('name') has-error @enderror">
                <label for="f-name" class="admin-label">Name<span class="admin-required">*</span></label>
                <input id="f-name" name="name" type="text" required maxlength="255" autocomplete="off"
                       class="admin-input" placeholder="e.g. Raja Ampat Islands"
                       value="{{ old('name', $destination->name ?? '') }}">
                @error('name')
                    <p class="admin-error" id="f-name-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field @error('slug') has-error @enderror">
                <label for="f-slug" class="admin-label">Slug</label>
                <input id="f-slug" name="slug" type="text" maxlength="255" autocomplete="off"
                       class="admin-input" placeholder="auto-generated from the name"
                       aria-describedby="f-slug-hint"
                       value="{{ old('slug', $destination->slug ?? '') }}">
                <p class="admin-hint" id="f-slug-hint">Used in the URL — <code style="font-weight:700">/destinations/…</code>. Leave empty to build it from the name.</p>
                @error('slug')
                    <p class="admin-error" id="f-slug-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field @error('category_id') has-error @enderror">
                <label for="f-category" class="admin-label">Category<span class="admin-required">*</span></label>
                <select id="f-category" name="category_id" required class="admin-select">
                    <option value="" disabled @selected(!old('category_id', $destination->category_id ?? ''))>Choose a category…</option>
                    @foreach ($categories as $c)
                        <option value="{{ $c->id }}" @selected(old('category_id', $destination->category_id ?? '') == $c->id)>{{ $c->name }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <p class="admin-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field @error('sustainability_score') has-error @enderror">
                <label for="f-score" class="admin-label">Sustainability score<span class="admin-required">*</span></label>

                <div class="flex items-center gap-3">
                    <input id="f-score" name="sustainability_score" type="number" min="0" max="100" step="1" required
                           inputmode="numeric" class="admin-input" style="max-width: 108px"
                           aria-describedby="f-score-hint"
                           value="{{ old('sustainability_score', $destination->sustainability_score ?? 80) }}">
                    <output class="score-readout" id="score-readout" for="f-score">— / 100</output>
                </div>

                <input id="f-score-range" type="range" min="0" max="100" step="1" class="admin-range" aria-label="Sustainability score slider"
                       value="{{ old('sustainability_score', $destination->sustainability_score ?? 80) }}">

                <p class="admin-hint" id="f-score-hint">70 and above reads as a strong, low-impact choice.</p>
                @error('sustainability_score')
                    <p class="admin-error">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </fieldset>

    {{-- 2. LOCATION --}}
    <fieldset class="admin-fieldset">
        <legend class="admin-legend">
            <span class="admin-legend-num">2</span>
            Location
        </legend>

        <div class="admin-grid admin-grid-2">
            <div class="admin-field @error('location') has-error @enderror">
                <label for="f-location" class="admin-label">Location<span class="admin-required">*</span></label>
                <input id="f-location" name="location" type="text" required maxlength="255" autocomplete="off"
                       class="admin-input" placeholder="e.g. Misool, Southwest Papua"
                       value="{{ old('location', $destination->location ?? '') }}">
                @error('location')
                    <p class="admin-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field @error('region') has-error @enderror">
                <label for="f-region" class="admin-label">Region</label>
                <input id="f-region" name="region" type="text" maxlength="255" autocomplete="off"
                       class="admin-input" placeholder="e.g. Papua"
                       aria-describedby="f-region-hint"
                       value="{{ old('region', $destination->region ?? '') }}">
                <p class="admin-hint" id="f-region-hint">Shown on cards and used by the national map.</p>
                @error('region')
                    <p class="admin-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field @error('latitude') has-error @enderror">
                <label for="f-latitude" class="admin-label">Latitude</label>
                <input id="f-latitude" name="latitude" type="number" step="any" inputmode="decimal"
                       class="admin-input" placeholder="-0.5600" aria-describedby="f-coord-hint"
                       value="{{ old('latitude', $destination->latitude ?? '') }}">
                @error('latitude')
                    <p class="admin-error">{{ $message }}</p>
                @enderror
            </div>

            <div class="admin-field @error('longitude') has-error @enderror">
                <label for="f-longitude" class="admin-label">Longitude</label>
                <input id="f-longitude" name="longitude" type="number" step="any" inputmode="decimal"
                       class="admin-input" placeholder="130.5100" aria-describedby="f-coord-hint"
                       value="{{ old('longitude', $destination->longitude ?? '') }}">
                @error('longitude')
                    <p class="admin-error">{{ $message }}</p>
                @enderror
            </div>

            <p class="admin-hint admin-span-2" id="f-coord-hint" style="margin-top: -6px">
                Optional. Coordinates pin the destination on the map — leave empty if you are unsure.
            </p>
        </div>
    </fieldset>

    {{-- 3. MEDIA --}}
    <fieldset class="admin-fieldset">
        <legend class="admin-legend">
            <span class="admin-legend-num">3</span>
            Photo
        </legend>

        <div class="admin-field @error('image') has-error @enderror">
            <label for="f-image" class="admin-label">Cover image</label>
            <input id="f-image" name="image" type="file" accept="image/jpeg,image/png" class="admin-file"
                   aria-describedby="f-image-hint">
            <p class="admin-hint" id="f-image-hint">JPG or PNG, up to 5 MB. A landscape photo around 1200 × 900 works best.</p>

            @error('image')
                <p class="admin-error">{{ $message }}</p>
            @enderror

            @if ($destination && $destination->image)
                <div class="admin-preview">
                    <span class="admin-preview-tag">Current photo</span>
                    <img src="{{ $destination->image_url }}" alt="Current cover image">
                </div>

                <label class="admin-check" style="margin-top: 14px">
                    <input type="checkbox" name="remove_image" value="1" @checked(old('remove_image'))>
                    Remove the current photo
                </label>
            @endif

            <div class="admin-preview" id="image-preview" hidden>
                <span class="admin-preview-tag">New selection</span>
                <img alt="Preview of the selected file">
            </div>
        </div>
    </fieldset>

    {{-- 4. DESCRIPTION --}}
    <fieldset class="admin-fieldset">
        <legend class="admin-legend">
            <span class="admin-legend-num">4</span>
            Description
        </legend>

        <div class="admin-field @error('description') has-error @enderror">
            <label for="f-description" class="admin-label">Description<span class="admin-required">*</span></label>
            <textarea id="f-description" name="description" rows="6" required class="admin-textarea"
                      placeholder="What makes this place worth visiting — and how to visit it responsibly."
                      aria-describedby="f-description-hint">{{ old('description', $destination->description ?? '') }}</textarea>
            <p class="admin-hint" id="f-description-hint">Plain text. Two or three short paragraphs read best on mobile.</p>
            @error('description')
                <p class="admin-error">{{ $message }}</p>
            @enderror
        </div>
    </fieldset>

    <div class="admin-form-actions">
        <button type="submit" class="admin-btn admin-btn-primary">
            {{ $isEdit ? 'Save changes' : 'Create destination' }}
        </button>
        <a href="{{ route('admin.destinations.index') }}" class="admin-btn admin-btn-light">Cancel</a>

        @if ($isEdit)
            <span class="admin-form-actions-end" style="font-size: 12.5px; color: var(--muted)">
                Added {{ $destination->created_at?->diffForHumans() }}
            </span>
        @endif
    </div>
</div>

<script>
    (function () {
        var score = document.getElementById('f-score');
        var range = document.getElementById('f-score-range');
        var readout = document.getElementById('score-readout');

        if (score && range && readout) {
            var paint = function (value) {
                var v = parseInt(value, 10);
                if (isNaN(v)) { v = 0; }
                v = Math.max(0, Math.min(100, v));
                readout.textContent = v + ' / 100';
                readout.className = 'score-readout' + (v >= 70 ? ' score-readout-hi' : (v >= 40 ? ' score-readout-mid' : ' score-readout-low'));
            };

            var sync = function (source, target) {
                source.addEventListener('input', function () {
                    target.value = source.value;
                    paint(source.value);
                });
            };

            sync(score, range);
            sync(range, score);
            paint(score.value);
        }

        var image = document.getElementById('f-image');
        var preview = document.getElementById('image-preview');

        if (image && preview) {
            image.addEventListener('change', function () {
                var file = image.files && image.files[0];
                if (!file) { preview.hidden = true; return; }

                var img = preview.querySelector('img');
                if (img.src) { URL.revokeObjectURL(img.src); }
                img.src = URL.createObjectURL(file);
                preview.hidden = false;
            });
        }

        var summary = document.getElementById('admin-validation-summary');
        if (summary) { summary.focus(); }
    })();
</script>
